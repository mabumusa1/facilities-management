<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\LeaseApplication;
use App\Models\LeaseApplicationStateHistory;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LeaseApplicationService
{
    public function __construct(
        protected LeaseService $leaseService
    ) {}

    /**
     * Get paginated lease applications for a tenant organization.
     */
    public function getApplicationsForTenant(
        int $tenantId,
        int $perPage = 15,
        ?string $status = null,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = LeaseApplication::byTenantOrg($tenantId)
            ->with(['applicant', 'community', 'building', 'units', 'assignedTo'])
            ->latest();

        if ($status) {
            $query->byStatus($status);
        }

        if ($search) {
            $query->search($search);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get application statistics for a tenant organization.
     */
    public function getApplicationStatistics(int $tenantId): array
    {
        $applications = LeaseApplication::byTenantOrg($tenantId);

        return [
            'total' => (clone $applications)->count(),
            'draft' => (clone $applications)->byStatus(LeaseApplication::STATUS_DRAFT)->count(),
            'in_progress' => (clone $applications)->byStatus(LeaseApplication::STATUS_IN_PROGRESS)->count(),
            'review' => (clone $applications)->byStatus(LeaseApplication::STATUS_REVIEW)->count(),
            'approved' => (clone $applications)->byStatus(LeaseApplication::STATUS_APPROVED)->count(),
            'rejected' => (clone $applications)->byStatus(LeaseApplication::STATUS_REJECTED)->count(),
            'cancelled' => (clone $applications)->byStatus(LeaseApplication::STATUS_CANCELLED)->count(),
            'pending' => (clone $applications)->pending()->count(),
            'awaiting_conversion' => (clone $applications)->approved()->notConverted()->count(),
        ];
    }

    /**
     * Create a new lease application.
     */
    public function createApplication(array $data, User $user): LeaseApplication
    {
        return DB::transaction(function () use ($data, $user) {
            $application = new LeaseApplication;
            $application->fill($data);
            $application->tenant_id = $user->tenant_id;
            $application->created_by_id = $user->contact_id ?? null;
            $application->application_number = $application->generateApplicationNumber();
            $application->save();

            // Attach units if provided
            if (isset($data['units']) && is_array($data['units'])) {
                $this->attachUnits($application, $data['units']);
            }

            // Record initial state
            $this->recordStateChange($application, null, LeaseApplication::STATUS_DRAFT, $user);

            return $application->fresh(['applicant', 'community', 'building', 'units', 'createdBy']);
        });
    }

    /**
     * Update a lease application.
     */
    public function updateApplication(LeaseApplication $application, array $data): LeaseApplication
    {
        return DB::transaction(function () use ($application, $data) {
            $application->fill($data);
            $application->save();

            // Update units if provided
            if (isset($data['units']) && is_array($data['units'])) {
                $application->units()->detach();
                $this->attachUnits($application, $data['units']);
            }

            return $application->fresh(['applicant', 'community', 'building', 'units']);
        });
    }

    /**
     * Delete a lease application.
     */
    public function deleteApplication(LeaseApplication $application): void
    {
        $application->delete();
    }

    /**
     * Transition application to a new status.
     */
    public function transitionStatus(
        LeaseApplication $application,
        string $newStatus,
        User $user,
        ?string $notes = null,
        ?string $rejectionReason = null
    ): LeaseApplication {
        if (! $application->canTransitionTo($newStatus)) {
            throw new \RuntimeException(
                "Cannot transition from '{$application->status}' to '{$newStatus}'. ".
                'Allowed transitions: '.implode(', ', $application->getAllowedTransitions())
            );
        }

        return DB::transaction(function () use ($application, $newStatus, $user, $notes, $rejectionReason) {
            $oldStatus = $application->status;

            $application->status = $newStatus;

            // Set reviewed info for approval/rejection
            if (in_array($newStatus, [LeaseApplication::STATUS_APPROVED, LeaseApplication::STATUS_REJECTED])) {
                $application->reviewed_by_id = $user->contact_id ?? null;
                $application->reviewed_at = now();
                $application->review_notes = $notes;

                if ($newStatus === LeaseApplication::STATUS_REJECTED && $rejectionReason) {
                    $application->rejection_reason = $rejectionReason;
                }
            }

            $application->save();

            // Record state change
            $this->recordStateChange($application, $oldStatus, $newStatus, $user, $notes);

            return $application->fresh();
        });
    }

    /**
     * Submit application for review.
     */
    public function submitForReview(LeaseApplication $application, User $user): LeaseApplication
    {
        // First transition to in_progress if draft
        if ($application->isDraft()) {
            $application = $this->transitionStatus($application, LeaseApplication::STATUS_IN_PROGRESS, $user);
        }

        return $this->transitionStatus($application, LeaseApplication::STATUS_REVIEW, $user);
    }

    /**
     * Approve an application.
     */
    public function approve(LeaseApplication $application, User $user, ?string $notes = null): LeaseApplication
    {
        return $this->transitionStatus($application, LeaseApplication::STATUS_APPROVED, $user, $notes);
    }

    /**
     * Reject an application.
     */
    public function reject(LeaseApplication $application, User $user, string $reason, ?string $notes = null): LeaseApplication
    {
        return $this->transitionStatus($application, LeaseApplication::STATUS_REJECTED, $user, $notes, $reason);
    }

    /**
     * Cancel an application.
     */
    public function cancel(LeaseApplication $application, User $user, ?string $notes = null): LeaseApplication
    {
        return $this->transitionStatus($application, LeaseApplication::STATUS_CANCELLED, $user, $notes);
    }

    /**
     * Put application on hold.
     */
    public function putOnHold(LeaseApplication $application, User $user, ?string $notes = null): LeaseApplication
    {
        return $this->transitionStatus($application, LeaseApplication::STATUS_ON_HOLD, $user, $notes);
    }

    /**
     * Resume application from hold.
     */
    public function resume(LeaseApplication $application, User $user, ?string $notes = null): LeaseApplication
    {
        return $this->transitionStatus($application, LeaseApplication::STATUS_IN_PROGRESS, $user, $notes);
    }

    /**
     * Send quote to applicant.
     */
    public function sendQuote(LeaseApplication $application, int $expirationDays = 30): LeaseApplication
    {
        $application->update([
            'quote_sent_at' => now(),
            'quote_expires_at' => now()->addDays($expirationDays),
        ]);

        // TODO: Send email notification to applicant

        return $application->fresh();
    }

    /**
     * Convert approved application to a lease.
     */
    public function convertToLease(LeaseApplication $application, User $user, array $overrides = []): Lease
    {
        if (! $application->canBeConverted()) {
            throw new \RuntimeException('This application cannot be converted to a lease. It must be approved and not already converted.');
        }

        return DB::transaction(function () use ($application, $user, $overrides) {
            // Prepare lease data from application
            // Map applicant_type (individual/company) to tenant_type (individual/corporate)
            $tenantType = $application->applicant_type === 'company' ? 'corporate' : 'individual';

            $leaseData = [
                'tenant_id' => $application->applicant_id,
                'community_id' => $application->community_id,
                'building_id' => $application->building_id,
                'tenant_type' => $tenantType,
                'start_date' => $application->proposed_start_date?->format('Y-m-d'),
                'end_date' => $application->proposed_end_date?->format('Y-m-d'),
                'rental_total_amount' => $application->quoted_rental_amount,
                'security_deposit_amount' => $application->security_deposit,
                'terms_conditions' => $application->special_terms,
                'units' => $this->getUnitsForLeaseCreation($application),
                'rental_type' => 'summary',
            ];

            // Apply any overrides
            $leaseData = array_merge($leaseData, $overrides);

            // Create the lease using LeaseService
            $lease = $this->leaseService->createLease($leaseData, $user);

            // Mark application as converted
            $application->update([
                'converted_lease_id' => $lease->id,
                'converted_at' => now(),
            ]);

            // Record the conversion in state history
            $this->recordStateChange(
                $application,
                $application->status,
                $application->status,
                $user,
                "Converted to Lease #{$lease->contract_number}"
            );

            return $lease;
        });
    }

    /**
     * Get state history for an application.
     */
    public function getStateHistory(LeaseApplication $application): array
    {
        return $application->stateHistory()
            ->with('changedBy')
            ->get()
            ->map(fn ($history) => [
                'id' => $history->id,
                'from_status' => $history->from_status,
                'from_status_label' => $history->getFromStatusLabel(),
                'to_status' => $history->to_status,
                'to_status_label' => $history->getToStatusLabel(),
                'changed_by' => $history->changedBy?->name,
                'notes' => $history->notes,
                'created_at' => $history->created_at->format('Y-m-d H:i:s'),
            ])
            ->toArray();
    }

    /**
     * Attach units to an application.
     */
    protected function attachUnits(LeaseApplication $application, array $units): void
    {
        foreach ($units as $unitData) {
            $unitId = $unitData['id'] ?? $unitData;

            $pivotData = [];
            if (isset($unitData['proposed_rental_amount'])) {
                $pivotData['proposed_rental_amount'] = $unitData['proposed_rental_amount'];
            }
            if (isset($unitData['net_area'])) {
                $pivotData['net_area'] = $unitData['net_area'];
            }
            if (isset($unitData['meter_cost'])) {
                $pivotData['meter_cost'] = $unitData['meter_cost'];
            }

            $application->units()->attach($unitId, $pivotData);
        }
    }

    /**
     * Get units data formatted for lease creation.
     */
    protected function getUnitsForLeaseCreation(LeaseApplication $application): array
    {
        return $application->units->map(fn ($unit) => [
            'id' => $unit->id,
            'rental_annual_type' => 'total',
            'annual_rental_amount' => $unit->pivot->proposed_rental_amount ?? 0,
            'net_area' => $unit->pivot->net_area ?? 0,
            'meter_cost' => $unit->pivot->meter_cost ?? 0,
        ])->toArray();
    }

    /**
     * Record a state change in history.
     */
    protected function recordStateChange(
        LeaseApplication $application,
        ?string $fromStatus,
        string $toStatus,
        User $user,
        ?string $notes = null
    ): LeaseApplicationStateHistory {
        return LeaseApplicationStateHistory::create([
            'lease_application_id' => $application->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_id' => $user->contact_id ?? null,
            'notes' => $notes,
        ]);
    }

    /**
     * Get available units for application.
     */
    public function getAvailableUnits(int $tenantId): array
    {
        return Unit::where('tenant_id', $tenantId)
            ->where('status_id', 26) // Available
            ->with(['building', 'community'])
            ->get()
            ->toArray();
    }
}
