<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Community;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UnitController extends Controller
{
    /**
     * Display a listing of units.
     */
    public function index(Request $request): Response
    {
        $units = Unit::query()
            ->forTenant(auth()->user()->tenant_id)
            ->with(['community', 'building', 'category', 'type', 'status'])
            ->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%"))
            ->when($request->community_id, fn ($q, $communityId) => $q->forCommunity($communityId))
            ->when($request->building_id, fn ($q, $buildingId) => $q->forBuilding($buildingId))
            ->when($request->category_id, fn ($q, $categoryId) => $q->forCategory($categoryId))
            ->when($request->is_marketplace !== null, fn ($q) => $request->is_marketplace ? $q->marketplace() : $q->notMarketplace())
            ->orderBy($request->sort ?? 'created_at', $request->direction ?? 'desc')
            ->paginate($request->per_page ?? 15)
            ->withQueryString();

        $communities = Community::query()
            ->forTenant(auth()->user()->tenant_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = UnitCategory::active()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('properties/units/index', [
            'units' => $units,
            'communities' => $communities,
            'categories' => $categories,
            'filters' => $request->only(['search', 'community_id', 'building_id', 'category_id', 'is_marketplace', 'sort', 'direction']),
        ]);
    }

    /**
     * Show the form for creating a new unit.
     */
    public function create(Request $request): Response
    {
        $communities = Community::query()
            ->forTenant(auth()->user()->tenant_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        $buildings = Building::query()
            ->forTenant(auth()->user()->tenant_id)
            ->active()
            ->when($request->community_id, fn ($q, $communityId) => $q->forCommunity($communityId))
            ->orderBy('name')
            ->get(['id', 'name', 'community_id']);

        $categories = UnitCategory::active()->orderBy('name')->get(['id', 'name']);
        $types = UnitType::active()->orderBy('name')->get(['id', 'name', 'unit_category_id']);

        return Inertia::render('properties/units/create', [
            'communities' => $communities,
            'buildings' => $buildings,
            'categories' => $categories,
            'types' => $types,
            'preselectedCommunityId' => $request->community_id,
            'preselectedBuildingId' => $request->building_id,
        ]);
    }

    /**
     * Store a newly created unit.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'community_id' => ['required', 'exists:communities,id'],
            'building_id' => ['nullable', 'exists:buildings,id'],
            'unit_category_id' => ['required', 'exists:unit_categories,id'],
            'unit_type_id' => ['required', 'exists:unit_types,id'],
            'floor_no' => ['nullable', 'integer', 'min:0', 'max:200'],
            'net_area' => ['nullable', 'numeric', 'min:0'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'market_rent' => ['nullable', 'numeric', 'min:0'],
            'about' => ['nullable', 'string'],
            'map' => ['nullable', 'array'],
            'is_marketplace' => ['boolean'],
            'is_off_plan_sale' => ['boolean'],
        ]);

        $validated['tenant_id'] = auth()->user()->tenant_id;

        $unit = Unit::create($validated);

        return redirect()
            ->route('units.show', $unit)
            ->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified unit.
     */
    public function show(Unit $unit): Response
    {
        $this->authorize('view', $unit);

        $unit->load(['community', 'building', 'category', 'type', 'status', 'city', 'district']);

        return Inertia::render('properties/units/show', [
            'unit' => $unit,
        ]);
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit(Unit $unit): Response
    {
        $this->authorize('update', $unit);

        $communities = Community::query()
            ->forTenant(auth()->user()->tenant_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        $buildings = Building::query()
            ->forTenant(auth()->user()->tenant_id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name', 'community_id']);

        $categories = UnitCategory::active()->orderBy('name')->get(['id', 'name']);
        $types = UnitType::active()->orderBy('name')->get(['id', 'name', 'unit_category_id']);

        return Inertia::render('properties/units/edit', [
            'unit' => $unit,
            'communities' => $communities,
            'buildings' => $buildings,
            'categories' => $categories,
            'types' => $types,
        ]);
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $this->authorize('update', $unit);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'community_id' => ['required', 'exists:communities,id'],
            'building_id' => ['nullable', 'exists:buildings,id'],
            'unit_category_id' => ['required', 'exists:unit_categories,id'],
            'unit_type_id' => ['required', 'exists:unit_types,id'],
            'floor_no' => ['nullable', 'integer', 'min:0', 'max:200'],
            'net_area' => ['nullable', 'numeric', 'min:0'],
            'year_built' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'market_rent' => ['nullable', 'numeric', 'min:0'],
            'about' => ['nullable', 'string'],
            'map' => ['nullable', 'array'],
            'is_marketplace' => ['boolean'],
            'is_off_plan_sale' => ['boolean'],
        ]);

        $unit->update($validated);

        return redirect()
            ->route('units.show', $unit)
            ->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified unit (soft delete).
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        $this->authorize('delete', $unit);

        $unit->delete();

        return redirect()
            ->route('units.index')
            ->with('success', 'Unit deleted successfully.');
    }
}
