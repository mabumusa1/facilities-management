<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestSubcategoryController extends Controller
{
    public function store(Request $request, RequestCategory $requestCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['boolean'],
            'is_all_day' => ['boolean'],
            'start' => ['nullable', 'string'],
            'end' => ['nullable', 'string'],
            'terms_and_conditions' => ['nullable', 'string'],
        ]);

        $requestCategory->subcategories()->create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subcategory created.')]);

        return to_route('app-settings.request-categories.index');
    }

    public function update(Request $request, RequestCategory $requestCategory, RequestSubcategory $requestSubcategory): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['boolean'],
            'is_all_day' => ['boolean'],
            'start' => ['nullable', 'string'],
            'end' => ['nullable', 'string'],
            'terms_and_conditions' => ['nullable', 'string'],
        ]);

        $requestSubcategory->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subcategory updated.')]);

        return to_route('app-settings.request-categories.index');
    }

    public function destroy(RequestCategory $requestCategory, RequestSubcategory $requestSubcategory): RedirectResponse
    {
        $requestSubcategory->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subcategory deleted.')]);

        return to_route('app-settings.request-categories.index');
    }
}
