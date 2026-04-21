<?php

namespace App\Http\Controllers\Leasing;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Lease;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Unit;
use App\Models\UnitCategory;
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
        return Inertia::render('leasing/leases/Create', [
            'tenants' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'statuses' => Status::where('type', 'lease')->select('id', 'name', 'name_en')->get(),
            'unitCategories' => UnitCategory::select('id', 'name', 'name_en')->get(),
            'rentalContractTypes' => Setting::where('type', 'rental_contract_type')->select('id', 'name', 'name_en')->get(),
            'paymentSchedules' => Setting::where('type', 'payment_schedule')->select('id', 'name', 'name_en', 'parent_id')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
            'admins' => Admin::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
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
            'deal_owner_id' => ['nullable', 'integer', 'exists:rf_admins,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'handover_date' => ['required', 'date'],
            'tenant_type' => ['required', 'in:individual,company'],
            'rental_type' => ['required', 'in:total,detailed'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'security_deposit_due_date' => ['nullable', 'date'],
            'terms_conditions' => ['nullable', 'string'],
            'legal_representative' => ['nullable', 'string', 'max:255'],
            'fit_out_status' => ['nullable', 'string', 'max:255'],
            'number_of_years' => ['nullable', 'integer', 'min:0'],
            'number_of_months' => ['nullable', 'integer', 'min:0'],
            'number_of_days' => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['created_by_id'] = $request->user()->id;
        $lease = Lease::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease created.')]);

        return to_route('leases.show', $lease);
    }

    public function show(Lease $lease): Response
    {
        $lease->load(['tenant', 'status', 'units', 'transactions.status', 'additionalFees', 'escalations', 'createdBy', 'dealOwner']);

        return Inertia::render('leasing/leases/Show', [
            'lease' => $lease,
        ]);
    }

    public function edit(Lease $lease): Response
    {
        return Inertia::render('leasing/leases/Edit', [
            'lease' => $lease->load(['tenant', 'units']),
            'tenants' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'statuses' => Status::where('type', 'lease')->select('id', 'name', 'name_en')->get(),
            'unitCategories' => UnitCategory::select('id', 'name', 'name_en')->get(),
            'rentalContractTypes' => Setting::where('type', 'rental_contract_type')->select('id', 'name', 'name_en')->get(),
            'paymentSchedules' => Setting::where('type', 'payment_schedule')->select('id', 'name', 'name_en', 'parent_id')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
            'admins' => Admin::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function update(Request $request, Lease $lease): RedirectResponse
    {
        $validated = $request->validate([
            'contract_number' => ['required', 'string', 'unique:rf_leases,contract_number,'.$lease->id],
            'tenant_id' => ['sometimes', 'integer', 'exists:rf_tenants,id'],
            'status_id' => ['sometimes', 'integer', 'exists:rf_statuses,id'],
            'deal_owner_id' => ['nullable', 'integer', 'exists:rf_admins,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'actual_end_at' => ['nullable', 'date'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'security_deposit_due_date' => ['nullable', 'date'],
            'terms_conditions' => ['nullable', 'string'],
            'legal_representative' => ['nullable', 'string', 'max:255'],
            'fit_out_status' => ['nullable', 'string', 'max:255'],
            'number_of_years' => ['nullable', 'integer', 'min:0'],
            'number_of_months' => ['nullable', 'integer', 'min:0'],
            'number_of_days' => ['nullable', 'integer', 'min:0'],
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
