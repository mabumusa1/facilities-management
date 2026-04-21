<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\FacilityCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FacilityCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('app-settings/facility-categories/Index', [
            'categories' => FacilityCategory::withCount('facilities')->latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('app-settings/facility-categories/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['boolean'],
        ]);

        FacilityCategory::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category created.')]);

        return to_route('app-settings.facility-categories.index');
    }

    public function edit(FacilityCategory $facilityCategory): Response
    {
        return Inertia::render('app-settings/facility-categories/Edit', [
            'category' => $facilityCategory,
        ]);
    }

    public function update(Request $request, FacilityCategory $facilityCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['boolean'],
        ]);

        $facilityCategory->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category updated.')]);

        return to_route('app-settings.facility-categories.index');
    }

    public function destroy(FacilityCategory $facilityCategory): RedirectResponse
    {
        $facilityCategory->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category deleted.')]);

        return to_route('app-settings.facility-categories.index');
    }
}
