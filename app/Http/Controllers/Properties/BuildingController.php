<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Community;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BuildingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
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
        return Inertia::render('properties/buildings/Create', [
            'communities' => Community::select('id', 'name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'no_floors' => ['nullable', 'integer', 'min:0'],
            'year_build' => ['nullable', 'digits:4'],
        ]);

        $building = Building::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Building created.')]);

        return to_route('buildings.show', $building);
    }

    /**
     * Display the specified resource.
     */
    public function show(Building $building): Response
    {
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
        return Inertia::render('properties/buildings/Edit', [
            'building' => $building,
            'communities' => Community::select('id', 'name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Building $building): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'rf_community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'no_floors' => ['nullable', 'integer', 'min:0'],
            'year_build' => ['nullable', 'digits:4'],
        ]);

        $building->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Building updated.')]);

        return to_route('buildings.show', $building);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Building $building): RedirectResponse
    {
        $building->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Building deleted.')]);

        return to_route('buildings.index');
    }
}
