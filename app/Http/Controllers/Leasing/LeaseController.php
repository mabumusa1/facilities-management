<?php

namespace App\Http\Controllers\Leasing;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Lease;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\UnitCategory;
use App\Support\StatusWorkflow;
use App\Support\WorkflowNotifier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LeaseController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $this->authorize('viewAny', Lease::class);

        $search = trim((string) $request->input('search', ''));
        $statusId = $request->input('status_id');
        $tenantId = $request->input('tenant_id');
        $perPage = min(max((int) $request->integer('per_page', 15), 5), 50);

        $leases = Lease::query()
            ->with(['tenant', 'status', 'units.status', 'leaseUnitType'])
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

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => collect($leases->items())->map(
                    fn (Lease $lease): array => $this->leaseListItem($lease)
                ),
                'meta' => $this->meta($leases),
            ]);
        }

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

    public function subLeases(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Lease::class);

        $subLeases = Lease::query()
            ->where('is_sub_lease', true)
            ->with(['tenant', 'status', 'units.status', 'leaseUnitType'])
            ->latest()
            ->paginate($this->perPage($request))
            ->withQueryString();

        return response()->json([
            'data' => collect($subLeases->items())->map(
                fn (Lease $lease): array => $this->leaseListItem($lease)
            ),
            'meta' => $this->meta($subLeases),
        ]);
    }

    public function create(Request $request): JsonResponse|Response
    {
        $this->authorize('create', Lease::class);

        $tenants = Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();
        $statuses = Status::where('type', 'lease')->select('id', 'name', 'name_ar', 'name_en')->get();
        $unitCategories = UnitCategory::select('id', 'name', 'name_ar', 'name_en', 'icon')->get();
        $rentalContractTypes = Setting::where('type', 'rental_contract_type')->select('id', 'name', 'name_ar', 'name_en')->get();
        $paymentSchedules = Setting::where('type', 'payment_schedule')->select('id', 'name', 'name_ar', 'name_en', 'parent_id')->get();
        $units = Unit::select('id', 'name')->orderBy('name')->get();
        $admins = Admin::select('id', 'first_name', 'last_name')->orderBy('first_name')->get();

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => [
                    'tenants' => $tenants->map(fn (Resident $tenant): array => [
                        'id' => $tenant->id,
                        'name' => trim(($tenant->first_name ?? '').' '.($tenant->last_name ?? '')),
                    ])->values()->all(),
                    'statuses' => $statuses->map(fn (Status $status): array => [
                        'id' => $status->id,
                        'name' => $status->name,
                        'name_ar' => $status->name_ar,
                        'name_en' => $status->name_en,
                    ])->values()->all(),
                    'unit_categories' => $unitCategories->map(fn (UnitCategory $category): array => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'name_ar' => $category->name_ar,
                        'name_en' => $category->name_en,
                        'icon' => $category->icon,
                    ])->values()->all(),
                    'units' => $units->map(fn (Unit $unit): array => [
                        'id' => $unit->id,
                        'name' => $unit->name,
                    ])->values()->all(),
                    'admins' => $admins->map(fn (Admin $admin): array => [
                        'id' => $admin->id,
                        'name' => trim(($admin->first_name ?? '').' '.($admin->last_name ?? '')),
                    ])->values()->all(),
                ],
                'specifications' => [
                    'rental_contract_type' => $rentalContractTypes->map(fn (Setting $type): array => [
                        'id' => $type->id,
                        'name' => $type->name,
                        'name_ar' => $type->name_ar,
                        'name_en' => $type->name_en,
                    ])->values()->all(),
                    'payment_schedule' => $paymentSchedules->map(fn (Setting $schedule): array => [
                        'id' => $schedule->id,
                        'name' => $schedule->name,
                        'name_ar' => $schedule->name_ar,
                        'name_en' => $schedule->name_en,
                        'parent_id' => $schedule->parent_id,
                    ])->values()->all(),
                ],
            ]);
        }

        return Inertia::render('leasing/leases/Create', [
            'tenants' => $tenants,
            'statuses' => $statuses,
            'unitCategories' => $unitCategories,
            'rentalContractTypes' => $rentalContractTypes,
            'paymentSchedules' => $paymentSchedules,
            'units' => $units,
            'admins' => $admins,
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Lease::class);

        $validated = $request->validate([
            'contract_number' => ['nullable', 'string', 'unique:rf_leases,contract_number'],
            'autoGenerateLeaseNumber' => ['nullable'],
            'tenant_id' => ['required', 'integer', 'exists:rf_tenants,id'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'lease_unit_type_id' => ['required', 'integer'],
            'lease_unit_type' => ['nullable', 'integer'],
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
            'units' => ['nullable', 'array'],
            'units.*.id' => ['required_with:units', 'integer', 'exists:rf_units,id'],
            'units.*.rental_annual_type' => ['nullable', 'string', 'max:255'],
            'units.*.rental_amount' => ['nullable', 'numeric', 'min:0'],
            'units.*.annual_rental_amount' => ['nullable', 'numeric', 'min:0'],
            'units.*.net_area' => ['nullable', 'numeric', 'min:0'],
            'units.*.meter_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (! array_key_exists('lease_unit_type_id', $validated) && array_key_exists('lease_unit_type', $validated)) {
            $validated['lease_unit_type_id'] = $validated['lease_unit_type'];
        }

        $autoGenerateLeaseNumber = $this->toBoolean($validated['autoGenerateLeaseNumber'] ?? false);

        if (($validated['contract_number'] ?? null) === null && $autoGenerateLeaseNumber) {
            $validated['contract_number'] = $this->generateLeaseContractNumber();
        }

        if (($validated['contract_number'] ?? null) === null) {
            abort(422, 'Lease contract number is required.');
        }

        $validated['created_by_id'] = $request->user()->id;
        $units = $validated['units'] ?? [];

        unset($validated['lease_unit_type'], $validated['autoGenerateLeaseNumber'], $validated['units']);

        $lease = Lease::create($validated);

        if ($units !== []) {
            $this->syncLeaseUnits($lease, $units);
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $lease->load([
                'tenant',
                'status',
                'leaseUnitType',
                'rentalContractType',
                'paymentSchedule',
                'units.category',
                'units.type',
                'units.status',
                'units.community',
                'units.building',
                'units.city',
                'units.district',
            ]);

            return response()->json([
                'data' => $this->leaseDetails($lease),
                'message' => __('Lease created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease created.')]);

        return to_route('leases.show', $lease);
    }

    public function storeFromCreateAlias(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Lease::class);

        return $this->store($request);
    }

    public function stepFour(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Lease::class);

        return $this->store($request);
    }

    public function renewStore(Request $request): JsonResponse
    {
        $this->authorize('update', Lease::class);

        $validated = $request->validate([
            'rf_lease_id' => ['required', 'integer', 'exists:rf_leases,id'],
            'rental_contract_type_id' => ['required', 'integer'],
            'payment_schedule_id' => ['required', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'tenant_type' => ['nullable', 'in:individual,company'],
            'rental_type' => ['required', 'in:total,detailed'],
            'autoGenerateLeaseNumber' => ['required'],
            'contract_number' => ['nullable', 'string', 'unique:rf_leases,contract_number'],
            'terms_conditions' => ['nullable', 'string'],
            'number_of_years' => ['nullable', 'integer', 'min:0'],
            'number_of_months' => ['nullable', 'integer', 'min:0'],
            'number_of_days' => ['nullable', 'integer', 'min:0'],
            'units' => ['required', 'array', 'min:1'],
            'units.*.id' => ['required', 'integer', 'exists:rf_units,id'],
            'units.*.rental_amount' => ['required', 'numeric', 'min:0'],
            'units.*.rental_annual_type' => ['nullable', 'string', 'max:255'],
            'units.*.net_area' => ['nullable', 'numeric', 'min:0'],
            'units.*.meter_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $baseLease = Lease::query()->findOrFail((int) $validated['rf_lease_id']);
        $autoGenerateLeaseNumber = $this->toBoolean($validated['autoGenerateLeaseNumber']);

        $contractNumber = $autoGenerateLeaseNumber
            ? $this->generateLeaseContractNumber()
            : ($validated['contract_number'] ?? null);

        if ($contractNumber === null) {
            abort(422, 'Lease contract number is required.');
        }

        $renewedLease = Lease::create([
            'contract_number' => $contractNumber,
            'tenant_id' => $baseLease->tenant_id,
            'status_id' => $this->leaseStatusIdByNames(['active', 'new']) ?? $baseLease->status_id,
            'lease_unit_type_id' => $baseLease->lease_unit_type_id,
            'rental_contract_type_id' => $validated['rental_contract_type_id'],
            'payment_schedule_id' => $validated['payment_schedule_id'],
            'deal_owner_id' => $baseLease->deal_owner_id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'handover_date' => $validated['start_date'],
            'tenant_type' => $validated['tenant_type'] ?? ($baseLease->tenant_type?->value ?? $baseLease->tenant_type),
            'rental_type' => $validated['rental_type'],
            'rental_total_amount' => collect($validated['units'])->sum(
                fn (array $unit): float => (float) $unit['rental_amount']
            ),
            'terms_conditions' => $validated['terms_conditions'] ?? $baseLease->terms_conditions,
            'number_of_years' => $validated['number_of_years'] ?? $baseLease->number_of_years,
            'number_of_months' => $validated['number_of_months'] ?? $baseLease->number_of_months,
            'number_of_days' => $validated['number_of_days'] ?? $baseLease->number_of_days,
            'created_by_id' => $request->user()?->id,
            'parent_lease_id' => $baseLease->id,
            'is_renew' => true,
        ]);

        $this->syncLeaseUnits($renewedLease, $validated['units']);

        $renewedLease->load([
            'tenant',
            'status',
            'leaseUnitType',
            'rentalContractType',
            'paymentSchedule',
            'units.category',
            'units.type',
            'units.status',
            'units.community',
            'units.building',
            'units.city',
            'units.district',
        ]);

        return response()->json([
            'data' => $this->leaseDetails($renewedLease),
            'message' => __('Lease renewed.'),
        ]);
    }

    public function addendum(Request $request, Lease $lease): JsonResponse
    {
        $this->authorize('update', $lease);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'effective_date' => ['required', 'date'],
        ]);

        $entry = sprintf(
            '[%s] %s: %s',
            $validated['effective_date'],
            ucfirst((string) $validated['type']),
            trim((string) $validated['description']),
        );

        $this->appendLeaseTerm($lease, $entry);

        return response()->json([
            'data' => [
                'lease_id' => $lease->id,
                'type' => $validated['type'],
                'description' => $validated['description'],
                'effective_date' => $validated['effective_date'],
                'terms_conditions' => $lease->fresh()?->terms_conditions,
            ],
            'message' => __('Lease addendum added.'),
        ]);
    }

    public function changeStatusMoveOut(Request $request): JsonResponse
    {
        $this->authorize('update', Lease::class);

        $validated = $request->validate([
            'rf_lease_id' => ['required', 'integer', 'exists:rf_leases,id'],
            'end_at' => ['required', 'date'],
        ]);

        $lease = Lease::query()->findOrFail((int) $validated['rf_lease_id']);
        $currentStatusName = $this->normalizedStatusName($lease->status);

        if (! in_array($currentStatusName, ['expired', 'expired contract', 'expired_contract'], true)) {
            return response()->json([
                'code' => 400,
                'message' => __('Move-out can only be applied to expired leases.'),
                'data' => [],
                'meta' => [],
            ], 400);
        }

        $lease->update([
            'status_id' => $this->leaseStatusIdByNames(['closed', 'closed contract', 'closed_contract']) ?? $lease->status_id,
            'actual_end_at' => $validated['end_at'],
            'is_move_out' => true,
        ]);

        return response()->json([
            'code' => 200,
            'message' => __('Operation completed successfully.'),
            'data' => [],
            'meta' => [],
        ]);
    }

    public function changeStatusTerminate(Request $request): JsonResponse
    {
        $this->authorize('update', Lease::class);

        $validated = $request->validate([
            'rf_lease_id' => ['required', 'integer', 'exists:rf_leases,id'],
            'end_at' => ['required', 'date'],
        ]);

        $lease = Lease::query()->findOrFail((int) $validated['rf_lease_id']);
        $currentStatusName = $this->normalizedStatusName($lease->status);

        if (! in_array($currentStatusName, ['new', 'active'], true)) {
            return response()->json([
                'code' => 400,
                'message' => __('Terminate can only be applied to new or active leases.'),
                'data' => [],
                'meta' => [],
            ], 400);
        }

        $lease->update([
            'status_id' => $this->leaseStatusIdByNames([
                'terminated',
                'terminated contract',
                'terminated_contract',
                'canceled',
                'cancelled',
                'canceled contract',
                'cancelled contract',
            ]) ?? $lease->status_id,
            'actual_end_at' => $validated['end_at'],
        ]);

        return response()->json([
            'code' => 200,
            'message' => __('Operation completed successfully.'),
            'data' => [],
            'meta' => [],
        ]);
    }

    public function changeStatusSuspend(Request $request): JsonResponse
    {
        $this->authorize('update', Lease::class);

        $validated = $request->validate([
            'lease_id' => ['required', 'integer', 'exists:rf_leases,id'],
            'effective_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string'],
        ]);

        $lease = Lease::query()->findOrFail((int) $validated['lease_id']);

        $updates = [
            'status_id' => $this->leaseStatusIdByNames(['suspended', 'suspend', 'paused']) ?? $lease->status_id,
        ];

        if (array_key_exists('effective_date', $validated) && $validated['effective_date'] !== null) {
            $updates['actual_end_at'] = $validated['effective_date'];
        }

        $lease->update($updates);

        if (! empty($validated['reason'])) {
            $this->appendLeaseTerm($lease, '[suspend] '.$validated['reason']);
        }

        return response()->json([
            'code' => 200,
            'message' => __('Operation completed successfully.'),
            'data' => [],
            'meta' => [],
        ]);
    }

    public function changeStatusReactivate(Request $request): JsonResponse
    {
        $this->authorize('update', Lease::class);

        $validated = $request->validate([
            'lease_id' => ['required', 'integer', 'exists:rf_leases,id'],
            'effective_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string'],
        ]);

        $lease = Lease::query()->findOrFail((int) $validated['lease_id']);

        $lease->update([
            'status_id' => $this->leaseStatusIdByNames(['active']) ?? $lease->status_id,
            'actual_end_at' => null,
        ]);

        if (! empty($validated['reason'])) {
            $this->appendLeaseTerm($lease, '[reactivate] '.$validated['reason']);
        }

        return response()->json([
            'code' => 200,
            'message' => __('Operation completed successfully.'),
            'data' => [],
            'meta' => [],
        ]);
    }

    public function show(Request $request, Lease $lease): JsonResponse|Response
    {
        $this->authorize('view', $lease);

        $lease->load([
            'tenant',
            'status',
            'leaseUnitType',
            'rentalContractType',
            'paymentSchedule',
            'units.category',
            'units.type',
            'units.status',
            'units.community',
            'units.building',
            'units.city',
            'units.district',
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

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => $this->leaseDetails($lease),
                'message' => __('Lease retrieved.'),
            ]);
        }

        return Inertia::render('leasing/leases/Show', [
            'lease' => $lease,
        ]);
    }

    public function expiring(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Lease::class);

        $leases = Lease::query()
            ->with(['tenant', 'status'])
            ->whereDate('end_date', '>=', now()->toDateString())
            ->whereDate('end_date', '<=', now()->addDays(30)->toDateString())
            ->orderBy('end_date')
            ->paginate($this->perPage($request));

        return response()->json([
            'data' => collect($leases->items())->map(fn (Lease $lease): array => [
                'id' => $lease->id,
                'contract_number' => $lease->contract_number,
                'end_date' => $lease->end_date?->toDateString(),
                'tenant' => $lease->tenant
                    ? [
                        'id' => $lease->tenant->id,
                        'name' => trim(($lease->tenant->first_name ?? '').' '.($lease->tenant->last_name ?? '')),
                    ]
                    : null,
                'status' => $lease->status
                    ? [
                        'id' => $lease->status->id,
                        'name' => $lease->status->name_en ?? $lease->status->name,
                    ]
                    : null,
            ]),
            'meta' => $this->meta($leases),
        ]);
    }

    public function statistics(): JsonResponse
    {
        $this->authorize('viewAny', Lease::class);

        $totalLeases = Lease::query()->count();
        $newLeases = $this->countLeasesByStatusNames(['new']);
        $activeLeases = $this->countLeasesByStatusNames(['active']);
        $expiredLeases = $this->countLeasesByStatusNames(['expired']);
        $terminatedLeases = $this->countLeasesByStatusNames(['terminated']);

        $activeCommercialLeases = Lease::query()
            ->whereHas('status', function (Builder $query): void {
                $query->whereRaw('LOWER(COALESCE(name_en, name)) = ?', ['active']);
            })
            ->whereHas('leaseUnitType', function (Builder $query): void {
                $query->whereRaw('LOWER(COALESCE(name_en, name)) = ?', ['commercial']);
            })
            ->count();

        $activeResidentialLeases = Lease::query()
            ->whereHas('status', function (Builder $query): void {
                $query->whereRaw('LOWER(COALESCE(name_en, name)) = ?', ['active']);
            })
            ->whereHas('leaseUnitType', function (Builder $query): void {
                $query->whereRaw('LOWER(COALESCE(name_en, name)) = ?', ['residential']);
            })
            ->count();

        $now = now();

        $currentMonthCollection = (float) Transaction::query()
            ->whereYear('due_on', (int) $now->format('Y'))
            ->whereMonth('due_on', (int) $now->format('m'))
            ->sum('amount');

        $currentYearCollection = (float) Transaction::query()
            ->whereYear('due_on', (int) $now->format('Y'))
            ->sum('amount');

        $calculatePaidCollectionForCurrentMonth = (float) Transaction::query()
            ->where('is_paid', true)
            ->whereYear('due_on', (int) $now->format('Y'))
            ->whereMonth('due_on', (int) $now->format('m'))
            ->sum('amount');

        $calculatePaidCollectionForCurrentYear = (float) Transaction::query()
            ->where('is_paid', true)
            ->whereYear('due_on', (int) $now->format('Y'))
            ->sum('amount');

        return response()->json([
            'data' => [
                'totalLeases' => $totalLeases,
                'newLeases' => $newLeases,
                'activeLeases' => $activeLeases,
                'expiredLeases' => $expiredLeases,
                'terminatedLeases' => $terminatedLeases,
                'percentNewLeases' => $this->percentage($newLeases, $totalLeases),
                'percentActiveLeases' => $this->percentage($activeLeases, $totalLeases),
                'percentExpiredLeases' => $this->percentage($expiredLeases, $totalLeases),
                'percentTerminatedLeases' => $this->percentage($terminatedLeases, $totalLeases),
                'activeCommercialLeases' => $activeCommercialLeases,
                'activeResidentialLeases' => $activeResidentialLeases,
                'currentMonthCollection' => $currentMonthCollection,
                'currentYearCollection' => $currentYearCollection,
                'calculatePaidCollectionForCurrentMonth' => $calculatePaidCollectionForCurrentMonth,
                'calculatePaidCollectionForCurrentYear' => $calculatePaidCollectionForCurrentYear,
            ],
        ]);
    }

    public function rentalContractTypes(): JsonResponse
    {
        $types = Setting::query()
            ->where('type', 'rental_contract_type')
            ->select('id', 'name', 'name_ar', 'name_en')
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get();

        return response()->json([
            'data' => $types->map(fn (Setting $type): array => [
                'id' => $type->id,
                'name' => $type->name,
                'name_ar' => $type->name_ar,
                'name_en' => $type->name_en,
            ])->values()->all(),
            'meta' => [],
        ]);
    }

    public function paymentSchedules(): JsonResponse
    {
        $paymentSchedules = Setting::query()
            ->where('type', 'payment_schedule')
            ->select('id', 'name', 'name_ar', 'name_en', 'parent_id')
            ->orderByRaw('COALESCE(name_en, name) asc')
            ->get();

        return response()->json([
            'data' => $paymentSchedules->map(fn (Setting $schedule): array => [
                'id' => $schedule->id,
                'name' => $schedule->name,
                'name_ar' => $schedule->name_ar,
                'name_en' => $schedule->name_en,
                'parent_id' => $schedule->parent_id,
            ])->values()->all(),
            'meta' => [],
        ]);
    }

    public function createSublease(Lease $lease): Response
    {
        $this->authorize('create', Lease::class);

        return Inertia::render('leasing/leases/SubleaseCreate', [
            'parentLease' => $lease->load(['tenant', 'status']),
            'statuses' => Status::where('type', 'lease')->select('id', 'name', 'name_en')->get(),
            'tenants' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
        ]);
    }

    public function storeSublease(Request $request, Lease $lease): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Lease::class);

        $validated = $request->validate([
            'contract_number' => ['nullable', 'string', 'unique:rf_leases,contract_number'],
            'autoGenerateLeaseNumber' => ['nullable'],
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

        $autoGenerateLeaseNumber = $this->toBoolean($validated['autoGenerateLeaseNumber'] ?? false);

        if (($validated['contract_number'] ?? null) === null && $autoGenerateLeaseNumber) {
            $validated['contract_number'] = $this->generateLeaseContractNumber();
        }

        if (($validated['contract_number'] ?? null) === null) {
            abort(422, 'Lease contract number is required.');
        }

        unset($validated['autoGenerateLeaseNumber']);

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

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $sublease->load([
                'tenant',
                'status',
                'leaseUnitType',
                'rentalContractType',
                'paymentSchedule',
                'units.category',
                'units.type',
                'units.status',
                'units.community',
                'units.building',
                'units.city',
                'units.district',
            ]);

            return response()->json([
                'data' => $this->leaseDetails($sublease),
                'message' => __('Sub-lease created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Sub-lease created.')]);

        return to_route('leases.show', $sublease);
    }

    public function storeSubleaseAlias(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Lease::class);

        $validated = $request->validate([
            'rf_lease_id' => ['required', 'integer', 'exists:rf_leases,id'],
        ]);

        $lease = Lease::query()->findOrFail((int) $validated['rf_lease_id']);

        return $this->storeSublease($request, $lease);
    }

    public function edit(Lease $lease): Response
    {
        $this->authorize('update', $lease);

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
    ): JsonResponse|RedirectResponse {
        $this->authorize('update', $lease);

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
            'notes' => ['nullable', 'string'],
            'legal_representative' => ['nullable', 'string', 'max:255'],
            'fit_out_status' => ['nullable', 'string', 'max:255'],
            'number_of_years' => ['nullable', 'integer', 'min:0'],
            'number_of_months' => ['nullable', 'integer', 'min:0'],
            'number_of_days' => ['nullable', 'integer', 'min:0'],
        ]);

        if (array_key_exists('notes', $validated) && ! array_key_exists('terms_conditions', $validated)) {
            $validated['terms_conditions'] = $validated['notes'];
        }

        unset($validated['notes']);

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
                url: $request->routeIs('rf.*')
                    ? route('rf.leases.show', $lease, false)
                    : route('leases.show', $lease, false),
                actor: $request->user()?->name,
            );
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $lease->load([
                'tenant',
                'status',
                'leaseUnitType',
                'rentalContractType',
                'paymentSchedule',
                'units.category',
                'units.type',
                'units.status',
                'units.community',
                'units.building',
                'units.city',
                'units.district',
            ]);

            return response()->json([
                'data' => $this->leaseDetails($lease),
                'message' => __('Lease updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease updated.')]);

        return to_route('leases.show', $lease);
    }

    public function destroy(Request $request, Lease $lease): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $lease);

        $leaseId = $lease->id;
        $lease->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $leaseId,
                ],
                'message' => __('Lease deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease deleted.')]);

        return to_route('leases.index');
    }

    public function destroySublease(Request $request, Lease $lease): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $lease);

        if (! $lease->is_sub_lease) {
            abort(404);
        }

        return $this->destroy($request, $lease);
    }

    private function toBoolean(mixed $value): bool
    {
        $parsed = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($parsed !== null) {
            return $parsed;
        }

        return in_array((string) $value, ['1', 'yes', 'on'], true);
    }

    private function generateLeaseContractNumber(): string
    {
        do {
            $candidate = 'LEASE-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));
        } while (Lease::query()->where('contract_number', $candidate)->exists());

        return $candidate;
    }

    private function leaseStatusIdByNames(array $statusNames): ?int
    {
        $normalizedNames = array_values(array_filter(array_map(
            fn (string $statusName): string => strtolower(trim($statusName)),
            $statusNames,
        )));

        if ($normalizedNames === []) {
            return null;
        }

        return Status::query()
            ->where('type', 'lease')
            ->where(function (Builder $query) use ($normalizedNames): void {
                foreach ($normalizedNames as $index => $statusName) {
                    if ($index === 0) {
                        $query->whereRaw('LOWER(COALESCE(name_en, name)) = ?', [$statusName]);
                    } else {
                        $query->orWhereRaw('LOWER(COALESCE(name_en, name)) = ?', [$statusName]);
                    }
                }
            })
            ->value('id');
    }

    private function normalizedStatusName(?Status $status): string
    {
        if (! $status instanceof Status) {
            return '';
        }

        return strtolower(trim((string) ($status->name_en ?: $status->name)));
    }

    /**
     * @param  array<int, array<string, mixed>>  $units
     */
    private function syncLeaseUnits(Lease $lease, array $units): void
    {
        $syncData = [];

        foreach ($units as $unit) {
            $unitId = isset($unit['id']) ? (int) $unit['id'] : null;

            if ($unitId === null || $unitId <= 0) {
                continue;
            }

            $syncData[$unitId] = [
                'rental_annual_type' => $unit['rental_annual_type'] ?? null,
                'annual_rental_amount' => $unit['annual_rental_amount']
                    ?? $unit['rental_amount']
                    ?? null,
                'net_area' => $unit['net_area'] ?? null,
                'meter_cost' => $unit['meter_cost'] ?? null,
            ];
        }

        if ($syncData !== []) {
            $lease->units()->sync($syncData);
        }
    }

    private function appendLeaseTerm(Lease $lease, string $entry): void
    {
        $currentTerms = trim((string) ($lease->terms_conditions ?? ''));

        $lease->update([
            'terms_conditions' => trim($currentTerms === '' ? $entry : $currentTerms.PHP_EOL.PHP_EOL.$entry),
        ]);
    }

    private function perPage(Request $request): int
    {
        return min(max((int) $request->integer('per_page', 10), 1), 50);
    }

    private function countLeasesByStatusNames(array $statusNames): int
    {
        return Lease::query()
            ->whereHas('status', function (Builder $query) use ($statusNames): void {
                $this->applyStatusNameFilter($query, $statusNames);
            })
            ->count();
    }

    private function applyStatusNameFilter(Builder $query, array $statusNames): void
    {
        $normalizedNames = array_values(array_filter(array_map(
            fn (string $name): string => strtolower(trim($name)),
            $statusNames,
        )));

        $query->where(function (Builder $nestedQuery) use ($normalizedNames): void {
            foreach ($normalizedNames as $index => $statusName) {
                if ($index === 0) {
                    $nestedQuery->whereRaw('LOWER(COALESCE(name_en, name)) = ?', [$statusName]);
                } else {
                    $nestedQuery->orWhereRaw('LOWER(COALESCE(name_en, name)) = ?', [$statusName]);
                }
            }
        });
    }

    private function percentage(int $part, int $whole): float
    {
        if ($whole === 0) {
            return 0;
        }

        return round(($part / $whole) * 100, 2);
    }

    /**
     * @return array<string, mixed>
     */
    private function meta(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'from' => $paginator->firstItem(),
            'last_page' => $paginator->lastPage(),
            'path' => $paginator->path(),
            'per_page' => $paginator->perPage(),
            'to' => $paginator->lastItem(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function leaseListItem(Lease $lease): array
    {
        return [
            'id' => $lease->id,
            'contract_number' => $lease->contract_number,
            'lease_unit_type' => $lease->leaseUnitType
                ? [
                    'id' => $lease->leaseUnitType->id,
                    'name' => $lease->leaseUnitType->name,
                    'icon' => $lease->leaseUnitType->icon,
                ]
                : null,
            'tenant' => $lease->tenant
                ? [
                    'id' => $lease->tenant->id,
                    'name' => trim(($lease->tenant->first_name ?? '').' '.($lease->tenant->last_name ?? '')),
                    'email' => $lease->tenant->email,
                    'phone_number' => $lease->tenant->phone_country_code.$lease->tenant->phone_number,
                    'national_id' => $lease->tenant->national_id,
                    'active' => $lease->tenant->active ? '1' : '0',
                    'role' => 'Tenant',
                ]
                : null,
            'units' => $lease->units->map(fn (Unit $unit): array => [
                'id' => $unit->id,
                'name' => $unit->name,
                'status' => $unit->status
                    ? [
                        'id' => $unit->status->id,
                        'name_ar' => $unit->status->name_ar,
                        'name_en' => $unit->status->name_en,
                    ]
                    : null,
                'map' => $unit->map,
            ])->values()->all(),
            'status' => $lease->status
                ? [
                    'id' => $lease->status->id,
                    'name_ar' => $lease->status->name_ar,
                    'name_en' => $lease->status->name_en,
                ]
                : null,
            'start_date' => $lease->start_date?->toDateString(),
            'end_date' => $lease->end_date?->toDateString(),
            'handover_date' => $lease->handover_date?->toDateString(),
            'created_at' => $lease->created_at?->toJSON(),
            'updated_at' => $lease->updated_at?->toJSON(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function leaseDetails(Lease $lease): array
    {
        return [
            'id' => $lease->id,
            'contract_number' => $lease->contract_number,
            'tenant' => ($lease->tenant
                ? [
                    'id' => $lease->tenant->id,
                    'name' => trim(($lease->tenant->first_name ?? '').' '.($lease->tenant->last_name ?? '')),
                    'email' => $lease->tenant->email,
                    'phone_number' => $lease->tenant->phone_country_code.$lease->tenant->phone_number,
                    'national_id' => $lease->tenant->national_id,
                    'active' => $lease->tenant->active ? '1' : '0',
                    'role' => 'Tenant',
                    'image' => $lease->tenant->image,
                    'gender' => $lease->tenant->gender,
                    'birthdate' => $lease->tenant->georgian_birthdate?->toDateString(),
                    'company' => null,
                    'nationality' => null,
                    'primary_user_name' => trim(($lease->tenant->first_name ?? '').' '.($lease->tenant->last_name ?? '')),
                    'rate' => null,
                ]
                : null),
            'units' => $lease->units->map(fn (Unit $unit): array => [
                'rental_annual_type' => $unit->pivot?->rental_annual_type,
                'annual_rental_amount' => (string) ($unit->pivot?->annual_rental_amount ?? ''),
                'unit' => [
                    'id' => $unit->id,
                    'name' => $unit->name,
                    'category' => $unit->category
                        ? [
                            'id' => $unit->category->id,
                            'name' => $unit->category->name,
                            'name_ar' => $unit->category->name_ar,
                            'name_en' => $unit->category->name_en,
                        ]
                        : null,
                    'type' => $unit->type
                        ? [
                            'id' => $unit->type->id,
                            'name' => $unit->type->name,
                            'name_ar' => $unit->type->name_ar,
                            'name_en' => $unit->type->name_en,
                        ]
                        : null,
                    'status' => $unit->status
                        ? [
                            'id' => $unit->status->id,
                            'name' => $unit->status->name,
                            'name_ar' => $unit->status->name_ar,
                            'name_en' => $unit->status->name_en,
                        ]
                        : null,
                    'rf_community' => $unit->community
                        ? [
                            'id' => $unit->community->id,
                            'name' => $unit->community->name,
                        ]
                        : null,
                    'rf_building' => $unit->building
                        ? [
                            'id' => $unit->building->id,
                            'name' => $unit->building->name,
                        ]
                        : null,
                    'year_build' => $unit->year_build,
                    'net_area' => $unit->net_area,
                    'rooms' => [],
                    'areas' => [],
                    'city' => $unit->city
                        ? [
                            'id' => $unit->city->id,
                            'name' => $unit->city->name,
                            'name_ar' => $unit->city->name_ar,
                            'name_en' => $unit->city->name_en,
                        ]
                        : null,
                    'district' => $unit->district
                        ? [
                            'id' => $unit->district->id,
                            'name' => $unit->district->name,
                            'name_ar' => $unit->district->name_ar,
                            'name_en' => $unit->district->name_en,
                        ]
                        : null,
                    'is_market_place' => $unit->is_market_place ? '1' : '0',
                    'is_buy' => $unit->is_buy ? '1' : '0',
                    'is_off_plan_sale' => $unit->is_off_plan_sale ? '1' : '0',
                    'map' => $unit->map,
                ],
            ])->values()->all(),
            'lease_unit_type' => $lease->leaseUnitType
                ? [
                    'id' => $lease->leaseUnitType->id,
                    'name' => $lease->leaseUnitType->name,
                    'icon' => $lease->leaseUnitType->icon,
                ]
                : null,
            'status' => $lease->status
                ? [
                    'id' => $lease->status->id,
                    'name' => $lease->status->name,
                    'name_ar' => $lease->status->name_ar,
                    'name_en' => $lease->status->name_en,
                ]
                : null,
            'rental_contract_type' => $lease->rentalContractType
                ? [
                    'id' => $lease->rentalContractType->id,
                    'name' => $lease->rentalContractType->name,
                    'name_ar' => $lease->rentalContractType->name_ar,
                    'name_en' => $lease->rentalContractType->name_en,
                ]
                : null,
            'payment_schedule' => $lease->paymentSchedule
                ? [
                    'id' => $lease->paymentSchedule->id,
                    'name' => $lease->paymentSchedule->name,
                    'name_ar' => $lease->paymentSchedule->name_ar,
                    'name_en' => $lease->paymentSchedule->name_en,
                    'parent_id' => $lease->paymentSchedule->parent_id,
                ]
                : null,
            'start_date' => $lease->start_date?->toDateString(),
            'end_date' => $lease->end_date?->toDateString(),
            'handover_date' => $lease->handover_date?->toDateString(),
            'rental_total_amount' => $lease->rental_total_amount,
            'security_deposit_amount' => $lease->security_deposit_amount,
            'terms_conditions' => $lease->terms_conditions,
            'created_at' => $lease->created_at?->toJSON(),
            'updated_at' => $lease->updated_at?->toJSON(),
        ];
    }
}
