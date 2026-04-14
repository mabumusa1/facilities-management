<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Lease;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SubLeaseControllerTest extends TestCase
{
    use RefreshDatabase;

    private const INDEX_COMPONENT = 'sub-leases/index';

    private const CREATE_COMPONENT = 'sub-leases/create';

    protected User $user;

    protected Tenant $tenant;

    protected Community $community;

    protected Building $building;

    protected Unit $unitOne;

    protected Unit $unitTwo;

    protected Contact $tenantContact;

    protected Lease $parentLease;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        Status::factory()->create([
            'id' => 30,
            'name' => 'New',
            'domain' => 'lease',
            'slug' => 'lease_new',
        ]);
        Status::factory()->create([
            'id' => 31,
            'name' => 'Active',
            'domain' => 'lease',
            'slug' => 'lease_active',
        ]);
        Status::factory()->create([
            'id' => 32,
            'name' => 'Expired',
            'domain' => 'lease',
            'slug' => 'lease_expired',
        ]);
        Status::factory()->create([
            'id' => 33,
            'name' => 'Cancelled',
            'domain' => 'lease',
            'slug' => 'lease_cancelled',
        ]);
        Status::factory()->create([
            'id' => 34,
            'name' => 'Closed',
            'domain' => 'lease',
            'slug' => 'lease_closed',
        ]);

        $this->community = Community::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        $this->building = Building::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
        ]);

        $this->unitOne = Unit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
        ]);

        $this->unitTwo = Unit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
        ]);

        $this->tenantContact = Contact::factory()->tenant()->create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'Samira',
            'last_name' => 'Tenant',
            'email' => 'samira.tenant@example.com',
            'phone_number' => '+966500001111',
            'national_phone_number' => '0500001111',
            'phone_country_code' => 'SA',
        ]);

        $this->parentLease = Lease::factory()->forTenant($this->tenantContact->id)->create([
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => 31,
            'is_sub_lease' => false,
            'contract_number' => 'PARENT-SL-0001',
            'start_date' => now()->subMonth()->toDateString(),
            'end_date' => now()->addMonths(11)->toDateString(),
        ]);

        $this->parentLease->units()->sync([$this->unitOne->id]);
    }

    public function test_index_displays_subleases_list(): void
    {
        $subLease = $this->createSubLease([
            'contract_number' => 'SUB-INDEX-0001',
        ]);

        $response = $this->actingAs($this->user)->get(route('sub-leases.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INDEX_COMPONENT)
            ->has('subleases.data', 1)
            ->where('subleases.data.0.id', $subLease->id)
            ->has('statistics')
            ->has('filters')
        );
    }

    public function test_index_scopes_results_to_authenticated_users_tenant(): void
    {
        $tenantSubLease = $this->createSubLease([
            'contract_number' => 'SUB-TENANT-ONLY',
        ]);

        $otherTenant = Tenant::factory()->create();
        $otherTenantContact = Contact::factory()->tenant()->forTenant($otherTenant)->create();

        $otherParentLease = Lease::factory()->forTenant($otherTenantContact->id)->create([
            'status_id' => 31,
            'is_sub_lease' => false,
            'contract_number' => 'PARENT-OTHER-0001',
        ]);

        Lease::factory()->forTenant($otherTenantContact->id)->create([
            'status_id' => 31,
            'is_sub_lease' => true,
            'parent_lease_id' => $otherParentLease->id,
            'contract_number' => 'SUB-OTHER-0001',
        ]);

        $response = $this->actingAs($this->user)->get(route('sub-leases.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INDEX_COMPONENT)
            ->has('subleases.data', 1)
            ->where('subleases.data.0.id', $tenantSubLease->id)
        );
    }

    public function test_index_can_search_by_tenant_name(): void
    {
        $matchingSubLease = $this->createSubLease([
            'tenant_id' => $this->tenantContact->id,
            'contract_number' => 'SUB-SEARCH-0001',
        ]);

        $otherTenantContact = Contact::factory()->tenant()->forTenant($this->tenant)->create([
            'first_name' => 'Different',
            'last_name' => 'Person',
        ]);

        $this->createSubLease([
            'tenant_id' => $otherTenantContact->id,
            'contract_number' => 'SUB-SEARCH-0002',
        ]);

        $response = $this->actingAs($this->user)->get(route('sub-leases.index', [
            'search' => 'Samira',
        ]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::INDEX_COMPONENT)
            ->where('filters.search', 'Samira')
            ->has('subleases.data', 1)
            ->where('subleases.data.0.id', $matchingSubLease->id)
        );
    }

    public function test_create_displays_normalized_tenant_data(): void
    {
        $response = $this->actingAs($this->user)->get(route('sub-leases.create'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component(self::CREATE_COMPONENT)
            ->has('parentLeases', 1)
            ->where('tenants.0.id', $this->tenantContact->id)
            ->where('tenants.0.name', $this->tenantContact->name)
            ->where('tenants.0.email', $this->tenantContact->email)
            ->where('tenants.0.phone', $this->tenantContact->phone_number)
        );
    }

    public function test_store_creates_sublease_and_syncs_units(): void
    {
        $payload = [
            'parent_lease_id' => $this->parentLease->id,
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => 30,
            'contract_number' => 'SUB-STORE-0001',
            'tenant_type' => 'individual',
            'rental_type' => 'detailed',
            'rental_total_amount' => 18000,
            'security_deposit_amount' => 2000,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'number_of_years' => 1,
            'number_of_months' => 0,
            'terms_conditions' => 'Sub-lease terms',
            'units' => [
                ['id' => $this->unitOne->id],
                ['id' => $this->unitTwo->id],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('sub-leases.store'), $payload);

        $subLease = Lease::query()->where('contract_number', 'SUB-STORE-0001')->firstOrFail();

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('sub-leases.show', $subLease));

        $this->assertDatabaseHas('leases', [
            'id' => $subLease->id,
            'is_sub_lease' => true,
            'parent_lease_id' => $this->parentLease->id,
            'tenant_id' => $this->tenantContact->id,
        ]);

        $this->assertDatabaseHas('lease_units', [
            'lease_id' => $subLease->id,
            'unit_id' => $this->unitOne->id,
        ]);

        $this->assertDatabaseHas('lease_units', [
            'lease_id' => $subLease->id,
            'unit_id' => $this->unitTwo->id,
        ]);
    }

    public function test_update_modifies_sublease_and_syncs_units(): void
    {
        $subLease = $this->createSubLease([
            'contract_number' => 'SUB-UPDATE-0001',
            'status_id' => 30,
            'rental_total_amount' => 14000,
        ]);

        $subLease->units()->sync([$this->unitOne->id]);

        $payload = [
            'parent_lease_id' => $this->parentLease->id,
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => 31,
            'contract_number' => 'SUB-UPDATE-0002',
            'tenant_type' => 'individual',
            'rental_type' => 'detailed',
            'rental_total_amount' => 22000,
            'security_deposit_amount' => 3000,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'number_of_years' => 1,
            'number_of_months' => 0,
            'terms_conditions' => 'Updated terms',
            'units' => [
                ['id' => $this->unitTwo->id],
            ],
        ];

        $response = $this->actingAs($this->user)->put(route('sub-leases.update', $subLease), $payload);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('sub-leases.show', $subLease));

        $this->assertDatabaseHas('leases', [
            'id' => $subLease->id,
            'status_id' => 31,
            'contract_number' => 'SUB-UPDATE-0002',
            'rental_total_amount' => 22000,
        ]);

        $this->assertDatabaseHas('lease_units', [
            'lease_id' => $subLease->id,
            'unit_id' => $this->unitTwo->id,
        ]);

        $this->assertDatabaseMissing('lease_units', [
            'lease_id' => $subLease->id,
            'unit_id' => $this->unitOne->id,
        ]);
    }

    public function test_show_and_edit_return_not_found_for_non_sublease(): void
    {
        $regularLease = Lease::factory()->forTenant($this->tenantContact->id)->create([
            'status_id' => 31,
            'is_sub_lease' => false,
            'contract_number' => 'LEASE-NON-SUB-0001',
        ]);

        $showResponse = $this->actingAs($this->user)->get(route('sub-leases.show', $regularLease));
        $showResponse->assertNotFound();

        $editResponse = $this->actingAs($this->user)->get(route('sub-leases.edit', $regularLease));
        $editResponse->assertNotFound();
    }

    public function test_destroy_soft_deletes_sublease(): void
    {
        $subLease = $this->createSubLease([
            'contract_number' => 'SUB-DELETE-0001',
        ]);

        $response = $this->actingAs($this->user)->delete(route('sub-leases.destroy', $subLease));

        $response->assertRedirect(route('sub-leases.index'));
        $this->assertSoftDeleted('leases', ['id' => $subLease->id]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    protected function createSubLease(array $overrides = []): Lease
    {
        $subLease = Lease::factory()->forTenant($overrides['tenant_id'] ?? $this->tenantContact->id)->create(array_merge([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => 30,
            'parent_lease_id' => $this->parentLease->id,
            'is_sub_lease' => true,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addYear()->toDateString(),
            'rental_total_amount' => 10000,
            'security_deposit_amount' => 1000,
        ], $overrides));

        $subLease->units()->sync([$this->unitOne->id]);

        return $subLease;
    }
}
