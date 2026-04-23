<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\District;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse|Response
    {
        $this->authorize('viewAny', Building::class);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $buildings = Building::query()
                ->withCount('units')
                ->with([
                    'community:id,name,map',
                    'city:id,name,name_en',
                    'district:id,name,name_en',
                    'images:id,mediable_id,mediable_type,name,url,collection',
                ])
                ->latest()
                ->paginate($this->perPage($request));

            return response()->json([
                'data' => collect($buildings->items())->map(
                    fn (Building $building): array => $this->buildingListItem($building)
                ),
                'meta' => $this->meta($buildings),
            ]);
        }

        $buildings = Building::query()
            ->withCount('units')
            ->with(['community', 'city', 'district'])
            ->latest()
            ->paginate(15);

        return Inertia::render('properties/buildings/Index', [
            'buildings' => $buildings,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $this->authorize('create', Building::class);

        return Inertia::render('properties/buildings/Create', [
            'communities' => Community::select('id', 'name')->get(),
            'cities' => City::select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Building::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'no_floors' => ['nullable', 'integer', 'min:0'],
            'year_build' => ['nullable', 'digits:4'],
        ]);

        $building = Building::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $building->loadCount('units')
                ->load([
                    'community:id,name,map',
                    'city:id,name,name_en',
                    'district:id,name,name_en',
                    'images:id,mediable_id,mediable_type,name,url,collection',
                ]);

            return response()->json([
                'data' => $this->buildingListItem($building),
                'message' => __('Building created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Building created.')]);

        return to_route('buildings.show', $building);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Building $building): JsonResponse|Response
    {
        $this->authorize('view', $building);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $building->loadCount('units')
                ->load([
                    'community' => fn ($query) => $query
                        ->withCount(['buildings', 'units', 'requests'])
                        ->with([
                            'city:id,name,name_en',
                            'district:id,name,name_en',
                            'images:id,mediable_id,mediable_type,name,url,collection',
                        ]),
                    'city:id,name,name_en',
                    'district:id,name,name_en',
                    'images:id,mediable_id,mediable_type,name,url,collection',
                    'documents:id,mediable_id,mediable_type,name,url,collection',
                ]);

            return response()->json([
                'data' => $this->buildingDetails($building),
                'message' => __('Building retrieved.'),
            ]);
        }

        $building->loadCount('units')
            ->load(['community', 'city', 'district', 'units']);

        return Inertia::render('properties/buildings/Show', [
            'building' => $building,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Building $building): Response
    {
        $this->authorize('update', $building);

        return Inertia::render('properties/buildings/Edit', [
            'building' => $building,
            'communities' => Community::select('id', 'name')->get(),
            'cities' => City::select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Building $building): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $building);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'no_floors' => ['nullable', 'integer', 'min:0'],
            'year_build' => ['nullable', 'digits:4'],
        ]);

        $building->update($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $building->loadCount('units')
                ->load([
                    'community:id,name,map',
                    'city:id,name,name_en',
                    'district:id,name,name_en',
                    'images:id,mediable_id,mediable_type,name,url,collection',
                ]);

            return response()->json([
                'data' => $this->buildingListItem($building),
                'message' => __('Building updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Building updated.')]);

        return to_route('buildings.show', $building);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Building $building): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $building);

        $buildingId = $building->id;
        $building->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $buildingId,
                ],
                'message' => __('Building deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Building deleted.')]);

        return to_route('buildings.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function buildingListItem(Building $building): array
    {
        return [
            'id' => $building->id,
            'name' => $building->name,
            'community' => $building->community
                ? [
                    'id' => $building->community->id,
                    'name' => $building->community->name,
                    'map' => $building->community->map,
                ]
                : null,
            'city' => $building->city
                ? [
                    'id' => $building->city->id,
                    'name' => $building->city->name,
                ]
                : null,
            'district' => $building->district
                ? [
                    'id' => $building->district->id,
                    'name' => $building->district->name,
                ]
                : null,
            'units_count' => $building->units_count ?? 0,
            'map' => $building->map,
            'year_build' => $building->year_build,
            'images' => $this->mediaItems($building->images),
            'is_selected_property' => 0,
            'count_selected_property' => 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildingDetails(Building $building): array
    {
        $community = $building->community;

        return [
            'id' => $building->id,
            'name' => $building->name,
            'community' => $community
                ? [
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
                    'buildings_count' => $community->buildings_count ?? 0,
                    'units_count' => $community->units_count ?? 0,
                    'map' => $community->map,
                    'images' => $this->mediaItems($community->images),
                    'is_selected_property' => (bool) $community->is_selected_property,
                    'count_selected_property' => (int) $community->count_selected_property,
                    'requests_count' => $community->requests_count,
                    'total_income' => (float) ($community->total_income ?? 0),
                    'is_market_place' => $community->is_market_place ? '1' : '0',
                    'is_buy' => (int) $community->is_buy,
                    'community_marketplace_type' => $community->community_marketplace_type?->value
                        ?? ($community->community_marketplace_type ?: 'rent'),
                    'is_off_plan_sale' => $community->is_off_plan_sale ? '1' : '0',
                ]
                : null,
            'city' => $building->city
                ? [
                    'id' => $building->city->id,
                    'name' => $building->city->name,
                ]
                : null,
            'district' => $building->district
                ? [
                    'id' => $building->district->id,
                    'name' => $building->district->name,
                ]
                : null,
            'no_floors' => (string) ($building->no_floors ?? 0),
            'year_build' => $building->year_build,
            'map' => $building->map,
            'images' => $this->mediaItems($building->images),
            'documents' => $this->mediaItems($building->documents),
            'units' => $building->units_count ?? 0,
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
