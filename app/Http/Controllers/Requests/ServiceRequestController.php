<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\Request as ServiceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $requests = ServiceRequest::query()
            ->with(['category', 'subcategory', 'status', 'unit', 'community'])
            ->latest()
            ->paginate(15);

        return Inertia::render('requests/Index', [
            'requests' => $requests,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('requests/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:rf_request_categories,id'],
            'subcategory_id' => ['nullable', 'integer', 'exists:rf_request_subcategories,id'],
            'unit_id' => ['nullable', 'integer', 'exists:rf_units,id'],
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
        ]);

        $validated['requester_id'] = $request->user()->id;
        $validated['requester_type'] = get_class($request->user());
        $validated['status_id'] = 1;

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
            'serviceRequest' => $serviceRequest,
        ]);
    }

    public function update(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status_id' => ['sometimes', 'integer', 'exists:rf_statuses,id'],
            'assignee_id' => ['nullable', 'integer'],
            'assignee_type' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $serviceRequest->update($validated);

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
