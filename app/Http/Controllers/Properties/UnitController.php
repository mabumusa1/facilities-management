<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\City;
use App\Models\Community;
use App\Models\District;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Status;
use App\Models\Unit;
use App\Models\UnitCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UnitController extends Controller
{
    public function index(Request $request): Response
    {
        $units = Unit::query()
            ->with(['community', 'building', 'category', 'type', 'status', 'owner', 'tenant'])
            ->latest()
            ->paginate(15);

        return Inertia::render('properties/units/Index', [
            'units' => $units,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('properties/units/Create', [
            'communities' => Community::select('id', 'name')->get(),
            'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
            'categories' => UnitCategory::with('types')->get(),
            'statuses' => Status::where('type', 'unit')->select('id', 'name', 'name_en')->get(),
            'owners' => Owner::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'residents' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'cities' => City::select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'rf_building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            'category_id' => ['required', 'integer', 'exists:rf_unit_categories,id'],
            'type_id' => ['required', 'integer', 'exists:rf_unit_types,id'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'owner_id' => ['nullable', 'integer', 'exists:rf_owners,id'],
            'tenant_id' => ['nullable', 'integer', 'exists:rf_tenants,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'net_area' => ['nullable', 'numeric', 'min:0'],
            'floor_no' => ['nullable', 'integer'],
            'year_build' => ['nullable', 'digits:4'],
            'about' => ['nullable', 'string'],
            'is_market_place' => ['sometimes', 'boolean'],
            'is_buy' => ['sometimes', 'boolean'],
            'is_off_plan_sale' => ['sometimes', 'boolean'],
        ]);

        $unit = Unit::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Unit created.')]);

        return to_route('units.show', $unit);
    }

    public function show(Unit $unit): Response
    {
        $unit->load(['community', 'building', 'category', 'type', 'status', 'owner', 'tenant']);

        return Inertia::render('properties/units/Show', [
            'unit' => $unit,
        ]);
    }

    public function edit(Unit $unit): Response
    {
        return Inertia::render('properties/units/Edit', [
            'unit' => $unit->load(['owner', 'tenant']),
            'communities' => Community::select('id', 'name')->get(),
            'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
            'categories' => UnitCategory::with('types')->get(),
            'statuses' => Status::where('type', 'unit')->select('id', 'name', 'name_en')->get(),
            'owners' => Owner::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'residents' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'cities' => City::select('id', 'name', 'name_en', 'country_id')->orderBy('name')->get(),
            'districts' => District::select('id', 'name', 'name_en', 'city_id')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'rf_building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            'category_id' => ['required', 'integer', 'exists:rf_unit_categories,id'],
            'type_id' => ['required', 'integer', 'exists:rf_unit_types,id'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'owner_id' => ['nullable', 'integer', 'exists:rf_owners,id'],
            'tenant_id' => ['nullable', 'integer', 'exists:rf_tenants,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'net_area' => ['nullable', 'numeric', 'min:0'],
            'floor_no' => ['nullable', 'integer'],
            'year_build' => ['nullable', 'digits:4'],
            'about' => ['nullable', 'string'],
            'is_market_place' => ['sometimes', 'boolean'],
            'is_buy' => ['sometimes', 'boolean'],
            'is_off_plan_sale' => ['sometimes', 'boolean'],
        ]);

        $unit->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Unit updated.')]);

        return to_route('units.show', $unit);
    }

    public function destroy(Unit $unit): RedirectResponse
    {
        $unit->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Unit deleted.')]);

        return to_route('units.index');
    }
}
