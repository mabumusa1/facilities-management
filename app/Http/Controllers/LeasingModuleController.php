<?php

namespace App\Http\Controllers;

use App\Models\Lease;
use App\Models\LeaseApplication;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeasingModuleController extends Controller
{
    public function index(Request $request): Response
    {
        $tenantId = $request->user()->tenant_id;

        $baseLeases = Lease::query()
            ->when($tenantId !== null, fn ($q) => $q->whereHas('tenant', fn ($tq) => $tq->where('tenant_id', $tenantId)));
        $baseApplications = LeaseApplication::query()
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId));

        $stats = [
            'leases' => [
                'total' => (clone $baseLeases)->where('is_sub_lease', false)->count(),
                'active' => (clone $baseLeases)->where('is_sub_lease', false)->where('status_id', 31)->count(),
                'expiring_soon' => (clone $baseLeases)->where('is_sub_lease', false)
                    ->where('status_id', 31)
                    ->whereDate('end_date', '>=', now())
                    ->whereDate('end_date', '<=', now()->addDays(30))
                    ->count(),
            ],
            'sub_leases' => [
                'total' => (clone $baseLeases)->where('is_sub_lease', true)->count(),
                'active' => (clone $baseLeases)->where('is_sub_lease', true)->where('status_id', 31)->count(),
            ],
            'applications' => [
                'total' => (clone $baseApplications)->count(),
                'pending' => (clone $baseApplications)
                    ->whereIn('status', ['draft', 'submitted', 'under_review'])
                    ->count(),
            ],
            'renewals' => [
                'total' => (clone $baseLeases)->where('is_renew', true)->count(),
            ],
        ];

        $recentLeases = (clone $baseLeases)
            ->where('is_sub_lease', false)
            ->with(['tenant', 'status', 'units'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $expiringLeases = (clone $baseLeases)
            ->where('is_sub_lease', false)
            ->where('status_id', 31)
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays(30))
            ->with(['tenant', 'units'])
            ->orderBy('end_date')
            ->limit(5)
            ->get();

        return Inertia::render('leasing/index', [
            'stats' => $stats,
            'recentLeases' => $recentLeases,
            'expiringLeases' => $expiringLeases,
        ]);
    }

    /**
     * Leasing statistics page.
     */
    public function statistics(Request $request): Response
    {
        $tenantId = $request->user()->tenant_id;

        $baseLeases = Lease::query()
            ->when($tenantId !== null, fn ($q) => $q->whereHas('tenant', fn ($tq) => $tq->where('tenant_id', $tenantId)));

        return Inertia::render('leasing/statistics', [
            'stats' => [
                'total' => (clone $baseLeases)->count(),
                'active' => (clone $baseLeases)->where('status_id', 31)->count(),
                'expired' => (clone $baseLeases)->where('status_id', 32)->count(),
                'draft' => (clone $baseLeases)->where('status_id', 30)->count(),
            ],
        ]);
    }

    /**
     * Leasing visits page (marketplace visits for leasing).
     */
    public function visits(Request $request): Response
    {
        return Inertia::render('leasing/visits');
    }
}
