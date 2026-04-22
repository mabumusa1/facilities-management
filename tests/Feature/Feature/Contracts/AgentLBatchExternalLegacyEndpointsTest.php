<?php

namespace Tests\Feature\Feature\Contracts;

use App\Models\AccountMembership;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AgentLBatchExternalLegacyEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser(): Tenant
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Agent L Contract Tenant']);

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
            'legacy.download-land-excel',
            'legacy.download-lead-excel',
            'legacy.cities.all',
            'legacy.cities.by-country',
            'legacy.countries',
            'legacy.districts.all',
            'legacy.districts.by-city',
            'legacy.integrations.powerbi.types',
            'legacy.me',
            'legacy.plans',
            'legacy.request-category',
            'legacy.images.multiple',
            'legacy.signup.create-tenant',
            'legacy.signup.send-verification',
            'legacy.signup.verify',
            'legacy.tenancy.login',
            'legacy.tenancy.logout',
            'legacy.tenancy.send-verification',
        ];

        foreach ($expectedRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Route [{$routeName}] must exist.");
        }
    }

    public function test_legacy_lookup_endpoints_return_structured_payloads(): void
    {
        $country = Country::factory()->create([
            'iso2' => 'SA',
            'iso3' => 'SAU',
            'name' => 'Saudi Arabia',
            'name_en' => 'Saudi Arabia',
            'name_ar' => 'Saudi Arabia',
            'dial' => '966',
            'currency' => 'SAR',
            'capital' => 'Riyadh',
            'continent' => 'AS',
            'excel' => 'SA (966)',
        ]);

        $otherCountry = Country::factory()->create([
            'iso2' => 'AE',
            'iso3' => 'ARE',
            'name' => 'United Arab Emirates',
            'name_en' => 'United Arab Emirates',
            'name_ar' => 'United Arab Emirates',
            'dial' => '971',
            'currency' => 'AED',
            'capital' => 'Abu Dhabi',
            'continent' => 'AS',
            'excel' => 'AE (971)',
        ]);

        $cityInCountry = City::factory()->create([
            'country_id' => $country->id,
            'name' => 'Riyadh',
            'name_en' => 'Riyadh',
            'name_ar' => 'Riyadh',
        ]);

        City::factory()->create([
            'country_id' => $otherCountry->id,
            'name' => 'Dubai',
            'name_en' => 'Dubai',
            'name_ar' => 'Dubai',
        ]);

        $district = District::factory()->create([
            'city_id' => $cityInCountry->id,
            'name' => 'Al Diriyah',
            'name_en' => 'Al Diriyah',
            'name_ar' => 'Al Diriyah',
        ]);

        $requestCategory = RequestCategory::factory()->create([
            'name' => 'Maintenance',
            'name_en' => 'Maintenance',
            'name_ar' => 'Maintenance',
            'status' => true,
        ]);

        RequestSubcategory::factory()->create([
            'category_id' => $requestCategory->id,
            'name' => 'Plumbing',
            'name_en' => 'Plumbing',
            'name_ar' => 'Plumbing',
            'status' => true,
        ]);

        $countriesResponse = $this->getJson('/countries');

        $countriesResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', 'Countries Data')
            ->assertJsonStructure([
                'success',
                'data',
                'message' => [
                    [
                        'Iso2',
                        'Name',
                        'Iso3',
                        'Excel',
                        'Unicode',
                        'Dial',
                        'Currency',
                        'Capital',
                        'Continent',
                    ],
                ],
            ]);

        $citiesAllResponse = $this->getJson('/cities/all');

        $citiesAllResponse
            ->assertOk()
            ->assertJsonFragment([
                'id' => $cityInCountry->id,
                'country_code' => 'SA',
            ])
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'name_ar',
                        'name_en',
                        'country_code',
                    ],
                ],
            ]);

        $citiesByCountryResponse = $this->getJson('/cities/SA');

        $citiesByCountryResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $cityInCountry->id)
            ->assertJsonPath('data.0.country_code', 'SA');

        $districtsAllResponse = $this->getJson('/districts/all');

        $districtsAllResponse
            ->assertOk()
            ->assertJsonPath('data.0.city_id', $cityInCountry->id)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'name_ar',
                        'name_en',
                        'city_id',
                    ],
                ],
            ]);

        $districtsByCityResponse = $this->getJson('/districts/'.$cityInCountry->id);

        $districtsByCityResponse
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $district->id);

        $powerBiResponse = $this->getJson('/integrations/powerbi/types');

        $powerBiResponse
            ->assertOk()
            ->assertJsonPath('code', 200)
            ->assertJsonStructure([
                'code',
                'message',
                'data' => [
                    [
                        'id',
                        'title',
                        'comming_soon',
                        'is_active',
                    ],
                ],
                'meta',
            ]);

        $plansResponse = $this->getJson('/plans');

        $plansResponse
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'slug',
                        'name',
                        'price',
                        'description',
                        'is_active',
                        'currency',
                        'trial_period',
                        'trial_interval',
                        'invoice_period',
                        'invoice_interval',
                        'features',
                    ],
                ],
                'meta',
            ]);

        $requestCategoryResponse = $this->getJson('/request-category');

        $requestCategoryResponse
            ->assertOk()
            ->assertJsonPath('data.0.id', $requestCategory->id)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'name',
                        'name_ar',
                        'name_en',
                        'status',
                        'sub_categories' => [
                            [
                                'id',
                                'name',
                                'name_ar',
                                'name_en',
                                'status',
                            ],
                        ],
                    ],
                ],
            ]);

        $landExcelResponse = $this->getJson('/api/general/static-files/download_land_excel');

        $landExcelResponse
            ->assertStatus(501)
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'legacy_endpoint_not_supported')
            ->assertJsonPath('data.endpoint', 'api/general/static-files/download_land_excel');

        $leadExcelResponse = $this->getJson('/api/general/static-files/download_lead_excel');

        $leadExcelResponse
            ->assertStatus(501)
            ->assertJsonPath('success', false)
            ->assertJsonPath('code', 'legacy_endpoint_not_supported')
            ->assertJsonPath('data.endpoint', 'api/general/static-files/download_lead_excel');
    }

    public function test_me_endpoint_returns_guest_unauthorized_and_authenticated_profile_shape(): void
    {
        $guestResponse = $this->getJson('/me');

        $guestResponse
            ->assertStatus(401)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Unauthenticated.');

        $tenant = $this->authenticateUser();

        $profileResponse = $this
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson('/me');

        $profileResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', auth()->user()?->name)
            ->assertJsonPath('data.type', 'account_admins')
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone_number',
                    'type',
                    'subscription' => [
                        'id',
                        'plan' => [
                            'id',
                            'name',
                            'slug',
                            'features',
                        ],
                        'status',
                        'start_date',
                        'end_date',
                    ],
                    'permissions',
                ],
            ]);
    }

    public function test_legacy_post_endpoints_validate_and_return_structured_payloads(): void
    {
        $imagesValidation = $this->postJson('/images/multiple', []);

        $imagesValidation
            ->assertStatus(422)
            ->assertJsonValidationErrors(['images']);

        $imagesResponse = $this->post('/images/multiple', [
            'images' => [UploadedFile::fake()->image('unit.jpg')],
        ], ['Accept' => 'application/json']);

        $imagesResponse
            ->assertStatus(501)
            ->assertJsonPath('code', 'legacy_endpoint_not_supported')
            ->assertJsonPath('data.endpoint', 'images/multiple')
            ->assertJsonPath('data.images_count', 1)
            ->assertJsonPath('data.uploaded', 0);

        $signupCreateValidation = $this->postJson('/signup/create-tenant', []);

        $signupCreateValidation
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);

        $signupCreateResponse = $this->postJson('/signup/create-tenant', [
            'name' => 'Compatibility Tenant',
            'email' => 'tenant@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $signupCreateResponse
            ->assertStatus(501)
            ->assertJsonPath('code', 'legacy_endpoint_not_supported')
            ->assertJsonPath('data.endpoint', 'signup/create-tenant')
            ->assertJsonPath('data.tenant_created', false);

        $signupVerificationValidation = $this->postJson('/signup/send-verification', []);

        $signupVerificationValidation
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $signupVerificationResponse = $this->postJson('/signup/send-verification', [
            'email' => 'tenant@example.test',
        ]);

        $signupVerificationResponse
            ->assertStatus(501)
            ->assertJsonPath('code', 'legacy_endpoint_not_supported')
            ->assertJsonPath('data.endpoint', 'signup/send-verification');

        $signupVerifyValidation = $this->postJson('/signup/verify', []);

        $signupVerifyValidation
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'code']);

        $signupVerifyResponse = $this->postJson('/signup/verify', [
            'email' => 'tenant@example.test',
            'code' => '123456',
        ]);

        $signupVerifyResponse
            ->assertStatus(501)
            ->assertJsonPath('code', 'legacy_endpoint_not_supported')
            ->assertJsonPath('data.endpoint', 'signup/verify')
            ->assertJsonPath('data.verified', false);

        $tenancyLoginValidation = $this->postJson('/tenancy/login', []);

        $tenancyLoginValidation
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        $tenancyLoginResponse = $this->postJson('/tenancy/login', [
            'email' => 'tenant@example.test',
            'password' => 'password123',
        ]);

        $tenancyLoginResponse
            ->assertStatus(501)
            ->assertJsonPath('code', 'legacy_endpoint_not_supported')
            ->assertJsonPath('data.endpoint', 'tenancy/login')
            ->assertJsonPath('data.authenticated', false)
            ->assertJsonPath('data.token', null);

        $tenancyLogoutResponse = $this->postJson('/tenancy/logout');

        $tenancyLogoutResponse
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.logged_out', true)
            ->assertJsonPath('data.stateful_session', false);

        $tenancySendVerificationValidation = $this->postJson('/tenancy/send-verification', []);

        $tenancySendVerificationValidation
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);

        $tenancySendVerificationResponse = $this->postJson('/tenancy/send-verification', [
            'email' => 'tenant@example.test',
        ]);

        $tenancySendVerificationResponse
            ->assertStatus(501)
            ->assertJsonPath('code', 'legacy_endpoint_not_supported')
            ->assertJsonPath('data.endpoint', 'tenancy/send-verification');
    }
}
