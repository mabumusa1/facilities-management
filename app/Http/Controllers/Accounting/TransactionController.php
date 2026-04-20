<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $transactions = Transaction::query()
            ->with(['lease', 'unit', 'status'])
            ->latest()
            ->paginate(15);

        return Inertia::render('accounting/transactions/Index', [
            'transactions' => $transactions,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('accounting/transactions/Create');
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
            'due_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $transaction = Transaction::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Transaction created.')]);

        return to_route('transactions.show', $transaction);
    }

    public function show(Transaction $transaction): Response
    {
        $transaction->load(['lease', 'unit', 'status', 'payments']);

        return Inertia::render('accounting/transactions/Show', [
            'transaction' => $transaction,
        ]);
    }

    public function edit(Transaction $transaction): Response
    {
        return Inertia::render('accounting/transactions/Edit', [
            'transaction' => $transaction,
        ]);
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'status_id' => ['sometimes', 'integer', 'exists:rf_statuses,id'],
            'amount' => ['sometimes', 'numeric', 'min:0'],
            'due_date' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

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
