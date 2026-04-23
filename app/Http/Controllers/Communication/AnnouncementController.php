<?php

namespace App\Http\Controllers\Communication;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $this->authorize('viewAny', Announcement::class);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $announcements = Announcement::query()
                ->with(['community:id,name', 'building:id,name'])
                ->latest()
                ->paginate($this->perPage($request));

            return response()->json([
                'data' => collect($announcements->items())->map(fn (Announcement $announcement): array => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'status' => $announcement->status ? '1' : '0',
                    'published_at' => $announcement->published_at?->toJSON(),
                    'community' => $announcement->community
                        ? [
                            'id' => $announcement->community->id,
                            'name' => $announcement->community->name,
                        ]
                        : null,
                    'building' => $announcement->building
                        ? [
                            'id' => $announcement->building->id,
                            'name' => $announcement->building->name,
                        ]
                        : null,
                ]),
                'meta' => $this->meta($announcements),
            ]);
        }

        $announcements = Announcement::query()
            ->with(['community', 'building'])
            ->latest()
            ->paginate(15);

        return Inertia::render('communication/announcements/Index', [
            'announcements' => $announcements,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Announcement::class);

        return Inertia::render('communication/announcements/Create', [
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
            'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Announcement::class);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'is_visible' => ['required'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'start_time' => ['required', 'string'],
                'end_time' => ['required', 'string'],
                'notify_user_type' => ['required', 'string'],
                'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
                'building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            ]);

            $isVisible = filter_var($validated['is_visible'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($isVisible === null) {
                $isVisible = in_array((string) $validated['is_visible'], ['1', 'yes', 'on'], true);
            }

            $publishedAt = Carbon::parse($validated['start_date'].' '.$validated['start_time']);

            $announcement = Announcement::create([
                'title' => $validated['title'],
                'content' => $validated['description'],
                'status' => $isVisible,
                'published_at' => $publishedAt,
                'community_id' => $validated['community_id'] ?? null,
                'building_id' => $validated['building_id'] ?? null,
            ]);

            return response()->json([
                'data' => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'status' => $announcement->status ? '1' : '0',
                    'published_at' => $announcement->published_at?->toJSON(),
                ],
                'message' => __('Announcement created.'),
            ]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        $announcement = Announcement::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement created.')]);

        return to_route('announcements.show', $announcement);
    }

    public function show(Announcement $announcement): Response
    {
        $this->authorize('view', $announcement);

        $announcement->load(['community', 'building']);

        return Inertia::render('communication/announcements/Show', [
            'announcement' => $announcement,
        ]);
    }

    public function edit(Announcement $announcement): Response
    {
        return Inertia::render('communication/announcements/Edit', [
            'announcement' => $announcement,
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
            'buildings' => Building::select('id', 'name', 'rf_community_id')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Announcement $announcement): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'is_visible' => ['required'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'start_time' => ['required', 'string'],
                'end_time' => ['required', 'string'],
                'notify_user_type' => ['required', 'string'],
                'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
                'building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            ]);

            $isVisible = filter_var($validated['is_visible'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

            if ($isVisible === null) {
                $isVisible = in_array((string) $validated['is_visible'], ['1', 'yes', 'on'], true);
            }

            $publishedAt = Carbon::parse($validated['start_date'].' '.$validated['start_time']);

            $announcement->update([
                'title' => $validated['title'],
                'content' => $validated['description'],
                'status' => $isVisible,
                'published_at' => $publishedAt,
                'community_id' => $validated['community_id'] ?? null,
                'building_id' => $validated['building_id'] ?? null,
            ]);

            $announcement->load(['community:id,name', 'building:id,name']);

            return response()->json([
                'data' => [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => $announcement->content,
                    'status' => $announcement->status ? '1' : '0',
                    'published_at' => $announcement->published_at?->toJSON(),
                    'community' => $announcement->community
                        ? [
                            'id' => $announcement->community->id,
                            'name' => $announcement->community->name,
                        ]
                        : null,
                    'building' => $announcement->building
                        ? [
                            'id' => $announcement->building->id,
                            'name' => $announcement->building->name,
                        ]
                        : null,
                ],
                'message' => __('Announcement updated.'),
            ]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'building_id' => ['nullable', 'integer', 'exists:rf_buildings,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        $announcement->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement updated.')]);

        return to_route('announcements.show', $announcement);
    }

    public function destroy(Request $request, Announcement $announcement): JsonResponse|RedirectResponse
    {
        $announcementId = $announcement->id;
        $announcement->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $announcementId,
                ],
                'message' => __('Announcement deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Announcement deleted.')]);

        return to_route('announcements.index');
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
