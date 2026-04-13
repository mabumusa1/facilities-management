<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Community;
use App\Models\Contact;
use App\Models\Lease;
use App\Models\Status;
use App\Models\Unit;
use App\Services\LeaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubLeaseController extends Controller
{
    public function __construct(
        protected LeaseService $leaseService
    ) {}

    /**
     * Display all sub-leases.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $search = $request->query('search');
        $status = $request->query('status');

        $query = Lease::where('is_sub_lease', true)
            ->whereHas('tenant', fn ($q) => $q->where('tenant_id', $user->tenant_id))
            ->with(['tenant', 'units', 'community', 'building', 'status', 'parentLease'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $statusMap = ['new' => 30, 'active' => 31, 'expired' => 32, 'cancelled' => 33, 'closed' => 34];
            if (isset($statusMap[$status])) {
                $query->where('status_id', $statusMap[$status]);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                    ->orWhereHas('tenant', fn ($tq) => $tq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('units', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $subleases = $query->paginate(15);

        $statistics = [
            'total' => Lease::where('is_sub_lease', true)
                ->whereHas('tenant', fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->count(),
            'active' => Lease::where('is_sub_lease', true)
                ->whereHas('tenant', fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->where('status_id', 31)->count(),
            'new' => Lease::where('is_sub_lease', true)
                ->whereHas('tenant', fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->where('status_id', 30)->count(),
            'expired' => Lease::where('is_sub_lease', true)
                ->whereHas('tenant', fn ($q) => $q->where('tenant_id', $user->tenant_id))
                ->where('status_id', 32)->count(),
        ];

        return Inertia::render('sub-leases/index', [
            'subleases' => $subleases,
            'statistics' => $statistics,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the sub-lease creation form.
     */
    public function create(Request $request): Response
    {
        $user = $request->user();

        $parentLeases = Lease::where('is_sub_lease', false)
            ->whereHas('tenant', fn ($q) => $q->where('tenant_id', $user->tenant_id))
            ->where('status_id', 31) // Active only
            ->with(['tenant', 'units'])
            ->get(['id', 'contract_number', 'tenant_id', 'start_date', 'end_date'])
            ->map(fn ($l) => [
                'id' => $l->id,
                'label' => ($l->contract_number ?? "#{$l->id}") . ' — ' . ($l->tenant?->name ?? 'Unknown'),
                'start_date' => $l->start_date,
                'end_date' => $l->end_date,
            ]);

        return Inertia::render('sub-leases/create', [
            'parentLeases' => $parentLeases,
            'communities' => Community::where('tenant_id', $user->tenant_id)->select('id', 'name')->get(),
            'buildings' => Building::where('tenant_id', $user->tenant_id)->select('id', 'name', 'community_id')->get(),
            'units' => Unit::where('tenant_id', $user->tenant_id)->select('id', 'name', 'building_id')->get(),
            'tenants' => Contact::where('tenant_id', $user->tenant_id)
                ->where('contact_type_id', 2)
                ->select('id', 'name', 'email', 'phone')->get(),
            'statuses' => Status::whereIn('id', [30, 31])->get(),
        ]);
    }

    /**
     * Store a new sub-lease.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'parent_lease_id' => ['required', 'integer', 'exists:leases,id'],
            'tenant_id' => ['required', 'integer', 'exists:contacts,id'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
            'contract_number' => ['required', 'string', 'max:100', 'unique:leases,contract_number'],
            'tenant_type' => ['required', 'in:individual,corporate'],
            'rental_type' => ['required', 'in:summary,detailed'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'number_of_years' => ['nullable', 'integer', 'min:0'],
            'number_of_months' => ['nullable', 'integer', 'min:0'],
            'terms_conditions' => ['nullable', 'string'],
            'units' => ['nullable', 'array'],
            'units.*.id' => ['required', 'integer', 'exists:units,id'],
        ]);

        $validated['is_sub_lease'] = true;

        $sublease = Lease::create($validated);

        if (! empty($validated['units'])) {
            $unitIds = array_column($validated['units'], 'id');
            $sublease->units()->sync($unitIds);
        }

        return redirect()
            ->route('sub-leases.show', $sublease)
            ->with('success', 'Sub-lease created successfully.');
    }

    /**
     * Display a sub-lease.
     */
    public function show(Lease $subLease): Response
    {
        abort_if(! $subLease->is_sub_lease, 404);

        $subLease->load(['tenant', 'units', 'community', 'building', 'status', 'parentLease.tenant', 'transactions']);

        return Inertia::render('sub-leases/show', [
            'sublease' => $subLease,
        ]);
    }

    /**
     * Show the form for editing a sub-lease.
     */
    public function edit(Request $request, Lease $subLease): Response
    {
        abort_if(! $subLease->is_sub_lease, 404);

        $user = $request->user();
        $subLease->load(['units', 'tenant']);

        $parentLeases = Lease::where('is_sub_lease', false)
            ->whereHas('tenant', fn ($q) => $q->where('tenant_id', $user->tenant_id))
            ->get(['id', 'contract_number', 'tenant_id'])
            ->map(fn ($l) => [
                'id' => $l->id,
                'label' => ($l->contract_number ?? "#{$l->id}"),
            ]);

        return Inertia::render('sub-leases/edit', [
            'sublease' => $subLease,
            'parentLeases' => $parentLeases,
            'communities' => Community::where('tenant_id', $user->tenant_id)->select('id', 'name')->get(),
            'buildings' => Building::where('tenant_id', $user->tenant_id)->select('id', 'name', 'community_id')->get(),
            'units' => Unit::where('tenant_id', $user->tenant_id)->select('id', 'name', 'building_id')->get(),
            'tenants' => Contact::where('tenant_id', $user->tenant_id)
                ->where('contact_type_id', 2)
                ->select('id', 'name', 'email', 'phone')->get(),
            'statuses' => Status::whereIn('id', [30, 31, 32, 33, 34])->get(),
        ]);
    }

    /**
     * Update a sub-lease.
     */
    public function update(Request $request, Lease $subLease): RedirectResponse
    {
        abort_if(! $subLease->is_sub_lease, 404);

        $validated = $request->validate([
            'parent_lease_id' => ['required', 'integer', 'exists:leases,id'],
            'tenant_id' => ['required', 'integer', 'exists:contacts,id'],
            'community_id' => ['nullable', 'integer', 'exists:communities,id'],
            'building_id' => ['nullable', 'integer', 'exists:buildings,id'],
            'status_id' => ['required', 'integer', 'exists:statuses,id'],
            'contract_number' => ['required', 'string', 'max:100', "unique:leases,contract_number,{$subLease->id}"],
            'tenant_type' => ['required', 'in:individual,corporate'],
            'rental_type' => ['required', 'in:summary,detailed'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'number_of_years' => ['nullable', 'integer', 'min:0'],
            'number_of_months' => ['nullable', 'integer', 'min:0'],
            'terms_conditions' => ['nullable', 'string'],
            'units' => ['nullable', 'array'],
            'units.*.id' => ['required', 'integer', 'exists:units,id'],
        ]);

        $subLease->update($validated);

        if (isset($validated['units'])) {
            $unitIds = array_column($validated['units'], 'id');
            $subLease->units()->sync($unitIds);
        }

        return redirect()
            ->route('sub-leases.show', $subLease)
            ->with('success', 'Sub-lease updated successfully.');
    }

    /**
     * Delete a sub-lease.
     */
    public function destroy(Lease $subLease): RedirectResponse
    {
        abort_if(! $subLease->is_sub_lease, 404);

        $subLease->delete();

        return redirect()
            ->route('sub-leases.index')
            ->with('success', 'Sub-lease deleted successfully.');
    }
}
