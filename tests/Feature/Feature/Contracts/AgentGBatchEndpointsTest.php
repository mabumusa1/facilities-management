<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentGBatchEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent G Contract Tenant']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($user);

        return $tenant;
    }

    public function test_assigned_route_names_exist(): void
    {
        $expectedRoutes = [
            'rf.tenants.show',
            'rf.transactions.index',
            'rf.transactions.show',
            'rf.units.index',
            'rf.units.show',
            'rf.units.create',
            'rf.users.requests.index',
            'rf.users.requests.categories',
            'rf.users.requests.types',
            'rf.users.visitor-access',
            'marketplace-admin.listings.store',
            'marketplace-admin.units.prices-visibility.legacy',
            'marketplace-admin.visits.cancel.legacy',
            'rf.admins.store',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_rf_tenant_transaction_and_unit_aliases_return_json_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $resident = Resident::factory()->create([
            'id' => 23,
            'account_tenant_id' => $tenant->id,
            'first_name' => 'Tenant',
            'last_name' => 'TwentyThree',
            'phone_country_code' => 'SA',
            'phone_number' => '542870128',
            'national_phone_number' => '0542870128',
        ]);

        $transaction = Transaction::factory()->create([
            'id' => 1,
            'account_tenant_id' => $tenant->id,
            'assignee_type' => Resident::class,
            'assignee_id' => $resident->id,
            'lease_number' => '2026000001RL',
            'amount' => 50000,
            'tax_amount' => 0,
            'rental_amount' => 50000,
            'additional_fees_amount' => 0,
            'vat' => 0,
        ]);

        $units = Unit::factory()->count(3)->create([
            'account_tenant_id' => $tenant->id,
            'name' => 'Agent G Unit',
        ]);

        $unitForShow = $units->last();

        $tenantResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/tenants/23');

        $tenantResponse
            ->assertOk()
            ->assertJsonPath('data.id', 23)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'first_name',
                    'last_name',
                    'phone_number',
                    'units',
                    'leases',
                    'active_requests',
                    'transaction',
                    'accepted_invite',
                ],
                'message',
            ]);

        $transactionsIndex = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/transactions');

        $transactionsIndex
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'amount',
                        'tax_amount',
                        'is_paid',
                        'due_on',
                        'details',
                        'lease',
                        'unit',
                        'status',
                        'category',
                        'subcategory',
                        'type',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $transactionsShow = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/transactions/1');

        $transactionsShow
            ->assertOk()
            ->assertJsonPath('data.id', $transaction->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'tax_amount',
                    'rental_amount',
                    'additional_fees_amount',
                    'status',
                    'type',
                    'is_paid',
                ],
                'message',
            ]);

        $unitsIndex = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/units');

        $unitsIndex
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    [
                        'id',
                        'name',
                        'category',
                        'type',
                        'rf_community',
                        'rf_building',
                        'status',
                        'is_market_place',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $unitsShow = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/units/'.$unitForShow->id);

        $unitsShow
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonPath('data.id', $unitForShow->id)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    'id',
                    'name',
                    'status',
                    'rf_community',
                    'rf_building',
                    'photos',
                    'floor_plans',
                    'documents',
                    'specifications',
                    'marketplace',
                    'rooms',
                    'areas',
                    'merge_document',
                ],
                'meta',
            ]);

        $unitsCreate = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/units/create');

        $unitsCreate
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'communities',
                    'buildings',
                    'categories',
                    'statuses',
                    'owners',
                    'residents',
                    'cities',
                    'districts',
                ],
                'meta',
            ]);
    }

    public function test_rf_users_request_aliases_and_visitor_access_alias_return_json(): void
    {
        $tenant = $this->authenticateUser();

        $requestStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'New',
            'name_en' => 'New',
        ]);

        $category = RequestCategory::factory()->create();

        $subcategory = RequestSubcategory::factory()->create([
            'category_id' => $category->id,
        ]);

        $requester = Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        ServiceRequest::factory()->create([
            'account_tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'status_id' => $requestStatus->id,
            'requester_type' => Resident::class,
            'requester_id' => $requester->id,
        ]);

        $unit = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $listing = MarketplaceUnit::factory()->create([
            'unit_id' => $unit->id,
        ]);

        $visitStatus = Status::factory()->create([
            'type' => 'property_visit',
            'name' => 'Pending',
            'name_en' => 'Pending',
        ]);

        MarketplaceVisit::factory()->create([
            'marketplace_unit_id' => $listing->id,
            'status_id' => $visitStatus->id,
        ]);

        $requests = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/users/requests');

        $requests
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'title',
                        'description',
                        'request_code',
                        'priority',
                        'category',
                        'sub_category',
                        'status',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $categories = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/users/requests/categories');

        $categories
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'status',
                        'sub_categories',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $types = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/users/requests/types');

        $types
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name_ar',
                        'name_en',
                        'name',
                        'status',
                        'rf_sub_category_id',
                        'icon',
                        'fee_type',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);

        $visitorAccess = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/users/visitor-access');

        $visitorAccess
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'marketplace_unit_id',
                        'unit',
                        'status',
                        'visitor_name',
                        'visitor_phone',
                        'scheduled_at',
                        'notes',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                ],
            ]);
    }

    public function test_marketplace_admin_post_aliases_handle_validation_and_legacy_paths(): void
    {
        $tenant = $this->authenticateUser();

        $unit = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        while ((int) (MarketplaceUnit::query()->max('id') ?? 0) < 3) {
            MarketplaceUnit::factory()->create(['unit_id' => $unit->id]);
        }

        $listingValidation = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/marketplace/admin/listings', []);

        $listingValidation
            ->assertStatus(422)
            ->assertJsonValidationErrors(['unit_id', 'listing_type', 'price']);

        $listingStore = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/marketplace/admin/listings', [
                'unit_id' => (string) $unit->id,
                'listing_type' => 'rent',
                'price' => 50000,
                'is_active' => true,
            ]);

        $listingStore
            ->assertOk()
            ->assertJsonPath('data.unit_id', (string) $unit->id)
            ->assertJsonPath('data.listing_type', 'rent');

        $priceVisibility = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/marketplace/admin/units/prices-visibility/3', [
                'show_price' => false,
            ]);

        $priceVisibility
            ->assertOk()
            ->assertJsonPath('data.id', 3)
            ->assertJsonPath('data.show_price', false);

        $this->assertDatabaseHas('rf_marketplace_units', [
            'id' => 3,
            'is_active' => false,
        ]);

        $cancelUnknownVisit = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/marketplace/admin/visits/cancel/999', [
                'reason' => 'Test cancellation',
            ]);

        $cancelUnknownVisit->assertNotFound();
    }

    public function test_rf_admins_post_alias_enforces_required_fields_and_creates_record(): void
    {
        $tenant = $this->authenticateUser();

        $validation = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/admins', []);

        $validation
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'phone_country_code',
                'phone_number',
                'role',
            ]);

        $store = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->postJson('/rf/admins', [
                'first_name' => 'Test',
                'last_name' => 'Manager',
                'phone_country_code' => 'SA',
                'phone_number' => '510216236',
                'email' => 'manager@example.com',
                'role' => 'Admins',
            ]);

        $store
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Test')
            ->assertJsonPath('data.last_name', 'Manager')
            ->assertJsonPath('data.role', 'Admins');

        $this->assertDatabaseHas('rf_admins', [
            'first_name' => 'Test',
            'last_name' => 'Manager',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assertGreaterThan(0, Admin::query()->count());
    }
}
