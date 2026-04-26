<?php

namespace App\Http\Controllers\Leasing;

use App\Console\Commands\ExpireLeaseQuotes;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leasing\RejectLeaseRequest;
use App\Models\Lease;
use App\Support\StatusWorkflow;
use App\Support\WorkflowNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ApprovalController extends Controller
{
    public function __construct(
        private readonly StatusWorkflow $statusWorkflow,
        private readonly WorkflowNotifier $notifier,
    ) {}

    /**
     * Display the pending lease approvals queue.
     * Only users with manager-level RBAC scope can access this.
     */
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Lease::class);

        $search = trim((string) $request->input('search', ''));
        $communityId = $request->input('community_id');

        $leases = Lease::query()
            ->with(['tenant', 'status', 'units.community', 'createdBy'])
            ->where('status_id', ExpireLeaseQuotes::STATUS_PENDING_APPLICATION)
            ->forManager($request->user())
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('contract_number', 'like', "%{$search}%")
                        ->orWhereHas('tenant', function ($tq) use ($search): void {
                            $tq->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($communityId, function ($query) use ($communityId): void {
                $query->whereIn(
                    'rf_leases.id',
                    fn ($sub) => $sub
                        ->select('lease_units.lease_id')
                        ->from('lease_units')
                        ->join('rf_units', 'rf_units.id', '=', 'lease_units.unit_id')
                        ->where('rf_units.rf_community_id', (int) $communityId)
                );
            })
            ->orderByDesc('kyc_submitted_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('leasing/approvals/Index', [
            'leases' => $leases,
            'filters' => [
                'search' => $search,
                'community_id' => $communityId,
            ],
        ]);
    }

    /**
     * Approve a pending lease application.
     * Records approver identity and timestamp; notifies the lease creator.
     */
    public function approve(Lease $lease): RedirectResponse
    {
        $this->authorize('approve', $lease);

        $user = request()->user();

        DB::transaction(function () use ($lease, $user): void {
            $this->statusWorkflow->ensureTransition(
                'lease',
                $lease->status_id,
                ExpireLeaseQuotes::STATUS_APPROVED_APPLICATION,
            );

            $lease->update([
                'status_id' => ExpireLeaseQuotes::STATUS_APPROVED_APPLICATION,
                'approved_by_id' => $user->id,
                'approved_at' => now(),
            ]);

            $this->notifier->notifyTenantUsers(
                $lease->account_tenant_id,
                'lease',
                $lease->id,
                'pending_application',
                'approved_application',
                url("/leases/{$lease->id}"),
                $user->name,
            );
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease application approved.')]);

        return to_route('leases.show', $lease);
    }

    /**
     * Reject a pending lease application.
     * Records rejector identity, timestamp, and reason; notifies the lease creator.
     * The linked quote remains in accepted status — only the lease status changes.
     */
    public function reject(RejectLeaseRequest $request, Lease $lease): RedirectResponse
    {
        $this->authorize('reject', $lease);

        $user = $request->user();
        $reason = $request->validated()['rejection_reason'];

        DB::transaction(function () use ($lease, $user, $reason): void {
            $this->statusWorkflow->ensureTransition(
                'lease',
                $lease->status_id,
                ExpireLeaseQuotes::STATUS_REJECTED_APPLICATION,
            );

            $lease->update([
                'status_id' => ExpireLeaseQuotes::STATUS_REJECTED_APPLICATION,
                'rejected_by_id' => $user->id,
                'rejected_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->notifier->notifyTenantUsers(
                $lease->account_tenant_id,
                'lease',
                $lease->id,
                'pending_application',
                'rejected_application',
                url("/leases/{$lease->id}"),
                $user->name,
            );
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Lease application rejected.')]);

        return to_route('leases.show', $lease);
    }
}
