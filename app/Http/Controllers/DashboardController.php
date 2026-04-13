<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    /**
     * Display the dashboard overview.
     */
    public function index(Request $request): Response
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return Inertia::render('dashboard', [
            'statistics' => $this->dashboardService->getDashboardData((int) $tenantId),
        ]);
    }

    /**
     * Get dashboard data as JSON (API endpoint).
     */
    public function data(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getDashboardData((int) $tenantId),
        ]);
    }

    /**
     * Get requires attention items.
     */
    public function requiresAttention(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getRequiresAttention((int) $tenantId),
        ]);
    }

    /**
     * Get unit statistics.
     */
    public function units(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getUnitStatistics((int) $tenantId),
        ]);
    }

    /**
     * Get lease statistics.
     */
    public function leases(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getLeaseStatistics((int) $tenantId),
        ]);
    }

    /**
     * Get service request statistics.
     */
    public function serviceRequests(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getServiceRequestStatistics((int) $tenantId),
        ]);
    }

    /**
     * Get marketplace statistics.
     */
    public function marketplace(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getMarketplaceStatistics((int) $tenantId),
        ]);
    }

    /**
     * Get financial overview.
     */
    public function financials(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getFinancialOverview((int) $tenantId),
        ]);
    }

    /**
     * Get facility statistics.
     */
    public function facilities(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getFacilityStatistics((int) $tenantId),
        ]);
    }

    /**
     * Get visitor statistics.
     */
    public function visitors(Request $request): JsonResponse
    {
        $tenantId = $request->header('X-Tenant') ?? $request->user()?->tenant_id ?? 1;

        return response()->json([
            'data' => $this->dashboardService->getVisitorStatistics((int) $tenantId),
        ]);
    }
}
