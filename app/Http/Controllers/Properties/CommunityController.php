<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Http\Requests\Properties\UpdateCommunityRequest;
use App\Models\Amenity;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|Response
    {
        $this->authorize('viewAny', Community::class);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $communities = Community::query()
                ->withCount(['buildings', 'units', 'requests'])
                ->with([
                    'city:id,name,name_en',
                    'district:id,name,name_en',
                    'images:id,mediable_id,mediable_type,name,url,collection',
                ])
                ->latest()
                ->paginate($this->perPage($request));

            return response()->json([
                'data' => collect($communities->items())->map(
                    fn (Community $community): array => $this->communityListItem($community)
                ),
                'meta' => $this->meta($communities),
            ]);
        }

        $communities = Community::query()
            ->withCount(['buildings', 'units', 'requests'])
            ->with(['country', 'city', 'district', 'currency'])
            ->latest()
            ->paginate(15);

        return Inertia::render('properties/communities/Index', [
            'communities' => $communities,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Community::class);

        return Inertia::render('properties/communities/Create', [
            'countries' => Country::query()->select('id', 'name', 'name_en', 'currency')->orderBy('id')->get(),
            'currencies' => Currency::query()->select('id', 'name', 'code', 'symbol')->orderBy('name')->get(),
            'cities' => City::query()->select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::query()->select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Community::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'sales_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'rental_commission_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $community = Community::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $community->loadCount(['buildings', 'units', 'requests'])
                ->load([
                    'city:id,name,name_en',
                    'district:id,name,name_en',
                    'images:id,mediable_id,mediable_type,name,url,collection',
                ]);

            return response()->json([
                'data' => $this->communityListItem($community),
                'message' => __('Community created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Community created.')]);

        return to_route('communities.show', $community);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Community $community): JsonResponse|Response
    {
        $this->authorize('view', $community);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $community->loadCount(['buildings', 'units', 'requests'])
                ->load([
                    'country:id,name,iso2',
                    'currency:id,name,code',
                    'city:id,name,name_en',
                    'district:id,name,name_en',
                    'amenities:id,name,name_en',
                    'images:id,mediable_id,mediable_type,name,url,collection',
                ]);

            return response()->json([
                'data' => $this->communityDetails($community),
                'message' => __('Community retrieved.'),
            ]);
        }

        $community->loadCount(['buildings', 'units', 'requests'])
            ->load(['country', 'city', 'district', 'currency', 'buildings', 'facilities', 'amenities:id,name,name_en,name_ar']);

        return Inertia::render('properties/communities/Show', [
            'community' => $community,
        ]);
    }

    public function edaatProductCodes(Request $request): JsonResponse
    {
        $communities = Community::query()
            ->select('id', 'name', 'product_code')
            ->whereNotNull('product_code')
            ->where('product_code', '!=', '')
            ->orderBy('name')
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($communities->items())->map(fn (Community $community): array => [
                'id' => $community->id,
                'name' => $community->name,
                'product_code' => $community->product_code,
            ]),
            'meta' => $this->meta($communities),
        ]);
    }

    public function offPlanSale(Request $request): JsonResponse
    {
        $communities = Community::query()
            ->where('is_off_plan_sale', true)
            ->withCount(['buildings', 'units', 'requests'])
            ->with([
                'city:id,name,name_en',
                'district:id,name,name_en',
                'images:id,mediable_id,mediable_type,name,url,collection',
            ])
            ->latest()
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($communities->items())->map(
                fn (Community $community): array => $this->communityListItem($community)
            ),
            'meta' => $this->meta($communities),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Community $community): Response
    {
        $this->authorize('update', $community);

        $community->load('amenities:id,name,name_en,name_ar');

        return Inertia::render('properties/communities/Edit', [
            'community' => $community,
            'countries' => Country::query()->select('id', 'name', 'name_en', 'currency')->orderBy('id')->get(),
            'currencies' => Currency::query()->select('id', 'name', 'code', 'symbol')->orderBy('name')->get(),
            'cities' => City::query()->select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::query()->select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
            'all_amenities' => Amenity::query()->select('id', 'name', 'name_en', 'name_ar')->orderBy('name_en')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityRequest $request, Community $community): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $community);

        $community->update($request->safe()->except('amenity_ids'));

        // Only sync amenities when the key is explicitly present in the payload.
        // Omitting amenity_ids entirely leaves the existing pivot rows untouched.
        if ($request->has('amenity_ids')) {
            $community->amenities()->sync($request->validated()['amenity_ids'] ?? []);
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $community->loadCount(['buildings', 'units', 'requests'])
                ->load([
                    'city:id,name,name_en',
                    'district:id,name,name_en',
                    'images:id,mediable_id,mediable_type,name,url,collection',
                ]);

            return response()->json([
                'data' => $this->communityListItem($community),
                'message' => __('Community updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Community updated.')]);

        return to_route('communities.show', $community);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Community $community): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $community);

        $communityId = $community->id;
        $community->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $communityId,
                ],
                'message' => __('Community deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Community deleted.')]);

        return to_route('communities.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function communityListItem(Community $community): array
    {
        return [
            'id' => $community->id,
            'name' => $community->name,
            'city' => $community->city
                ? [
                    'id' => $community->city->id,
                    'name' => $community->city->name,
                ]
                : null,
            'district' => $community->district
                ? [
                    'id' => $community->district->id,
                    'name' => $community->district->name,
                ]
                : null,
            'sales_commission_rate' => $this->decimalString($community->sales_commission_rate),
            'rental_commission_rate' => $this->decimalString($community->rental_commission_rate),
            'buildings_count' => (string) ($community->buildings_count ?? 0),
            'units_count' => (string) ($community->units_count ?? 0),
            'map' => $community->map,
            'images' => $this->mediaItems($community->images),
            'is_selected_property' => (bool) $community->is_selected_property,
            'count_selected_property' => (int) $community->count_selected_property,
            'requests_count' => (string) ($community->requests_count ?? 0),
            'total_income' => (float) ($community->total_income ?? 0),
            'is_market_place' => $community->is_market_place ? '1' : '0',
            'is_buy' => (int) $community->is_buy,
            'community_marketplace_type' => $community->community_marketplace_type?->value
                ?? ($community->community_marketplace_type ?: 'rent'),
            'is_off_plan_sale' => $community->is_off_plan_sale ? '1' : '0',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function communityDetails(Community $community): array
    {
        return [
            'id' => $community->id,
            'name' => $community->name,
            'description' => $community->description,
            'country' => $community->country
                ? [
                    'id' => $community->country->id,
                    'name' => $community->country->name,
                    'code' => $community->country->iso2,
                ]
                : null,
            'currency' => $community->currency
                ? [
                    'id' => $community->currency->id,
                    'name' => $community->currency->name,
                    'code' => $community->currency->code,
                ]
                : null,
            'city' => $community->city
                ? [
                    'id' => $community->city->id,
                    'name' => $community->city->name,
                ]
                : null,
            'district' => $community->district
                ? [
                    'id' => $community->district->id,
                    'name' => $community->district->name,
                ]
                : null,
            'amenities' => $community->amenities
                ->map(fn ($amenity): array => [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                ])
                ->values()
                ->all(),
            'map' => $community->map,
            'working_days' => $community->working_days,
            'latitude' => $community->latitude,
            'longitude' => $community->longitude,
            'images' => $this->mediaItems($community->images),
            'documents' => [],
            'buildings_count' => (int) ($community->buildings_count ?? 0),
            'units_count' => (int) ($community->units_count ?? 0),
            'requests_count' => (int) ($community->requests_count ?? 0),
            'total_income' => (float) ($community->total_income ?? 0),
            'sales_commission_rate' => $this->decimalString($community->sales_commission_rate),
            'rental_commission_rate' => $this->decimalString($community->rental_commission_rate),
            'product_code' => $community->product_code,
            'license_number' => $community->license_number,
            'license_issue_date' => $community->license_issue_date?->toDateString(),
            'license_expiry_date' => $community->license_expiry_date?->toDateString(),
            'record_payments' => [],
            'additional_payments' => [],
            'completion_percent' => (int) $community->completion_percent,
            'allow_cash_sale' => (int) $community->allow_cash_sale,
            'allow_bank_financing' => (int) $community->allow_bank_financing,
            'listed_percentage' => (float) $community->listed_percentage,
            'is_market_place' => $community->is_market_place ? '1' : '0',
            'is_buy' => (int) $community->is_buy,
            'community_marketplace_type' => $community->community_marketplace_type?->value
                ?? ($community->community_marketplace_type ?: 'rent'),
            'is_off_plan_sale' => $community->is_off_plan_sale ? '1' : '0',
        ];
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->integer('per_page', 10), 1), 50);
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * @param  iterable<int, mixed>  $media
     * @return array<int, array<string, mixed>>
     */
    private function mediaItems(iterable $media): array
    {
        return collect($media)->map(fn ($item): array => [
            'id' => $item->id,
            'name' => $item->name,
            'url' => $item->url,
        ])->values()->all();
    }

    private function decimalString(float|int|string|null $value): string
    {
        return number_format((float) ($value ?? 0), 2, '.', '');
    }
}
