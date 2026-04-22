<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Request as ServiceRequest;
use App\Models\RequestCategory;
use App\Models\Status;
use App\Models\Unit;
use App\Support\StatusWorkflow;
use App\Support\WorkflowNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ServiceRequestController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $search = trim((string) $request->input('search', ''));
        $statusId = $request->input('status_id');
        $categoryId = $request->input('category_id');
        $priority = $request->input('priority');
        $perPage = min(max((int) $request->integer('per_page', 15), 5), 50);

        $requests = ServiceRequest::query()
            ->with(['category', 'subcategory', 'status', 'unit', 'community'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nestedQuery) use ($search): void {
                    $nestedQuery->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('request_code', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $nestedQuery->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($statusId, fn ($query) => $query->where('status_id', (int) $statusId))
            ->when($categoryId, fn ($query) => $query->where('category_id', (int) $categoryId))
            ->when($priority, fn ($query) => $query->where('priority', (string) $priority))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => collect($requests->items())->map(
                    fn (ServiceRequest $serviceRequest): array => $this->requestListItem($serviceRequest)
                ),
                'meta' => $this->meta($requests),
            ]);
        }

        return Inertia::render('requests/Index', [
            'requests' => $requests,
            'statuses' => Status::query()
                ->where('type', 'request')
                ->select('id', 'name', 'name_ar', 'name_en')
                ->orderBy('priority')
                ->orderBy('id')
                ->get(),
            'categories' => RequestCategory::query()
                ->select('id', 'name', 'name_ar', 'name_en')
                ->orderByRaw('COALESCE(name_en, name) asc')
                ->get(),
            'priorities' => ['low', 'medium', 'high', 'urgent'],
            'filters' => [
                'search' => $search,
                'status_id' => $statusId ? (string) $statusId : '',
                'category_id' => $categoryId ? (string) $categoryId : '',
                'priority' => $priority ? (string) $priority : '',
                'per_page' => (string) $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('requests/Create', [
            'categories' => RequestCategory::query()
                ->with(['subcategories:id,category_id,name,name_ar,name_en'])
                ->select('id', 'name', 'name_ar', 'name_en')
                ->get(),
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
            'statuses' => Status::where('type', 'request')->select('id', 'name', 'name_ar', 'name_en')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:rf_request_categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:rf_request_subcategories,id'],
            'unit_id' => ['nullable', 'integer', 'exists:rf_units,id'],
            'rf_unit_id' => ['nullable', 'integer', 'exists:rf_units,id'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
        ]);

        if (! array_key_exists('unit_id', $validated) && array_key_exists('rf_unit_id', $validated)) {
            $validated['unit_id'] = $validated['rf_unit_id'];
        }

        unset($validated['rf_unit_id']);

        $validated['requester_id'] = $request->user()->id;
        $validated['requester_type'] = get_class($request->user());
        $validated['status_id'] = Status::query()
            ->where('type', 'request')
            ->where(function ($query): void {
                $query->where('name_en', 'New')
                    ->orWhere('name', 'New')
                    ->orWhere('name_en', 'Open')
                    ->orWhere('name', 'Open');
            })
            ->value('id')
            ?? Status::query()
                ->where('type', 'request')
                ->orderBy('priority')
                ->orderBy('id')
                ->value('id')
            ?? Status::query()->value('id');

        if ($validated['status_id'] === null) {
            abort(422, 'Request status is not configured.');
        }

        $validated['title'] = $validated['title'] ?? Str::limit(
            (string) ($validated['description'] ?: 'Service request '.now()->format('YmdHis')),
            255,
        );

        $serviceRequest = ServiceRequest::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $serviceRequest->load(['category', 'subcategory', 'status', 'unit', 'community']);

            return response()->json([
                'data' => $this->requestListItem($serviceRequest),
                'message' => __('Request created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Request created.')]);

        return to_route('requests.show', $serviceRequest);
    }

    public function assign(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'integer', 'exists:rf_professionals,id'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $assignedStatusId = Status::query()
            ->where('type', 'request')
            ->where(function ($query): void {
                $query->where('name_en', 'Assigned')
                    ->orWhere('name', 'Assigned');
            })
            ->value('id');

        $updates = [
            'professional_id' => $validated['professional_id'],
            'assigned_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? $serviceRequest->admin_notes,
        ];

        if ($assignedStatusId !== null) {
            $updates['status_id'] = $assignedStatusId;
        }

        $serviceRequest->update($updates);
        $serviceRequest->load(['category', 'subcategory', 'status', 'unit', 'community']);

        return response()->json([
            'data' => $this->requestListItem($serviceRequest),
            'message' => __('Request assigned.'),
        ]);
    }

    public function reassign(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $validated = $request->validate([
            'professional_id' => ['required', 'integer', 'exists:rf_professionals,id'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $serviceRequest->update([
            'professional_id' => $validated['professional_id'],
            'assigned_at' => now(),
            'admin_notes' => $validated['admin_notes'] ?? $serviceRequest->admin_notes,
        ]);

        $serviceRequest->load(['category', 'subcategory', 'status', 'unit', 'community']);

        return response()->json([
            'data' => $this->requestListItem($serviceRequest),
            'message' => __('Request reassigned.'),
        ]);
    }

    public function changeStatusApproved(Request $request): JsonResponse
    {
        return $this->changeStatus($request, ['approved'], 'approved');
    }

    public function changeStatusCanceled(Request $request): JsonResponse
    {
        return $this->changeStatus($request, ['canceled', 'cancelled'], 'canceled');
    }

    public function changeStatusCompleted(Request $request): JsonResponse
    {
        return $this->changeStatus($request, ['completed'], 'completed');
    }

    public function changeStatusInProgress(Request $request): JsonResponse
    {
        return $this->changeStatus($request, ['in progress', 'in-progress'], 'in-progress');
    }

    public function changeStatusPending(Request $request): JsonResponse
    {
        return $this->changeStatus($request, ['pending'], 'pending');
    }

    public function changeStatusRejected(Request $request): JsonResponse
    {
        return $this->changeStatus($request, ['rejected'], 'rejected');
    }

    public function show(ServiceRequest $serviceRequest): Response
    {
        $serviceRequest->load(['category', 'subcategory', 'status', 'unit', 'community']);

        return Inertia::render('requests/Show', [
            'serviceRequest' => $serviceRequest,
        ]);
    }

    public function edit(ServiceRequest $serviceRequest): Response
    {
        return Inertia::render('requests/Edit', [
            'serviceRequest' => $serviceRequest->load(['category', 'subcategory', 'status', 'unit', 'community']),
            'categories' => RequestCategory::query()
                ->with(['subcategories:id,category_id,name,name_ar,name_en'])
                ->select('id', 'name', 'name_ar', 'name_en')
                ->get(),
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
            'statuses' => Status::where('type', 'request')->select('id', 'name', 'name_ar', 'name_en')->get(),
        ]);
    }

    public function update(
        Request $request,
        ServiceRequest $serviceRequest,
        StatusWorkflow $statusWorkflow,
        WorkflowNotifier $workflowNotifier,
    ): JsonResponse|RedirectResponse {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'status_id' => ['sometimes', 'integer', 'exists:rf_statuses,id'],
            'assignee_id' => ['nullable', 'integer'],
            'assignee_type' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $nextStatusId = array_key_exists('status_id', $validated)
            ? (int) $validated['status_id']
            : null;

        $fromStatus = null;
        $toStatus = null;

        if ($nextStatusId !== null && $nextStatusId !== (int) $serviceRequest->status_id) {
            $fromStatus = Status::query()->find($serviceRequest->status_id);
            $statusWorkflow->ensureTransition('request', $serviceRequest->status_id, $nextStatusId);
            $toStatus = Status::query()->find($nextStatusId);
        }

        $serviceRequest->update($validated);

        if ($toStatus instanceof Status) {
            $workflowNotifier->notifyTenantUsers(
                tenantId: (int) ($request->session()->get('tenant_id') ?: $serviceRequest->account_tenant_id),
                module: 'service-request',
                resourceId: $serviceRequest->id,
                fromStatus: $fromStatus?->name_en ?? $fromStatus?->name,
                toStatus: $toStatus->name_en ?? $toStatus->name ?? (string) $toStatus->id,
                url: $request->routeIs('rf.*')
                    ? route('rf.requests.index', [], false)
                    : route('requests.show', $serviceRequest, false),
                actor: $request->user()?->name,
            );
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $serviceRequest->load(['category', 'subcategory', 'status', 'unit', 'community']);

            return response()->json([
                'data' => $this->requestListItem($serviceRequest),
                'message' => __('Request updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Request updated.')]);

        return to_route('requests.show', $serviceRequest);
    }

    public function destroy(Request $request, ServiceRequest $serviceRequest): JsonResponse|RedirectResponse
    {
        $serviceRequestId = $serviceRequest->id;
        $serviceRequest->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $serviceRequestId,
                ],
                'message' => __('Request deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Request deleted.')]);

        return to_route('requests.index');
    }

    private function changeStatus(Request $request, array $statusNames, string $statusLabel): JsonResponse
    {
        $validated = $request->validate([
            'rf_request_id' => ['nullable', 'integer', 'exists:rf_requests,id', 'required_without_all:request_id,id'],
            'request_id' => ['nullable', 'integer', 'exists:rf_requests,id', 'required_without_all:rf_request_id,id'],
            'id' => ['nullable', 'integer', 'exists:rf_requests,id', 'required_without_all:rf_request_id,request_id'],
            'professional_id' => ['nullable', 'integer', 'exists:rf_professionals,id'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $serviceRequestId = (int) ($validated['rf_request_id'] ?? $validated['request_id'] ?? $validated['id']);
        $serviceRequest = ServiceRequest::query()->findOrFail($serviceRequestId);
        $statusId = $this->resolveRequestStatusId($statusNames);

        if ($statusId === null) {
            abort(422, 'Request status is not configured.');
        }

        $updates = [
            'status_id' => $statusId,
        ];

        if (array_key_exists('professional_id', $validated) && $validated['professional_id'] !== null) {
            $updates['professional_id'] = $validated['professional_id'];
            $updates['assigned_at'] = now();
        }

        if (array_key_exists('admin_notes', $validated)) {
            $updates['admin_notes'] = $validated['admin_notes'];
        }

        $serviceRequest->update($updates);
        $serviceRequest->load(['category', 'subcategory', 'status', 'unit', 'community']);

        return response()->json([
            'data' => $this->requestListItem($serviceRequest),
            'message' => __('Request status updated to :status.', ['status' => $statusLabel]),
        ]);
    }

    private function resolveRequestStatusId(array $statusNames): ?int
    {
        $normalizedNames = collect($statusNames)
            ->map(fn (string $statusName): string => strtolower(trim($statusName)))
            ->filter()
            ->values()
            ->all();

        if ($normalizedNames === []) {
            return null;
        }

        return Status::query()
            ->where('type', 'request')
            ->where(function ($query) use ($normalizedNames): void {
                foreach ($normalizedNames as $index => $statusName) {
                    if ($index === 0) {
                        $query->whereRaw('LOWER(COALESCE(name_en, name)) = ?', [$statusName]);
                    } else {
                        $query->orWhereRaw('LOWER(COALESCE(name_en, name)) = ?', [$statusName]);
                    }
                }
            })
            ->value('id');
    }

    /**
     * @return array<string, mixed>
     */
    private function requestListItem(ServiceRequest $serviceRequest): array
    {
        return [
            'id' => $serviceRequest->id,
            'title' => $serviceRequest->title,
            'description' => $serviceRequest->description,
            'request_code' => $serviceRequest->request_code,
            'priority' => $serviceRequest->priority,
            'category' => $serviceRequest->category
                ? [
                    'id' => $serviceRequest->category->id,
                    'name' => $serviceRequest->category->name,
                    'name_ar' => $serviceRequest->category->name_ar,
                    'name_en' => $serviceRequest->category->name_en,
                ]
                : null,
            'sub_category' => $serviceRequest->subcategory
                ? [
                    'id' => $serviceRequest->subcategory->id,
                    'name' => $serviceRequest->subcategory->name,
                    'name_ar' => $serviceRequest->subcategory->name_ar,
                    'name_en' => $serviceRequest->subcategory->name_en,
                ]
                : null,
            'status' => $serviceRequest->status
                ? [
                    'id' => $serviceRequest->status->id,
                    'name' => $serviceRequest->status->name,
                    'name_ar' => $serviceRequest->status->name_ar,
                    'name_en' => $serviceRequest->status->name_en,
                ]
                : null,
            'unit' => $serviceRequest->unit
                ? [
                    'id' => $serviceRequest->unit->id,
                    'name' => $serviceRequest->unit->name,
                ]
                : null,
            'community' => $serviceRequest->community
                ? [
                    'id' => $serviceRequest->community->id,
                    'name' => $serviceRequest->community->name,
                ]
                : null,
            'created_at' => $serviceRequest->created_at?->toJSON(),
            'updated_at' => $serviceRequest->updated_at?->toJSON(),
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
