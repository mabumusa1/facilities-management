<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\Community;
use App\Models\FeaturedService;
use App\Models\Lease;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\ServiceSetting;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\UnitCategory;
use App\Models\User;
use App\Models\WorkingDay;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentFBatchGetEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent F Contract Tenant']);

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
            'rf.requests.service-settings.show',
            'rf.requests.sub-categories.index',
            'rf.requests.sub-categories.show',
            'rf.requests.types.index',
            'rf.requests.types.create',
            'rf.requests.types.list',
            'rf.requests.types.show',
            'rf.sub-leases.index',
            'rf.tenants.index',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_rf_request_service_settings_and_sub_categories_endpoints_return_contract_like_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $category = RequestCategory::factory()->create([
            'name' => 'Maintenance',
            'name_ar' => 'الصيانة',
            'name_en' => 'Maintenance',
        ]);

        $serviceSetting = ServiceSetting::factory()->create([
            'id' => 2,
            'category_id' => $category->id,
        ]);

        $subCategoryOne = RequestSubcategory::factory()->create([
            'id' => 1,
            'category_id' => $category->id,
            'name' => 'General Maintenance',
            'name_ar' => 'صيانة عامة',
            'name_en' => 'General Maintenance',
            'status' => true,
            'is_all_day' => false,
            'start' => '08:00',
            'end' => '17:00',
            'terms_and_conditions' => 'Service terms',
        ]);

        $subCategorySeven = RequestSubcategory::factory()->create([
            'id' => 7,
            'category_id' => $category->id,
            'name' => 'Emergency Maintenance',
            'name_ar' => 'صيانة طارئة',
            'name_en' => 'Emergency Maintenance',
            'status' => true,
            'is_all_day' => true,
        ]);

        WorkingDay::factory()->create([
            'subcategory_id' => $subCategoryOne->id,
            'day' => 'monday',
            'start' => '08:00',
            'end' => '12:00',
            'is_active' => true,
        ]);

        FeaturedService::factory()->create([
            'subcategory_id' => $subCategoryOne->id,
            'title' => 'Inspection',
            'title_ar' => 'فحص',
            'title_en' => 'Inspection',
            'description' => 'Initial inspection',
            'is_active' => true,
        ]);

        $community = Community::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        $building = Building::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
            'city_id' => $community->city_id,
            'district_id' => $community->district_id,
        ]);

        $subCategoryOne->communities()->sync([$community->id]);
        $subCategoryOne->buildings()->sync([$building->id]);

        $requestStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'New',
            'name_ar' => 'جديد',
            'name_en' => 'New',
        ]);

        $resident = Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        ServiceRequest::factory()->create([
            'category_id' => $category->id,
            'subcategory_id' => $subCategoryOne->id,
            'status_id' => $requestStatus->id,
            'requester_type' => Resident::class,
            'requester_id' => $resident->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $serviceSettingResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/requests/service-settings/2');

        $serviceSettingResponse
            ->assertOk()
            ->assertJsonPath('data.id', $serviceSetting->id);

        $subCategoriesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.requests.sub-categories.index'));

        $subCategoriesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name_ar',
                        'name_en',
                        'name',
                        'start',
                        'end',
                        'is_all_day',
                        'working_days',
                        'status',
                        'requests_count',
                        'types_count',
                        'request',
                        'icon',
                        'terms_and_conditions',
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

        $subCategoryIds = collect($subCategoriesResponse->json('data'))->pluck('id')->all();

        $this->assertContains($subCategoryOne->id, $subCategoryIds);
        $this->assertContains($subCategorySeven->id, $subCategoryIds);

        $listedSubCategoryOne = collect($subCategoriesResponse->json('data'))->firstWhere('id', $subCategoryOne->id);

        $this->assertSame(1, $listedSubCategoryOne['requests_count']);

        $subCategoryOneResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/requests/sub-categories/1');

        $subCategoryOneResponse
            ->assertOk()
            ->assertJsonPath('data.id', $subCategoryOne->id)
            ->assertJsonPath('data.requests_count', 1)
            ->assertJsonPath('data.selects.buildings.count', 1)
            ->assertJsonPath('data.selects.communities.count', 1)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name_ar',
                    'name_en',
                    'name',
                    'start',
                    'end',
                    'is_all_day',
                    'working_days',
                    'status',
                    'requests_count',
                    'request',
                    'selects' => [
                        'buildings' => ['data', 'count'],
                        'communities' => ['data', 'count'],
                    ],
                    'featured',
                    'icon',
                    'terms_and_conditions',
                ],
                'message',
            ]);

        $subCategorySevenResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/requests/sub-categories/7');

        $subCategorySevenResponse
            ->assertOk()
            ->assertJsonPath('data.id', $subCategorySeven->id);
    }

    public function test_rf_request_types_endpoints_return_contract_like_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $category = RequestCategory::factory()->create();

        $subCategoryOne = RequestSubcategory::factory()->create([
            'id' => 1,
            'category_id' => $category->id,
            'name' => 'Type One',
            'name_ar' => 'نوع 1',
            'name_en' => 'Type One',
        ]);

        $subCategorySeven = RequestSubcategory::factory()->create([
            'id' => 7,
            'category_id' => $category->id,
            'name' => 'Type Seven',
            'name_ar' => 'نوع 7',
            'name_en' => 'Type Seven',
        ]);

        $typesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.requests.types.index'));

        $typesResponse
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

        $typeIds = collect($typesResponse->json('data'))->pluck('id')->all();

        $this->assertContains($subCategoryOne->id, $typeIds);
        $this->assertContains($subCategorySeven->id, $typeIds);

        $typesCreateResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.requests.types.create'));

        $typesCreateResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'sub_categories',
                    'fee_types',
                ],
                'meta',
            ]);

        $createIds = collect($typesCreateResponse->json('data.sub_categories'))->pluck('id')->all();

        $this->assertContains($subCategoryOne->id, $createIds);
        $this->assertContains($subCategorySeven->id, $createIds);

        $typesListOneResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/requests/types/list/1');

        $typesListOneResponse
            ->assertOk()
            ->assertJsonPath('data.0.id', $subCategoryOne->id);

        $typesListSevenResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/requests/types/list/7');

        $typesListSevenResponse
            ->assertOk()
            ->assertJsonPath('data.0.id', $subCategorySeven->id);

        $typesShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/requests/types/1');

        $typesShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $subCategoryOne->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name_ar',
                    'name_en',
                    'name',
                    'status',
                    'rf_sub_category_id',
                    'icon',
                    'fee_type',
                ],
                'message',
            ]);
    }

    public function test_rf_sub_leases_and_tenants_endpoints_return_contract_like_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $leaseStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Active',
            'name_ar' => 'نشط',
            'name_en' => 'Active',
        ]);

        $leaseUnitType = UnitCategory::factory()->create([
            'name' => 'Residential',
            'name_ar' => 'سكني',
            'name_en' => 'Residential',
        ]);

        $rentalContractType = Setting::factory()->create([
            'type' => 'rental_contract_type',
            'name' => 'Annual',
            'name_ar' => 'سنوي',
            'name_en' => 'Annual',
        ]);

        $paymentSchedule = Setting::factory()->create([
            'type' => 'payment_schedule',
            'name' => 'Monthly',
            'name_ar' => 'شهري',
            'name_en' => 'Monthly',
        ]);

        $residentOne = Resident::factory()->create([
            'first_name' => 'Tenant',
            'last_name' => 'One',
            'phone_number' => '0501234567',
            'phone_country_code' => 'SA',
            'accepted_invite' => false,
            'account_tenant_id' => $tenant->id,
        ]);

        $residentTwo = Resident::factory()->create([
            'first_name' => 'Tenant',
            'last_name' => 'Two',
            'phone_number' => '0507654321',
            'phone_country_code' => 'SA',
            'accepted_invite' => true,
            'account_tenant_id' => $tenant->id,
        ]);

        $subLease = Lease::factory()->create([
            'tenant_id' => $residentOne->id,
            'status_id' => $leaseStatus->id,
            'lease_unit_type_id' => $leaseUnitType->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentSchedule->id,
            'created_by_id' => (int) auth()->id(),
            'is_sub_lease' => true,
            'account_tenant_id' => $tenant->id,
        ]);

        $regularLease = Lease::factory()->create([
            'tenant_id' => $residentTwo->id,
            'status_id' => $leaseStatus->id,
            'lease_unit_type_id' => $leaseUnitType->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentSchedule->id,
            'created_by_id' => (int) auth()->id(),
            'is_sub_lease' => false,
            'account_tenant_id' => $tenant->id,
        ]);

        $subLeasesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.sub-leases.index'));

        $subLeasesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'contract_number',
                        'lease_unit_type',
                        'tenant',
                        'units',
                        'status',
                        'start_date',
                        'end_date',
                        'handover_date',
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

        $subLeaseIds = collect($subLeasesResponse->json('data'))->pluck('id')->all();

        $this->assertContains($subLease->id, $subLeaseIds);
        $this->assertNotContains($regularLease->id, $subLeaseIds);

        $tenantsResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.tenants.index'));

        $tenantsResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'image',
                        'phone_number',
                        'invited',
                        'created_at',
                        'units',
                        'accepted_invite',
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

        $tenantIds = collect($tenantsResponse->json('data'))->pluck('id')->all();

        $this->assertContains($residentOne->id, $tenantIds);
        $this->assertContains($residentTwo->id, $tenantIds);

        $firstTenant = collect($tenantsResponse->json('data'))->firstWhere('id', $residentOne->id);

        $this->assertSame(0, $firstTenant['accepted_invite']);
        $this->assertSame('0', $firstTenant['invited']);
    }
}
