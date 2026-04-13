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

class LeaseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Tenant $tenant;

    protected Community $community;

    protected Building $building;

    protected Unit $unit;

    protected Contact $tenantContact;

    protected Status $statusNew;

    protected Status $statusActive;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Create tenant and user
        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Create property hierarchy
        $this->community = Community::factory()->create([
            'tenant_id' => $this->tenant->id,
        ]);
        $this->building = Building::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
        ]);
        $this->unit = Unit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
        ]);

        // Create tenant contact
        $this->tenantContact = Contact::factory()->tenant()->create([
            'tenant_id' => $this->tenant->id,
        ]);

        // Create statuses
        $this->statusNew = Status::factory()->create([
            'id' => 30,
            'name' => 'New',
            'domain' => 'lease',
            'slug' => 'lease_new',
        ]);
        $this->statusActive = Status::factory()->create([
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

        // Create unit statuses
        Status::factory()->create([
            'id' => 25,
            'name' => 'Rented',
            'domain' => 'unit',
            'slug' => 'unit_rented',
        ]);
        Status::factory()->create([
            'id' => 26,
            'name' => 'Available',
            'domain' => 'unit',
            'slug' => 'unit_available',
        ]);
    }

    public function test_index_displays_leases_list(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
        ]);

        $response = $this->actingAs($this->user)->get('/leases');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('leases/index')
            ->has('leases')
            ->has('statistics')
            ->has('filters')
        );
    }

    public function test_index_can_filter_by_status(): void
    {
        Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
        ]);
        Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusNew->id,
        ]);

        $response = $this->actingAs($this->user)->get('/leases?status=active');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('leases/index')
            ->where('filters.status', 'active')
        );
    }

    public function test_index_can_search_leases(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'contract_number' => 'TEST123456',
            'status_id' => $this->statusActive->id,
        ]);

        $response = $this->actingAs($this->user)->get('/leases?search=TEST123456');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('leases/index')
            ->where('filters.search', 'TEST123456')
        );
    }

    public function test_create_displays_wizard_form(): void
    {
        $response = $this->actingAs($this->user)->get('/leases/create');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('leases/create')
            ->has('step')
            ->has('communities')
            ->has('buildings')
            ->has('availableUnits')
            ->has('tenants')
            ->has('statuses')
        );
    }

    public function test_create_wizard_can_navigate_steps(): void
    {
        $response = $this->actingAs($this->user)->get('/leases/create?step=2');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('leases/create')
            ->where('step', 2)
        );
    }

    public function test_save_step_stores_wizard_data_in_session(): void
    {
        $response = $this->actingAs($this->user)
            ->postJson('/leases/wizard/save-step', [
                'step' => 1,
                'data' => ['units' => [$this->unit->id]],
            ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_store_creates_new_lease(): void
    {
        $leaseData = [
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'units' => [
                [
                    'id' => $this->unit->id,
                    'rental_annual_type' => 'total',
                    'annual_rental_amount' => 50000,
                    'net_area' => 100,
                    'meter_cost' => 500,
                ],
            ],
            'tenant_type' => 'individual',
            'rental_type' => 'detailed',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addYear()->format('Y-m-d'),
            'rental_total_amount' => 50000,
        ];

        $response = $this->actingAs($this->user)->post('/leases', $leaseData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('leases', [
            'tenant_id' => $this->tenantContact->id,
            'tenant_type' => 'individual',
            'rental_total_amount' => 50000,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/leases', []);

        $response->assertSessionHasErrors(['tenant_id', 'units', 'start_date', 'end_date', 'rental_total_amount']);
    }

    public function test_store_validates_end_date_after_start_date(): void
    {
        $leaseData = [
            'tenant_id' => $this->tenantContact->id,
            'units' => [
                ['id' => $this->unit->id],
            ],
            'tenant_type' => 'individual',
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->subDay()->format('Y-m-d'),
            'rental_total_amount' => 50000,
        ];

        $response = $this->actingAs($this->user)->post('/leases', $leaseData);

        $response->assertSessionHasErrors('end_date');
    }

    public function test_show_displays_lease_details(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
        ]);

        $response = $this->actingAs($this->user)->get("/leases/{$lease->id}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('leases/show')
            ->has('lease')
            ->has('canRenew')
            ->has('canTerminate')
        );
    }

    public function test_edit_displays_edit_form(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusNew->id,
        ]);

        $response = $this->actingAs($this->user)->get("/leases/{$lease->id}/edit");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('leases/edit')
            ->has('lease')
            ->has('communities')
            ->has('buildings')
            ->has('units')
            ->has('tenants')
            ->has('statuses')
        );
    }

    public function test_update_modifies_lease(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusNew->id,
            'rental_total_amount' => 50000,
        ]);

        $response = $this->actingAs($this->user)
            ->put("/leases/{$lease->id}", [
                'rental_total_amount' => 60000,
            ]);

        $response->assertRedirect("/leases/{$lease->id}");
        $this->assertDatabaseHas('leases', [
            'id' => $lease->id,
            'rental_total_amount' => 60000,
        ]);
    }

    public function test_destroy_deletes_lease(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusNew->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/leases/{$lease->id}");

        $response->assertRedirect('/leases');
        $this->assertSoftDeleted('leases', ['id' => $lease->id]);
    }

    public function test_activate_changes_lease_status_to_active(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusNew->id,
        ]);

        $response = $this->actingAs($this->user)->post("/leases/{$lease->id}/activate");

        $response->assertRedirect();
        $this->assertDatabaseHas('leases', [
            'id' => $lease->id,
            'status_id' => $this->statusActive->id,
        ]);
    }

    public function test_terminate_changes_lease_status_to_cancelled(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
        ]);

        $response = $this->actingAs($this->user)->post("/leases/{$lease->id}/terminate");

        $response->assertRedirect();
        $this->assertDatabaseHas('leases', [
            'id' => $lease->id,
            'status_id' => 33, // Cancelled
        ]);
    }

    public function test_move_out_closes_lease(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
        ]);

        $response = $this->actingAs($this->user)->post("/leases/{$lease->id}/move-out");

        $response->assertRedirect();
        $lease->refresh();
        $this->assertTrue($lease->is_move_out);
        $this->assertNotNull($lease->actual_end_at);
    }

    public function test_api_list_returns_leases_json(): void
    {
        Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/leases');

        $response->assertOk();
        $response->assertJsonStructure([
            'leases' => [
                'data' => [
                    '*' => [
                        'id',
                        'contract_number',
                        'tenant_id',
                        'status_id',
                        'start_date',
                        'end_date',
                    ],
                ],
            ],
        ]);
    }

    public function test_api_statistics_returns_lease_statistics(): void
    {
        Lease::factory()->count(3)->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/leases/statistics');

        $response->assertOk();
        $response->assertJsonStructure([
            'total',
            'active',
        ]);
    }

    public function test_api_expiring_returns_expiring_leases(): void
    {
        Lease::factory()->expiringSoon(15)->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/leases/expiring');

        $response->assertOk();
        $response->assertJsonStructure([
            'leases',
        ]);
    }

    public function test_api_available_units_returns_available_units(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/leases/available-units');

        $response->assertOk();
        $response->assertJsonStructure([
            'units',
        ]);
    }

    public function test_unauthenticated_user_cannot_access_leases(): void
    {
        $response = $this->get('/leases');

        $response->assertRedirect('/login');
    }

    public function test_renew_form_displays_for_active_lease(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
            'is_renew' => false,
            'is_move_out' => false,
        ]);

        $response = $this->actingAs($this->user)->get("/leases/{$lease->id}/renew");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('leases/renew')
            ->has('originalLease')
            ->has('renewalDefaults')
            ->has('communities')
            ->has('buildings')
            ->has('units')
            ->has('tenants')
        );
    }

    public function test_renew_creates_new_lease(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
            'is_renew' => false,
            'is_move_out' => false,
            'rental_total_amount' => 50000,
        ]);
        $lease->units()->attach($this->unit->id, [
            'rental_annual_type' => 'total',
            'annual_rental_amount' => 50000,
            'net_area' => 100,
            'meter_cost' => 500,
        ]);

        $renewalData = [
            'start_date' => now()->addYear()->format('Y-m-d'),
            'end_date' => now()->addYears(2)->format('Y-m-d'),
            'rental_total_amount' => 55000,
            'rental_type' => 'detailed',
            'units' => [
                [
                    'id' => $this->unit->id,
                    'rental_annual_type' => 'total',
                    'annual_rental_amount' => 55000,
                    'net_area' => 100,
                    'meter_cost' => 550,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post("/leases/{$lease->id}/renew", $renewalData);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Check original lease is marked as renewed
        $lease->refresh();
        $this->assertTrue($lease->is_renew);

        // Check new lease was created
        $this->assertDatabaseHas('leases', [
            'parent_lease_id' => $lease->id,
            'rental_total_amount' => 55000,
            'tenant_id' => $this->tenantContact->id,
        ]);
    }

    public function test_renew_fails_for_already_renewed_lease(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
            'is_renew' => true, // Already renewed
        ]);

        $renewalData = [
            'start_date' => now()->addYear()->format('Y-m-d'),
            'end_date' => now()->addYears(2)->format('Y-m-d'),
            'rental_total_amount' => 55000,
        ];

        $response = $this->actingAs($this->user)->post("/leases/{$lease->id}/renew", $renewalData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_renew_fails_for_moved_out_lease(): void
    {
        $lease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
            'is_move_out' => true, // Already moved out
        ]);

        $renewalData = [
            'start_date' => now()->addYear()->format('Y-m-d'),
            'end_date' => now()->addYears(2)->format('Y-m-d'),
            'rental_total_amount' => 55000,
        ];

        $response = $this->actingAs($this->user)->post("/leases/{$lease->id}/renew", $renewalData);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_api_renewal_history_returns_lease_chain(): void
    {
        $parentLease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusActive->id,
            'is_renew' => true,
        ]);

        $childLease = Lease::factory()->create([
            'tenant_id' => $this->tenantContact->id,
            'community_id' => $this->community->id,
            'building_id' => $this->building->id,
            'status_id' => $this->statusNew->id,
            'parent_lease_id' => $parentLease->id,
        ]);

        $response = $this->actingAs($this->user)->getJson("/api/leases/{$childLease->id}/renewal-history");

        $response->assertOk();
        $response->assertJsonStructure([
            'history',
        ]);
    }
}
