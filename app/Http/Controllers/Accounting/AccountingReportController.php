<?php

namespace App\Http\Controllers\Accounting;

use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AccountingReportController extends Controller
{
    // -------------------------------------------------------------------------
    // #191 Payment recording
    // -------------------------------------------------------------------------

    public function recordPayment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'transaction_id' => ['required', 'integer', 'exists:rf_transactions,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $transaction = Transaction::findOrFail($validated['transaction_id']);

        $payment = Payment::create([
            'account_tenant_id' => $transaction->account_tenant_id,
            'transaction_id' => $transaction->id,
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'] ?? 'bank_transfer',
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $totalPaid = $transaction->payments()->sum('amount');
        $transaction->update([
            'amount' => $totalPaid,
            'is_paid' => $totalPaid >= $transaction->amount ? 1 : 0,
        ]);

        return response()->json([
            'data' => ['id' => $payment->id, 'amount' => $payment->amount],
            'message' => 'Payment recorded.',
        ]);
    }

    // -------------------------------------------------------------------------
    // #193 Aging report
    // -------------------------------------------------------------------------

    public function agingReport(Request $request): JsonResponse
    {
        $asOf = $request->input('as_of') ?? now()->toDateString();

        $transactions = Transaction::query()
            ->whereNull('is_reconciled')
            ->get();

        $aging = [
            'current' => ['count' => 0, 'total' => 0],
            '1_30' => ['count' => 0, 'total' => 0],
            '31_60' => ['count' => 0, 'total' => 0],
            '61_90' => ['count' => 0, 'total' => 0],
            'over_90' => ['count' => 0, 'total' => 0],
        ];

        foreach ($transactions as $t) {
            $dueDate = $t->due_on ?? $t->created_at->toDateString();
            $daysOverdue = (int) now()->parse($asOf)->diffInDays($dueDate, false);

            $remaining = (float) $t->amount;

            if ($remaining <= 0) {
                continue;
            }

            if ($daysOverdue <= 0) {
                $bucket = 'current';
            } elseif ($daysOverdue <= 30) {
                $bucket = '1_30';
            } elseif ($daysOverdue <= 60) {
                $bucket = '31_60';
            } elseif ($daysOverdue <= 90) {
                $bucket = '61_90';
            } else {
                $bucket = 'over_90';
            }

            $aging[$bucket]['count']++;
            $aging[$bucket]['total'] += $remaining;
        }

        return response()->json(['data' => $aging]);
    }

    // -------------------------------------------------------------------------
    // #195 Bank accounts CRUD
    // -------------------------------------------------------------------------

    public function bankAccounts(Request $request): JsonResponse
    {
        $accounts = BankAccount::query()
            ->with('community:id,name')
            ->when($request->input('community_id'), fn ($q) => $q->where('community_id', (int) $request->input('community_id')))
            ->orderBy('is_default', 'desc')
            ->orderBy('bank_name')
            ->get();

        return response()->json(['data' => $accounts]);
    }

    public function storeBankAccount(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:30'],
            'iban' => ['nullable', 'string', 'max:34'],
            'currency' => ['nullable', 'string', 'max:3'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (! empty($validated['is_default'])) {
            BankAccount::query()->update(['is_default' => false]);
        }

        $account = BankAccount::create($validated);

        return response()->json(['data' => $account, 'message' => 'Bank account added.']);
    }

    public function updateBankAccount(Request $request, BankAccount $bankAccount): JsonResponse
    {
        $validated = $request->validate([
            'bank_name' => ['sometimes', 'string', 'max:255'],
            'account_name' => ['sometimes', 'string', 'max:255'],
            'account_number' => ['sometimes', 'string', 'max:30'],
            'iban' => ['nullable', 'string', 'max:34'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (! empty($validated['is_default'])) {
            BankAccount::query()->whereKeyNot($bankAccount->id)->update(['is_default' => false]);
        }

        $bankAccount->update($validated);

        return response()->json(['data' => $bankAccount, 'message' => 'Bank account updated.']);
    }

    public function destroyBankAccount(BankAccount $bankAccount): JsonResponse
    {
        $bankAccount->delete();

        return response()->json(['message' => 'Bank account deleted.']);
    }

    // -------------------------------------------------------------------------
    // #196 Reconciliation
    // -------------------------------------------------------------------------

    public function reconcile(Transaction $transaction, Request $request): JsonResponse
    {
        if ($transaction->is_reconciled) {
            throw ValidationException::withMessages([
                'transaction' => 'Already reconciled.',
            ]);
        }

        $transaction->update([
            'is_reconciled' => true,
            'reconciled_at' => now(),
            'reconciled_by' => $request->user()?->id,
        ]);

        return response()->json(['message' => 'Transaction reconciled.']);
    }

    public function reconciliationSummary(Request $request): JsonResponse
    {
        $total = Transaction::query()->count();
        $reconciled = Transaction::query()->where('is_reconciled', true)->count();

        return response()->json([
            'data' => [
                'total_transactions' => $total,
                'reconciled' => $reconciled,
                'unreconciled' => $total - $reconciled,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // #197 Export to Excel
    // -------------------------------------------------------------------------

    public function export(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['direction', 'category_id', 'from', 'to', 'search']);

        return Excel::download(
            new TransactionsExport($filters),
            'transactions-'.now()->format('Y-m-d-His').'.xlsx'
        );
    }

    // -------------------------------------------------------------------------
    // #198 Financial summary
    // -------------------------------------------------------------------------

    public function financialSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        $query = Transaction::query()
            ->when($validated['from'] ?? null, fn ($q) => $q->whereDate('created_at', '>=', $validated['from']))
            ->when($validated['to'] ?? null, fn ($q) => $q->whereDate('created_at', '<=', $validated['to']));

        $income = (clone $query)->where('direction', 'money_in')->sum('amount');
        $expense = (clone $query)->where('direction', 'money_out')->sum('amount');

        return response()->json([
            'data' => [
                'total_income' => (float) $income,
                'total_expense' => (float) $expense,
                'net' => (float) ($income - $expense),
                'transaction_count' => (clone $query)->count(),
            ],
        ]);
    }
}
