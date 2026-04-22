<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\Community;
use App\Models\Dependent;
use App\Models\Lease;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\ServiceSetting;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentBBatchEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent B Contract Tenant']);

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
            'rf.requests.categories.destroy',
            'rf.requests.service-settings.destroy',
            'rf.requests.types.destroy',
            'rf.sub-leases.destroy',
            'rf.tenants.family-members.destroy',
            'rf.tenants.destroy',
            'rf.transactions.destroy',
            'rf.units.destroy',
            'dashboard.statistics',
            'marketplace-admin.communities.index',
            'marketplace-admin.communities.list-index',
            'rf.admins.index',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_requests_delete_aliases_return_json_and_remove_records(): void
    {
        $tenant = $this->authenticateUser();

        $category = RequestCategory::factory()->create();

        $serviceSetting = ServiceSetting::factory()->create([
            'category_id' => $category->id,
        ]);

        $type = RequestSubcategory::factory()->create([
            'category_id' => $category->id,
        ]);

        $serviceSettingDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.requests.service-settings.destroy', $serviceSetting));

        $serviceSettingDelete
            ->assertOk()
            ->assertJsonPath('data.id', $serviceSetting->id)
            ->assertJsonPath('message', 'Service settings deleted.');

        $this->assertDatabaseMissing('rf_service_settings', [
            'id' => $serviceSetting->id,
        ]);

        $typeDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.requests.types.destroy', $type));

        $typeDelete
            ->assertOk()
            ->assertJsonPath('data.id', $type->id)
            ->assertJsonPath('message', 'Type deleted.');

        $this->assertDatabaseMissing('rf_request_subcategories', [
            'id' => $type->id,
        ]);

        $categoryDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.requests.categories.destroy', $category));

        $categoryDelete
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('message', 'Category deleted.');

        $this->assertDatabaseMissing('rf_request_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_rf_delete_aliases_for_leasing_contacts_transactions_and_properties_work(): void
    {
        $tenant = $this->authenticateUser();

        $sublease = Lease::factory()->create([
            'account_tenant_id' => $tenant->id,
            'is_sub_lease' => true,
        ]);

        $resident = Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $familyMember = Dependent::factory()->create([
            'dependable_type' => Resident::class,
            'dependable_id' => $resident->id,
        ]);

        $transaction = Transaction::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $unit = Unit::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $subleaseDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.sub-leases.destroy', $sublease));

        $subleaseDelete
            ->assertOk()
            ->assertJsonPath('data.id', $sublease->id)
            ->assertJsonPath('message', 'Lease deleted.');

        $this->assertSoftDeleted('rf_leases', [
            'id' => $sublease->id,
        ]);

        $familyMemberDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.tenants.family-members.destroy', [
                'resident' => $resident,
                'dependent' => $familyMember,
            ]));

        $familyMemberDelete
            ->assertOk()
            ->assertJsonPath('data.id', $familyMember->id)
            ->assertJsonPath('message', 'Family member deleted.');

        $this->assertDatabaseMissing('rf_dependents', [
            'id' => $familyMember->id,
        ]);

        $tenantDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.tenants.destroy', $resident));

        $tenantDelete
            ->assertOk()
            ->assertJsonPath('data.id', $resident->id)
            ->assertJsonPath('message', 'Tenant deleted.');

        $this->assertSoftDeleted('rf_tenants', [
            'id' => $resident->id,
        ]);

        $transactionDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.transactions.destroy', $transaction));

        $transactionDelete
            ->assertOk()
            ->assertJsonPath('data.id', $transaction->id)
            ->assertJsonPath('message', 'Transaction deleted.');

        $this->assertSoftDeleted('rf_transactions', [
            'id' => $transaction->id,
        ]);

        $unitDelete = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->deleteJson(route('rf.units.destroy', $unit));

        $unitDelete
            ->assertOk()
            ->assertJsonPath('data.id', $unit->id)
            ->assertJsonPath('message', 'Unit deleted.');

        $this->assertDatabaseMissing('rf_units', [
            'id' => $unit->id,
        ]);
    }

    public function test_dashboard_statistics_endpoint_returns_expected_shape(): void
    {
        $tenant = $this->authenticateUser();

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('dashboard.statistics'));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'requests_approval',
                    'pending_complaints',
                    'expiring_leases',
                    'overdue_recipes',
                    'communities',
                    'buildings',
                    'units',
                    'tenants',
                    'activeLeases',
                    'openRequests',
                    'pendingTransactions',
                    'totalRevenue',
                    'expiringLeases',
                    'overdueTransactions',
                ],
            ]);
    }

    public function test_marketplace_communities_and_rf_admins_endpoints_return_contract_like_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $admin = Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
            'first_name' => 'Atar',
            'last_name' => 'Support',
            'phone_number' => '0502879676',
            'full_phone_number' => '+966502879676',
            'role' => 'Admins',
        ]);

        $listedCommunity = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
            'name' => 'Listed Community',
            'is_market_place' => true,
        ]);

        $unlistedCommunity = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
            'name' => 'Unlisted Community',
            'is_market_place' => false,
        ]);

        $adminsResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.admins.index'));

        $adminsResponse
            ->assertOk()
            ->assertJsonPath('data.0.id', $admin->id)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'image',
                        'phone_number',
                        'phone_country_code',
                        'national_id',
                        'email',
                        'role',
                        'created_at',
                        'types',
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

        $communitiesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('marketplace-admin.communities.index'));

        $communitiesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'list',
                    'paginator' => [
                        'current_page',
                        'from',
                        'last_page',
                        'path',
                        'per_page',
                        'to',
                        'total',
                    ],
                ],
            ]);

        $listedResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('marketplace-admin.communities.list-index'));

        $listedResponse->assertOk();

        $listedIds = collect($listedResponse->json('data.list'))->pluck('id')->all();

        $this->assertContains($listedCommunity->id, $listedIds);
        $this->assertNotContains($unlistedCommunity->id, $listedIds);
    }
}
