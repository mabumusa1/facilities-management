<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\RequestCategory;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LegacyCompatibilityController extends Controller
{
    public function downloadLandExcel(): JsonResponse
    {
        return $this->legacyUnsupported(
            endpoint: 'api/general/static-files/download_land_excel',
            message: 'Legacy land excel export is not available in this application.',
            extraData: ['download_url' => null],
        );
    }

    public function downloadLeadExcel(): JsonResponse
    {
        return $this->legacyUnsupported(
            endpoint: 'api/general/static-files/download_lead_excel',
            message: 'Legacy lead excel export is not available in this application.',
            extraData: ['download_url' => null],
        );
    }

    public function citiesAll(): JsonResponse
    {
        $cities = City::query()
            ->select('id', 'name', 'name_ar', 'name_en', 'country_id')
            ->with('country:id,iso2')
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get();

        return response()->json([
            'data' => $cities->map(fn (City $city): array => $this->cityPayload($city))->values(),
        ]);
    }

    public function citiesByCountryCode(string $countryCode): JsonResponse
    {
        $normalizedCode = strtoupper($countryCode);

        $cities = City::query()
            ->select('id', 'name', 'name_ar', 'name_en', 'country_id')
            ->with('country:id,iso2')
            ->whereHas('country', static function ($query) use ($normalizedCode): void {
                $query->where('iso2', $normalizedCode);
            })
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get();

        return response()->json([
            'data' => $cities->map(fn (City $city): array => $this->cityPayload($city))->values(),
        ]);
    }

    public function countries(): JsonResponse
    {
        $countries = Country::query()
            ->select([
                'id',
                'iso2',
                'iso3',
                'name',
                'name_ar',
                'name_en',
                'dial',
                'currency',
                'capital',
                'continent',
                'unicode',
                'excel',
            ])
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => 'Countries Data',
            'message' => $countries->map(fn (Country $country): array => [
                'Iso2' => $country->iso2,
                'Name' => $country->name_en ?? $country->name ?? $country->name_ar,
                'Iso3' => $country->iso3,
                'Excel' => $country->excel ?? trim(($country->iso2 ?? '').' ('.($country->dial ?? '').')'),
                'Unicode' => $country->unicode,
                'Dial' => (string) ($country->dial ?? ''),
                'Currency' => $country->currency,
                'Capital' => $country->capital,
                'Continent' => $country->continent,
            ])->values(),
        ]);
    }

    public function districtsAll(): JsonResponse
    {
        $districts = District::query()
            ->select('id', 'name', 'name_ar', 'name_en', 'city_id')
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get();

        return response()->json([
            'data' => $districts->map(fn (District $district): array => $this->districtPayload($district))->values(),
        ]);
    }

    public function districtsByCityId(int $cityId): JsonResponse
    {
        $districts = District::query()
            ->select('id', 'name', 'name_ar', 'name_en', 'city_id')
            ->where('city_id', $cityId)
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get();

        return response()->json([
            'data' => $districts->map(fn (District $district): array => $this->districtPayload($district))->values(),
        ]);
    }

    public function powerBiTypes(): JsonResponse
    {
        return response()->json([
            'code' => 200,
            'message' => '',
            'data' => [
                ['id' => 5, 'title' => 'Financial Reports', 'comming_soon' => '1', 'is_active' => '1'],
                ['id' => 2, 'title' => 'Leasing Reports', 'comming_soon' => '1', 'is_active' => '1'],
                ['id' => 4, 'title' => 'Property Reports', 'comming_soon' => '1', 'is_active' => '1'],
                ['id' => 3, 'title' => 'Sales Reports', 'comming_soon' => '1', 'is_active' => '1'],
                ['id' => 1, 'title' => 'Service Requests Reports', 'comming_soon' => '1', 'is_active' => '1'],
            ],
            'meta' => [],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'data' => null,
            ], 401);
        }

        $membership = $user->accountMemberships()->select('role')->first();

        return response()->json([
            'success' => true,
            'message' => 'Authenticated user profile fetched.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => null,
                'type' => $membership?->role ?? 'user',
                'subscription' => [
                    'id' => null,
                    'plan' => [
                        'id' => null,
                        'name' => null,
                        'slug' => null,
                        'features' => [],
                    ],
                    'status' => null,
                    'start_date' => null,
                    'end_date' => null,
                ],
                'permissions' => $user->getPermissionNames()->values()->all(),
            ],
        ]);
    }

    public function plans(): JsonResponse
    {
        return response()->json([
            'data' => [
                [
                    'id' => 1,
                    'slug' => 'starter-plan',
                    'name' => 'Starter Plan',
                    'price' => 89.55,
                    'description' => 'Individuals and real estate agents',
                    'is_active' => true,
                    'currency' => 'SAR',
                    'trial_period' => 14,
                    'trial_interval' => 'day',
                    'invoice_period' => 1,
                    'invoice_interval' => 'month',
                    'features' => [],
                ],
                [
                    'id' => 2,
                    'slug' => 'professional-plan',
                    'name' => 'Professional Plan',
                    'price' => 267.30,
                    'description' => 'Property managers and residential communities',
                    'is_active' => true,
                    'currency' => 'SAR',
                    'trial_period' => 14,
                    'trial_interval' => 'day',
                    'invoice_period' => 1,
                    'invoice_interval' => 'month',
                    'features' => [],
                ],
                [
                    'id' => 3,
                    'slug' => 'enterprise-plan',
                    'name' => 'Enterprise Plan',
                    'price' => 1,
                    'description' => 'Custom package',
                    'is_active' => true,
                    'currency' => 'SAR',
                    'trial_period' => 0,
                    'trial_interval' => 'day',
                    'invoice_period' => 1,
                    'invoice_interval' => 'month',
                    'features' => [],
                ],
            ],
            'meta' => [],
        ]);
    }

    public function requestCategory(): JsonResponse
    {
        $categories = RequestCategory::query()
            ->with('subcategories:id,category_id,name,name_ar,name_en,status')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $categories->map(fn (RequestCategory $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'name_ar' => $category->name_ar,
                'name_en' => $category->name_en,
                'status' => (bool) $category->status,
                'sub_categories' => $category->subcategories->map(
                    fn ($subcategory): array => [
                        'id' => $subcategory->id,
                        'name' => $subcategory->name,
                        'name_ar' => $subcategory->name_ar,
                        'name_en' => $subcategory->name_en,
                        'status' => (bool) $subcategory->status,
                    ]
                )->values(),
            ])->values(),
        ]);
    }

    public function imagesMultiple(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        return $this->legacyUnsupported(
            endpoint: 'images/multiple',
            message: 'Legacy multi-image upload is not enabled for this application.',
            extraData: [
                'images_count' => count($validated['images']),
                'uploaded' => 0,
            ],
        );
    }

    public function signupCreateTenant(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        return $this->legacyUnsupported(
            endpoint: 'signup/create-tenant',
            message: 'Legacy signup tenant creation is not supported in this application.',
            extraData: [
                'tenant_created' => false,
                'email' => $validated['email'],
            ],
        );
    }

    public function signupSendVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        return $this->legacyUnsupported(
            endpoint: 'signup/send-verification',
            message: 'Legacy signup verification dispatch is not supported in this application.',
            extraData: [
                'email' => $validated['email'],
            ],
        );
    }

    public function signupVerify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'code' => ['required', 'string', 'max:20'],
        ]);

        return $this->legacyUnsupported(
            endpoint: 'signup/verify',
            message: 'Legacy signup verification is not supported in this application.',
            extraData: [
                'email' => $validated['email'],
                'verified' => false,
            ],
        );
    }

    public function tenancyLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        return $this->legacyUnsupported(
            endpoint: 'tenancy/login',
            message: 'Legacy tenancy login is not supported in this application.',
            extraData: [
                'authenticated' => false,
                'email' => $validated['email'],
                'token' => null,
            ],
        );
    }

    public function tenancyLogout(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Legacy tenancy logout compatibility endpoint acknowledged.',
            'data' => [
                'logged_out' => true,
                'stateful_session' => false,
            ],
        ]);
    }

    public function tenancySendVerification(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        return $this->legacyUnsupported(
            endpoint: 'tenancy/send-verification',
            message: 'Legacy tenancy verification dispatch is not supported in this application.',
            extraData: [
                'email' => $validated['email'],
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function cityPayload(City $city): array
    {
        return [
            'id' => $city->id,
            'name' => $city->name_en ?? $city->name,
            'name_ar' => $city->name_ar,
            'name_en' => $city->name_en,
            'country_code' => $city->country?->iso2,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function districtPayload(District $district): array
    {
        return [
            'id' => $district->id,
            'name' => $district->name_en ?? $district->name,
            'name_ar' => $district->name_ar,
            'name_en' => $district->name_en,
            'city_id' => $district->city_id,
        ];
    }

    /**
     * @param  array<string, mixed>  $extraData
     */
    private function legacyUnsupported(string $endpoint, string $message, array $extraData = []): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => 'legacy_endpoint_not_supported',
            'message' => $message,
            'data' => array_merge([
                'endpoint' => $endpoint,
            ], $extraData),
        ], 501);
    }
}
