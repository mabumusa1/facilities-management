<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\AddInternalNoteRequest;
use App\Http\Requests\Services\AssignServiceRequestRequest;
use App\Models\Community;
use App\Models\Request as ServiceRequest;
use App\Models\ServiceCategory;
use App\Models\ServiceRequestMessage;
use App\Models\Status;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminServiceRequestController extends Controller
{
    /**
     * Reserved rf_statuses.id for the "Assigned" service request status.
     *
     * ID is the reserved primary key seeded by StatusSeeder (id=2, type=request).
     */
    public const STATUS_ASSIGNED = 2;

    /**
     * Display the admin triage dashboard of all service requests.
     *
     * Filterable by status, category, community, urgency, and a search term.
     * Tabs for All / Unassigned / Overdue / SLA Breach counts are returned.
     */
    public function index(Request $request): Response
    {
        $this->authorize('triage', ServiceRequest::class);

        $search = trim((string) $request->input('search', ''));
        $statusId = $request->input('status_id');
        $categoryId = $request->input('service_category_id');
        $communityId = $request->input('community_id');
        $urgency = $request->input('urgency');
        $tab = $request->input('tab', 'all');

        $baseQuery = ServiceRequest::query()
            ->with([
                'serviceCategory:id,name_en,name_ar',
                'serviceSubcategory:id,name_en,name_ar',
                'status:id,name,name_en,name_ar',
                'unit:id,name',
                'community:id,name',
                'assignedTo:id,name',
                'requester',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('request_code', 'like', "%{$search}%")
                        ->orWhereHas('requester', function ($rq) use ($search): void {
                            $rq->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($statusId, fn ($q) => $q->where('status_id', (int) $statusId))
            ->when($categoryId, fn ($q) => $q->where('service_category_id', (int) $categoryId))
            ->when($communityId, fn ($q) => $q->where('community_id', (int) $communityId))
            ->when($urgency, fn ($q) => $q->where('urgency', $urgency))
            ->when($tab === 'unassigned', fn ($q) => $q->whereNull('assigned_to_user_id'))
            ->when($tab === 'overdue', fn ($q) => $q->whereNull('assigned_to_user_id')
                ->where('sla_response_due_at', '<', now()))
            ->when($tab === 'sla_breach', fn ($q) => $q->where('sla_resolution_due_at', '<', now()))
            ->latest('created_at');

        $serviceRequests = $baseQuery->paginate(15)->withQueryString();

        // Tab counts (tenant-scoped via global scope; independent queries for accuracy).
        $countsBase = ServiceRequest::query();
        $tabCounts = [
            'all' => $countsBase->count(),
            'unassigned' => (clone $countsBase)->whereNull('assigned_to_user_id')->count(),
            'overdue' => (clone $countsBase)->whereNull('assigned_to_user_id')->where('sla_response_due_at', '<', now())->count(),
            'sla_breach' => (clone $countsBase)->where('sla_resolution_due_at', '<', now())->count(),
        ];

        return Inertia::render('services/requests/admin/Index', [
            'serviceRequests' => $serviceRequests->through(fn (ServiceRequest $sr) => $this->formatListItem($sr)),
            'tabCounts' => $tabCounts,
            'statuses' => Status::query()
                ->where('type', 'request')
                ->select('id', 'name', 'name_en', 'name_ar')
                ->orderBy('priority')
                ->get(),
            'serviceCategories' => ServiceCategory::query()
                ->where('status', 'active')
                ->select('id', 'name_en', 'name_ar')
                ->orderByRaw('COALESCE(name_en, name_ar) asc')
                ->get(),
            'communities' => Community::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
            'urgencies' => ['normal', 'urgent'],
            'filters' => [
                'search' => $search,
                'status_id' => $statusId ? (string) $statusId : '',
                'service_category_id' => $categoryId ? (string) $categoryId : '',
                'community_id' => $communityId ? (string) $communityId : '',
                'urgency' => $urgency ? (string) $urgency : '',
                'tab' => $tab,
            ],
        ]);
    }

    /**
     * Display a single service request for triage (assign + internal notes).
     */
    public function show(ServiceRequest $serviceRequest): Response
    {
        $this->authorize('view', $serviceRequest);

        $serviceRequest->load([
            'serviceCategory:id,name_en,name_ar',
            'serviceSubcategory:id,name_en,name_ar',
            'status:id,name,name_en,name_ar',
            'unit:id,name',
            'community:id,name',
            'assignedTo:id,name',
            'requester',
        ]);

        $internalNotes = ServiceRequestMessage::query()
            ->where('service_request_id', $serviceRequest->id)
            ->where('is_internal', true)
            ->with('sender:id,name')
            ->orderBy('created_at')
            ->get()
            ->map(fn (ServiceRequestMessage $m) => [
                'id' => $m->id,
                'body' => $m->body,
                'sender_name' => $m->sender?->name,
                'created_at' => $m->created_at?->toISOString(),
            ]);

        // Users eligible for assignment: scoped to current tenant via membership.
        $assignees = User::query()
            ->whereHas('memberships', fn ($q) => $q->whereColumn('account_tenant_id', 'account_tenant_id'))
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $requester = $serviceRequest->requester;

        return Inertia::render('services/requests/admin/Show', [
            'serviceRequest' => [
                'id' => $serviceRequest->id,
                'request_code' => $serviceRequest->request_code,
                'urgency' => $serviceRequest->urgency,
                'priority' => $serviceRequest->priority,
                'room_location' => $serviceRequest->room_location,
                'description' => $serviceRequest->description,
                'sla_response_due_at' => $serviceRequest->sla_response_due_at?->toISOString(),
                'sla_resolution_due_at' => $serviceRequest->sla_resolution_due_at?->toISOString(),
                'assigned_to_user_id' => $serviceRequest->assigned_to_user_id,
                'assigned_at' => $serviceRequest->assigned_at?->toISOString(),
                'created_at' => $serviceRequest->created_at?->toISOString(),
                'category' => $serviceRequest->serviceCategory
                    ? ['id' => $serviceRequest->serviceCategory->id, 'name_en' => $serviceRequest->serviceCategory->name_en, 'name_ar' => $serviceRequest->serviceCategory->name_ar]
                    : null,
                'subcategory' => $serviceRequest->serviceSubcategory
                    ? ['id' => $serviceRequest->serviceSubcategory->id, 'name_en' => $serviceRequest->serviceSubcategory->name_en, 'name_ar' => $serviceRequest->serviceSubcategory->name_ar]
                    : null,
                'status' => $serviceRequest->status
                    ? ['id' => $serviceRequest->status->id, 'name' => $serviceRequest->status->name, 'name_en' => $serviceRequest->status->name_en, 'name_ar' => $serviceRequest->status->name_ar]
                    : null,
                'unit' => $serviceRequest->unit
                    ? ['id' => $serviceRequest->unit->id, 'name' => $serviceRequest->unit->name]
                    : null,
                'community' => $serviceRequest->community
                    ? ['id' => $serviceRequest->community->id, 'name' => $serviceRequest->community->name]
                    : null,
                'assigned_to' => $serviceRequest->assignedTo
                    ? ['id' => $serviceRequest->assignedTo->id, 'name' => $serviceRequest->assignedTo->name]
                    : null,
                'requester_name' => $requester?->name,
                'requester_phone' => method_exists($requester, 'phone') ? $requester->phone : null,
            ],
            'internalNotes' => $internalNotes,
            'assignees' => $assignees,
            'priorityOptions' => ['low', 'medium', 'high', 'urgent'],
        ]);
    }

    /**
     * Assign the service request to a user (technician/manager) and optionally update priority.
     */
    public function assign(AssignServiceRequestRequest $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorize('assign', $serviceRequest);

        $validated = $request->validated();

        $serviceRequest->update([
            'assigned_to_user_id' => $validated['assigned_to_user_id'],
            'priority' => $validated['priority'] ?? $serviceRequest->priority,
            'status_id' => self::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);

        return back()->with('success', __('Request assigned successfully.'));
    }

    /**
     * Add an internal note to the service request (not visible to the resident).
     */
    public function addNote(AddInternalNoteRequest $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $this->authorize('addInternalNote', $serviceRequest);

        $validated = $request->validated();

        ServiceRequestMessage::create([
            'service_request_id' => $serviceRequest->id,
            'sender_type' => $request->user()::class,
            'sender_id' => $request->user()->id,
            'body' => $validated['body'],
            'is_internal' => true,
        ]);

        return back()->with('success', __('Note added successfully.'));
    }

    /**
     * Format a service request for the list view.
     *
     * @return array<string, mixed>
     */
    private function formatListItem(ServiceRequest $sr): array
    {
        $isOverdue = $sr->sla_response_due_at !== null
            && $sr->sla_response_due_at->isPast()
            && $sr->assigned_to_user_id === null;

        $isNearSla = ! $isOverdue
            && $sr->sla_response_due_at !== null
            && $sr->sla_response_due_at->isFuture()
            && $sr->sla_response_due_at->diffInMinutes(now()) <= 120;

        return [
            'id' => $sr->id,
            'request_code' => $sr->request_code,
            'urgency' => $sr->urgency,
            'priority' => $sr->priority,
            'room_location' => $sr->room_location,
            'description' => $sr->description,
            'sla_response_due_at' => $sr->sla_response_due_at?->toISOString(),
            'sla_resolution_due_at' => $sr->sla_resolution_due_at?->toISOString(),
            'assigned_to_user_id' => $sr->assigned_to_user_id,
            'assigned_at' => $sr->assigned_at?->toISOString(),
            'created_at' => $sr->created_at?->toISOString(),
            'is_overdue' => $isOverdue,
            'is_near_sla' => $isNearSla,
            'category' => $sr->serviceCategory
                ? ['id' => $sr->serviceCategory->id, 'name_en' => $sr->serviceCategory->name_en, 'name_ar' => $sr->serviceCategory->name_ar]
                : null,
            'subcategory' => $sr->serviceSubcategory
                ? ['id' => $sr->serviceSubcategory->id, 'name_en' => $sr->serviceSubcategory->name_en, 'name_ar' => $sr->serviceSubcategory->name_ar]
                : null,
            'status' => $sr->status
                ? ['id' => $sr->status->id, 'name' => $sr->status->name, 'name_en' => $sr->status->name_en, 'name_ar' => $sr->status->name_ar]
                : null,
            'unit' => $sr->unit
                ? ['id' => $sr->unit->id, 'name' => $sr->unit->name]
                : null,
            'community' => $sr->community
                ? ['id' => $sr->community->id, 'name' => $sr->community->name]
                : null,
            'assigned_to' => $sr->assignedTo
                ? ['id' => $sr->assignedTo->id, 'name' => $sr->assignedTo->name]
                : null,
            'requester_name' => $sr->requester?->name,
        ];
    }
}
