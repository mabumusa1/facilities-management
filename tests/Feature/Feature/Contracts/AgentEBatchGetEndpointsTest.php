<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\Country;
use App\Models\Lease;
use App\Models\Media;
use App\Models\Owner;
use App\Models\Professional;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Resident;
use App\Models\ServiceSetting;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\UnitCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentEBatchGetEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent E Contract Tenant']);

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
            'rf.leases.statistics',
            'rf.owners.index',
            'rf.owners.show',
            'rf.payment-schedules',
            'rf.professionals.index',
            'rf.rental-contract-types',
            'rf.requests.index',
            'rf.requests.categories.index',
            'rf.requests.categories.show',
            'rf.requests.service-settings.index',
            'rf.requests.service-settings.show',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_rf_leasing_statistics_and_lookup_endpoints_return_expected_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $newStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'New',
            'name_en' => 'New',
            'name_ar' => 'جديد',
        ]);

        $activeStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Active',
            'name_en' => 'Active',
            'name_ar' => 'نشط',
        ]);

        $expiredStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Expired',
            'name_en' => 'Expired',
            'name_ar' => 'منتهي',
        ]);

        $terminatedStatus = Status::factory()->create([
            'type' => 'lease',
            'name' => 'Terminated',
            'name_en' => 'Terminated',
            'name_ar' => 'ملغى',
        ]);

        $residentialCategory = UnitCategory::factory()->create([
            'name' => 'Residential',
            'name_en' => 'Residential',
            'name_ar' => 'سكني',
        ]);

        $commercialCategory = UnitCategory::factory()->create([
            'name' => 'Commercial',
            'name_en' => 'Commercial',
            'name_ar' => 'تجاري',
        ]);

        $rentalContractType = Setting::factory()->create([
            'type' => 'rental_contract_type',
            'name' => 'Annual',
            'name_ar' => 'سنوي',
            'name_en' => 'Annual',
        ]);

        $paymentScheduleParent = Setting::factory()->create([
            'type' => 'payment_schedule',
            'name' => 'Monthly',
            'name_ar' => 'شهري',
            'name_en' => 'Monthly',
        ]);

        $paymentScheduleChild = Setting::factory()->childOf($paymentScheduleParent)->create([
            'name' => 'Installment',
            'name_ar' => 'قسط',
            'name_en' => 'Installment',
        ]);

        $resident = Resident::factory()->create([
            'account_tenant_id' => $tenant->id,
        ]);

        Lease::factory()->create([
            'tenant_id' => $resident->id,
            'status_id' => $newStatus->id,
            'lease_unit_type_id' => $residentialCategory->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentScheduleParent->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        Lease::factory()->create([
            'tenant_id' => $resident->id,
            'status_id' => $activeStatus->id,
            'lease_unit_type_id' => $commercialCategory->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentScheduleParent->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        Lease::factory()->create([
            'tenant_id' => $resident->id,
            'status_id' => $expiredStatus->id,
            'lease_unit_type_id' => $residentialCategory->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentScheduleParent->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        Lease::factory()->create([
            'tenant_id' => $resident->id,
            'status_id' => $terminatedStatus->id,
            'lease_unit_type_id' => $residentialCategory->id,
            'rental_contract_type_id' => $rentalContractType->id,
            'payment_schedule_id' => $paymentScheduleParent->id,
            'created_by_id' => (int) auth()->id(),
            'account_tenant_id' => $tenant->id,
        ]);

        $transactionStatus = Status::factory()->create([
            'type' => 'transaction',
            'name' => 'Pending',
            'name_en' => 'Pending',
            'name_ar' => 'قيد الانتظار',
        ]);

        Transaction::factory()->create([
            'status_id' => $transactionStatus->id,
            'amount' => 3000,
            'due_on' => now()->toDateString(),
            'is_paid' => false,
            'account_tenant_id' => $tenant->id,
        ]);

        Transaction::factory()->paid()->create([
            'status_id' => $transactionStatus->id,
            'amount' => 700,
            'due_on' => now()->toDateString(),
            'account_tenant_id' => $tenant->id,
        ]);

        Transaction::factory()->paid()->create([
            'status_id' => $transactionStatus->id,
            'amount' => 500,
            'due_on' => now()->startOfYear()->addDays(5)->toDateString(),
            'account_tenant_id' => $tenant->id,
        ]);

        $statisticsResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.leases.statistics'));

        $statisticsResponse
            ->assertOk()
            ->assertJsonPath('data.totalLeases', 4)
            ->assertJsonPath('data.newLeases', 1)
            ->assertJsonPath('data.activeLeases', 1)
            ->assertJsonPath('data.expiredLeases', 1)
            ->assertJsonPath('data.terminatedLeases', 1)
            ->assertJsonPath('data.activeCommercialLeases', 1)
            ->assertJsonPath('data.activeResidentialLeases', 0)
            ->assertJsonStructure([
                'data' => [
                    'totalLeases',
                    'newLeases',
                    'activeLeases',
                    'expiredLeases',
                    'terminatedLeases',
                    'percentNewLeases',
                    'percentActiveLeases',
                    'percentExpiredLeases',
                    'percentTerminatedLeases',
                    'activeCommercialLeases',
                    'activeResidentialLeases',
                    'currentMonthCollection',
                    'currentYearCollection',
                    'calculatePaidCollectionForCurrentMonth',
                    'calculatePaidCollectionForCurrentYear',
                ],
            ]);

        $statisticsData = $statisticsResponse->json('data');

        $this->assertIsNumeric($statisticsData['currentMonthCollection']);
        $this->assertIsNumeric($statisticsData['currentYearCollection']);
        $this->assertIsNumeric($statisticsData['calculatePaidCollectionForCurrentMonth']);
        $this->assertIsNumeric($statisticsData['calculatePaidCollectionForCurrentYear']);

        $rentalContractTypesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.rental-contract-types'));

        $rentalContractTypesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'name_ar',
                        'name_en',
                    ],
                ],
                'meta',
            ]);

        $rentalContractTypeIds = collect($rentalContractTypesResponse->json('data'))->pluck('id')->all();

        $this->assertContains($rentalContractType->id, $rentalContractTypeIds);

        $paymentSchedulesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.payment-schedules'));

        $paymentSchedulesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'name_ar',
                        'name_en',
                        'parent_id',
                    ],
                ],
                'meta',
            ]);

        $paymentScheduleIds = collect($paymentSchedulesResponse->json('data'))->pluck('id')->all();

        $this->assertContains($paymentScheduleParent->id, $paymentScheduleIds);
        $this->assertContains($paymentScheduleChild->id, $paymentScheduleIds);
    }

    public function test_rf_owner_and_professional_endpoints_return_expected_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $country = Country::factory()->create([
            'name' => 'Saudi Arabia',
            'name_ar' => 'المملكة العربية السعودية',
            'name_en' => 'Saudi Arabia',
            'iso2' => 'SA',
            'iso3' => 'SAU',
        ]);

        $owner = Owner::factory()->create([
            'first_name' => 'Mohammed',
            'last_name' => 'Al-Saud',
            'phone_number' => '0500000002',
            'national_phone_number' => '0500000002',
            'phone_country_code' => 'SA',
            'national_id' => '1122334455',
            'nationality_id' => $country->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $professionalCategory = RequestCategory::factory()->create([
            'name' => 'Home Services',
            'name_ar' => 'خدمات منزلية',
            'name_en' => 'Home Services',
        ]);

        $professionalSubcategory = RequestSubcategory::factory()->create([
            'category_id' => $professionalCategory->id,
            'name' => 'Maintenance',
            'name_ar' => 'صيانة',
            'name_en' => 'Maintenance',
        ]);

        $professional = Professional::factory()->create([
            'first_name' => 'Nora',
            'last_name' => 'Tech',
            'phone_number' => '0501111111',
            'phone_country_code' => 'SA',
            'account_tenant_id' => $tenant->id,
        ]);

        $professional->subcategories()->attach($professionalSubcategory->id);

        $ownersIndexResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.owners.index', ['search' => 'Mohammed']));

        $ownersIndexResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'image',
                        'phone_number',
                        'created_at',
                        'units',
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

        $ownerIds = collect($ownersIndexResponse->json('data'))->pluck('id')->all();

        $this->assertContains($owner->id, $ownerIds);

        $ownerShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.owners.show', $owner));

        $ownerShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $owner->id)
            ->assertJsonPath('data.first_name', 'Mohammed')
            ->assertJsonPath('data.last_name', 'Al-Saud')
            ->assertJsonPath('data.phone_number', '+966500000002')
            ->assertJsonPath('data.national_phone_number', '0500000002')
            ->assertJsonPath('data.phone_country_code', 'SA')
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
                    'phone_number',
                    'national_phone_number',
                    'phone_country_code',
                    'nationality',
                    'created_at',
                    'active',
                    'account_creation_date',
                    'last_active',
                    'units',
                    'active_requests',
                    'transaction',
                    'relation',
                    'relation_key',
                ],
                'message',
            ]);

        $literalOwnerShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/owners/'.$owner->id);

        $literalOwnerShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $owner->id);

        $professionalsResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.professionals.index'));

        $professionalsResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'image',
                        'phone_number',
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

        $firstProfessional = collect($professionalsResponse->json('data'))->firstWhere('id', $professional->id);

        $this->assertNotNull($firstProfessional);
        $this->assertSame('+966501111111', $firstProfessional['phone_number']);
        $this->assertNotEmpty($firstProfessional['types']);
        $this->assertSame($professionalSubcategory->id, $firstProfessional['types'][0]['id']);
    }

    public function test_rf_requests_category_and_service_settings_endpoints_return_expected_payloads(): void
    {
        $tenant = $this->authenticateUser();

        $mediaCountry = Country::factory()->create();

        $categoryIcon = Media::query()->create([
            'url' => 'https://example.test/icons/category-1.png',
            'name' => 'category-1.png',
            'notes' => 'predefined',
            'mediable_type' => Country::class,
            'mediable_id' => $mediaCountry->id,
            'collection' => 'photos',
        ]);

        $subcategoryIcon = Media::query()->create([
            'url' => 'https://example.test/icons/sub-category-1.png',
            'name' => 'sub-category-1.png',
            'notes' => 'predefined',
            'mediable_type' => Country::class,
            'mediable_id' => $mediaCountry->id,
            'collection' => 'photos',
        ]);

        $category = RequestCategory::factory()->create([
            'name' => 'Unit Services',
            'name_ar' => 'خدمات الوحدات',
            'name_en' => 'Unit Services',
            'description' => 'Services related to units',
            'status' => true,
            'has_sub_categories' => true,
            'icon_id' => $categoryIcon->id,
        ]);

        $subcategory = RequestSubcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Maintenance',
            'name_ar' => 'صيانة',
            'name_en' => 'Maintenance',
            'status' => true,
            'icon_id' => $subcategoryIcon->id,
        ]);

        $serviceSetting = ServiceSetting::factory()->create([
            'category_id' => $category->id,
            'account_tenant_id' => $tenant->id,
            'visibilities' => [
                'hide_resident_number' => false,
                'hide_resident_name' => false,
                'hide_professional_number_and_name' => false,
                'show_unified_number_only' => false,
            ],
            'permissions' => [
                'manager_close_Request' => false,
                'not_require_professional_enter_request_code' => false,
                'not_require_professional_upload_request_photo' => false,
                'attachments_required' => false,
                'allow_professional_reschedule' => false,
            ],
            'submit_request_before_type' => 'hours',
            'submit_request_before_value' => 24,
            'capacity_type' => 'daily',
            'capacity_value' => 5,
        ]);

        $requestStatus = Status::factory()->create([
            'type' => 'request',
            'name' => 'New',
            'name_en' => 'New',
            'name_ar' => 'جديد',
        ]);

        $serviceRequest = ServiceRequest::query()->create([
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'status_id' => $requestStatus->id,
            'requester_type' => User::class,
            'requester_id' => (int) auth()->id(),
            'title' => 'Agent E request',
            'description' => 'Please handle this service request.',
            'request_code' => 'REQ-AGENT-E-1',
            'priority' => 'high',
            'account_tenant_id' => $tenant->id,
        ]);

        $requestsResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.requests.index'));

        $requestsResponse
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
                        'unit',
                        'community',
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

        $requestIds = collect($requestsResponse->json('data'))->pluck('id')->all();

        $this->assertContains($serviceRequest->id, $requestIds);

        $categoriesResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.requests.categories.index'));

        $categoriesResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'description',
                        'status',
                        'has_sub_categories',
                        'sub_categories' => [
                            [
                                'id',
                                'name',
                                'icon',
                                'status',
                            ],
                        ],
                        'serviceSettings' => [
                            'visibilities',
                            'permissions',
                            'submit_request_before_type',
                            'submit_request_before_value',
                            'capacity_type',
                            'capacity_value',
                        ],
                        'icon',
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

        $categoryIds = collect($categoriesResponse->json('data'))->pluck('id')->all();

        $this->assertContains($category->id, $categoryIds);

        $categoryShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.requests.categories.show', $category));

        $categoryShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'status',
                    'has_sub_categories',
                    'sub_categories',
                    'serviceSettings',
                    'icon',
                ],
                'message',
            ]);

        $serviceSettingsResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.requests.service-settings.index'));

        $serviceSettingsResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'category_id',
                        'category',
                        'visibilities',
                        'permissions',
                        'submit_request_before_type',
                        'submit_request_before_value',
                        'capacity_type',
                        'capacity_value',
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

        $serviceSettingIds = collect($serviceSettingsResponse->json('data'))->pluck('id')->all();

        $this->assertContains($serviceSetting->id, $serviceSettingIds);

        $serviceSettingShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson(route('rf.requests.service-settings.show', $serviceSetting));

        $serviceSettingShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $serviceSetting->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'category_id',
                    'category',
                    'visibilities',
                    'permissions',
                    'submit_request_before_type',
                    'submit_request_before_value',
                    'capacity_type',
                    'capacity_value',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $literalServiceSettingShowResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/rf/requests/service-settings/'.$serviceSetting->id);

        $literalServiceSettingShowResponse
            ->assertOk()
            ->assertJsonPath('data.id', $serviceSetting->id);
    }
}
