<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Admin;
use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use App\Models\ManagerRole;
use App\Models\ServiceManagerType;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentCBatchGetEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent C Contract Tenant']);

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
            'rf.admins.show',
            'rf.admins.manager-roles',
            'rf.announcements.index',
            'rf.buildings.index',
            'rf.buildings.show',
            'rf.communities.index',
            'rf.communities.show',
            'rf.communities.edaat-product-codes',
            'rf.communities.edaat.product-codes',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_rf_admin_show_and_manager_roles_endpoints_return_contract_like_payloads(): void
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

        ManagerRole::query()->create([
            'role' => 'Admins',
            'name_ar' => 'مدير',
            'name_en' => 'Admin',
        ]);

        ManagerRole::query()->create([
            'role' => 'serviceManagers',
            'name_ar' => 'مسؤول الخدمات',
            'name_en' => 'Service Manager',
        ]);

        ServiceManagerType::query()->create([
            'name' => 'Home Services Requests',
            'name_ar' => 'طلبات خدمات المنازل',
            'name_en' => 'Home Services Requests',
        ]);

        $showResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.admins.show', $admin));

        $showResponse
            ->assertOk()
            ->assertJsonPath('data.id', $admin->id)
            ->assertJsonPath('data.first_name', 'Atar')
            ->assertJsonPath('data.last_name', 'Support')
            ->assertJsonPath('data.role', 'Admins')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'first_name',
                    'last_name',
                    'image',
                    'email',
                    'georgian_birthdate',
                    'gender',
                    'national_id',
                    'full_phone_number',
                    'phone_number',
                    'phone_country_code',
                    'nationality',
                    'role',
                    'selects' => [
                        'is_all_buildings',
                        'is_all_communities',
                    ],
                    'created_at',
                    'last_login_at',
                    'active',
                    'types',
                ],
                'message',
            ]);

        $literalShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/admins/'.$admin->id);

        $literalShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $admin->id);

        $managerRolesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.admins.manager-roles'));

        $managerRolesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'role',
                        'name_ar',
                        'name_en',
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

        $roles = collect($managerRolesResponse->json('data'));

        $this->assertTrue($roles->contains(fn (array $role): bool => $role['role'] === 'Admins'));
        $this->assertTrue($roles->contains(fn (array $role): bool => $role['role'] === 'serviceManagers'));
    }

    public function test_rf_announcements_endpoint_returns_paginated_json_payload(): void
    {
        $tenant = $this->authenticateUser();

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $building = Building::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'city_id' => $community->city_id,
            'district_id' => $community->district_id,
        ]);

        $announcement = Announcement::factory()->create([
            'account_tenant_id' => $tenant->id,
            'community_id' => $community->id,
            'building_id' => $building->id,
            'title' => 'Agent C Announcement',
        ]);

        $response = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.announcements.index'));

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'title',
                        'content',
                        'status',
                        'published_at',
                        'community',
                        'building',
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

        $ids = collect($response->json('data'))->pluck('id')->all();

        $this->assertContains($announcement->id, $ids);
    }

    public function test_rf_buildings_index_and_show_endpoints_return_expected_shape(): void
    {
        $tenant = $this->authenticateUser();

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $building = Building::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'city_id' => $community->city_id,
            'district_id' => $community->district_id,
            'no_floors' => 8,
        ]);

        $indexResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.buildings.index'));

        $indexResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'community',
                        'city',
                        'district',
                        'units_count',
                        'map',
                        'year_build',
                        'images',
                        'is_selected_property',
                        'count_selected_property',
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

        $ids = collect($indexResponse->json('data'))->pluck('id')->all();

        $this->assertContains($building->id, $ids);

        $showResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.buildings.show', $building));

        $showResponse
            ->assertOk()
            ->assertJsonPath('data.id', $building->id)
            ->assertJsonPath('data.community.id', $community->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'community',
                    'city',
                    'district',
                    'no_floors',
                    'year_build',
                    'map',
                    'images',
                    'documents',
                    'units',
                ],
                'message',
            ]);

        $literalShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/buildings/'.$building->id);

        $literalShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $building->id);
    }

    public function test_rf_communities_endpoints_and_edaat_aliases_return_expected_shape(): void
    {
        $tenant = $this->authenticateUser();

        $communityWithCode = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
            'name' => 'Alpha Community',
            'product_code' => 'EDAAT-001',
        ]);

        Community::factory()->create([
            'account_tenant_id' => $tenant->id,
            'name' => 'Beta Community',
            'product_code' => null,
        ]);

        $indexResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.communities.index'));

        $indexResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'city',
                        'district',
                        'sales_commission_rate',
                        'rental_commission_rate',
                        'buildings_count',
                        'units_count',
                        'map',
                        'images',
                        'is_selected_property',
                        'count_selected_property',
                        'requests_count',
                        'total_income',
                        'is_market_place',
                        'is_buy',
                        'community_marketplace_type',
                        'is_off_plan_sale',
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

        $showResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.communities.show', $communityWithCode));

        $showResponse
            ->assertOk()
            ->assertJsonPath('data.id', $communityWithCode->id)
            ->assertJsonPath('data.product_code', 'EDAAT-001')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'country',
                    'currency',
                    'city',
                    'district',
                    'amenities',
                    'map',
                    'images',
                    'documents',
                    'buildings_count',
                    'units_count',
                    'requests_count',
                    'total_income',
                    'sales_commission_rate',
                    'rental_commission_rate',
                    'product_code',
                    'license_number',
                    'license_issue_date',
                    'license_expiry_date',
                    'record_payments',
                    'additional_payments',
                    'completion_percent',
                    'allow_cash_sale',
                    'allow_bank_financing',
                    'listed_percentage',
                    'is_market_place',
                    'is_buy',
                    'community_marketplace_type',
                    'is_off_plan_sale',
                ],
                'message',
            ]);

        $legacyEdaatCodesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.communities.edaat-product-codes'));

        $legacyEdaatCodesResponse
            ->assertOk()
            ->assertJsonPath('data.0.product_code', 'EDAAT-001');

        $newEdaatCodesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.communities.edaat.product-codes'));

        $newEdaatCodesResponse
            ->assertOk()
            ->assertJsonPath('data.0.product_code', 'EDAAT-001');

        $this->assertSame(
            $legacyEdaatCodesResponse->json('data'),
            $newEdaatCodesResponse->json('data'),
            'Both EDAAT product-codes aliases must return the same payload.'
        );

        $this->assertCount(1, $newEdaatCodesResponse->json('data'));
    }
}
