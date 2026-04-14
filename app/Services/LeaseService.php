<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lease;
use App\Models\Unit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LeaseService
{
    // Status IDs from the system
    private const STATUS_NEW = 30;

    private const STATUS_ACTIVE = 31;

    private const STATUS_EXPIRED = 32;

    private const STATUS_CANCELLED = 33;

    private const STATUS_CLOSED = 34;

    // Unit Status IDs
    private const UNIT_AVAILABLE = 26;

    private const UNIT_RENTED = 25;

    /**
     * Get paginated leases for a tenant.
     */
    public function getLeasesForTenant(
        ?int $tenantId,
        int $perPage = 15,
        ?string $status = null,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = Lease::query()
            ->when($tenantId !== null, function ($q) use ($tenantId) {
                $q->whereHas('tenant', fn ($tq) => $tq->where('tenant_id', $tenantId));
            })
            ->with(['tenant', 'units', 'community', 'building', 'status'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $statusId = $this->getStatusIdFromName($status);
            if ($statusId) {
                $query->where('status_id', $statusId);
            }
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                    ->orWhereHas('tenant', fn ($tq) => $tq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('units', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get lease statistics for a tenant.
     *
     * @return array<string, int|float>
     */
    public function getLeaseStatistics(?int $tenantId): array
    {
        $baseQuery = Lease::query()
            ->when($tenantId !== null, function ($q) use ($tenantId) {
                $q->whereHas('tenant', fn ($tq) => $tq->where('tenant_id', $tenantId));
            });

        $total = (clone $baseQuery)->count();
        $active = (clone $baseQuery)->where('status_id', self::STATUS_ACTIVE)->count();
        $new = (clone $baseQuery)->where('status_id', self::STATUS_NEW)->count();
        $expired = (clone $baseQuery)->where('status_id', self::STATUS_EXPIRED)->count();
        $cancelled = (clone $baseQuery)->where('status_id', self::STATUS_CANCELLED)->count();
        $closed = (clone $baseQuery)->where('status_id', self::STATUS_CLOSED)->count();

        $expiringIn30Days = (clone $baseQuery)
            ->where('status_id', self::STATUS_ACTIVE)
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays(30))
            ->count();

        $totalRentalAmount = (clone $baseQuery)
            ->where('status_id', self::STATUS_ACTIVE)
            ->sum('rental_total_amount');

        return [
            'total' => $total,
            'active' => $active,
            'new' => $new,
            'expired' => $expired,
            'cancelled' => $cancelled,
            'closed' => $closed,
            'expiring_soon' => $expiringIn30Days,
            'total_rental_amount' => (float) $totalRentalAmount,
        ];
    }

    /**
     * Get expiring leases.
     */
    public function getExpiringLeases(?int $tenantId, int $days = 30): Collection
    {
        return Lease::query()
            ->when($tenantId !== null, function ($q) use ($tenantId) {
                $q->whereHas('tenant', fn ($tq) => $tq->where('tenant_id', $tenantId));
            })
            ->where('status_id', self::STATUS_ACTIVE)
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays($days))
            ->with(['tenant', 'units', 'community', 'building'])
            ->orderBy('end_date')
            ->get();
    }

    /**
     * Get available units for leasing.
     */
    public function getAvailableUnits(?int $tenantId): Collection
    {
        return Unit::query()
            ->when($tenantId !== null, fn ($q) => $q->where('tenant_id', $tenantId))
            ->where('status_id', self::UNIT_AVAILABLE)
            ->with(['building', 'community'])
            ->get();
    }

    /**
     * Create a new lease.
     *
     * @param  array<string, mixed>  $data
     */
    public function createLease(array $data, User $creator): Lease
    {
        // Extract units data
        $units = $data['units'] ?? [];
        unset($data['units']);

        // Set defaults
        $data['status_id'] = $data['status_id'] ?? self::STATUS_NEW;
        $data['created_by_id'] = $creator->contact_id ?? null;

        // Calculate duration if not provided
        if (isset($data['start_date'], $data['end_date']) && ! isset($data['number_of_years'])) {
            $duration = $this->calculateDuration($data['start_date'], $data['end_date']);
            $data = array_merge($data, $duration);
        }

        // Generate contract number if needed
        if (empty($data['contract_number'])) {
            $data['contract_number'] = $this->generateContractNumber();
        }

        $lease = Lease::create($data);

        // Attach units
        if (! empty($units)) {
            $unitData = [];
            foreach ($units as $unit) {
                $unitData[$unit['id']] = [
                    'rental_annual_type' => $unit['rental_annual_type'] ?? null,
                    'annual_rental_amount' => $unit['annual_rental_amount'] ?? null,
                    'net_area' => $unit['net_area'] ?? null,
                    'meter_cost' => $unit['meter_cost'] ?? null,
                ];

                // Update unit status to rented
                Unit::where('id', $unit['id'])->update(['status_id' => self::UNIT_RENTED]);
            }
            $lease->units()->attach($unitData);
        }

        return $lease->fresh(['tenant', 'units', 'community', 'building', 'status']);
    }

    /**
     * Update a lease.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateLease(Lease $lease, array $data): Lease
    {
        // Extract units data
        $units = $data['units'] ?? null;
        unset($data['units']);

        // Recalculate duration if dates changed
        if (isset($data['start_date'], $data['end_date'])) {
            $duration = $this->calculateDuration($data['start_date'], $data['end_date']);
            $data = array_merge($data, $duration);
        }

        $lease->update($data);

        // Update units if provided
        if ($units !== null) {
            // Release old units
            $oldUnitIds = $lease->units->pluck('id')->toArray();
            Unit::whereIn('id', $oldUnitIds)->update(['status_id' => self::UNIT_AVAILABLE]);

            // Sync new units
            $unitData = [];
            foreach ($units as $unit) {
                $unitData[$unit['id']] = [
                    'rental_annual_type' => $unit['rental_annual_type'] ?? null,
                    'annual_rental_amount' => $unit['annual_rental_amount'] ?? null,
                    'net_area' => $unit['net_area'] ?? null,
                    'meter_cost' => $unit['meter_cost'] ?? null,
                ];

                // Update unit status to rented
                Unit::where('id', $unit['id'])->update(['status_id' => self::UNIT_RENTED]);
            }
            $lease->units()->sync($unitData);
        }

        return $lease->fresh(['tenant', 'units', 'community', 'building', 'status']);
    }

    /**
     * Delete a lease.
     */
    public function deleteLease(Lease $lease): bool
    {
        // Release units
        $unitIds = $lease->units->pluck('id')->toArray();
        Unit::whereIn('id', $unitIds)->update(['status_id' => self::UNIT_AVAILABLE]);

        return $lease->delete();
    }

    /**
     * Activate a lease (New -> Active).
     */
    public function activateLease(Lease $lease): Lease
    {
        if ($lease->status_id !== self::STATUS_NEW) {
            throw new \RuntimeException('Only new leases can be activated.');
        }

        $lease->update(['status_id' => self::STATUS_ACTIVE]);

        return $lease->fresh();
    }

    /**
     * Terminate a lease (Active -> Cancelled).
     *
     * @param  array<string, mixed>  $data
     */
    public function terminateLease(Lease $lease, array $data = []): Lease
    {
        if ($lease->status_id !== self::STATUS_ACTIVE) {
            throw new \RuntimeException('Only active leases can be terminated.');
        }

        $updateData = [
            'status_id' => self::STATUS_CANCELLED,
            'actual_end_at' => isset($data['termination_date'])
                ? Carbon::parse($data['termination_date'])
                : now(),
        ];

        // Store termination reason if provided
        if (isset($data['termination_reason'])) {
            $updateData['terms_conditions'] = ($lease->terms_conditions ?? '').
                "\n\n--- Termination ---\nReason: ".$data['termination_reason'].
                "\nDate: ".$updateData['actual_end_at']->format('Y-m-d');
        }

        $lease->update($updateData);

        // Release units
        $unitIds = $lease->units->pluck('id')->toArray();
        Unit::whereIn('id', $unitIds)->update(['status_id' => self::UNIT_AVAILABLE]);

        return $lease->fresh(['tenant', 'units', 'status']);
    }

    /**
     * Move out from a lease (Active/Expired -> Closed).
     *
     * @param  array<string, mixed>  $data
     */
    public function moveOut(Lease $lease, array $data = []): Lease
    {
        if ($lease->status_id !== self::STATUS_ACTIVE && $lease->status_id !== self::STATUS_EXPIRED) {
            throw new \RuntimeException('Only active or expired leases can be moved out.');
        }

        $updateData = [
            'status_id' => self::STATUS_CLOSED,
            'is_move_out' => true,
            'actual_end_at' => isset($data['move_out_date'])
                ? Carbon::parse($data['move_out_date'])
                : now(),
        ];

        // Store move-out notes if provided
        if (isset($data['inspection_notes']) || isset($data['deposit_deductions'])) {
            $moveOutNotes = "\n\n--- Move Out ---";
            $moveOutNotes .= "\nDate: ".$updateData['actual_end_at']->format('Y-m-d');

            if (isset($data['inspection_notes'])) {
                $moveOutNotes .= "\nInspection Notes: ".$data['inspection_notes'];
            }

            if (isset($data['deposit_deductions'])) {
                $moveOutNotes .= "\nDeposit Deductions: ".$data['deposit_deductions'];
            }

            if (isset($data['deposit_refund_amount'])) {
                $moveOutNotes .= "\nDeposit Refund: ".$data['deposit_refund_amount'];
            }

            $updateData['terms_conditions'] = ($lease->terms_conditions ?? '').$moveOutNotes;
        }

        $lease->update($updateData);

        // Release units
        $unitIds = $lease->units->pluck('id')->toArray();
        Unit::whereIn('id', $unitIds)->update(['status_id' => self::UNIT_AVAILABLE]);

        return $lease->fresh(['tenant', 'units', 'status']);
    }

    /**
     * Check if a lease can be moved out.
     */
    public function canMoveOut(Lease $lease): bool
    {
        return in_array($lease->status_id, [self::STATUS_ACTIVE, self::STATUS_EXPIRED])
            && ! $lease->is_move_out;
    }

    /**
     * Get termination summary for a lease.
     *
     * @return array<string, mixed>
     */
    public function getTerminationSummary(Lease $lease): array
    {
        $today = Carbon::now();
        $endDate = Carbon::parse($lease->end_date);
        $daysRemaining = $today->diffInDays($endDate, false);
        $isEarlyTermination = $daysRemaining > 0;

        return [
            'lease_id' => $lease->id,
            'contract_number' => $lease->contract_number,
            'tenant_name' => $lease->tenant?->name,
            'start_date' => $lease->start_date->format('Y-m-d'),
            'end_date' => $lease->end_date->format('Y-m-d'),
            'days_remaining' => max(0, $daysRemaining),
            'is_early_termination' => $isEarlyTermination,
            'security_deposit' => (float) $lease->security_deposit_amount,
            'rental_total_amount' => (float) $lease->rental_total_amount,
        ];
    }

    /**
     * Get move-out summary for a lease.
     *
     * @return array<string, mixed>
     */
    public function getMoveOutSummary(Lease $lease): array
    {
        $today = Carbon::now();
        $endDate = Carbon::parse($lease->end_date);
        $isAfterEndDate = $today->gt($endDate);

        return [
            'lease_id' => $lease->id,
            'contract_number' => $lease->contract_number,
            'tenant_name' => $lease->tenant?->name,
            'start_date' => $lease->start_date->format('Y-m-d'),
            'end_date' => $lease->end_date->format('Y-m-d'),
            'is_after_end_date' => $isAfterEndDate,
            'security_deposit' => (float) $lease->security_deposit_amount,
            'rental_total_amount' => (float) $lease->rental_total_amount,
            'unpaid_amount' => $lease->getTotalUnpaidAmount(),
            'units' => $lease->units->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
            ])->toArray(),
        ];
    }

    /**
     * Check if a lease can be renewed.
     */
    public function canRenew(Lease $lease): bool
    {
        return in_array($lease->status_id, [self::STATUS_ACTIVE, self::STATUS_EXPIRED])
            && ! $lease->is_move_out
            && ! $lease->is_renew;
    }

    /**
     * Check if a lease can be terminated.
     */
    public function canTerminate(Lease $lease): bool
    {
        return $lease->status_id === self::STATUS_ACTIVE;
    }

    /**
     * Renew a lease by creating a new lease linked to the original.
     *
     * @param  array<string, mixed>  $data
     */
    public function renewLease(Lease $originalLease, array $data, User $creator): Lease
    {
        if (! $this->canRenew($originalLease)) {
            throw new \RuntimeException('This lease cannot be renewed.');
        }

        // Extract units data, default to original lease units if not provided
        $units = $data['units'] ?? $this->getUnitsDataFromLease($originalLease);
        unset($data['units']);

        // Build renewal data from original lease + overrides
        $renewalData = [
            'tenant_id' => $originalLease->tenant_id,
            'community_id' => $originalLease->community_id,
            'building_id' => $originalLease->building_id,
            'tenant_type' => $originalLease->tenant_type,
            'rental_type' => $originalLease->rental_type,
            'payment_schedule_id' => $originalLease->payment_schedule_id,
            'rental_contract_type_id' => $originalLease->rental_contract_type_id,
            'lease_unit_type_id' => $originalLease->lease_unit_type_id,
            'deal_owner_id' => $originalLease->deal_owner_id,
            'parent_lease_id' => $originalLease->id,
            'status_id' => self::STATUS_NEW,
            'created_by_id' => $creator->contact_id ?? null,
        ];

        // Override with provided data
        $renewalData = array_merge($renewalData, $data);

        // Calculate duration if not provided
        if (isset($renewalData['start_date'], $renewalData['end_date']) && ! isset($renewalData['number_of_years'])) {
            $duration = $this->calculateDuration($renewalData['start_date'], $renewalData['end_date']);
            $renewalData = array_merge($renewalData, $duration);
        }

        // Generate new contract number
        $renewalData['contract_number'] = $this->generateContractNumber();

        // Create the new lease
        $newLease = Lease::create($renewalData);

        // Attach units (same as original or overridden)
        if (! empty($units)) {
            $unitData = [];
            foreach ($units as $unit) {
                $unitData[$unit['id']] = [
                    'rental_annual_type' => $unit['rental_annual_type'] ?? null,
                    'annual_rental_amount' => $unit['annual_rental_amount'] ?? null,
                    'net_area' => $unit['net_area'] ?? null,
                    'meter_cost' => $unit['meter_cost'] ?? null,
                ];
            }
            $newLease->units()->attach($unitData);
        }

        // Mark original lease as renewed
        $originalLease->markAsRenewed($newLease);

        return $newLease->fresh(['tenant', 'units', 'community', 'building', 'status', 'parentLease']);
    }

    /**
     * Get renewal data pre-filled from original lease for the form.
     *
     * @return array<string, mixed>
     */
    public function getRenewalDefaults(Lease $lease): array
    {
        return [
            'original_lease_id' => $lease->id,
            'tenant_id' => $lease->tenant_id,
            'community_id' => $lease->community_id,
            'building_id' => $lease->building_id,
            'tenant_type' => $lease->tenant_type,
            'rental_type' => $lease->rental_type,
            'payment_schedule_id' => $lease->payment_schedule_id,
            'rental_contract_type_id' => $lease->rental_contract_type_id,
            'lease_unit_type_id' => $lease->lease_unit_type_id,
            'rental_total_amount' => $lease->rental_total_amount,
            'security_deposit_amount' => $lease->security_deposit_amount,
            'units' => $this->getUnitsDataFromLease($lease),
            // Suggest new dates: start from day after original ends
            'start_date' => $lease->end_date->addDay()->format('Y-m-d'),
            'end_date' => $lease->end_date->addDay()->addYear()->format('Y-m-d'),
        ];
    }

    /**
     * Get renewal history for a lease.
     */
    public function getRenewalHistory(Lease $lease): Collection
    {
        // Get all leases in the chain (parent and children)
        $leaseIds = collect([$lease->id]);

        // Get parent leases (renewals from)
        $parent = $lease->parentLease;
        while ($parent) {
            $leaseIds->prepend($parent->id);
            $parent = $parent->parentLease;
        }

        // Get child leases (renewed to)
        $renewalIds = Lease::where('parent_lease_id', $lease->id)->pluck('id');
        $leaseIds = $leaseIds->merge($renewalIds);

        // Fetch all leases with relationships
        return Lease::whereIn('id', $leaseIds)
            ->with(['status', 'tenant'])
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Extract units data from an existing lease.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getUnitsDataFromLease(Lease $lease): array
    {
        return $lease->units->map(function ($unit) {
            return [
                'id' => $unit->id,
                'rental_annual_type' => $unit->pivot->rental_annual_type,
                'annual_rental_amount' => $unit->pivot->annual_rental_amount,
                'net_area' => $unit->pivot->net_area,
                'meter_cost' => $unit->pivot->meter_cost,
            ];
        })->toArray();
    }

    /**
     * Calculate lease duration from dates.
     *
     * @return array<string, int>
     */
    protected function calculateDuration(string $startDate, string $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        $diff = $start->diff($end);

        return [
            'number_of_years' => $diff->y,
            'number_of_months' => $diff->m,
            'number_of_days' => $diff->d,
        ];
    }

    /**
     * Generate a unique contract number.
     */
    protected function generateContractNumber(): string
    {
        $prefix = 'LC';
        $year = now()->format('Y');
        $sequence = str_pad((string) (Lease::whereYear('created_at', $year)->count() + 1), 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$year}-{$sequence}";
    }

    /**
     * Get status ID from name.
     */
    protected function getStatusIdFromName(string $name): ?int
    {
        return match (strtolower($name)) {
            'new' => self::STATUS_NEW,
            'active' => self::STATUS_ACTIVE,
            'expired' => self::STATUS_EXPIRED,
            'cancelled', 'canceled' => self::STATUS_CANCELLED,
            'closed' => self::STATUS_CLOSED,
            default => null,
        };
    }
}
