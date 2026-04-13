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
        int $tenantId,
        int $perPage = 15,
        ?string $status = null,
        ?string $search = null
    ): LengthAwarePaginator {
        $query = Lease::whereHas('tenant', function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
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
    public function getLeaseStatistics(int $tenantId): array
    {
        $baseQuery = Lease::whereHas('tenant', function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
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
    public function getExpiringLeases(int $tenantId, int $days = 30): Collection
    {
        return Lease::whereHas('tenant', function ($q) use ($tenantId) {
            $q->where('tenant_id', $tenantId);
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
    public function getAvailableUnits(int $tenantId): Collection
    {
        return Unit::where('tenant_id', $tenantId)
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
        $data['created_by_id'] = $creator->contact_id ?? $creator->id;

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
     */
    public function terminateLease(Lease $lease): Lease
    {
        if ($lease->status_id !== self::STATUS_ACTIVE) {
            throw new \RuntimeException('Only active leases can be terminated.');
        }

        $lease->update([
            'status_id' => self::STATUS_CANCELLED,
            'actual_end_at' => now(),
        ]);

        // Release units
        $unitIds = $lease->units->pluck('id')->toArray();
        Unit::whereIn('id', $unitIds)->update(['status_id' => self::UNIT_AVAILABLE]);

        return $lease->fresh();
    }

    /**
     * Move out from a lease (Active -> Closed).
     */
    public function moveOut(Lease $lease, ?string $actualEndDate = null): Lease
    {
        if ($lease->status_id !== self::STATUS_ACTIVE && $lease->status_id !== self::STATUS_EXPIRED) {
            throw new \RuntimeException('Only active or expired leases can be moved out.');
        }

        $lease->update([
            'status_id' => self::STATUS_CLOSED,
            'is_move_out' => true,
            'actual_end_at' => $actualEndDate ? Carbon::parse($actualEndDate) : now(),
        ]);

        // Release units
        $unitIds = $lease->units->pluck('id')->toArray();
        Unit::whereIn('id', $unitIds)->update(['status_id' => self::UNIT_AVAILABLE]);

        return $lease->fresh();
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
