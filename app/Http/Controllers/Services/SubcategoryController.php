<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\StoreSubcategoryRequest;
use App\Http\Requests\Services\UpdateSubcategoryRequest;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class SubcategoryController extends Controller
{
    /**
     * Store a newly created subcategory nested under a service category.
     */
    public function store(StoreSubcategoryRequest $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $serviceCategory->subcategories()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('service_categories.subcategory_created')]);

        return to_route('services.categories.index');
    }

    /**
     * Update the specified subcategory.
     */
    public function update(UpdateSubcategoryRequest $request, ServiceCategory $serviceCategory, ServiceSubcategory $serviceSubcategory): RedirectResponse
    {
        $serviceSubcategory->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('service_categories.subcategory_updated')]);

        return to_route('services.categories.index');
    }

    /**
     * Remove the specified subcategory.
     */
    public function destroy(ServiceCategory $serviceCategory, ServiceSubcategory $serviceSubcategory): RedirectResponse
    {
        $this->authorize('delete', $serviceCategory);

        $serviceSubcategory->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('service_categories.subcategory_deleted')]);

        return to_route('services.categories.index');
    }
}
