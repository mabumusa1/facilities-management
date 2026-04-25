<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ReportsController extends Controller
{
    public function reports(Request $request): Response
    {
        Gate::authorize('reports.VIEW');

        return Inertia::render('reports/Index', [
            'reportMode' => 'reports',
            'title' => 'Reports',
        ]);
    }

    public function systemReports(Request $request): Response
    {
        Gate::authorize('reports.VIEW');

        return Inertia::render('reports/Index', [
            'reportMode' => 'system-reports',
            'title' => 'System Reports',
        ]);
    }

    public function leaseReports(Request $request): Response
    {
        Gate::authorize('reports.VIEW');

        return Inertia::render('reports/Index', [
            'reportMode' => 'system-reports-lease',
            'title' => 'Lease Reports',
        ]);
    }

    public function maintenanceReports(Request $request): Response
    {
        Gate::authorize('reports.VIEW');

        return Inertia::render('reports/Index', [
            'reportMode' => 'system-reports-maintenance',
            'title' => 'Maintenance Reports',
        ]);
    }

    public function load(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return $this->jsonOk('Report loaded.', [
            'reportId' => (string) $request->input('reportId', 'default-report'),
        ]);
    }

    public function prepare(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return $this->jsonOk('Report prepared.', [
            'prepared' => true,
        ]);
    }

    public function renderReport(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return $this->jsonOk('Report rendered.', [
            'rendered' => true,
            'token' => sha1((string) $request->user()?->id.'|render'),
        ]);
    }

    public function pages(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return response()->json([
            'data' => [
                ['name' => 'Overview', 'display_name' => 'Overview', 'is_active' => true],
                ['name' => 'Collections', 'display_name' => 'Collections', 'is_active' => false],
                ['name' => 'Performance', 'display_name' => 'Performance', 'is_active' => false],
            ],
        ]);
    }

    public function activePage(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return response()->json([
            'data' => [
                'name' => 'Overview',
                'display_name' => 'Overview',
            ],
        ]);
    }

    public function filters(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return response()->json([
            'data' => [
                ['name' => 'community', 'value' => null],
                ['name' => 'building', 'value' => null],
                ['name' => 'date_range', 'value' => null],
            ],
        ]);
    }

    public function bookmarks(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return response()->json([
            'data' => [],
        ]);
    }

    public function settings(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return response()->json([
            'data' => [
                'allow_export' => true,
                'allow_print' => true,
                'default_theme' => 'light',
            ],
        ]);
    }

    public function print(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return $this->jsonOk('Print queued.', ['queued' => true]);
    }

    public function refresh(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return $this->jsonOk('Report refreshed.', ['refreshed' => true]);
    }

    public function save(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        return $this->jsonOk('Report saved.', ['saved' => true]);
    }

    public function saveAs(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        return $this->jsonOk('Report saved as new copy.', [
            'name' => $validated['name'] ?? 'Untitled Copy',
        ]);
    }

    public function theme(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        $validated = $request->validate([
            'theme' => ['nullable', 'string', 'max:50'],
        ]);

        return $this->jsonOk('Theme applied.', [
            'theme' => $validated['theme'] ?? 'light',
        ]);
    }

    public function zoom(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        $validated = $request->validate([
            'value' => ['nullable', 'numeric', 'min:0.25', 'max:4'],
        ]);

        return $this->jsonOk('Zoom updated.', [
            'value' => (float) ($validated['value'] ?? 1),
        ]);
    }

    public function expenses(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        $transactions = Transaction::query()
            ->with(['category:id,name,name_ar,name_en'])
            ->select(['id', 'category_id', 'amount', 'is_paid'])
            ->get();

        $expenses = $transactions->filter(fn (Transaction $transaction): bool => $this->isExpenseTransaction($transaction));

        return response()->json([
            'success' => true,
            'data' => $this->transactionSummary($expenses),
            'message' => 'تمت العمليه بنجاح.',
        ]);
    }

    public function income(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        $transactions = Transaction::query()
            ->with(['category:id,name,name_ar,name_en'])
            ->select(['id', 'category_id', 'amount', 'is_paid'])
            ->get();

        $income = $transactions->reject(fn (Transaction $transaction): bool => $this->isExpenseTransaction($transaction));

        return response()->json([
            'success' => true,
            'data' => $this->transactionSummary($income),
            'message' => 'تمت العمليه بنجاح.',
        ]);
    }

    public function performanceUnits(Request $request): JsonResponse
    {
        Gate::authorize('reports.VIEW');

        $units = Unit::query()
            ->with(['status:id,name,name_en'])
            ->select(['id', 'status_id', 'tenant_id', 'is_buy'])
            ->get();

        $counts = [
            'vacant' => 0,
            'sold' => 0,
            'leased' => 0,
            'soldAndLeased' => 0,
        ];

        foreach ($units as $unit) {
            $bucket = $this->performanceBucket($unit);
            $counts[$bucket] += 1;
        }

        return response()->json([
            'success' => true,
            'data' => $counts,
            'message' => 'تمت العمليه بنجاح.',
        ]);
    }

    private function performanceBucket(Unit $unit): string
    {
        $statusName = Str::lower(trim((string) ($unit->status?->name_en ?? $unit->status?->name ?? '')));

        $hasSold = $statusName !== ''
            && (str_contains($statusName, 'sold') || str_contains($statusName, 'بيع'));

        $hasLeased = $statusName !== ''
            && (
                str_contains($statusName, 'leased')
                || str_contains($statusName, 'lease')
                || str_contains($statusName, 'rent')
                || str_contains($statusName, 'إيجار')
                || str_contains($statusName, 'ايجار')
                || str_contains($statusName, 'مؤجر')
            );

        if ($hasSold && $hasLeased) {
            return 'soldAndLeased';
        }

        if ($hasSold) {
            return 'sold';
        }

        if ($hasLeased) {
            return 'leased';
        }

        if ($unit->tenant_id !== null && $unit->is_buy) {
            return 'soldAndLeased';
        }

        if ($unit->tenant_id !== null) {
            return 'leased';
        }

        if ($unit->is_buy) {
            return 'sold';
        }

        return 'vacant';
    }

    private function isExpenseTransaction(Transaction $transaction): bool
    {
        $categoryName = Str::lower(trim((string) ($transaction->category?->name_en ?? $transaction->category?->name ?? $transaction->category?->name_ar ?? '')));

        if ($categoryName === '') {
            return false;
        }

        return str_contains($categoryName, 'expense')
            || str_contains($categoryName, 'cost')
            || str_contains($categoryName, 'fee')
            || str_contains($categoryName, 'maintenance')
            || str_contains($categoryName, 'مصروف')
            || str_contains($categoryName, 'تكلفة')
            || str_contains($categoryName, 'رسوم');
    }

    /**
     * @param  Collection<int, Transaction>  $transactions
     * @return array<string, int|float>
     */
    private function transactionSummary(Collection $transactions): array
    {
        $count = $transactions->count();
        $total = round($transactions->sum(fn (Transaction $transaction): float => (float) $transaction->amount), 2);

        $paid = round(
            $transactions
                ->where('is_paid', true)
                ->sum(fn (Transaction $transaction): float => (float) $transaction->amount),
            2,
        );

        return [
            'count' => $count,
            'total' => $total,
            'paid' => $paid,
            'unpaid' => round(max($total - $paid, 0), 2),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function jsonOk(string $message, array $data = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
        ]);
    }
}
