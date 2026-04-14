<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Amenity;
use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\Country;
use App\Models\Currency;
use App\Models\District;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CommunityController extends Controller
{
    /**
     * Display a listing of communities.
     */
    public function index(Request $request): Response
    {
        $tenantId = auth()->user()->tenant_id;
        $sortBy = $request->get('sortBy', $request->get('sort', 'created_at'));
        $sortDirection = $request->get('sortDirection', $request->get('direction', 'desc'));

        if (! in_array($sortBy, ['created_at', 'name'], true)) {
            $sortBy = 'created_at';
        }

        if (! in_array($sortDirection, ['asc', 'desc'], true)) {
            $sortDirection = 'desc';
        }

        $communities = Community::query()
            ->forTenant($tenantId)
            ->with(['city', 'district'])
            ->withCount('buildings')
            ->addSelect([
                'units_count' => Unit::query()
                    ->selectRaw('count(*)')
                    ->join('buildings as units_buildings', 'units_buildings.id', '=', 'units.building_id')
                    ->whereColumn('units_buildings.community_id', 'communities.id'),
            ])
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->orderBy($sortBy, $sortDirection)
            ->paginate($request->per_page ?? 15)
            ->withQueryString();

        return Inertia::render('properties/communities/index', [
            'communities' => $communities,
            'filters' => [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'sortBy' => $sortBy,
                'sortDirection' => $sortDirection,
            ],
            'tabCounts' => [
                'communities' => Community::query()->forTenant($tenantId)->count(),
                'buildings' => Building::query()->forTenant($tenantId)->count(),
                'units' => Unit::query()->forTenant($tenantId)->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new community.
     */
    public function create(): Response
    {
        $countries = Country::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar', 'currency_code']);

        $currencies = Currency::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar', 'code']);

        $cities = City::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar', 'country_id']);

        $districts = District::query()
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar', 'city_id']);

        $amenities = Amenity::query()
            ->active()
            ->orderBy('id')
            ->get(['id', 'name', 'name_ar', 'icon']);

        $defaultCountry = $countries->first();
        $defaultCurrency = $currencies->firstWhere('code', $defaultCountry?->currency_code)
            ?? $currencies->first();

        return Inertia::render('properties/communities/create', [
            'countries' => $countries,
            'currencies' => $currencies,
            'cities' => $cities,
            'districts' => $districts,
            'amenities' => $amenities,
            'defaults' => [
                'country_id' => $defaultCountry?->id,
                'currency_id' => $defaultCurrency?->id,
            ],
        ]);
    }

    /**
     * Store a newly created community.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'currency_id' => [
                'required',
                Rule::exists('currencies', 'id')->where(
                    fn ($query) => $query->when(
                        Country::query()
                            ->whereKey($request->integer('country_id'))
                            ->value('currency_code'),
                        fn ($currencyQuery, $currencyCode) => $currencyQuery->where('code', $currencyCode),
                    ),
                ),
            ],
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')
                    ->where(fn ($query) => $query->where('country_id', $request->integer('country_id'))),
            ],
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')
                    ->where(fn ($query) => $query->where('city_id', $request->integer('city_id'))),
            ],
            'location' => ['required', 'string', 'max:255'],
            'sales_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'rental_commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'about' => ['nullable', 'string', 'max:10000'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', Rule::exists('amenities', 'id')],
            'community_image' => ['nullable', 'image', 'mimes:png,jpeg,jpg,webp', 'max:30720'],
            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*' => [
                'file',
                'mimes:png,jpeg,jpg,webp,pdf,doc,docx,xls,xlsx,ppt,pptx',
                'max:30720',
            ],
        ]);

        $location = $validated['location'];
        $about = $validated['about'] ?? null;
        $amenityIds = $validated['amenity_ids'] ?? [];
        $communityImage = $request->file('community_image');
        $documents = $request->file('documents', []);

        $validated['map'] = [
            'location' => $location,
            'about' => $about,
        ];
        unset(
            $validated['location'],
            $validated['about'],
            $validated['amenity_ids'],
            $validated['community_image'],
            $validated['documents'],
        );

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = Community::STATUS_ACTIVE;

        $community = Community::create($validated);

        if ($amenityIds !== []) {
            $community->amenities()->sync($amenityIds);
        }

        $map = is_array($community->map) ? $community->map : [];

        if ($communityImage !== null) {
            $map['community_image'] = $communityImage->store("communities/{$community->id}/images", 'public');
        }

        if ($documents !== []) {
            $map['documents'] = collect($documents)
                ->map(fn ($file) => $file->store("communities/{$community->id}/documents", 'public'))
                ->all();
        }

        if ($map !== $community->map) {
            $community->forceFill(['map' => $map])->save();
        }

        return redirect()
            ->route('communities.show', $community)
            ->with('success', 'Community created successfully.');
    }

    /**
     * Display the specified community.
     */
    public function show(Community $community): Response
    {
        $this->authorize('view', $community);

        $community->load(['city', 'district', 'buildings' => fn ($q) => $q->withCount('units')]);

        return Inertia::render('properties/communities/show', [
            'community' => $community,
        ]);
    }

    /**
     * Show the form for editing the specified community.
     */
    public function edit(Community $community): Response
    {
        $this->authorize('update', $community);

        return Inertia::render('properties/communities/edit', [
            'community' => $community,
        ]);
    }

    /**
     * Update the specified community.
     */
    public function update(Request $request, Community $community): RedirectResponse
    {
        $this->authorize('update', $community);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'description' => ['nullable', 'string'],
            'map' => ['nullable', 'array'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $community->update($validated);

        return redirect()
            ->route('communities.show', $community)
            ->with('success', 'Community updated successfully.');
    }

    /**
     * Remove the specified community (soft delete).
     */
    public function destroy(Community $community): RedirectResponse
    {
        $this->authorize('delete', $community);

        $community->delete();

        return redirect()
            ->route('communities.index')
            ->with('success', 'Community deleted successfully.');
    }

    /**
     * Bulk upload page for communities.
     */
    public function bulkUpload(): Response
    {
        return Inertia::render('properties/communities/bulk-upload');
    }
}
