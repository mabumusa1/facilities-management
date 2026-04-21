<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\RequestCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RequestCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('app-settings/request-categories/Index', [
            'categories' => RequestCategory::withCount('subcategories')->latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('app-settings/request-categories/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['boolean'],
            'has_sub_categories' => ['boolean'],
        ]);

        RequestCategory::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category created.')]);

        return to_route('app-settings.request-categories.index');
    }

    public function show(RequestCategory $requestCategory): Response
    {
        return Inertia::render('app-settings/request-categories/Show', [
            'category' => $requestCategory->load('subcategories'),
        ]);
    }

    public function edit(RequestCategory $requestCategory): Response
    {
        $requestCategory->load(['subcategories', 'serviceSettings']);

        return Inertia::render('app-settings/request-categories/Edit', [
            'category' => $requestCategory,
            'serviceSetting' => $requestCategory->serviceSettings->first(),
        ]);
    }

    public function update(Request $request, RequestCategory $requestCategory): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['boolean'],
            'has_sub_categories' => ['boolean'],
        ]);

        $requestCategory->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category updated.')]);

        return to_route('app-settings.request-categories.index');
    }

    public function destroy(RequestCategory $requestCategory): RedirectResponse
    {
        $requestCategory->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category deleted.')]);

        return to_route('app-settings.request-categories.index');
    }
}
