<?php

namespace App\Http\Controllers\Facilities;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Facility;
use App\Models\FacilityCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class FacilityController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $this->authorize('viewAny', Facility::class);

        $query = Facility::query()
            ->with(['category', 'community'])
            ->latest();

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $facilities = $query->paginate($this->perPage($request));

            return response()->json([
                'data' => collect($facilities->items())->map(fn (Facility $facility): array => [
                    'id' => $facility->id,
                    'name' => $facility->name,
                    'name_ar' => $facility->name_ar,
                    'name_en' => $facility->name_en,
                    'description' => $facility->description,
                    'capacity' => $facility->capacity,
                    'open_time' => $facility->open_time,
                    'close_time' => $facility->close_time,
                    'booking_fee' => $facility->booking_fee,
                    'is_active' => $facility->is_active ? '1' : '0',
                    'requires_approval' => $facility->requires_approval ? '1' : '0',
                    'category' => $facility->category
                        ? [
                            'id' => $facility->category->id,
                            'name' => $facility->category->name,
                            'name_ar' => $facility->category->name_ar,
                            'name_en' => $facility->category->name_en,
                        ]
                        : null,
                    'community' => $facility->community
                        ? [
                            'id' => $facility->community->id,
                            'name' => $facility->community->name,
                        ]
                        : null,
                    'created_at' => $facility->created_at?->toJSON(),
                    'updated_at' => $facility->updated_at?->toJSON(),
                ]),
                'meta' => $this->meta($facilities),
            ]);
        }

        $facilities = $query->paginate(15);

        return Inertia::render('facilities/Index', [
            'facilities' => $facilities,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Facility::class);

        return Inertia::render('facilities/Create', [
            'categories' => FacilityCategory::query()
                ->select('id', 'name', 'name_ar', 'name_en')
                ->orderByRaw('COALESCE(name_en, name) asc')
                ->get(),
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Facility::class);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $validated = $request->validate([
                'name_ar' => ['required', 'string', 'max:255'],
                'name_en' => ['required', 'string', 'max:255'],
                'days' => ['required'],
                'booking_type' => ['required', 'string', 'max:255'],
                'complex_id' => ['required', 'integer', 'exists:rf_communities,id'],
                'gender' => ['required', 'string', 'max:255'],
                'approved' => ['required'],
                'category_id' => ['required', 'integer', 'exists:rf_facility_categories,id'],
                'capacity' => ['nullable', 'integer', 'min:1'],
            ]);

            $approved = filter_var($validated['approved'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($approved === null) {
                $approved = in_array((string) $validated['approved'], ['1', 'yes', 'on'], true);
            }

            $facility = Facility::create([
                'name' => $validated['name_en'],
                'name_ar' => $validated['name_ar'],
                'name_en' => $validated['name_en'],
                'description' => 'days: '.json_encode($validated['days']).' | booking_type: '.$validated['booking_type'].' | gender: '.$validated['gender'],
                'category_id' => $validated['category_id'],
                'community_id' => $validated['complex_id'],
                'capacity' => $validated['capacity'] ?? null,
                'requires_approval' => $approved,
                'is_active' => true,
            ]);

            return response()->json([
                'data' => [
                    'id' => $facility->id,
                    'name' => $facility->name,
                    'name_ar' => $facility->name_ar,
                    'name_en' => $facility->name_en,
                    'community_id' => $facility->community_id,
                    'category_id' => $facility->category_id,
                    'requires_approval' => $facility->requires_approval ? '1' : '0',
                ],
                'message' => __('Facility created.'),
            ]);
        }

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
        $this->authorize('view', $facility);

        $facility->load(['category', 'community', 'bookings']);

        return Inertia::render('facilities/Show', [
            'facility' => $facility,
        ]);
    }

    public function edit(Facility $facility): Response
    {
        $this->authorize('update', $facility);

        return Inertia::render('facilities/Edit', [
            'facility' => $facility,
            'categories' => FacilityCategory::query()
                ->select('id', 'name', 'name_ar', 'name_en')
                ->orderByRaw('COALESCE(name_en, name) asc')
                ->get(),
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Facility $facility): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $facility);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $validated = $request->validate([
                'name_ar' => ['required', 'string', 'max:255'],
                'name_en' => ['required', 'string', 'max:255'],
                'days' => ['required'],
                'booking_type' => ['required', 'string', 'max:255'],
                'complex_id' => ['required', 'integer', 'exists:rf_communities,id'],
                'gender' => ['required', 'string', 'max:255'],
                'approved' => ['required'],
                'category_id' => ['required', 'integer', 'exists:rf_facility_categories,id'],
                'capacity' => ['nullable', 'integer', 'min:1'],
            ]);

            $approved = filter_var($validated['approved'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($approved === null) {
                $approved = in_array((string) $validated['approved'], ['1', 'yes', 'on'], true);
            }

            $facility->update([
                'name' => $validated['name_en'],
                'name_ar' => $validated['name_ar'],
                'name_en' => $validated['name_en'],
                'description' => 'days: '.json_encode($validated['days']).' | booking_type: '.$validated['booking_type'].' | gender: '.$validated['gender'],
                'category_id' => $validated['category_id'],
                'community_id' => $validated['complex_id'],
                'capacity' => $validated['capacity'] ?? null,
                'requires_approval' => $approved,
            ]);

            return response()->json([
                'data' => [
                    'id' => $facility->id,
                    'name' => $facility->name,
                    'name_ar' => $facility->name_ar,
                    'name_en' => $facility->name_en,
                    'community_id' => $facility->community_id,
                    'category_id' => $facility->category_id,
                    'requires_approval' => $facility->requires_approval ? '1' : '0',
                ],
                'message' => __('Facility updated.'),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:rf_facility_categories,id'],
            'community_id' => ['required', 'integer', 'exists:rf_communities,id'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $facility->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Facility updated.')]);

        return to_route('facilities.show', $facility);
    }

    public function destroy(Request $request, Facility $facility): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $facility);

        $facilityId = $facility->id;
        $facility->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $facilityId,
                ],
                'message' => __('Facility deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Facility deleted.')]);

        return to_route('facilities.index');
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->integer('per_page', 10), 1), 50);
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
