<?php

namespace App\Services;

use App\Models\FacilityBooking;
use App\Models\Lease;
use App\Models\MarketplaceOffer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\ServiceRequest;
use App\Models\Status;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\VisitorAccess;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Service for aggregating dashboard statistics across the system.
 */
class DashboardService
{
    /**
     * Get unit statistics by status.
     *
     * @return array<string, int>
     */
    public function getUnitStatistics(int $tenantId): array
    {
        $stats = Unit::where('tenant_id', $tenantId)
            ->join('statuses', 'units.status_id', '=', 'statuses.id')
            ->select('statuses.slug', DB::raw('count(*) as count'))
            ->groupBy('statuses.slug')
            ->pluck('count', 'slug')
            ->toArray();

        return [
            'vacant' => $stats['unit_vacant'] ?? 0,
            'leased' => $stats['unit_leased'] ?? 0,
            'sold' => $stats['unit_sold'] ?? 0,
            'sold_and_lease' => $stats['unit_sold_and_lease'] ?? 0,
            'maintenance' => $stats['unit_maintenance'] ?? 0,
            'reserved' => $stats['unit_reserved'] ?? 0,
            'total' => array_sum($stats),
        ];
    }

    /**
     * Get items requiring attention.
     *
     * @return array<string, int>
     */
    public function getRequiresAttention(int $tenantId): array
    {
        return [
            'requests_approval' => $this->getServiceRequestsAwaitingApproval($tenantId),
            'pending_complaints' => $this->getPendingComplaints($tenantId),
            'expiring_leases' => $this->getExpiringLeases($tenantId),
            'overdue_receipts' => $this->getOverdueReceipts($tenantId),
        ];
    }

    /**
     * Get lease statistics.
     *
     * @return array<string, mixed>
     */
    public function getLeaseStatistics(int $tenantId): array
    {
        $now = Carbon::now();
        $thirtyDaysFromNow = $now->copy()->addDays(30);

        $leases = Lease::where('tenant_id', $tenantId);

        $activeLeases = (clone $leases)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->count();

        $expiringWithin30Days = (clone $leases)
            ->where('end_date', '>=', $now)
            ->where('end_date', '<=', $thirtyDaysFromNow)
            ->count();

        $expiredLeases = (clone $leases)
            ->where('end_date', '<', $now)
            ->where('is_move_out', false)
            ->count();

        $totalLeases = $leases->count();

        return [
            'active' => $activeLeases,
            'expiring_soon' => $expiringWithin30Days,
            'expired' => $expiredLeases,
            'total' => $totalLeases,
        ];
    }

    /**
     * Get service request statistics.
     *
     * @return array<string, int>
     */
    public function getServiceRequestStatistics(int $tenantId): array
    {
        $stats = ServiceRequest::forTenant($tenantId)
            ->join('statuses', 'service_requests.status_id', '=', 'statuses.id')
            ->select('statuses.slug', DB::raw('count(*) as count'))
            ->groupBy('statuses.slug')
            ->pluck('count', 'slug')
            ->toArray();

        return [
            'open' => $stats['service_request_open'] ?? 0,
            'in_progress' => $stats['service_request_in_progress'] ?? 0,
            'pending_approval' => $stats['service_request_pending_approval'] ?? 0,
            'completed' => $stats['service_request_completed'] ?? 0,
            'closed' => $stats['service_request_closed'] ?? 0,
            'total' => array_sum($stats),
        ];
    }

    /**
     * Get marketplace statistics.
     *
     * @return array<string, mixed>
     */
    public function getMarketplaceStatistics(int $tenantId): array
    {
        $units = MarketplaceUnit::where('tenant_id', $tenantId);
        $visits = MarketplaceVisit::where('tenant_id', $tenantId);
        $offers = MarketplaceOffer::where('tenant_id', $tenantId);

        return [
            'active_listings' => (clone $units)->whereHas('status', function ($q) {
                $q->where('slug', 'marketplace_available');
            })->count(),
            'total_listings' => $units->count(),
            'scheduled_visits' => (clone $visits)->whereHas('status', function ($q) {
                $q->whereIn('slug', ['marketplace_visit_pending', 'marketplace_visit_confirmed']);
            })->count(),
            'pending_offers' => (clone $offers)->whereHas('status', function ($q) {
                $q->whereIn('slug', [
                    'marketplace_offer_submitted',
                    'marketplace_offer_negotiating',
                    'marketplace_offer_review',
                ]);
            })->count(),
            'completed_sales' => (clone $offers)->whereNotNull('completed_at')->count(),
        ];
    }

    /**
     * Get financial overview.
     *
     * @return array<string, mixed>
     */
    public function getFinancialOverview(int $tenantId): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $transactions = Transaction::where('tenant_id', $tenantId);

        $monthlyIncome = (clone $transactions)
            ->where('is_paid', true)
            ->whereBetween('due_on', [$startOfMonth, $endOfMonth])
            ->sum('paid');

        $monthlyExpenses = (clone $transactions)
            ->whereHas('category', function ($q) {
                $q->where('name', 'Insurance Refund');
            })
            ->whereBetween('due_on', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $pendingPayments = (clone $transactions)
            ->whereHas('status', function ($q) {
                $q->where('slug', 'transaction_pending');
            })
            ->sum('amount');

        $overduePayments = (clone $transactions)
            ->whereHas('status', function ($q) {
                $q->where('slug', 'transaction_overdue');
            })
            ->sum('amount');

        return [
            'monthly_income' => round($monthlyIncome, 2),
            'monthly_expenses' => round($monthlyExpenses, 2),
            'net_income' => round($monthlyIncome - $monthlyExpenses, 2),
            'pending_payments' => round($pendingPayments, 2),
            'overdue_payments' => round($overduePayments, 2),
        ];
    }

    /**
     * Get facility booking statistics.
     *
     * @return array<string, int>
     */
    public function getFacilityStatistics(int $tenantId): array
    {
        $today = Carbon::today();
        $bookings = FacilityBooking::where('tenant_id', $tenantId);

        return [
            'today_bookings' => (clone $bookings)->whereDate('booking_date', $today)->count(),
            'upcoming_bookings' => (clone $bookings)->whereDate('booking_date', '>', $today)->count(),
            'pending_approval' => (clone $bookings)->whereHas('status', function ($q) {
                $q->where('slug', 'facility_booking_pending');
            })->count(),
            'total_bookings' => $bookings->count(),
        ];
    }

    /**
     * Get visitor access statistics.
     *
     * @return array<string, int>
     */
    public function getVisitorStatistics(int $tenantId): array
    {
        $today = Carbon::today();
        $visitors = VisitorAccess::where('tenant_id', $tenantId);

        return [
            'expected_today' => (clone $visitors)
                ->whereDate('visit_start_date', $today)
                ->whereNull('checked_in_at')
                ->count(),
            'checked_in_today' => (clone $visitors)
                ->whereDate('checked_in_at', $today)
                ->count(),
            'pending_approval' => (clone $visitors)->whereHas('status', function ($q) {
                $q->where('slug', 'visitor_pending');
            })->count(),
        ];
    }

    /**
     * Get complete dashboard data.
     *
     * @return array<string, mixed>
     */
    public function getDashboardData(int $tenantId): array
    {
        return [
            'units' => $this->getUnitStatistics($tenantId),
            'requires_attention' => $this->getRequiresAttention($tenantId),
            'leases' => $this->getLeaseStatistics($tenantId),
            'service_requests' => $this->getServiceRequestStatistics($tenantId),
            'marketplace' => $this->getMarketplaceStatistics($tenantId),
            'financials' => $this->getFinancialOverview($tenantId),
            'facilities' => $this->getFacilityStatistics($tenantId),
            'visitors' => $this->getVisitorStatistics($tenantId),
        ];
    }

    /**
     * Count service requests awaiting approval.
     */
    private function getServiceRequestsAwaitingApproval(int $tenantId): int
    {
        return ServiceRequest::forTenant($tenantId)
            ->whereHas('status', function ($q) {
                $q->where('slug', 'service_request_pending_approval');
            })
            ->count();
    }

    /**
     * Count pending complaints.
     */
    private function getPendingComplaints(int $tenantId): int
    {
        return ServiceRequest::forTenant($tenantId)
            ->where('priority', 'high')
            ->whereHas('status', function ($q) {
                $q->whereIn('slug', ['service_request_open', 'service_request_in_progress']);
            })
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%complaint%');
            })
            ->count();
    }

    /**
     * Count leases expiring within 30 days.
     */
    private function getExpiringLeases(int $tenantId): int
    {
        $now = Carbon::now();
        $thirtyDaysFromNow = $now->copy()->addDays(30);

        return Lease::where('tenant_id', $tenantId)
            ->where('end_date', '>=', $now)
            ->where('end_date', '<=', $thirtyDaysFromNow)
            ->count();
    }

    /**
     * Count overdue receipts/payments.
     */
    private function getOverdueReceipts(int $tenantId): int
    {
        return Transaction::where('tenant_id', $tenantId)
            ->whereHas('status', function ($q) {
                $q->where('slug', 'transaction_overdue');
            })
            ->count();
    }
}
