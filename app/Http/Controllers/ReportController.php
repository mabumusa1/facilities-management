<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService
    ) {}

    /**
     * Display the reports index page.
     */
    public function index(Request $request): Response
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return Inertia::render('reports/index', [
            'leaseStatistics' => $this->reportService->getLeaseStatistics((int) $tenantId),
            'maintenanceStatistics' => $this->reportService->getMaintenanceStatistics((int) $tenantId),
            'occupancyReport' => $this->reportService->getOccupancyReport((int) $tenantId),
        ]);
    }

    /**
     * Display the lease reports page.
     */
    public function leases(Request $request): Response
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return Inertia::render('reports/leases', [
            'statistics' => $this->reportService->getLeaseStatistics((int) $tenantId),
            'statusReport' => $this->reportService->getLeasesByStatusReport((int) $tenantId),
            'expiringLeases' => $this->reportService->getExpiringLeasesReport((int) $tenantId, 30),
            'rentCollection' => $this->reportService->getRentCollectionReport((int) $tenantId),
        ]);
    }

    /**
     * Display the maintenance reports page.
     */
    public function maintenance(Request $request): Response
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return Inertia::render('reports/maintenance', [
            'statistics' => $this->reportService->getMaintenanceStatistics((int) $tenantId),
            'categoryReport' => $this->reportService->getMaintenanceByCategoryReport((int) $tenantId),
            'priorityReport' => $this->reportService->getMaintenanceByPriorityReport((int) $tenantId),
            'trendReport' => $this->reportService->getMaintenanceTrendReport((int) $tenantId, 12),
        ]);
    }

    /**
     * Get lease statistics (API).
     */
    public function leaseStatistics(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json($this->reportService->getLeaseStatistics((int) $tenantId));
    }

    /**
     * Get leases by status (API).
     */
    public function leasesByStatus(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json($this->reportService->getLeasesByStatusReport((int) $tenantId));
    }

    /**
     * Get expiring leases (API).
     */
    public function expiringLeases(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;
        $days = $request->input('days', 30);

        return response()->json($this->reportService->getExpiringLeasesReport((int) $tenantId, (int) $days));
    }

    /**
     * Get rent collection report (API).
     */
    public function rentCollection(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json($this->reportService->getRentCollectionReport((int) $tenantId));
    }

    /**
     * Get maintenance statistics (API).
     */
    public function maintenanceStatistics(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json($this->reportService->getMaintenanceStatistics((int) $tenantId));
    }

    /**
     * Get maintenance by category (API).
     */
    public function maintenanceByCategory(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json($this->reportService->getMaintenanceByCategoryReport((int) $tenantId));
    }

    /**
     * Get maintenance by priority (API).
     */
    public function maintenanceByPriority(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json($this->reportService->getMaintenanceByPriorityReport((int) $tenantId));
    }

    /**
     * Get maintenance trend (API).
     */
    public function maintenanceTrend(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;
        $months = $request->input('months', 12);

        return response()->json($this->reportService->getMaintenanceTrendReport((int) $tenantId, (int) $months));
    }

    /**
     * Get occupancy report (API).
     */
    public function occupancy(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json($this->reportService->getOccupancyReport((int) $tenantId));
    }
}
