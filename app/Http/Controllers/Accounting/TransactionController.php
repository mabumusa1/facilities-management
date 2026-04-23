<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Lease;
use App\Models\Owner;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\Unit;
use App\Support\StatusWorkflow;
use App\Support\WorkflowNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $this->authorize('viewAny', Transaction::class);

        $search = trim((string) $request->input('search', ''));
        $statusId = $request->input('status_id');
        $categoryId = $request->input('category_id');
        $isPaid = $request->input('is_paid');
        $perPage = min(max((int) $request->integer('per_page', 15), 5), 50);

        $transactions = Transaction::query()
            ->with(['lease', 'unit', 'status', 'category', 'subcategory', 'type'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nestedQuery) use ($search): void {
                    $nestedQuery->where('details', 'like', "%{$search}%")
                        ->orWhere('lease_number', 'like', "%{$search}%")
                        ->orWhereHas('lease', fn ($leaseQuery) => $leaseQuery->where('contract_number', 'like', "%{$search}%"))
                        ->orWhereHas('unit', fn ($unitQuery) => $unitQuery->where('name', 'like', "%{$search}%"));

                    if (is_numeric($search)) {
                        $nestedQuery->orWhere('id', (int) $search);
                    }
                });
            })
            ->when($statusId, fn ($query) => $query->where('status_id', (int) $statusId))
            ->when($categoryId, fn ($query) => $query->where('category_id', (int) $categoryId))
            ->when($isPaid !== null && $isPaid !== '', fn ($query) => $query->where('is_paid', (bool) $isPaid))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            return response()->json([
                'data' => collect($transactions->items())->map(fn (Transaction $transaction): array => [
                    'id' => $transaction->id,
                    'amount' => $transaction->amount,
                    'tax_amount' => $transaction->tax_amount,
                    'is_paid' => $transaction->is_paid ? '1' : '0',
                    'due_on' => $transaction->due_on?->toDateString(),
                    'details' => $transaction->details,
                    'lease' => $transaction->lease
                        ? [
                            'id' => $transaction->lease->id,
                            'contract_number' => $transaction->lease->contract_number,
                        ]
                        : null,
                    'unit' => $transaction->unit
                        ? [
                            'id' => $transaction->unit->id,
                            'name' => $transaction->unit->name,
                        ]
                        : null,
                    'status' => $transaction->status
                        ? [
                            'id' => $transaction->status->id,
                            'name' => $transaction->status->name_en ?? $transaction->status->name,
                        ]
                        : null,
                    'category' => $transaction->category
                        ? [
                            'id' => $transaction->category->id,
                            'name' => $transaction->category->name_en ?? $transaction->category->name,
                        ]
                        : null,
                    'subcategory' => $transaction->subcategory
                        ? [
                            'id' => $transaction->subcategory->id,
                            'name' => $transaction->subcategory->name_en ?? $transaction->subcategory->name,
                        ]
                        : null,
                    'type' => $transaction->type
                        ? [
                            'id' => $transaction->type->id,
                            'name' => $transaction->type->name_en ?? $transaction->type->name,
                        ]
                        : null,
                    'created_at' => $transaction->created_at?->toJSON(),
                    'updated_at' => $transaction->updated_at?->toJSON(),
                ]),
                'meta' => $this->meta($transactions),
            ]);
        }

        return Inertia::render('accounting/transactions/Index', [
            'transactions' => $transactions,
            'statuses' => Status::query()
                ->where('type', 'invoice')
                ->select('id', 'name', 'name_en')
                ->orderBy('priority')
                ->orderBy('id')
                ->get(),
            'transactionCategories' => Setting::query()
                ->where('type', 'transaction_category')
                ->select('id', 'name', 'name_en')
                ->orderByRaw('COALESCE(name_en, name) asc')
                ->get(),
            'filters' => [
                'search' => $search,
                'status_id' => $statusId ? (string) $statusId : '',
                'category_id' => $categoryId ? (string) $categoryId : '',
                'is_paid' => $isPaid !== null && $isPaid !== '' ? (string) (int) ((bool) $isPaid) : '',
                'per_page' => (string) $perPage,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Transaction::class);

        return Inertia::render('accounting/transactions/Create', [
            'leases' => Lease::select('id', 'contract_number')->orderBy('contract_number')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
            'statuses' => Status::where('type', 'invoice')->select('id', 'name', 'name_en')->get(),
            'tenants' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'owners' => Owner::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'transactionCategories' => Setting::where('type', 'transaction_category')->select('id', 'name', 'name_en')->get(),
            'transactionSubcategories' => Setting::where('type', 'transaction_subcategory')->select('id', 'name', 'name_en', 'parent_id')->get(),
            'transactionTypes' => Setting::where('type', 'transaction_type')->select('id', 'name', 'name_en')->get(),
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->authorize('create', Transaction::class);

        $validated = $request->validate([
            'lease_id' => ['nullable', 'integer', 'exists:rf_leases,id'],
            'unit_id' => ['nullable', 'integer', 'exists:rf_units,id'],
            'category_id' => ['required', 'integer'],
            'type_id' => ['required', 'integer'],
            'status_id' => ['required', 'integer', 'exists:rf_statuses,id'],
            'assignee_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date', 'required_without:due_on'],
            'due_on' => ['nullable', 'date', 'required_without:due_date'],
            'notes' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        $validated['due_on'] = $validated['due_on'] ?? $validated['due_date'] ?? null;
        $validated['details'] = $validated['details'] ?? $validated['notes'] ?? null;

        unset($validated['due_date'], $validated['notes']);

        $transaction = Transaction::create($validated);

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $transaction->load([
                'lease',
                'unit',
                'status',
                'payments',
                'category',
                'subcategory',
                'type',
                'additionalFees',
                'assignee',
                'images',
            ]);

            return response()->json([
                'data' => $this->rfTransactionPayload($transaction),
                'message' => __('Transaction created.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction created.')]);

        return to_route('transactions.show', $transaction);
    }

    public function show(Transaction $transaction): Response
    {
        $this->authorize('view', $transaction);

        $transaction->load(['lease', 'unit', 'status', 'payments', 'category', 'subcategory', 'type']);

        return Inertia::render('accounting/transactions/Show', [
            'transaction' => $transaction,
        ]);
    }

    public function rfShow(Transaction $transaction): JsonResponse
    {
        $transaction->load(['lease', 'unit', 'status', 'payments', 'category', 'subcategory', 'type', 'additionalFees', 'assignee', 'images']);

        return response()->json([
            'data' => $this->rfTransactionPayload($transaction),
            'message' => __('Transaction retrieved.'),
        ]);
    }

    public function edit(Transaction $transaction): Response
    {
        $this->authorize('update', $transaction);

        return Inertia::render('accounting/transactions/Edit', [
            'transaction' => $transaction->load(['lease', 'unit', 'status']),
            'leases' => Lease::select('id', 'contract_number')->orderBy('contract_number')->get(),
            'units' => Unit::select('id', 'name')->orderBy('name')->get(),
            'statuses' => Status::where('type', 'invoice')->select('id', 'name', 'name_en')->get(),
            'tenants' => Resident::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'owners' => Owner::select('id', 'first_name', 'last_name')->orderBy('first_name')->get(),
            'transactionCategories' => Setting::where('type', 'transaction_category')->select('id', 'name', 'name_en')->get(),
            'transactionSubcategories' => Setting::where('type', 'transaction_subcategory')->select('id', 'name', 'name_en', 'parent_id')->get(),
            'transactionTypes' => Setting::where('type', 'transaction_type')->select('id', 'name', 'name_en')->get(),
        ]);
    }

    public function update(
        Request $request,
        Transaction $transaction,
        StatusWorkflow $statusWorkflow,
        WorkflowNotifier $workflowNotifier,
    ): JsonResponse|RedirectResponse {
        $this->authorize('update', $transaction);

        $validated = $request->validate([
            'status_id' => ['sometimes', 'integer', 'exists:rf_statuses,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'due_date' => ['sometimes', 'date'],
            'due_on' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $nextStatusId = array_key_exists('status_id', $validated)
            ? (int) $validated['status_id']
            : null;

        $fromStatus = null;
        $toStatus = null;

        if ($nextStatusId !== null && $nextStatusId !== (int) $transaction->status_id) {
            $fromStatus = Status::query()->find($transaction->status_id);
            $statusWorkflow->ensureTransition('invoice', $transaction->status_id, $nextStatusId);
            $toStatus = Status::query()->find($nextStatusId);
        }

        if (array_key_exists('due_date', $validated) && ! array_key_exists('due_on', $validated)) {
            $validated['due_on'] = $validated['due_date'];
        }

        if (array_key_exists('notes', $validated) && ! array_key_exists('details', $validated)) {
            $validated['details'] = $validated['notes'];
        }

        if (array_key_exists('description', $validated) && ! array_key_exists('details', $validated)) {
            $validated['details'] = $validated['description'];
        }

        unset($validated['due_date'], $validated['notes'], $validated['description']);

        $transaction->update($validated);

        if ($toStatus instanceof Status) {
            $workflowNotifier->notifyTenantUsers(
                tenantId: (int) ($request->session()->get('tenant_id') ?: $transaction->account_tenant_id),
                module: 'transaction',
                resourceId: $transaction->id,
                fromStatus: $fromStatus?->name_en ?? $fromStatus?->name,
                toStatus: $toStatus->name_en ?? $toStatus->name ?? (string) $toStatus->id,
                url: $request->routeIs('rf.*')
                    ? route('rf.transactions.show', $transaction, false)
                    : route('transactions.show', $transaction, false),
                actor: $request->user()?->name,
            );
        }

        if ($request->expectsJson() || $request->routeIs('rf.*')) {
            $transaction->load(['lease', 'unit', 'status', 'payments', 'category', 'subcategory', 'type', 'additionalFees', 'assignee', 'images']);

            return response()->json([
                'data' => $this->rfTransactionPayload($transaction),
                'message' => __('Transaction updated.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction updated.')]);

        return to_route('transactions.show', $transaction);
    }

    public function destroy(Request $request, Transaction $transaction): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $transactionId = $transaction->id;
        $transaction->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => [
                    'id' => $transactionId,
                ],
                'message' => __('Transaction deleted.'),
            ]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction deleted.')]);

        return to_route('transactions.index');
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
    private function rfTransactionPayload(Transaction $transaction): array
    {
        $paidAmount = (float) $transaction->payments->sum('amount');
        $leftAmount = max((float) $transaction->amount - $paidAmount, 0);

        return [
            'id' => $transaction->id,
            'images' => $transaction->images->map(fn ($image): array => [
                'id' => $image->id,
                'name' => $image->name,
            ])->values()->all(),
            'payments' => $transaction->payments->map(fn ($payment): array => [
                'id' => $payment->id,
                'amount' => $payment->amount,
            ])->values()->all(),
            'unit' => $transaction->unit
                ? [
                    'id' => $transaction->unit->id,
                    'name' => $transaction->unit->name,
                ]
                : null,
            'amount' => (float) $transaction->amount,
            'tax_amount' => number_format((float) $transaction->tax_amount, 2, '.', ''),
            'rental_amount' => number_format((float) $transaction->rental_amount, 2, '.', ''),
            'additional_fees_amount' => number_format((float) $transaction->additional_fees_amount, 2, '.', ''),
            'vat' => number_format((float) $transaction->vat, 2, '.', ''),
            'lease_number' => $transaction->lease_number,
            'additional_fees' => $transaction->additionalFees->map(fn ($additionalFee): array => [
                'id' => $additionalFee->id,
                'name' => $additionalFee->name,
                'amount' => $additionalFee->amount,
            ])->values()->all(),
            'amount_fmt' => number_format((float) $transaction->amount, 2, '.', ','),
            'category' => $transaction->category
                ? [
                    'id' => (string) $transaction->category->id,
                    'name' => $transaction->category->name_en ?? $transaction->category->name,
                ]
                : ['id' => null, 'name' => ''],
            'subcategory' => $transaction->subcategory
                ? [
                    'id' => $transaction->subcategory->id,
                    'name' => $transaction->subcategory->name_en ?? $transaction->subcategory->name,
                ]
                : ['id' => null, 'name' => ''],
            'due_on' => $transaction->due_on?->toDateString(),
            'assignee' => $transaction->assignee?->name
                ?? trim(((string) ($transaction->assignee?->first_name ?? '')).' '.((string) ($transaction->assignee?->last_name ?? '')))
                ?: null,
            'assignee_id' => $transaction->assignee_id,
            'assignee_active' => $transaction->assignee?->active !== null
                ? (((bool) $transaction->assignee?->active) ? '1' : '0')
                : null,
            'details' => $transaction->details,
            'payments_sum' => number_format($paidAmount, 2, '.', ''),
            'paid_fmt' => number_format($paidAmount, 2, '.', ''),
            'left_fmt' => number_format($leftAmount, 2, '.', ''),
            'paid' => $paidAmount,
            'left' => $leftAmount,
            'type' => $transaction->type
                ? [
                    'id' => $transaction->type->id,
                    'name' => $transaction->type->name_en ?? $transaction->type->name,
                ]
                : ['id' => null, 'name' => ''],
            'status' => $transaction->status
                ? [
                    'id' => $transaction->status->id,
                    'name' => $transaction->status->name_en ?? $transaction->status->name,
                ]
                : ['id' => null, 'name' => ''],
            'is_paid' => (bool) $transaction->is_paid,
            'created_at' => $transaction->created_at?->toDateString(),
            'is_old' => $transaction->is_old ? '1' : '0',
        ];
    }
}
