<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    /**
     * Display transactions page.
     */
    public function index(Request $request): Response
    {
        $filters = $this->validatedFilters($request);

        $transactions = $this->buildTransactionsQuery($request, $filters)
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Transaction $transaction): array => [
                'id' => $transaction->id,
                'amount' => (float) $transaction->amount,
                'paid' => (float) $transaction->paid,
                'left' => (float) $transaction->left,
                'direction' => (float) $transaction->amount < 0 ? 'money_out' : 'money_in',
                'details' => $transaction->details,
                'lease_number' => $transaction->lease_number,
                'is_paid' => $transaction->is_paid,
                'due_on' => $transaction->due_on?->toDateString(),
                'created_at' => $transaction->created_at?->toDateTimeString(),
                'type' => $transaction->type?->name,
                'category' => $transaction->category?->name,
                'status' => $transaction->status?->name,
                'assignee' => $transaction->assignee?->name,
            ]);

        return Inertia::render('transactions/index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'tabs' => [
                ['name' => 'All', 'filter_type' => 'all'],
                ['name' => 'Money In', 'filter_type' => 'money_in'],
                ['name' => 'Money Out', 'filter_type' => 'money_out'],
            ],
            'currency' => 'SAR',
        ]);
    }

    /**
     * Return transactions for API clients.
     */
    public function list(Request $request): JsonResponse
    {
        $filters = $this->validatedFilters($request);
        $perPage = (int) $request->integer('per_page', 15);

        $transactions = $this->buildTransactionsQuery($request, $filters)
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'transactions' => $transactions,
            'filters' => $filters,
        ]);
    }

    /**
     * @return array{filter_type: string, from: string, to: string, search: string}
     */
    protected function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'filter_type' => ['nullable', Rule::in(['all', 'money_in', 'money_out'])],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        return [
            'filter_type' => $validated['filter_type'] ?? 'all',
            'from' => $validated['from'] ?? '2021-01-01',
            'to' => $validated['to'] ?? now()->toDateString(),
            'search' => trim((string) ($validated['search'] ?? '')),
        ];
    }

    /**
     * @param  array{filter_type: string, from: string, to: string, search: string}  $filters
     */
    protected function buildTransactionsQuery(Request $request, array $filters): Builder
    {
        $query = Transaction::query()
            ->with([
                'type:id,name',
                'category:id,name',
                'status:id,name',
                'assignee:id,first_name,last_name',
            ])
            ->forTenant($request->user()?->tenant_id)
            ->whereDate('created_at', '>=', $filters['from'])
            ->whereDate('created_at', '<=', $filters['to']);

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('lease_number', 'like', "%{$search}%")
                    ->orWhere('details', 'like', "%{$search}%");
            });
        }

        if ($filters['filter_type'] === 'money_in') {
            $query->where('amount', '>=', 0);
        }

        if ($filters['filter_type'] === 'money_out') {
            $query->where('amount', '<', 0);
        }

        return $query;
    }
}
