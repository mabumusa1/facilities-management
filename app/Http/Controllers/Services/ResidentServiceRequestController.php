<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\Services\StoreResidentServiceRequestRequest;
use App\Models\Community;
use App\Models\Request as ServiceRequest;
use App\Models\ServiceCategory;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ResidentServiceRequestController extends Controller
{
    /**
     * Show the resident's service request creation form.
     *
     * Loads active service categories (scoped to current tenant) with their
     * subcategories and the community list for the location picker.
     */
    public function create(): Response
    {
        $this->authorize('createOwn', ServiceRequest::class);

        $categories = ServiceCategory::query()
            ->where('status', 'active')
            ->with(['subcategories' => fn ($q) => $q->where('status', 'active')
                ->select('id', 'service_category_id', 'name_en', 'name_ar', 'response_sla_hours', 'resolution_sla_hours'),
            ])
            ->select('id', 'name_en', 'name_ar', 'icon', 'response_sla_hours', 'resolution_sla_hours')
            ->orderByRaw('COALESCE(name_en, name_ar) asc')
            ->get();

        $communities = Community::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $units = Unit::query()
            ->select('id', 'name', 'rf_community_id')
            ->orderBy('name')
            ->get();

        return Inertia::render('services/requests/Create', [
            'categories' => $categories,
            'communities' => $communities,
            'units' => $units,
            'roomOptions' => $this->roomOptions(),
        ]);
    }

    /**
     * Store a new service request submitted by the resident.
     */
    public function store(StoreResidentServiceRequestRequest $request): RedirectResponse
    {
        $this->authorize('createOwn', ServiceRequest::class);

        $validated = $request->validated();

        $serviceCategory = ServiceCategory::query()->find((int) $validated['service_category_id']);
        $serviceSubcategoryId = isset($validated['service_subcategory_id']) ? (int) $validated['service_subcategory_id'] : null;
        $serviceSubcategory = $serviceSubcategoryId
            ? $serviceCategory?->subcategories()->find($serviceSubcategoryId)
            : null;

        $responseHours = $serviceSubcategory?->resolvedResponseSlaHours() ?? $serviceCategory?->response_sla_hours;
        $resolutionHours = $serviceSubcategory?->resolvedResolutionSlaHours() ?? $serviceCategory?->resolution_sla_hours;

        $openStatusId = Status::query()
            ->where('type', 'request')
            ->where(function ($q): void {
                $q->whereRaw("LOWER(COALESCE(name_en, name)) = 'open'")
                    ->orWhereRaw("LOWER(COALESCE(name_en, name)) = 'new'");
            })
            ->orderBy('priority')
            ->value('id')
            ?? Status::query()
                ->where('type', 'request')
                ->orderBy('priority')
                ->orderBy('id')
                ->value('id');

        if ($openStatusId === null) {
            abort(422, 'Service request status is not configured.');
        }

        $serviceRequest = ServiceRequest::create([
            'requester_type' => $request->user()::class,
            'requester_id' => $request->user()->id,
            'service_category_id' => $validated['service_category_id'],
            'service_subcategory_id' => $validated['service_subcategory_id'] ?? null,
            'unit_id' => $validated['unit_id'],
            'community_id' => $validated['community_id'],
            'room_location' => $validated['room_location'] ?? null,
            'urgency' => $validated['urgency'],
            'description' => $validated['description'],
            'title' => Str::limit($validated['description'], 255),
            'status_id' => $openStatusId,
            'priority' => $validated['urgency'] === 'urgent' ? 'urgent' : 'medium',
            'sla_response_due_at' => $responseHours !== null ? now()->addHours($responseHours) : null,
            'sla_resolution_due_at' => $resolutionHours !== null ? now()->addHours($resolutionHours) : null,
        ]);

        return to_route('service-requests.created', ['serviceRequest' => $serviceRequest->id]);
    }

    /**
     * Show the submission confirmation page with reference number and SLA times.
     */
    public function created(ServiceRequest $serviceRequest): Response
    {
        $this->authorize('viewOwn', $serviceRequest);

        $serviceRequest->load(['serviceCategory', 'serviceSubcategory', 'status', 'unit', 'community']);

        return Inertia::render('services/requests/Created', [
            'serviceRequest' => [
                'id' => $serviceRequest->id,
                'request_code' => $serviceRequest->request_code,
                'urgency' => $serviceRequest->urgency,
                'room_location' => $serviceRequest->room_location,
                'description' => $serviceRequest->description,
                'sla_response_due_at' => $serviceRequest->sla_response_due_at?->toISOString(),
                'sla_resolution_due_at' => $serviceRequest->sla_resolution_due_at?->toISOString(),
                'category' => $serviceRequest->serviceCategory
                    ? ['id' => $serviceRequest->serviceCategory->id, 'name_en' => $serviceRequest->serviceCategory->name_en, 'name_ar' => $serviceRequest->serviceCategory->name_ar]
                    : null,
                'subcategory' => $serviceRequest->serviceSubcategory
                    ? ['id' => $serviceRequest->serviceSubcategory->id, 'name_en' => $serviceRequest->serviceSubcategory->name_en, 'name_ar' => $serviceRequest->serviceSubcategory->name_ar]
                    : null,
            ],
        ]);
    }

    /**
     * Show the resident's own service request history.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAnyOwn', ServiceRequest::class);

        $myRequests = ServiceRequest::query()
            ->where('requester_type', $request->user()::class)
            ->where('requester_id', $request->user()->id)
            ->with(['serviceCategory', 'serviceSubcategory', 'status'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('services/requests/Index', [
            'serviceRequests' => $myRequests->through(fn (ServiceRequest $sr) => [
                'id' => $sr->id,
                'request_code' => $sr->request_code,
                'urgency' => $sr->urgency,
                'room_location' => $sr->room_location,
                'description' => $sr->description,
                'sla_response_due_at' => $sr->sla_response_due_at?->toISOString(),
                'sla_resolution_due_at' => $sr->sla_resolution_due_at?->toISOString(),
                'created_at' => $sr->created_at?->toISOString(),
                'category' => $sr->serviceCategory
                    ? ['id' => $sr->serviceCategory->id, 'name_en' => $sr->serviceCategory->name_en, 'name_ar' => $sr->serviceCategory->name_ar]
                    : null,
                'subcategory' => $sr->serviceSubcategory
                    ? ['id' => $sr->serviceSubcategory->id, 'name_en' => $sr->serviceSubcategory->name_en, 'name_ar' => $sr->serviceSubcategory->name_ar]
                    : null,
                'status' => $sr->status
                    ? ['id' => $sr->status->id, 'name_en' => $sr->status->name_en, 'name_ar' => $sr->status->name_ar, 'name' => $sr->status->name]
                    : null,
            ]),
        ]);
    }

    /**
     * Pre-defined room / location options shown in the picker.
     *
     * @return string[]
     */
    private function roomOptions(): array
    {
        return [
            'kitchen',
            'bathroom',
            'living_room',
            'bedroom',
            'balcony',
            'other',
        ];
    }
}
