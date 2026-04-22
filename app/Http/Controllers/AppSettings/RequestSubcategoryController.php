<?php

namespace App\Http\Controllers\AppSettings;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\Community;
use App\Models\Media;
use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Inertia\Inertia;

class RequestSubcategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);
        $categoryId = (int) $request->integer('category_id', 0);

        $subcategories = RequestSubcategory::query()
            ->withCount('requests')
            ->when($categoryId > 0, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $icons = $this->resolveIcons($subcategories->getCollection());

        return response()->json([
            'data' => collect($subcategories->items())->map(
                fn (RequestSubcategory $subcategory): array => $this->subcategoryListPayload($subcategory, $icons)
            ),
            'meta' => $this->meta($subcategories),
        ]);
    }

    public function show(RequestSubcategory $requestSubcategory): JsonResponse
    {
        $requestSubcategory
            ->load([
                'workingDays:id,subcategory_id,day,start,end,is_active',
                'featuredServices:id,subcategory_id,title,title_ar,title_en,description,is_active',
                'buildings:id,name',
                'communities:id,name',
            ])
            ->loadCount('requests');

        $icons = $this->resolveIcons(collect([$requestSubcategory]));

        return response()->json([
            'data' => $this->subcategoryDetailsPayload($requestSubcategory, $icons),
            'message' => __('Subcategory retrieved.'),
        ]);
    }

    public function typesIndex(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

        $types = RequestSubcategory::query()
            ->orderBy('id')
            ->paginate($perPage)
            ->withQueryString();

        $icons = $this->resolveIcons($types->getCollection());

        return response()->json([
            'data' => collect($types->items())->map(
                fn (RequestSubcategory $subcategory): array => $this->typePayload($subcategory, $icons)
            ),
            'meta' => $this->meta($types),
        ]);
    }

    public function typesCreate(): JsonResponse
    {
        $subcategories = RequestSubcategory::query()
            ->select('id', 'name', 'name_ar', 'name_en')
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => [
                'sub_categories' => $subcategories->map(fn (RequestSubcategory $subcategory): array => [
                    'id' => $subcategory->id,
                    'name' => $subcategory->name,
                    'name_ar' => $subcategory->name_ar,
                    'name_en' => $subcategory->name_en,
                ])->values()->all(),
                'fee_types' => [],
            ],
            'meta' => [],
        ]);
    }

    public function typesList(RequestSubcategory $requestSubcategory): JsonResponse
    {
        $icons = $this->resolveIcons(collect([$requestSubcategory]));

        return response()->json([
            'data' => [
                $this->typePayload($requestSubcategory, $icons),
            ],
            'meta' => [],
        ]);
    }

    public function typesShow(RequestSubcategory $requestSubcategory): JsonResponse
    {
        $icons = $this->resolveIcons(collect([$requestSubcategory]));

        return response()->json([
            'data' => $this->typePayload($requestSubcategory, $icons),
            'message' => __('Type retrieved.'),
        ]);
    }

    public function storeRf(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:rf_request_categories,id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'is_all_day' => ['sometimes', 'boolean'],
            'start' => ['nullable', 'date_format:H:i'],
            'end' => ['nullable', 'date_format:H:i'],
            'terms_and_conditions' => ['nullable', 'string'],
            'icon_id' => ['nullable', 'integer', 'exists:media,id'],
        ]);

        $requestSubcategory = RequestSubcategory::query()->create([
            'category_id' => (int) $validated['category_id'],
            'name' => (string) $validated['name_en'],
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'status' => array_key_exists('status', $validated) ? (bool) $validated['status'] : true,
            'is_all_day' => $validated['is_all_day'] ?? null,
            'start' => $validated['start'] ?? null,
            'end' => $validated['end'] ?? null,
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
            'icon_id' => $validated['icon_id'] ?? null,
        ]);

        $requestSubcategory
            ->load([
                'workingDays:id,subcategory_id,day,start,end,is_active',
                'featuredServices:id,subcategory_id,title,title_ar,title_en,description,is_active',
                'buildings:id,name',
                'communities:id,name',
            ])
            ->loadCount('requests');

        $icons = $this->resolveIcons(collect([$requestSubcategory]));

        return response()->json([
            'data' => $this->subcategoryDetailsPayload($requestSubcategory, $icons),
            'message' => __('Subcategory created.'),
        ]);
    }

    public function storeType(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:rf_request_categories,id', 'required_without:rf_sub_category_id'],
            'rf_sub_category_id' => ['nullable', 'integer', 'exists:rf_request_subcategories,id', 'required_without:category_id'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'icon_id' => ['nullable', 'integer', 'exists:media,id'],
        ]);

        $categoryId = $validated['category_id'] ?? null;

        if ($categoryId === null && array_key_exists('rf_sub_category_id', $validated)) {
            $categoryId = RequestSubcategory::query()
                ->whereKey((int) $validated['rf_sub_category_id'])
                ->value('category_id');
        }

        if ($categoryId === null) {
            abort(422, 'Category is required to create request type.');
        }

        $requestSubcategory = RequestSubcategory::query()->create([
            'category_id' => (int) $categoryId,
            'name' => (string) $validated['name_en'],
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
            'status' => array_key_exists('status', $validated) ? (bool) $validated['status'] : true,
            'icon_id' => $validated['icon_id'] ?? null,
        ]);

        $icons = $this->resolveIcons(collect([$requestSubcategory]));

        return response()->json([
            'data' => $this->typePayload($requestSubcategory, $icons),
            'message' => __('Type created.'),
        ]);
    }

    public function updateRf(Request $request, RequestSubcategory $requestSubcategory): JsonResponse
    {
        $validated = $request->validate([
            'terms_and_conditions' => ['required', 'string'],
            'name_ar' => ['sometimes', 'string', 'max:255'],
            'name_en' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'is_all_day' => ['sometimes', 'boolean'],
            'start' => ['nullable', 'date_format:H:i'],
            'end' => ['nullable', 'date_format:H:i'],
            'icon_id' => ['nullable', 'integer', 'exists:media,id'],
            'icon' => ['nullable'],
        ]);

        if (! array_key_exists('icon_id', $validated) && array_key_exists('icon', $validated) && is_numeric((string) $validated['icon'])) {
            $validated['icon_id'] = (int) $validated['icon'];
        }

        $updates = [
            'terms_and_conditions' => $validated['terms_and_conditions'],
        ];

        if (array_key_exists('name_ar', $validated)) {
            $updates['name_ar'] = $validated['name_ar'];
        }

        if (array_key_exists('name_en', $validated)) {
            $updates['name_en'] = $validated['name_en'];
            $updates['name'] = $validated['name_en'];
        }

        if (array_key_exists('status', $validated)) {
            $updates['status'] = (bool) $validated['status'];
        }

        if (array_key_exists('is_all_day', $validated)) {
            $updates['is_all_day'] = (bool) $validated['is_all_day'];
        }

        if (array_key_exists('start', $validated)) {
            $updates['start'] = $validated['start'];
        }

        if (array_key_exists('end', $validated)) {
            $updates['end'] = $validated['end'];
        }

        if (array_key_exists('icon_id', $validated)) {
            $updates['icon_id'] = $validated['icon_id'];
        }

        $requestSubcategory->update($updates);
        $requestSubcategory
            ->load([
                'workingDays:id,subcategory_id,day,start,end,is_active',
                'featuredServices:id,subcategory_id,title,title_ar,title_en,description,is_active',
                'buildings:id,name',
                'communities:id,name',
            ])
            ->loadCount('requests');

        $icons = $this->resolveIcons(collect([$requestSubcategory]));

        return response()->json([
            'data' => $this->subcategoryDetailsPayload($requestSubcategory, $icons),
            'message' => __('Subcategory updated.'),
        ]);
    }

    public function updateTypeRf(Request $request, RequestSubcategory $requestSubcategory): JsonResponse
    {
        $validated = $request->validate([
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'rf_sub_category_id' => ['required', 'integer', 'exists:rf_request_subcategories,id'],
            'fee_type' => ['required', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'icon_id' => ['nullable', 'integer', 'exists:media,id'],
            'icon' => ['nullable'],
        ]);

        if (! array_key_exists('icon_id', $validated) && array_key_exists('icon', $validated) && is_numeric((string) $validated['icon'])) {
            $validated['icon_id'] = (int) $validated['icon'];
        }

        $categoryId = RequestSubcategory::query()
            ->whereKey((int) $validated['rf_sub_category_id'])
            ->value('category_id');

        if ($categoryId === null) {
            abort(422, 'Category is required to update request type.');
        }

        $updates = [
            'category_id' => (int) $categoryId,
            'name' => $validated['name_en'],
            'name_ar' => $validated['name_ar'],
            'name_en' => $validated['name_en'],
        ];

        if (array_key_exists('status', $validated)) {
            $updates['status'] = (bool) $validated['status'];
        }

        if (array_key_exists('icon_id', $validated)) {
            $updates['icon_id'] = $validated['icon_id'];
        }

        $requestSubcategory->update($updates);
        $requestSubcategory->refresh();

        $icons = $this->resolveIcons(collect([$requestSubcategory]));
        $payload = $this->typePayload($requestSubcategory, $icons);
        $payload['fee_type'] = $validated['fee_type'];

        return response()->json([
            'data' => $payload,
            'message' => __('Type updated.'),
        ]);
    }

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

    public function destroyType(Request $request, RequestSubcategory $requestSubcategory): JsonResponse|RedirectResponse
    {
        $typeId = $requestSubcategory->id;

        $requestSubcategory->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $typeId,
                ],
                'message' => __('Type deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Type deleted.')]);

        return to_route('app-settings.request-categories.index');
    }

    /**
     * @param  Collection<int, RequestSubcategory>  $subcategories
     * @return Collection<int, Media>
     */
    private function resolveIcons(Collection $subcategories): Collection
    {
        $iconIds = $subcategories
            ->pluck('icon_id')
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
    private function subcategoryListPayload(RequestSubcategory $subcategory, Collection $icons): array
    {
        return [
            'id' => $subcategory->id,
            'name_ar' => $subcategory->name_ar,
            'name_en' => $subcategory->name_en,
            'name' => $subcategory->name,
            'start' => $subcategory->start,
            'end' => $subcategory->end,
            'is_all_day' => $this->boolToStringOrNull($subcategory->is_all_day),
            'working_days' => null,
            'status' => $subcategory->status ? '1' : '0',
            'requests_count' => (int) ($subcategory->requests_count ?? 0),
            'types_count' => 0,
            'request' => [],
            'icon' => $this->iconPayload($icons->get($subcategory->icon_id)),
            'terms_and_conditions' => $subcategory->terms_and_conditions,
        ];
    }

    /**
     * @param  Collection<int, Media>  $icons
     * @return array<string, mixed>
     */
    private function subcategoryDetailsPayload(RequestSubcategory $subcategory, Collection $icons): array
    {
        return [
            'id' => $subcategory->id,
            'name_ar' => $subcategory->name_ar,
            'name_en' => $subcategory->name_en,
            'name' => $subcategory->name,
            'start' => $subcategory->start,
            'end' => $subcategory->end,
            'is_all_day' => $this->boolToStringOrNull($subcategory->is_all_day),
            'working_days' => $subcategory->workingDays->map(fn ($workingDay): array => [
                'id' => $workingDay->id,
                'day' => $workingDay->day,
                'start' => $workingDay->start,
                'end' => $workingDay->end,
                'is_active' => $workingDay->is_active ? '1' : '0',
            ])->values()->all(),
            'status' => $subcategory->status ? '1' : '0',
            'requests_count' => (int) ($subcategory->requests_count ?? 0),
            'request' => [],
            'selects' => [
                'buildings' => [
                    'data' => $subcategory->buildings->map(fn (Building $building): array => [
                        'id' => $building->id,
                        'name' => $building->name,
                        'name_ar' => $building->name,
                        'name_en' => $building->name,
                    ])->values()->all(),
                    'count' => $subcategory->buildings->count(),
                ],
                'communities' => [
                    'data' => $subcategory->communities->map(fn (Community $community): array => [
                        'id' => $community->id,
                        'name' => $community->name,
                        'name_ar' => $community->name,
                        'name_en' => $community->name,
                    ])->values()->all(),
                    'count' => $subcategory->communities->count(),
                ],
            ],
            'featured' => $subcategory->featuredServices->map(fn ($featuredService): array => [
                'id' => $featuredService->id,
                'title' => $featuredService->title,
                'title_ar' => $featuredService->title_ar,
                'title_en' => $featuredService->title_en,
                'description' => $featuredService->description,
                'is_active' => $featuredService->is_active ? '1' : '0',
            ])->values()->all(),
            'icon' => $this->iconPayload($icons->get($subcategory->icon_id)),
            'terms_and_conditions' => $subcategory->terms_and_conditions,
        ];
    }

    /**
     * @param  Collection<int, Media>  $icons
     * @return array<string, mixed>
     */
    private function typePayload(RequestSubcategory $subcategory, Collection $icons): array
    {
        return [
            'id' => $subcategory->id,
            'name_ar' => $subcategory->name_ar,
            'name_en' => $subcategory->name_en,
            'name' => $subcategory->name,
            'status' => $subcategory->status ? '1' : '0',
            'rf_sub_category_id' => $subcategory->id,
            'icon' => $this->iconPayload($icons->get($subcategory->icon_id)),
            'fee_type' => null,
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

    private function boolToStringOrNull(?bool $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return $value ? '1' : '0';
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
