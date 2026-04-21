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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $transactions = Transaction::query()
            ->with(['lease', 'unit', 'status', 'category', 'subcategory', 'type'])
            ->latest()
            ->paginate(15);

        return Inertia::render('accounting/transactions/Index', [
            'transactions' => $transactions,
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

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'status_id' => ['sometimes', 'integer', 'exists:rf_statuses,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'due_date' => ['sometimes', 'date'],
            'due_on' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
            'details' => ['nullable', 'string'],
        ]);

        if (array_key_exists('due_date', $validated) && ! array_key_exists('due_on', $validated)) {
            $validated['due_on'] = $validated['due_date'];
        }

        if (array_key_exists('notes', $validated) && ! array_key_exists('details', $validated)) {
            $validated['details'] = $validated['notes'];
        }

        unset($validated['due_date'], $validated['notes']);

        $transaction->update($validated);

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
