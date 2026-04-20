<?php

namespace App\Http\Controllers\Leasing;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaseController extends Controller
{
    public function index(Request $request): Response
    {
        $leases = Lease::query()
            ->with(['tenant', 'status', 'units'])
            ->latest()
            ->paginate(15);

        return Inertia::render('leasing/leases/Index', [
            'leases' => $leases,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('leasing/leases/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contract_number' => ['required', 'string', 'unique:rf_leases,contract_number'],
            'tenant_id' => ['required', 'integer', 'exists:rf_tenants,id'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'lease_unit_type_id' => ['required', 'integer'],
            'rental_contract_type_id' => ['required', 'integer'],
            'payment_schedule_id' => ['required', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'handover_date' => ['required', 'date'],
            'tenant_type' => ['required', 'in:individual,company'],
            'rental_type' => ['required', 'in:total,detailed'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'terms_conditions' => ['nullable', 'string'],
        ]);

        $validated['created_by_id'] = $request->user()->id;
        $lease = Lease::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease created.')]);

        return to_route('leases.show', $lease);
    }

    public function show(Lease $lease): Response
    {
        $lease->load(['tenant', 'status', 'units', 'transactions']);

        return Inertia::render('leasing/leases/Show', [
            'lease' => $lease,
        ]);
    }

    public function edit(Lease $lease): Response
    {
        return Inertia::render('leasing/leases/Edit', [
            'lease' => $lease->load(['tenant', 'units']),
        ]);
    }

    public function update(Request $request, Lease $lease): RedirectResponse
    {
        $validated = $request->validate([
            'contract_number' => ['required', 'string', 'unique:rf_leases,contract_number,'.$lease->id],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'terms_conditions' => ['nullable', 'string'],
        ]);

        $lease->update($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease updated.')]);

        return to_route('leases.show', $lease);
    }

    public function destroy(Lease $lease): RedirectResponse
    {
        $lease->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease deleted.')]);

        return to_route('leases.index');
    }
}
