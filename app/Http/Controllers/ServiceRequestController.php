<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequestRequest;
use App\Http\Requests\UpdateServiceRequestRequest;
use App\Http\Resources\ServiceRequestResource;
use App\Models\Community;
use App\Models\Contact;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\Status;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceRequestController extends Controller
{
    /**
     * Display a listing of service requests.
     */
    public function index(Request $request): Response
    {
        $status = $request->get('status');
        $category = $request->get('category');
        $priority = $request->get('priority');
        $search = $request->get('search');

        $query = ServiceRequest::query()
            ->with([
                'category',
                'subcategory',
                'status',
                'requester',
                'professional',
                'community',
                'building',
                'unit',
            ]);

        // Apply filters
        if ($status) {
            $query->whereHas('status', fn ($q) => $q->where('slug', $status));
        }

        if ($category) {
            $query->where('category_id', $category);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('request_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $requests = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('service-requests/index', [
            'requests' => ServiceRequestResource::collection($requests),
            'filters' => [
                'status' => $status,
                'category' => $category,
                'priority' => $priority,
                'search' => $search,
            ],
            'categories' => ServiceRequestCategory::all(),
        ]);
    }

    /**
     * Show the form for creating a new service request.
     */
    public function create(): Response
    {
        return Inertia::render('service-requests/create', [
            'categories' => ServiceRequestCategory::with('subcategories')->get(),
            'communities' => Community::all(),
            'contacts' => Contact::where('contact_type', 'professional')->get(),
        ]);
    }

    /**
     * Store a newly created service request.
     */
    public function store(StoreServiceRequestRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Create or use contact for the authenticated user
        $user = auth()->user();
        $contact = Contact::firstOrCreate(
            ['email' => $user->email],
            [
                'first_name' => $user->name,
                'last_name' => '',
                'contact_type' => 'admin',
                'phone_number' => '',
                'national_phone_number' => '',
                'phone_country_code' => 'SA',
                'tenant_id' => $user->tenant_id,
            ]
        );

        $validated['requester_id'] = $contact->id;

        // Set default status if not provided
        if (! isset($validated['status_id'])) {
            $defaultStatus = Status::where('domain', 'request')
                ->where('slug', 'request_new')
                ->first();
            $validated['status_id'] = $defaultStatus?->id ?? 1;
        }

        $serviceRequest = ServiceRequest::create($validated);

        return redirect()->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request created successfully.');
    }

    /**
     * Display the specified service request.
     */
    public function show(ServiceRequest $serviceRequest): Response
    {
        $serviceRequest->load([
            'category',
            'subcategory',
            'status',
            'requester',
            'professional',
            'community',
            'building',
            'unit',
            'assignedBy',
            'createdBy',
            'stateHistory.fromStatus',
            'stateHistory.toStatus',
            'stateHistory.changedBy',
        ]);

        return Inertia::render('service-requests/show', [
            'request' => new ServiceRequestResource($serviceRequest),
        ]);
    }

    /**
     * Show the form for editing the specified service request.
     */
    public function edit(ServiceRequest $serviceRequest): Response
    {
        return Inertia::render('service-requests/edit', [
            'request' => new ServiceRequestResource($serviceRequest),
            'categories' => ServiceRequestCategory::with('subcategories')->get(),
            'communities' => Community::all(),
            'contacts' => Contact::where('contact_type', 'professional')->get(),
        ]);
    }

    /**
     * Update the specified service request.
     */
    public function update(UpdateServiceRequestRequest $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update($request->validated());

        return redirect()->route('service-requests.show', $serviceRequest)
            ->with('success', 'Service request updated successfully.');
    }

    /**
     * Remove the specified service request.
     */
    public function destroy(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->delete();

        return redirect()->route('service-requests.index')
            ->with('success', 'Service request deleted successfully.');
    }

    /**
     * Display service request history page.
     */
    public function history(Request $request): Response
    {
        $tenantId = $request->user()->tenant_id;

        $requests = ServiceRequest::query()
            ->where(function ($q) use ($tenantId): void {
                $q->whereHas('community', fn ($cq) => $cq->where('tenant_id', $tenantId))
                    ->orWhereHas('building', fn ($bq) => $bq->where('tenant_id', $tenantId))
                    ->orWhereHas('unit', fn ($uq) => $uq->where('tenant_id', $tenantId));
            })
            ->whereNotNull('completed_at')
            ->with(['status', 'category'])
            ->orderByDesc('completed_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (ServiceRequest $r): array => [
                'id' => $r->id,
                'request_number' => $r->request_number,
                'title' => $r->title,
                'category' => $r->category?->name,
                'status' => $r->status?->name,
                'completed_at' => $r->completed_at?->toDateString(),
            ]);

        return Inertia::render('service-requests/history', [
            'requests' => $requests,
        ]);
    }
}
