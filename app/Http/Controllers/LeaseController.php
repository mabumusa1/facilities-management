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
            'communities' => Community::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->select('id', 'name')
                ->get(),
            'buildings' => Building::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->select('id', 'name', 'community_id')
                ->get(),
            'availableUnits' => $this->leaseService->getAvailableUnits($user->tenant_id),
            'tenants' => Contact::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
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
            'communities' => Community::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->select('id', 'name')
                ->get(),
            'buildings' => Building::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->select('id', 'name', 'community_id')
                ->get(),
            'units' => Unit::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->select('id', 'name', 'building_id')
                ->get(),
            'tenants' => Contact::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
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
     * Show the termination form.
     */
    public function terminateForm(Lease $lease): Response|RedirectResponse
    {
        if (! $this->leaseService->canTerminate($lease)) {
            return redirect()
                ->route('leases.show', $lease)
                ->with('error', 'This lease cannot be terminated.');
        }

        $lease->load(['units', 'tenant', 'community', 'building', 'status']);

        return Inertia::render('leases/terminate', [
            'lease' => $lease,
            'terminationSummary' => $this->leaseService->getTerminationSummary($lease),
        ]);
    }

    /**
     * Terminate a lease (Active -> Cancelled).
     */
    public function terminate(Request $request, Lease $lease): RedirectResponse
    {
        $validated = $request->validate([
            'termination_date' => ['required', 'date'],
            'termination_reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $this->leaseService->terminateLease($lease, $validated);

            return redirect()
                ->route('leases.show', $lease)
                ->with('success', 'Lease terminated successfully.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the move-out form.
     */
    public function moveOutForm(Lease $lease): Response|RedirectResponse
    {
        if (! $this->leaseService->canMoveOut($lease)) {
            return redirect()
                ->route('leases.show', $lease)
                ->with('error', 'This lease cannot be moved out.');
        }

        $lease->load(['units', 'tenant', 'community', 'building', 'status', 'transactions']);

        return Inertia::render('leases/move-out', [
            'lease' => $lease,
            'moveOutSummary' => $this->leaseService->getMoveOutSummary($lease),
        ]);
    }

    /**
     * Move out from a lease (Active -> Closed).
     */
    public function moveOut(Request $request, Lease $lease): RedirectResponse
    {
        $validated = $request->validate([
            'move_out_date' => ['required', 'date'],
            'inspection_notes' => ['nullable', 'string', 'max:1000'],
            'deposit_deductions' => ['nullable', 'string', 'max:500'],
            'deposit_refund_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $this->leaseService->moveOut($lease, $validated);

            return redirect()
                ->route('leases.show', $lease)
                ->with('success', 'Move-out completed successfully.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the lease renewal form.
     */
    public function renewForm(Request $request, Lease $lease): Response
    {
        $user = $request->user();

        if (! $this->leaseService->canRenew($lease)) {
            return redirect()
                ->route('leases.show', $lease)
                ->with('error', 'This lease cannot be renewed.');
        }

        $lease->load(['units', 'tenant', 'community', 'building']);

        return Inertia::render('leases/renew', [
            'originalLease' => $lease,
            'renewalDefaults' => $this->leaseService->getRenewalDefaults($lease),
            'communities' => Community::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->select('id', 'name')
                ->get(),
            'buildings' => Building::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->select('id', 'name', 'community_id')
                ->get(),
            'units' => Unit::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->select('id', 'name', 'building_id')
                ->get(),
            'tenants' => Contact::query()
                ->when($user->tenant_id !== null, fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->where('contact_type_id', 2)
                ->select('id', 'name', 'email', 'phone')
                ->get(),
            'statuses' => Status::whereIn('id', [30, 31])->get(),
        ]);
    }

    /**
     * Process lease renewal.
     */
    public function renew(Request $request, Lease $lease): RedirectResponse
    {
        $validated = $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'rental_type' => ['nullable', 'string', 'in:summary,detailed'],
            'units' => ['sometimes', 'array'],
            'units.*.id' => ['required', 'integer', 'exists:units,id'],
            'units.*.rental_annual_type' => ['nullable', 'string'],
            'units.*.annual_rental_amount' => ['nullable', 'numeric', 'min:0'],
            'units.*.net_area' => ['nullable', 'numeric', 'min:0'],
            'units.*.meter_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $newLease = $this->leaseService->renewLease($lease, $validated, $request->user());

            return redirect()
                ->route('leases.show', $newLease)
                ->with('success', 'Lease renewed successfully. A new lease has been created.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
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

    /**
     * Get renewal history for a lease.
     */
    public function renewalHistory(Lease $lease): JsonResponse
    {
        return response()->json([
            'history' => $this->leaseService->getRenewalHistory($lease),
        ]);
    }

    /**
     * Display expiring leases page.
     */
    public function expiringLeases(Request $request): Response
    {
        $user = $request->user();
        $leases = $this->leaseService->getExpiringLeases($user->tenant_id, 60);

        return Inertia::render('leases/expiring-leases', [
            'leases' => $leases,
        ]);
    }

    /**
     * Display expiring lease detail page.
     */
    public function expiringLeaseDetails(Lease $lease): Response
    {
        $lease->load(['units', 'tenant', 'community', 'building', 'status']);

        return Inertia::render('leases/expiring-lease-details', [
            'lease' => [
                'id' => $lease->id,
                'contract_number' => $lease->contract_number,
                'start_date' => $lease->start_date?->toDateString(),
                'end_date' => $lease->end_date?->toDateString(),
                'status' => $lease->status?->name,
                'tenant' => $lease->tenant ? ['name' => trim($lease->tenant->first_name.' '.$lease->tenant->last_name)] : null,
            ],
        ]);
    }

    /**
     * Display overdue leases page.
     */
    public function overdues(Request $request): Response
    {
        $user = $request->user();
        $leases = $this->leaseService->getLeasesForTenant($user->tenant_id, 15, 'overdue', null);

        return Inertia::render('leases/overdues', [
            'leases' => $leases,
        ]);
    }
}
