<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Community;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class BuildingController extends Controller
{
    /**
     * Display a listing of buildings.
     */
    public function index(Request $request): Response
    {
        $buildings = Building::query()
            ->forTenant(auth()->user()->tenant_id)
            ->with(['community', 'city', 'district'])
            ->withCount('units')
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->community_id, fn ($q, $communityId) => $q->forCommunity($communityId))
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate($request->per_page ?? 15)
            ->withQueryString();

        $communities = Community::query()
            ->forTenant(auth()->user()->tenant_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('properties/buildings/index', [
            'buildings' => $buildings,
            'communities' => $communities,
            'filters' => $request->only(['search', 'status', 'community_id', 'sort', 'direction']),
            'tabCounts' => [
                'communities' => Community::query()->forTenant(auth()->user()->tenant_id)->count(),
                'buildings' => Building::query()->forTenant(auth()->user()->tenant_id)->count(),
                'units' => Unit::query()->forTenant(auth()->user()->tenant_id)->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new building.
     */
    public function create(Request $request): Response
    {
        $communities = Community::query()
            ->forTenant(auth()->user()->tenant_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('properties/buildings/create', [
            'communities' => $communities,
            'preselectedCommunityId' => $request->community_id,
        ]);
    }

    /**
     * Store a newly created building.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'community_id' => [
                'required',
                Rule::exists('communities', 'id')->where(
                    fn ($query) => $query->where('tenant_id', auth()->user()->tenant_id)
                ),
            ],
            'city_id' => [
                'nullable',
                'required_with:district_id',
                Rule::exists('cities', 'id'),
            ],
            'district_id' => [
                'nullable',
                Rule::exists('districts', 'id')->where(
                    fn ($query) => $query->where('city_id', $request->integer('city_id'))
                ),
            ],
            'no_floors' => ['nullable', 'integer', 'min:0', 'max:200'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'map' => ['nullable', 'array'],
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;
        $validated['status'] = Building::STATUS_ACTIVE;

        $building = Building::create($validated);

        return redirect()
            ->route('buildings.show', $building)
            ->with('success', 'Building created successfully.');
    }

    /**
     * Display the specified building.
     */
    public function show(Building $building): Response
    {
        $this->authorize('view', $building);

        $building->load(['community', 'city', 'district', 'units' => fn ($q) => $q->with(['category', 'type'])]);

        return Inertia::render('properties/buildings/show', [
            'building' => $building,
        ]);
    }

    /**
     * Show the form for editing the specified building.
     */
    public function edit(Building $building): Response
    {
        $this->authorize('update', $building);

        $communities = Community::query()
            ->forTenant(auth()->user()->tenant_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('properties/buildings/edit', [
            'building' => $building,
            'communities' => $communities,
        ]);
    }

    /**
     * Update the specified building.
     */
    public function update(Request $request, Building $building): RedirectResponse
    {
        $this->authorize('update', $building);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'community_id' => [
                'required',
                Rule::exists('communities', 'id')->where(
                    fn ($query) => $query->where('tenant_id', auth()->user()->tenant_id)
                ),
            ],
            'city_id' => [
                'nullable',
                'required_with:district_id',
                Rule::exists('cities', 'id'),
            ],
            'district_id' => [
                'nullable',
                Rule::exists('districts', 'id')->where(
                    fn ($query) => $query->where('city_id', $request->integer('city_id'))
                ),
            ],
            'no_floors' => ['nullable', 'integer', 'min:0', 'max:200'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'map' => ['nullable', 'array'],
            'status' => ['sometimes', 'in:active,inactive'],
        ]);

        $building->update($validated);

        return redirect()
            ->route('buildings.show', $building)
            ->with('success', 'Building updated successfully.');
    }

    /**
     * Remove the specified building (soft delete).
     */
    public function destroy(Building $building): RedirectResponse
    {
        $this->authorize('delete', $building);

        $building->delete();

        return redirect()
            ->route('buildings.index')
            ->with('success', 'Building deleted successfully.');
    }
}
