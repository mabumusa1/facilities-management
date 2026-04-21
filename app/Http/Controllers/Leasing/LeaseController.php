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
use App\Support\StatusWorkflow;
use App\Support\WorkflowNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaseController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim((string) $request->input('search', ''));
        $statusId = $request->input('status_id');
        $tenantId = $request->input('tenant_id');
        $perPage = min(max((int) $request->integer('per_page', 15), 5), 50);

        $leases = Lease::query()
            ->with(['tenant', 'status', 'units'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nestedQuery) use ($search): void {
                    $nestedQuery->where('contract_number', 'like', "%{$search}%")
                        ->orWhereHas('tenant', function ($tenantQuery) use ($search): void {
                            $tenantQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });

                    if (is_numeric($search)) {
                        $nestedQuery->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($statusId, fn ($query) => $query->where('status_id', (int) $statusId))
            ->when($tenantId, fn ($query) => $query->where('tenant_id', (int) $tenantId))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('leasing/leases/Index', [
            'leases' => $leases,
            'statuses' => Status::query()
                ->where('type', 'lease')
                ->select('id', 'name', 'name_en')
                ->orderBy('priority')
                ->orderBy('id')
                ->get(),
            'tenants' => Resident::query()
                ->select('id', 'first_name', 'last_name')
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
            'filters' => [
                'search' => $search,
                'status_id' => $statusId ? (string) $statusId : '',
                'tenant_id' => $tenantId ? (string) $tenantId : '',
                'per_page' => (string) $perPage,
            ],
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
        $lease->load([
            'tenant',
            'status',
            'units',
            'transactions.status',
            'additionalFees',
            'escalations',
            'createdBy',
            'dealOwner',
            'subleases.status',
            'subleases.tenant',
            'parentLease.status',
            'parentLease.tenant',
        ]);

        return Inertia::render('leasing/leases/Show', [
            'lease' => $lease,
        ]);
    }

    public function createSublease(Lease $lease): Response
    {
        return Inertia::render('leasing/leases/SubleaseCreate', [
            'parentLease' => $lease->load(['tenant', 'status']),
            'statuses' => Status::where('type', 'lease')->select('id', 'name', 'name_en')->get(),
            'tenants' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function storeSublease(Request $request, Lease $lease): RedirectResponse
    {
        $validated = $request->validate([
            'contract_number' => ['required', 'string', 'unique:rf_leases,contract_number'],
            'tenant_id' => ['nullable', 'integer', 'exists:rf_tenants,id'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'handover_date' => ['required', 'date'],
            'rental_total_amount' => ['required', 'numeric', 'min:0'],
            'security_deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'terms_conditions' => ['nullable', 'string'],
            'legal_representative' => ['nullable', 'string', 'max:255'],
            'fit_out_status' => ['nullable', 'string', 'max:255'],
        ]);

        $sublease = Lease::create([
            ...$validated,
            'tenant_id' => $validated['tenant_id'] ?? $lease->tenant_id,
            'lease_unit_type_id' => $lease->lease_unit_type_id,
            'rental_contract_type_id' => $lease->rental_contract_type_id,
            'payment_schedule_id' => $lease->payment_schedule_id,
            'tenant_type' => $lease->tenant_type?->value ?? $lease->tenant_type,
            'rental_type' => $lease->rental_type?->value ?? $lease->rental_type,
            'created_by_id' => $request->user()?->id,
            'deal_owner_id' => $lease->deal_owner_id,
            'parent_lease_id' => $lease->id,
            'is_sub_lease' => true,
        ]);

        $lease->load('units');

        $unitSyncData = [];

        foreach ($lease->units as $unit) {
            $unitSyncData[$unit->id] = [
                'rental_annual_type' => $unit->pivot?->rental_annual_type,
                'annual_rental_amount' => $unit->pivot?->annual_rental_amount,
                'net_area' => $unit->pivot?->net_area,
                'meter_cost' => $unit->pivot?->meter_cost,
            ];
        }

        if ($unitSyncData !== []) {
            $sublease->units()->sync($unitSyncData);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Sub-lease created.')]);

        return to_route('leases.show', $sublease);
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

    public function update(
        Request $request,
        Lease $lease,
        StatusWorkflow $statusWorkflow,
        WorkflowNotifier $workflowNotifier,
    ): RedirectResponse {
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

        $nextStatusId = array_key_exists('status_id', $validated)
            ? (int) $validated['status_id']
            : null;

        $fromStatus = null;
        $toStatus = null;

        if ($nextStatusId !== null && $nextStatusId !== (int) $lease->status_id) {
            $fromStatus = Status::query()->find($lease->status_id);
            $statusWorkflow->ensureTransition('lease', $lease->status_id, $nextStatusId);
            $toStatus = Status::query()->find($nextStatusId);
        }

        $lease->update($validated);

        if ($toStatus instanceof Status) {
            $workflowNotifier->notifyTenantUsers(
                tenantId: (int) ($request->session()->get('tenant_id') ?: $lease->account_tenant_id),
                module: 'lease',
                resourceId: $lease->id,
                fromStatus: $fromStatus?->name_en ?? $fromStatus?->name,
                toStatus: $toStatus->name_en ?? $toStatus->name ?? (string) $toStatus->id,
                url: route('leases.show', $lease, false),
                actor: $request->user()?->name,
            );
        }

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
