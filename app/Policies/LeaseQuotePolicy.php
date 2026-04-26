<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Console\Commands\ExpireLeaseQuotes;
use App\Models\LeaseQuote;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class LeaseQuotePolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('leases.VIEW');
    }

    public function view(User $user, LeaseQuote $leaseQuote): bool
    {
        return $user->can('leases.VIEW')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote);
    }

    public function create(User $user): bool
    {
        return $user->can('leases.CREATE');
    }

    public function update(User $user, LeaseQuote $leaseQuote): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote);
    }

    public function send(User $user, LeaseQuote $leaseQuote): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote);
    }

    /**
     * Allowed from sent or viewed status only.
     */
    public function revise(User $user, LeaseQuote $leaseQuote): bool
    {
        $allowedStatuses = [
            ExpireLeaseQuotes::STATUS_SENT,
            ExpireLeaseQuotes::STATUS_VIEWED,
        ];

        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote)
            && in_array((int) $leaseQuote->status_id, $allowedStatuses, strict: true);
    }

    /**
     * Allowed from viewed status only.
     */
    public function reject(User $user, LeaseQuote $leaseQuote): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote)
            && (int) $leaseQuote->status_id === ExpireLeaseQuotes::STATUS_VIEWED;
    }

    /**
     * Allowed from any non-terminal status.
     */
    public function expire(User $user, LeaseQuote $leaseQuote): bool
    {
        $terminalStatuses = [
            ExpireLeaseQuotes::STATUS_ACCEPTED,
            ExpireLeaseQuotes::STATUS_REJECTED,
            ExpireLeaseQuotes::STATUS_EXPIRED,
        ];

        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote)
            && ! in_array((int) $leaseQuote->status_id, $terminalStatuses, strict: true);
    }

    public function delete(User $user, LeaseQuote $leaseQuote): bool
    {
        return $user->can('leases.DELETE')
            && $this->belongsToCurrentTenant($leaseQuote)
            && ManagerScopeHelper::userCanAccessModel($user, $leaseQuote);
    }
}
