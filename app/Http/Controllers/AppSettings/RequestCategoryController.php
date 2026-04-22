<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use App\Models\ServiceSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class RequestCategoryController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

            $categories = RequestCategory::query()
                ->with([
                    'subcategories:id,category_id,name,name_ar,name_en,status,icon_id',
                    'serviceSettings:id,category_id,visibilities,permissions,submit_request_before_type,submit_request_before_value,capacity_type,capacity_value',
                ])
                ->orderBy('id')
                ->paginate($perPage)
                ->withQueryString();

            $icons = $this->resolveIcons($categories->getCollection());

            return response()->json([
                'data' => collect($categories->items())->map(
                    fn (RequestCategory $category): array => $this->categoryPayload($category, $icons)
                ),
                'meta' => $this->meta($categories),
            ]);
        }

        return Inertia::render('app-settings/request-categories/Index', [
            'categories' => RequestCategory::withCount('subcategories')->latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('app-settings/request-categories/Create');
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['boolean'],
            'has_sub_categories' => ['boolean'],
        ]);

        $requestCategory = RequestCategory::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $requestCategory->load([
                'subcategories:id,category_id,name,name_ar,name_en,status,icon_id',
                'serviceSettings:id,category_id,visibilities,permissions,submit_request_before_type,submit_request_before_value,capacity_type,capacity_value',
            ]);

            $icons = $this->resolveIcons(collect([$requestCategory]));

            return response()->json([
                'data' => $this->categoryPayload($requestCategory, $icons),
                'message' => __('Category created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category created.')]);

        return to_route('app-settings.request-categories.index');
    }

    public function show(Request $request, RequestCategory $requestCategory): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $requestCategory->load([
                'subcategories:id,category_id,name,name_ar,name_en,status,icon_id',
                'serviceSettings:id,category_id,visibilities,permissions,submit_request_before_type,submit_request_before_value,capacity_type,capacity_value',
            ]);

            $icons = $this->resolveIcons(collect([$requestCategory]));

            return response()->json([
                'data' => $this->categoryPayload($requestCategory, $icons),
                'message' => __('Category retrieved.'),
            ]);
        }

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

    public function update(Request $request, RequestCategory $requestCategory): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $validated = $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'name_ar' => ['nullable', 'string', 'max:255', 'required_without_all:name,name_en'],
                'name_en' => ['nullable', 'string', 'max:255', 'required_without_all:name,name_ar'],
                'status' => ['sometimes', 'boolean'],
                'has_sub_categories' => ['sometimes', 'boolean'],
            ]);

            $nameEn = $validated['name_en'] ?? $validated['name'] ?? $requestCategory->name_en ?? $requestCategory->name;
            $nameAr = $validated['name_ar'] ?? $requestCategory->name_ar ?? $nameEn;

            $updates = [
                'name' => $validated['name'] ?? $nameEn,
                'name_ar' => $nameAr,
                'name_en' => $nameEn,
            ];

            if (array_key_exists('status', $validated)) {
                $updates['status'] = (bool) $validated['status'];
            }

            if (array_key_exists('has_sub_categories', $validated)) {
                $updates['has_sub_categories'] = (bool) $validated['has_sub_categories'];
            }

            $requestCategory->update($updates);
            $requestCategory->load([
                'subcategories:id,category_id,name,name_ar,name_en,status,icon_id',
                'serviceSettings:id,category_id,visibilities,permissions,submit_request_before_type,submit_request_before_value,capacity_type,capacity_value',
            ]);

            $icons = $this->resolveIcons(collect([$requestCategory]));

            return response()->json([
                'data' => $this->categoryPayload($requestCategory, $icons),
                'message' => __('Category updated.'),
            ]);
        }

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

    public function destroy(Request $request, RequestCategory $requestCategory): JsonResponse|RedirectResponse
    {
        $categoryId = $requestCategory->id;
        $requestCategory->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $categoryId,
                ],
                'message' => __('Category deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Category deleted.')]);

        return to_route('app-settings.request-categories.index');
    }

    /**
     * @param  Collection<int, RequestCategory>  $categories
     * @return Collection<int, Media>
     */
    private function resolveIcons(Collection $categories): Collection
    {
        $iconIds = $categories
            ->pluck('icon_id')
            ->merge(
                $categories->flatMap(
                    fn (RequestCategory $category): Collection => $category->subcategories->pluck('icon_id')
                )
            )
            ->filter()
            ->unique()
            ->values();

        if ($iconIds->isEmpty()) {
            return collect();
        }

        return Media::query()
            ->whereIn('id', $iconIds)
            ->get()
            ->keyBy('id');
    }

    /**
     * @param  Collection<int, Media>  $icons
     * @return array<string, mixed>
     */
    private function categoryPayload(RequestCategory $category, Collection $icons): array
    {
        $serviceSetting = $category->serviceSettings->first();

        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'status' => $category->status ? '1' : '0',
            'has_sub_categories' => $category->has_sub_categories ? '1' : '0',
            'sub_categories' => $category->subcategories->map(
                fn (RequestSubcategory $subcategory): array => [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'icon' => $this->iconPayload($icons->get($subcategory->icon_id)),
                    'status' => $subcategory->status ? '1' : '0',
                ]
            )->values()->all(),
            'serviceSettings' => $this->serviceSettingsPayload($serviceSetting),
            'icon' => $this->iconPayload($icons->get($category->icon_id)),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serviceSettingsPayload(?ServiceSetting $serviceSetting): array
    {
        $visibilities = [
            'hide_resident_number' => false,
            'hide_resident_name' => false,
            'hide_professional_number_and_name' => false,
            'show_unified_number_only' => false,
        ];

        $permissions = [
            'manager_close_Request' => false,
            'not_require_professional_enter_request_code' => false,
            'not_require_professional_upload_request_photo' => false,
            'attachments_required' => false,
            'allow_professional_reschedule' => false,
        ];

        if ($serviceSetting !== null) {
            $visibilities = [
                ...$visibilities,
                ...($serviceSetting->visibilities ?? []),
            ];

            $permissions = [
                ...$permissions,
                ...($serviceSetting->permissions ?? []),
            ];
        }

        return [
            'visibilities' => $visibilities,
            'permissions' => $permissions,
            'submit_request_before_type' => $serviceSetting?->submit_request_before_type,
            'submit_request_before_value' => $serviceSetting?->submit_request_before_value,
            'capacity_type' => $serviceSetting?->capacity_type,
            'capacity_value' => $serviceSetting?->capacity_value,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function iconPayload(?Media $icon): ?array
    {
        if ($icon === null) {
            return null;
        }

        return [
            'id' => $icon->id,
            'url' => $icon->url,
            'name' => $icon->name,
            'notes' => $icon->notes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }
}
