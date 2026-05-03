<?php

namespace App\Http\Controllers\Leasing;

use App\Exports\LeasePipelineExport;
use App\Http\Controllers\Controller;
use App\Models\Community;
use App\Models\Lease;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Lease pipeline — read-only grouped view with expiry filtering and Excel export.
 *
 * Status grouping:
 *   expiring_soon: Active/New leases whose end_date ≤ now + expiry_window days
 *   active:        Active/New leases whose end_date > now + expiry_window days
 *   expired:       status_id in EXPIRED_IDS (Expired Contract = 32)
 *   terminated:    status_id in TERMINATED_IDS (Cancelled = 33, Closed = 34)
 *   pending:       status_id = PENDING_ID (Pending Application = 76)
 *
 * Routes:
 *   GET  leasing/pipeline                 → leasing.pipeline.index
 *   GET  leasing/pipeline/export-preview  → leasing.pipeline.export-preview
 *   GET  leasing/pipeline/export          → leasing.pipeline.export
 */
class LeasePipelineController extends Controller
{
    /** Lease statuses that represent an active/ongoing contract. */
    private const ACTIVE_IDS = [30, 31]; // New Contract, Active Contract

    /** Lease statuses that represent an expired contract. */
    private const EXPIRED_IDS = [32]; // Expired Contract

    /** Lease statuses that represent a terminated/cancelled/closed contract. */
    private const TERMINATED_IDS = [33, 34]; // Cancelled Contract, Closed Contract

    /** Pending Application status ID. */
    private const PENDING_ID = 76;

    /** Allowed expiry window values (days). */
    private const ALLOWED_WINDOWS = [30, 60, 90];

    /** Default expiry window (days). */
    private const DEFAULT_WINDOW = 30;

    /** Columns included in every pipeline export. */
    public const EXPORT_COLUMNS = [
        'lease_id', 'contract_number', 'unit', 'building', 'community',
        'tenant_name', 'start_date', 'end_date', 'rent_amount',
        'payment_frequency', 'status',
    ];

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Lease::class);

        $window = $this->resolveWindow($request);
        $statusId = $request->input('status_id');
        $communityId = $request->input('community_id');
        $search = trim((string) $request->input('search', ''));

        $leases = Lease::query()
            ->with(['tenant', 'status', 'paymentSchedule', 'units.community', 'units.building'])
            ->when(
                $statusId,
                fn ($q) => $q->where('rf_leases.status_id', (int) $statusId),
            )
            ->when(
                $communityId,
                fn ($q) => $q->whereIn(
                    'rf_leases.id',
                    fn ($sub) => $sub
                        ->select('lease_units.lease_id')
                        ->from('lease_units')
                        ->join('rf_units', 'rf_units.id', '=', 'lease_units.unit_id')
                        ->where('rf_units.rf_community_id', (int) $communityId)
                )
            )
            ->when(
                $search !== '',
                fn ($q) => $q->where(function ($inner) use ($search): void {
                    $inner->where('contract_number', 'like', "%{$search}%")
                        ->orWhereHas('tenant', fn ($tq) => $tq
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                        );
                })
            )
            ->orderByDesc('rf_leases.created_at')
            ->get();

        $groups = $this->groupLeases($leases, $window);

        $communities = Community::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $statuses = Status::query()
            ->whereIn('id', array_merge(self::ACTIVE_IDS, self::EXPIRED_IDS, self::TERMINATED_IDS, [self::PENDING_ID]))
            ->select('id', 'name', 'name_en', 'name_ar')
            ->orderBy('priority')
            ->get();

        return Inertia::render('leasing/pipeline/Index', [
            'groups' => $groups,
            'totalCount' => $leases->count(),
            'communities' => $communities,
            'statuses' => $statuses,
            'filters' => [
                'expiry_window' => $window,
                'status_id' => $statusId ? (string) $statusId : '',
                'community_id' => $communityId ? (string) $communityId : '',
                'search' => $search,
            ],
        ]);
    }

    public function exportPreview(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Lease::class);

        $window = $this->resolveWindow($request);
        $statusId = $request->input('status_id');
        $communityId = $request->input('community_id');
        $search = trim((string) $request->input('search', ''));

        $count = Lease::query()
            ->when(
                $statusId,
                fn ($q) => $q->where('rf_leases.status_id', (int) $statusId),
            )
            ->when(
                $communityId,
                fn ($q) => $q->whereIn(
                    'rf_leases.id',
                    fn ($sub) => $sub
                        ->select('lease_units.lease_id')
                        ->from('lease_units')
                        ->join('rf_units', 'rf_units.id', '=', 'lease_units.unit_id')
                        ->where('rf_units.rf_community_id', (int) $communityId)
                )
            )
            ->when(
                $search !== '',
                fn ($q) => $q->where(function ($inner) use ($search): void {
                    $inner->where('contract_number', 'like', "%{$search}%")
                        ->orWhereHas('tenant', fn ($tq) => $tq
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                        );
                })
            )
            ->count();

        return response()->json([
            'count' => $count,
            'columns' => self::EXPORT_COLUMNS,
            'expiry_window' => $window,
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $this->authorize('viewAny', Lease::class);

        $filters = [
            'expiry_window' => $this->resolveWindow($request),
            'status_id' => $request->input('status_id'),
            'community_id' => $request->input('community_id'),
            'search' => trim((string) $request->input('search', '')),
        ];

        return Excel::download(
            new LeasePipelineExport($filters),
            'lease-pipeline-'.now()->format('Y-m-d-His').'.xlsx'
        );
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function resolveWindow(Request $request): int
    {
        $window = (int) $request->input('expiry_window', self::DEFAULT_WINDOW);

        return in_array($window, self::ALLOWED_WINDOWS, true)
            ? $window
            : self::DEFAULT_WINDOW;
    }

    /**
     * Group a flat collection of leases into pipeline buckets.
     *
     * @param  Collection<int, Lease>  $leases
     * @return array{
     *     expiring_soon: list<array<string, mixed>>,
     *     active: list<array<string, mixed>>,
     *     expired: list<array<string, mixed>>,
     *     terminated: list<array<string, mixed>>,
     *     pending: list<array<string, mixed>>,
     * }
     */
    private function groupLeases(Collection $leases, int $window): array
    {
        $groups = [
            'expiring_soon' => [],
            'active' => [],
            'expired' => [],
            'terminated' => [],
            'pending' => [],
        ];

        $windowCutoff = now()->startOfDay()->addDays($window);

        foreach ($leases as $lease) {
            $row = $this->formatLeaseRow($lease);

            if (in_array($lease->status_id, self::EXPIRED_IDS, true)) {
                $groups['expired'][] = $row;
            } elseif (in_array($lease->status_id, self::TERMINATED_IDS, true)) {
                $groups['terminated'][] = $row;
            } elseif ($lease->status_id === self::PENDING_ID) {
                $groups['pending'][] = $row;
            } elseif (in_array($lease->status_id, self::ACTIVE_IDS, true)) {
                if ($lease->end_date && Carbon::parse($lease->end_date)->startOfDay()->lte($windowCutoff)) {
                    $groups['expiring_soon'][] = $row;
                } else {
                    $groups['active'][] = $row;
                }
            }
        }

        return $groups;
    }

    /**
     * Serialize a Lease model into a pipeline row array.
     *
     * @return array<string, mixed>
     */
    private function formatLeaseRow(Lease $lease): array
    {
        $firstUnit = $lease->units->first();

        $daysUntilExpiry = $lease->end_date
            ? (int) now()->startOfDay()->diffInDays(Carbon::parse($lease->end_date)->startOfDay(), false)
            : null;

        return [
            'id' => $lease->id,
            'contract_number' => $lease->contract_number,
            'unit' => $firstUnit?->name,
            'building' => $firstUnit?->building?->name,
            'community' => $firstUnit?->community?->name,
            'tenant_name' => trim(($lease->tenant?->first_name ?? '').' '.($lease->tenant?->last_name ?? '')),
            'start_date' => $lease->start_date?->format('Y-m-d'),
            'end_date' => $lease->end_date?->format('Y-m-d'),
            'rental_total_amount' => $lease->rental_total_amount,
            'payment_frequency' => $lease->paymentSchedule?->name,
            'status' => [
                'id' => $lease->status?->id,
                'name' => $lease->status?->name,
                'name_en' => $lease->status?->name_en,
                'name_ar' => $lease->status?->name_ar,
            ],
            'days_until_expiry' => $daysUntilExpiry,
        ];
    }
}
