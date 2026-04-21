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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ServiceRequestController extends Controller
{
    public function index(Request $request): Response
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

        return Inertia::render('requests/Index', [
            'requests' => $requests,
            'statuses' => Status::query()
                ->where('type', 'request')
                ->select('id', 'name', 'name_en')
                ->orderBy('priority')
                ->orderBy('id')
                ->get(),
            'categories' => RequestCategory::query()
                ->select('id', 'name', 'name_en')
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
            'categories' => RequestCategory::with('subcategories')->select('id', 'name', 'name_en')->get(),
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
            'statuses' => Status::where('type', 'request')->select('id', 'name', 'name_en')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:rf_request_categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:rf_request_subcategories,id'],
            'unit_id' => ['nullable', 'integer', 'exists:rf_units,id'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
        ]);

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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Request created.')]);

        return to_route('requests.show', $serviceRequest);
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
            'categories' => RequestCategory::with('subcategories')->select('id', 'name', 'name_en')->get(),
            'communities' => Community::select('id', 'name')->orderBy('name')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
            'statuses' => Status::where('type', 'request')->select('id', 'name', 'name_en')->get(),
        ]);
    }

    public function update(
        Request $request,
        ServiceRequest $serviceRequest,
        StatusWorkflow $statusWorkflow,
        WorkflowNotifier $workflowNotifier,
    ): RedirectResponse {
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
                url: route('requests.show', $serviceRequest, false),
                actor: $request->user()?->name,
            );
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Request updated.')]);

        return to_route('requests.show', $serviceRequest);
    }

    public function destroy(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Request deleted.')]);

        return to_route('requests.index');
    }
}
