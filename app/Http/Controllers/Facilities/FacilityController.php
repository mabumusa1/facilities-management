<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\FacilityCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FacilityController extends Controller
{
    public function index(Request $request): Response
    {
        $facilities = Facility::query()
            ->with(['category', 'community'])
            ->latest()
            ->paginate(15);

        return Inertia::render('facilities/Index', [
            'facilities' => $facilities,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('facilities/Create', [
            'categories' => FacilityCategory::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:rf_facility_categories,id'],
            'community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'capacity' => ['nullable', 'integer', 'min:1'],
        ]);

        $facility = Facility::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Facility created.')]);

        return to_route('facilities.show', $facility);
    }

    public function show(Facility $facility): Response
    {
        $facility->load(['category', 'community', 'bookings']);

        return Inertia::render('facilities/Show', [
            'facility' => $facility,
        ]);
    }

    public function edit(Facility $facility): Response
    {
        return Inertia::render('facilities/Edit', [
            'facility' => $facility,
            'categories' => FacilityCategory::all(),
        ]);
    }

    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:rf_facility_categories,id'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $facility->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Facility updated.')]);

        return to_route('facilities.show', $facility);
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Facility deleted.')]);

        return to_route('facilities.index');
    }
}
