<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeaseRequest;
use App\Http\Requests\UpdateLeaseRequest;
use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Lease;
use App\Models\Status;
use App\Models\Unit;
use App\Services\LeaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaseController extends Controller
{
    public function __construct(
        protected LeaseService $leaseService
    ) {}

    /**
     * Display a listing of leases.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('leases/index', [
            'leases' => $this->leaseService->getLeasesForTenant(
                $user->tenant_id,
                15,
                $request->query('status'),
                $request->query('search')
            ),
            'statistics' => $this->leaseService->getLeaseStatistics($user->tenant_id),
            'filters' => [
                'status' => $request->query('status'),
                'search' => $request->query('search'),
            ],
        ]);
    }

    /**
     * Show the lease creation wizard.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();
        $step = (int) $request->query('step', 1);

        return Inertia::render('leases/create', [
            'step' => $step,
            'communities' => Community::where('tenant_id', $user->tenant_id)
                ->select('id', 'name')
                ->get(),
            'buildings' => Building::where('tenant_id', $user->tenant_id)
                ->select('id', 'name', 'community_id')
                ->get(),
            'availableUnits' => $this->leaseService->getAvailableUnits($user->tenant_id),
            'tenants' => Contact::where('tenant_id', $user->tenant_id)
                ->where('contact_type_id', 2) // Tenant contact type
                ->select('id', 'name', 'email', 'phone')
                ->get(),
            'statuses' => Status::whereIn('id', [30, 31, 32, 33, 34])->get(),
            'wizardData' => session('lease_wizard_data', []),
        ]);
    }

    /**
     * Save wizard step data.
     */
    public function saveStep(Request $request): JsonResponse
    {
        $step = $request->input('step');
        $data = $request->input('data');

        $wizardData = session('lease_wizard_data', []);
        $wizardData["step_{$step}"] = $data;

        session(['lease_wizard_data' => $wizardData]);

        return response()->json([
            'success' => true,
            'wizardData' => $wizardData,
        ]);
    }

    /**
     * Store a newly created lease.
     */
    public function store(StoreLeaseRequest $request): RedirectResponse
    {
        $lease = $this->leaseService->createLease(
            $request->validated(),
            $request->user()
        );

        // Clear wizard data
        session()->forget('lease_wizard_data');

        return redirect()
            ->route('leases.show', $lease)
            ->with('success', 'Lease created successfully.');
    }

    /**
     * Display the specified lease.
     */
    public function show(Lease $lease): Response
    {
        $lease->load(['tenant', 'units', 'community', 'building', 'status', 'transactions', 'createdBy', 'dealOwner']);

        return Inertia::render('leases/show', [
            'lease' => $lease,
            'canRenew' => $this->leaseService->canRenew($lease),
            'canTerminate' => $this->leaseService->canTerminate($lease),
        ]);
    }

    /**
     * Show the form for editing the specified lease.
     */
    public function edit(Request $request, Lease $lease): Response
    {
        $user = $request->user();
        $lease->load(['units', 'tenant']);

        return Inertia::render('leases/edit', [
            'lease' => $lease,
            'communities' => Community::where('tenant_id', $user->tenant_id)
                ->select('id', 'name')
                ->get(),
            'buildings' => Building::where('tenant_id', $user->tenant_id)
                ->select('id', 'name', 'community_id')
                ->get(),
            'units' => Unit::where('tenant_id', $user->tenant_id)
                ->select('id', 'name', 'building_id')
                ->get(),
            'tenants' => Contact::where('tenant_id', $user->tenant_id)
                ->where('contact_type_id', 2)
                ->select('id', 'name', 'email', 'phone')
                ->get(),
            'statuses' => Status::whereIn('id', [30, 31, 32, 33, 34])->get(),
        ]);
    }

    /**
     * Update the specified lease.
     */
    public function update(UpdateLeaseRequest $request, Lease $lease): RedirectResponse
    {
        $this->leaseService->updateLease($lease, $request->validated());

        return redirect()
            ->route('leases.show', $lease)
            ->with('success', 'Lease updated successfully.');
    }

    /**
     * Remove the specified lease.
     */
    public function destroy(Lease $lease): RedirectResponse
    {
        $this->leaseService->deleteLease($lease);

        return redirect()
            ->route('leases.index')
            ->with('success', 'Lease deleted successfully.');
    }

    /**
     * Activate a lease (New -> Active).
     */
    public function activate(Lease $lease): RedirectResponse
    {
        $this->leaseService->activateLease($lease);

        return redirect()
            ->back()
            ->with('success', 'Lease activated successfully.');
    }

    /**
     * Terminate a lease (Active -> Cancelled).
     */
    public function terminate(Lease $lease): RedirectResponse
    {
        $this->leaseService->terminateLease($lease);

        return redirect()
            ->back()
            ->with('success', 'Lease terminated successfully.');
    }

    /**
     * Move out from a lease (Active -> Closed).
     */
    public function moveOut(Request $request, Lease $lease): RedirectResponse
    {
        $this->leaseService->moveOut($lease, $request->input('actual_end_date'));

        return redirect()
            ->back()
            ->with('success', 'Move-out completed successfully.');
    }

    // API Methods

    /**
     * Get leases list for API.
     */
    public function list(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'leases' => $this->leaseService->getLeasesForTenant(
                $user->tenant_id,
                $request->query('per_page', 15),
                $request->query('status'),
                $request->query('search')
            ),
        ]);
    }

    /**
     * Get lease statistics.
     */
    public function statistics(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json(
            $this->leaseService->getLeaseStatistics($user->tenant_id)
        );
    }

    /**
     * Get expiring leases.
     */
    public function expiring(Request $request): JsonResponse
    {
        $user = $request->user();
        $days = (int) $request->query('days', 30);

        return response()->json([
            'leases' => $this->leaseService->getExpiringLeases($user->tenant_id, $days),
        ]);
    }

    /**
     * Get available units for lease creation.
     */
    public function availableUnits(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'units' => $this->leaseService->getAvailableUnits($user->tenant_id),
        ]);
    }
}
