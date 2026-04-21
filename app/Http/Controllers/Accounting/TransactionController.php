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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
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

    public function store(Request $request): RedirectResponse
    {
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

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction created.')]);

        return to_route('transactions.show', $transaction);
    }

    public function show(Transaction $transaction): Response
    {
        $transaction->load(['lease', 'unit', 'status', 'payments', 'category', 'subcategory', 'type']);

        return Inertia::render('accounting/transactions/Show', [
            'transaction' => $transaction,
        ]);
    }

    public function edit(Transaction $transaction): Response
    {
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
    ): RedirectResponse {
        $validated = $request->validate([
            'status_id' => ['sometimes', 'integer', 'exists:rf_statuses,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'due_date' => ['sometimes', 'date'],
            'due_on' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
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

        unset($validated['due_date'], $validated['notes']);

        $transaction->update($validated);

        if ($toStatus instanceof Status) {
            $workflowNotifier->notifyTenantUsers(
                tenantId: (int) ($request->session()->get('tenant_id') ?: $transaction->account_tenant_id),
                module: 'transaction',
                resourceId: $transaction->id,
                fromStatus: $fromStatus?->name_en ?? $fromStatus?->name,
                toStatus: $toStatus->name_en ?? $toStatus->name ?? (string) $toStatus->id,
                url: route('transactions.show', $transaction, false),
                actor: $request->user()?->name,
            );
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction updated.')]);

        return to_route('transactions.show', $transaction);
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction deleted.')]);

        return to_route('transactions.index');
    }
}
