<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\ServiceRequest;
use App\Models\Transaction;
use App\Models\Unit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Service for generating system reports.
 */
class ReportService
{
    /**
     * Get lease statistics report.
     *
     * @return array<string, mixed>
     */
    public function getLeaseStatistics(int $tenantId): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        $leases = Lease::where('tenant_id', $tenantId);

        $totalLeases = (clone $leases)->count();
        $newLeases = (clone $leases)->where('created_at', '>=', $startOfMonth)->count();
        $activeLeases = (clone $leases)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('is_move_out', false)
            ->count();
        $expiredLeases = (clone $leases)
            ->where('end_date', '<', $now)
            ->where('is_move_out', false)
            ->count();
        $terminatedLeases = (clone $leases)->where('is_move_out', true)->count();

        // Calculate percentages
        $percentNew = $totalLeases > 0 ? round(($newLeases / $totalLeases) * 100, 2) : 0;
        $percentActive = $totalLeases > 0 ? round(($activeLeases / $totalLeases) * 100, 2) : 0;
        $percentExpired = $totalLeases > 0 ? round(($expiredLeases / $totalLeases) * 100, 2) : 0;
        $percentTerminated = $totalLeases > 0 ? round(($terminatedLeases / $totalLeases) * 100, 2) : 0;

        // Lease types
        $commercialLeases = (clone $leases)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('is_move_out', false)
            ->where('lease_type', 'commercial')
            ->count();
        $residentialLeases = (clone $leases)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('is_move_out', false)
            ->where('lease_type', 'residential')
            ->count();

        // Collections
        $monthlyCollection = Transaction::where('tenant_id', $tenantId)
            ->whereHas('category', fn ($q) => $q->where('name', 'Rentals'))
            ->whereBetween('due_on', [$startOfMonth, $now])
            ->sum('amount');

        $yearlyCollection = Transaction::where('tenant_id', $tenantId)
            ->whereHas('category', fn ($q) => $q->where('name', 'Rentals'))
            ->whereBetween('due_on', [$startOfYear, $now])
            ->sum('amount');

        $paidMonthlyCollection = Transaction::where('tenant_id', $tenantId)
            ->whereHas('category', fn ($q) => $q->where('name', 'Rentals'))
            ->whereBetween('due_on', [$startOfMonth, $now])
            ->where('is_paid', true)
            ->sum('paid');

        $paidYearlyCollection = Transaction::where('tenant_id', $tenantId)
            ->whereHas('category', fn ($q) => $q->where('name', 'Rentals'))
            ->whereBetween('due_on', [$startOfYear, $now])
            ->where('is_paid', true)
            ->sum('paid');

        return [
            'total_leases' => $totalLeases,
            'new_leases' => $newLeases,
            'active_leases' => $activeLeases,
            'expired_leases' => $expiredLeases,
            'terminated_leases' => $terminatedLeases,
            'percent_new_leases' => $percentNew,
            'percent_active_leases' => $percentActive,
            'percent_expired_leases' => $percentExpired,
            'percent_terminated_leases' => $percentTerminated,
            'active_commercial_leases' => $commercialLeases,
            'active_residential_leases' => $residentialLeases,
            'current_month_collection' => round($monthlyCollection, 2),
            'current_year_collection' => round($yearlyCollection, 2),
            'paid_collection_current_month' => round($paidMonthlyCollection, 2),
            'paid_collection_current_year' => round($paidYearlyCollection, 2),
        ];
    }

    /**
     * Get expiring leases report.
     *
     * @return Collection<int, Lease>
     */
    public function getExpiringLeasesReport(int $tenantId, int $days = 30): Collection
    {
        $now = Carbon::now();
        $futureDate = $now->copy()->addDays($days);

        return Lease::where('tenant_id', $tenantId)
            ->where('end_date', '>=', $now)
            ->where('end_date', '<=', $futureDate)
            ->where('is_move_out', false)
            ->with(['unit', 'contact', 'status'])
            ->orderBy('end_date')
            ->get();
    }

    /**
     * Get leases by status report.
     *
     * @return array<string, mixed>
     */
    public function getLeasesByStatusReport(int $tenantId): array
    {
        $statusCounts = Lease::where('tenant_id', $tenantId)
            ->join('statuses', 'leases.status_id', '=', 'statuses.id')
            ->select('statuses.name', 'statuses.slug', DB::raw('count(*) as count'))
            ->groupBy('statuses.id', 'statuses.name', 'statuses.slug')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->name,
                'slug' => $item->slug,
                'count' => $item->count,
            ])
            ->toArray();

        return [
            'statuses' => $statusCounts,
            'total' => array_sum(array_column($statusCounts, 'count')),
        ];
    }

    /**
     * Get maintenance/service request statistics report.
     *
     * @return array<string, mixed>
     */
    public function getMaintenanceStatistics(int $tenantId): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        $requests = ServiceRequest::forTenant($tenantId);

        return [
            'total_requests' => (clone $requests)->count(),
            'open_requests' => (clone $requests)
                ->whereHas('status', fn ($q) => $q->where('slug', 'service_request_open'))
                ->count(),
            'in_progress_requests' => (clone $requests)
                ->whereHas('status', fn ($q) => $q->where('slug', 'service_request_in_progress'))
                ->count(),
            'completed_requests' => (clone $requests)
                ->whereHas('status', fn ($q) => $q->where('slug', 'service_request_completed'))
                ->count(),
            'closed_requests' => (clone $requests)
                ->whereHas('status', fn ($q) => $q->where('slug', 'service_request_closed'))
                ->count(),
            'requests_this_month' => (clone $requests)
                ->where('created_at', '>=', $startOfMonth)
                ->count(),
            'requests_this_year' => (clone $requests)
                ->where('created_at', '>=', $startOfYear)
                ->count(),
            'high_priority_count' => (clone $requests)
                ->where('priority', 'high')
                ->whereHas('status', fn ($q) => $q->whereIn('slug', ['service_request_open', 'service_request_in_progress']))
                ->count(),
            'average_resolution_days' => $this->calculateAverageResolutionDays($tenantId),
        ];
    }

    /**
     * Get maintenance requests by category report.
     *
     * @return array<string, mixed>
     */
    public function getMaintenanceByCategoryReport(int $tenantId): array
    {
        $categoryCounts = ServiceRequest::forTenant($tenantId)
            ->join('service_request_categories', 'service_requests.category_id', '=', 'service_request_categories.id')
            ->select('service_request_categories.name', DB::raw('count(*) as count'))
            ->groupBy('service_request_categories.id', 'service_request_categories.name')
            ->get()
            ->map(fn ($item) => [
                'name' => $item->name,
                'slug' => Str::slug($item->name),
                'count' => $item->count,
            ])
            ->toArray();

        return [
            'categories' => $categoryCounts,
            'total' => array_sum(array_column($categoryCounts, 'count')),
        ];
    }

    /**
     * Get maintenance requests by priority report.
     *
     * @return array<string, int>
     */
    public function getMaintenanceByPriorityReport(int $tenantId): array
    {
        $priorityCounts = ServiceRequest::forTenant($tenantId)
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        return [
            'low' => $priorityCounts['low'] ?? 0,
            'medium' => $priorityCounts['medium'] ?? 0,
            'high' => $priorityCounts['high'] ?? 0,
            'urgent' => $priorityCounts['urgent'] ?? 0,
        ];
    }

    /**
     * Get maintenance requests trend report.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMaintenanceTrendReport(int $tenantId, int $months = 12): array
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subMonths($months)->startOfMonth();

        // Fetch all requests in the period and group in PHP for database agnosticity
        $requests = ServiceRequest::forTenant($tenantId)
            ->where('created_at', '>=', $startDate)
            ->select('created_at', 'priority')
            ->get();

        // Group by year-month
        $grouped = $requests->groupBy(function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m');
        });

        $result = [];
        foreach ($grouped as $yearMonth => $items) {
            $date = Carbon::createFromFormat('Y-m', $yearMonth);
            $result[] = [
                'year' => (int) $date->year,
                'month' => (int) $date->month,
                'month_name' => $date->format('M Y'),
                'total' => $items->count(),
                'high_priority' => $items->whereIn('priority', ['high', 'urgent'])->count(),
            ];
        }

        // Sort by year and month
        usort($result, function ($a, $b) {
            if ($a['year'] !== $b['year']) {
                return $a['year'] <=> $b['year'];
            }

            return $a['month'] <=> $b['month'];
        });

        return $result;
    }

    /**
     * Get occupancy report.
     *
     * @return array<string, mixed>
     */
    public function getOccupancyReport(int $tenantId): array
    {
        $units = Unit::where('tenant_id', $tenantId);

        $totalUnits = (clone $units)->count();
        $occupiedUnits = (clone $units)
            ->whereHas('status', fn ($q) => $q->whereIn('slug', ['unit_leased', 'unit_sold_and_lease']))
            ->count();
        $vacantUnits = (clone $units)
            ->whereHas('status', fn ($q) => $q->where('slug', 'unit_vacant'))
            ->count();
        $maintenanceUnits = (clone $units)
            ->whereHas('status', fn ($q) => $q->where('slug', 'unit_maintenance'))
            ->count();

        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 2) : 0;
        $vacancyRate = $totalUnits > 0 ? round(($vacantUnits / $totalUnits) * 100, 2) : 0;

        return [
            'total_units' => $totalUnits,
            'occupied_units' => $occupiedUnits,
            'vacant_units' => $vacantUnits,
            'maintenance_units' => $maintenanceUnits,
            'occupancy_rate' => $occupancyRate,
            'vacancy_rate' => $vacancyRate,
        ];
    }

    /**
     * Get rent collection report.
     *
     * @return array<string, mixed>
     */
    public function getRentCollectionReport(int $tenantId): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $rentTransactions = Transaction::where('tenant_id', $tenantId)
            ->whereHas('category', fn ($q) => $q->where('name', 'Rentals'))
            ->whereBetween('due_on', [$startOfMonth, $endOfMonth]);

        $totalDue = (clone $rentTransactions)->sum('amount');
        $totalCollected = (clone $rentTransactions)
            ->whereHas('status', fn ($q) => $q->where('slug', 'transaction_paid'))
            ->sum('amount');
        $totalPending = (clone $rentTransactions)
            ->whereHas('status', fn ($q) => $q->where('slug', 'transaction_pending'))
            ->sum('amount');
        $totalOverdue = (clone $rentTransactions)
            ->whereHas('status', fn ($q) => $q->where('slug', 'transaction_overdue'))
            ->sum('amount');

        $collectionRate = $totalDue > 0 ? round(($totalCollected / $totalDue) * 100, 2) : 0;

        return [
            'total_due' => round($totalDue, 2),
            'total_collected' => round($totalCollected, 2),
            'total_pending' => round($totalPending, 2),
            'total_overdue' => round($totalOverdue, 2),
            'collection_rate' => $collectionRate,
            'period' => [
                'start' => $startOfMonth->toDateString(),
                'end' => $endOfMonth->toDateString(),
            ],
        ];
    }

    /**
     * Calculate average resolution days for completed service requests.
     */
    private function calculateAverageResolutionDays(int $tenantId): float
    {
        $completedRequests = ServiceRequest::forTenant($tenantId)
            ->whereHas('status', fn ($q) => $q->whereIn('slug', ['service_request_completed', 'service_request_closed']))
            ->whereNotNull('completed_at')
            ->get();

        if ($completedRequests->isEmpty()) {
            return 0;
        }

        $totalDays = $completedRequests->sum(function ($request) {
            return Carbon::parse($request->created_at)->diffInDays(Carbon::parse($request->completed_at));
        });

        return round($totalDays / $completedRequests->count(), 1);
    }
}
