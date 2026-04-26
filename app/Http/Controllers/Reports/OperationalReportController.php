<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Lease;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OperationalReportController extends Controller
{
    /**
     * #305 Occupancy Report — per community, per building, historical trend.
     */
    public function occupancy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => ['nullable', 'integer', 'exists:rf_communities,id'],
            'as_of' => ['nullable', 'date'],
        ]);

        $query = Unit::query()
            ->with('community:id,name')
            ->when($validated['community_id'] ?? null, fn ($q) => $q->where('rf_community_id', (int) $validated['community_id']));

        $total = (clone $query)->count();
        $occupied = (clone $query)->where('status', 'occupied')->count();
        $available = (clone $query)->where('status', 'available')->count();
        $maintenance = (clone $query)->where('status', 'under_maintenance')->count();
        $offPlan = (clone $query)->where('status', 'off_plan')->count();

        return response()->json([
            'data' => [
                'total_units' => $total,
                'occupied' => $occupied,
                'available' => $available,
                'under_maintenance' => $maintenance,
                'off_plan' => $offPlan,
                'occupancy_rate_pct' => $total > 0 ? round(($occupied / $total) * 100, 1) : 0,
            ],
        ]);
    }

    /**
     * #306 Lease Pipeline — expiring leases by month, renewal rate.
     */
    public function leasePipeline(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'community_id' => ['nullable', 'integer'],
            'months_ahead' => ['nullable', 'integer', 'min:1', 'max:24'],
        ]);

        $months = $validated['months_ahead'] ?? 12;

        $leases = Lease::query()
            ->with(['units.community:id,name'])
            ->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addMonths($months))
            ->where('end_date', '>=', now())
            ->when($validated['community_id'] ?? null, function ($q) use ($validated): void {
                $q->whereIn('rf_leases.id', fn ($sub) => $sub
                    ->select('lease_units.lease_id')
                    ->from('lease_units')
                    ->join('rf_units', 'rf_units.id', '=', 'lease_units.unit_id')
                    ->where('rf_units.rf_community_id', (int) $validated['community_id'])
                );
            })
            ->get();

        $byMonth = $leases->groupBy(fn ($l) => $l->end_date->format('Y-m'))
            ->map(fn ($group) => [
                'count' => $group->count(),
                'renewed' => $group->where('renewal_status', 1)->count(),
            ]);

        return response()->json([
            'data' => [
                'total_expiring' => $leases->count(),
                'by_month' => $byMonth,
            ],
        ]);
    }

    /**
     * #309 Property Portfolio Health — maintenance, off-plan, occupancy.
     */
    public function portfolioHealth(Request $request): JsonResponse
    {
        $communities = Community::query()
            ->withCount('units')
            ->withCount(['units as occupied_units' => fn ($q) => $q->where('status', 'occupied')])
            ->withCount(['units as maintenance_units' => fn ($q) => $q->where('status', 'under_maintenance')])
            ->withCount(['units as off_plan_units' => fn ($q) => $q->where('status', 'off_plan')])
            ->get()
            ->map(fn ($c): array => [
                'id' => $c->id,
                'name' => $c->name,
                'total_units' => $c->units_count,
                'occupied' => $c->occupied_units,
                'maintenance' => $c->maintenance_units,
                'off_plan' => $c->off_plan_units,
                'occupancy_rate_pct' => $c->units_count > 0
                    ? round(($c->occupied_units / $c->units_count) * 100, 1)
                    : 0,
            ]);

        return response()->json(['data' => $communities]);
    }

    /**
     * #304 Financial Summary — revenue, collected, outstanding by period.
     */
    public function financialSummary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'community_id' => ['nullable', 'integer'],
        ]);

        $from = $validated['from'] ?? now()->startOfYear()->toDateString();
        $to = $validated['to'] ?? now()->toDateString();

        $stats = DB::table('rf_transactions')
            ->selectRaw("
                SUM(CASE WHEN direction = 'money_in' THEN amount ELSE 0 END) as revenue,
                SUM(CASE WHEN is_paid = true AND direction = 'money_in' THEN amount ELSE 0 END) as collected,
                SUM(CASE WHEN is_paid = false AND direction = 'money_in' THEN amount ELSE 0 END) as outstanding
            ")
            ->whereBetween('created_at', [$from, $to])
            ->when($validated['community_id'] ?? null, fn ($q) => $q->where('community_id', (int) $validated['community_id']))
            ->first();

        return response()->json([
            'data' => [
                'period' => ['from' => $from, 'to' => $to],
                'revenue' => (float) ($stats->revenue ?? 0),
                'collected' => (float) ($stats->collected ?? 0),
                'outstanding' => (float) ($stats->outstanding ?? 0),
                'collection_rate_pct' => ($stats->revenue ?? 0) > 0
                    ? round((($stats->collected ?? 0) / ($stats->revenue ?? 1)) * 100, 1)
                    : 0,
            ],
        ]);
    }
}
