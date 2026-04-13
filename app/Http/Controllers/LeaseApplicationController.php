<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaseApplicationRequest;
use App\Http\Requests\UpdateLeaseApplicationRequest;
use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\LeaseApplication;
use App\Services\LeaseApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaseApplicationController extends Controller
{
    public function __construct(
        protected LeaseApplicationService $applicationService
    ) {}

    /**
     * Display a listing of lease applications.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('lease-applications/index', [
            'applications' => $this->applicationService->getApplicationsForTenant(
                $user->tenant_id,
                15,
                $request->query('status'),
                $request->query('search')
            ),
            'statistics' => $this->applicationService->getApplicationStatistics($user->tenant_id),
            'filters' => [
                'status' => $request->query('status'),
                'search' => $request->query('search'),
            ],
            'statuses' => LeaseApplication::getStatusLabels(),
        ]);
    }

    /**
     * Show the form for creating a new application.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('lease-applications/create', [
            'communities' => Community::where('tenant_id', $user->tenant_id)
                ->select('id', 'name')
                ->get(),
            'buildings' => Building::where('tenant_id', $user->tenant_id)
                ->select('id', 'name', 'community_id')
                ->get(),
            'availableUnits' => $this->applicationService->getAvailableUnits($user->tenant_id),
            'contacts' => Contact::where('tenant_id', $user->tenant_id)
                ->where('contact_type_id', 2) // Tenant contact type
                ->select('id', 'name', 'email', 'phone')
                ->get(),
            'sources' => [
                ['value' => LeaseApplication::SOURCE_WALK_IN, 'label' => 'Walk-in'],
                ['value' => LeaseApplication::SOURCE_WEBSITE, 'label' => 'Website'],
                ['value' => LeaseApplication::SOURCE_REFERRAL, 'label' => 'Referral'],
                ['value' => LeaseApplication::SOURCE_MARKETPLACE, 'label' => 'Marketplace'],
            ],
        ]);
    }

    /**
     * Store a newly created application.
     */
    public function store(StoreLeaseApplicationRequest $request): RedirectResponse
    {
        $application = $this->applicationService->createApplication(
            $request->validated(),
            $request->user()
        );

        return redirect()
            ->route('lease-applications.show', $application)
            ->with('success', 'Lease application created successfully.');
    }

    /**
     * Display the specified application.
     */
    public function show(LeaseApplication $leaseApplication): Response
    {
        $leaseApplication->load([
            'applicant',
            'community',
            'building',
            'units',
            'reviewedBy',
            'createdBy',
            'assignedTo',
            'convertedLease',
        ]);

        return Inertia::render('lease-applications/show', [
            'application' => $leaseApplication,
            'allowedTransitions' => $leaseApplication->getAllowedTransitions(),
            'stateHistory' => $this->applicationService->getStateHistory($leaseApplication),
            'canConvert' => $leaseApplication->canBeConverted(),
        ]);
    }

    /**
     * Show the form for editing the specified application.
     */
    public function edit(Request $request, LeaseApplication $leaseApplication): Response
    {
        $user = $request->user();
        $leaseApplication->load(['units', 'applicant']);

        return Inertia::render('lease-applications/edit', [
            'application' => $leaseApplication,
            'communities' => Community::where('tenant_id', $user->tenant_id)
                ->select('id', 'name')
                ->get(),
            'buildings' => Building::where('tenant_id', $user->tenant_id)
                ->select('id', 'name', 'community_id')
                ->get(),
            'availableUnits' => $this->applicationService->getAvailableUnits($user->tenant_id),
            'contacts' => Contact::where('tenant_id', $user->tenant_id)
                ->where('contact_type_id', 2)
                ->select('id', 'name', 'email', 'phone')
                ->get(),
            'sources' => [
                ['value' => LeaseApplication::SOURCE_WALK_IN, 'label' => 'Walk-in'],
                ['value' => LeaseApplication::SOURCE_WEBSITE, 'label' => 'Website'],
                ['value' => LeaseApplication::SOURCE_REFERRAL, 'label' => 'Referral'],
                ['value' => LeaseApplication::SOURCE_MARKETPLACE, 'label' => 'Marketplace'],
            ],
        ]);
    }

    /**
     * Update the specified application.
     */
    public function update(UpdateLeaseApplicationRequest $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        $this->applicationService->updateApplication($leaseApplication, $request->validated());

        return redirect()
            ->route('lease-applications.show', $leaseApplication)
            ->with('success', 'Lease application updated successfully.');
    }

    /**
     * Remove the specified application.
     */
    public function destroy(LeaseApplication $leaseApplication): RedirectResponse
    {
        $this->applicationService->deleteApplication($leaseApplication);

        return redirect()
            ->route('lease-applications.index')
            ->with('success', 'Lease application deleted successfully.');
    }

    /**
     * Transition application status.
     */
    public function transition(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:500'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->applicationService->transitionStatus(
                $leaseApplication,
                $validated['status'],
                $request->user(),
                $validated['notes'] ?? null,
                $validated['rejection_reason'] ?? null
            );

            return redirect()
                ->back()
                ->with('success', 'Application status updated successfully.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Submit application for review.
     */
    public function submitForReview(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        try {
            $this->applicationService->submitForReview($leaseApplication, $request->user());

            return redirect()
                ->back()
                ->with('success', 'Application submitted for review.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Approve an application.
     */
    public function approve(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->applicationService->approve(
                $leaseApplication,
                $request->user(),
                $validated['notes'] ?? null
            );

            return redirect()
                ->back()
                ->with('success', 'Application approved successfully.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject an application.
     */
    public function reject(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->applicationService->reject(
                $leaseApplication,
                $request->user(),
                $validated['reason'],
                $validated['notes'] ?? null
            );

            return redirect()
                ->back()
                ->with('success', 'Application rejected.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel an application.
     */
    public function cancel(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->applicationService->cancel(
                $leaseApplication,
                $request->user(),
                $validated['notes'] ?? null
            );

            return redirect()
                ->back()
                ->with('success', 'Application cancelled.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Put application on hold.
     */
    public function hold(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->applicationService->putOnHold(
                $leaseApplication,
                $request->user(),
                $validated['notes'] ?? null
            );

            return redirect()
                ->back()
                ->with('success', 'Application put on hold.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Resume application from hold.
     */
    public function resume(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        $validated = $request->validate([
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->applicationService->resume(
                $leaseApplication,
                $request->user(),
                $validated['notes'] ?? null
            );

            return redirect()
                ->back()
                ->with('success', 'Application resumed.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Send quote to applicant.
     */
    public function sendQuote(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        $validated = $request->validate([
            'expiration_days' => ['nullable', 'integer', 'min:1', 'max:90'],
        ]);

        $this->applicationService->sendQuote(
            $leaseApplication,
            $validated['expiration_days'] ?? 30
        );

        return redirect()
            ->back()
            ->with('success', 'Quote sent to applicant.');
    }

    /**
     * Convert application to lease.
     */
    public function convertToLease(Request $request, LeaseApplication $leaseApplication): RedirectResponse
    {
        try {
            $lease = $this->applicationService->convertToLease(
                $leaseApplication,
                $request->user()
            );

            return redirect()
                ->route('leases.show', $lease)
                ->with('success', 'Application converted to lease successfully.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Get state history for an application (API).
     */
    public function history(LeaseApplication $leaseApplication): JsonResponse
    {
        return response()->json([
            'history' => $this->applicationService->getStateHistory($leaseApplication),
        ]);
    }
}
