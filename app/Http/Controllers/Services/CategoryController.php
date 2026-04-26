<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\StoreCategoryRequest;
use App\Http\Requests\Services\UpdateCategoryRequest;
use App\Models\Community;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of service categories with subcategories.
     */
    public function index(): Response
    {
        $this->authorize('viewAny', ServiceCategory::class);

        $categories = ServiceCategory::query()
            ->with([
                'subcategories:id,service_category_id,name_en,name_ar,response_sla_hours,resolution_sla_hours,status',
                'communities:id,name',
                'defaultAssignee:id,name',
            ])
            ->withCount('subcategories')
            ->latest()
            ->get();

        $communities = Community::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $assignees = User::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return Inertia::render('services/categories/Index', [
            'categories' => $categories->map(fn (ServiceCategory $cat): array => $this->categoryPayload($cat)),
            'communities' => $communities,
            'assignees' => $assignees,
        ]);
    }

    /**
     * Store a newly created service category.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $communityIds = $validated['community_ids'];
        unset($validated['community_ids']);

        $category = ServiceCategory::create($validated);
        $category->communities()->sync($communityIds);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('service_categories.created')]);

        return to_route('services.categories.index');
    }

    /**
     * Update the specified service category.
     */
    public function update(UpdateCategoryRequest $request, ServiceCategory $serviceCategory): RedirectResponse
    {
        $validated = $request->validated();
        $communityIds = $validated['community_ids'];
        unset($validated['community_ids']);

        $serviceCategory->update($validated);
        $serviceCategory->communities()->sync($communityIds);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('service_categories.updated')]);

        return to_route('services.categories.index');
    }

    /**
     * Toggle the active/inactive status of a service category.
     */
    public function toggleStatus(ServiceCategory $serviceCategory): RedirectResponse
    {
        $this->authorize('update', $serviceCategory);

        $serviceCategory->update([
            'status' => $serviceCategory->status === 'active' ? 'inactive' : 'active',
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('service_categories.status_updated')]);

        return to_route('services.categories.index');
    }

    /**
     * Remove the specified service category if no service requests reference it.
     */
    public function destroy(ServiceCategory $serviceCategory): RedirectResponse
    {
        $this->authorize('delete', $serviceCategory);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('service_categories.deleted')]);

        $serviceCategory->delete();

        return to_route('services.categories.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function categoryPayload(ServiceCategory $category): array
    {
        return [
            'id' => $category->id,
            'name_en' => $category->name_en,
            'name_ar' => $category->name_ar,
            'icon' => $category->icon,
            'response_sla_hours' => $category->response_sla_hours,
            'resolution_sla_hours' => $category->resolution_sla_hours,
            'require_completion_photo' => $category->require_completion_photo,
            'status' => $category->status,
            'subcategories_count' => $category->subcategories_count ?? 0,
            'default_assignee' => $category->defaultAssignee ? [
                'id' => $category->defaultAssignee->id,
                'name' => $category->defaultAssignee->name,
            ] : null,
            'communities' => $category->communities->map(fn (Community $c): array => [
                'id' => $c->id,
                'name' => $c->name,
            ])->values()->all(),
            'subcategories' => $category->subcategories->map(fn ($sub): array => [
                'id' => $sub->id,
                'name_en' => $sub->name_en,
                'name_ar' => $sub->name_ar,
                'response_sla_hours' => $sub->response_sla_hours,
                'resolution_sla_hours' => $sub->resolution_sla_hours,
                'status' => $sub->status,
            ])->values()->all(),
        ];
    }
}
