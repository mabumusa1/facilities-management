<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Lease;
use App\Models\LeaseNotice;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class LeaseNoticePolicy
{
    use ChecksTenantOwnership;

    /**
     * View the notice history for a lease.
     * Requires the same permission as viewing a lease.
     */
    public function viewAny(User $user, Lease $lease): bool
    {
        return $user->can('leases.VIEW')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    /**
     * View a single notice body.
     */
    public function view(User $user, LeaseNotice $leaseNotice): bool
    {
        return $user->can('leases.VIEW')
            && $this->belongsToCurrentTenant($leaseNotice);
    }

    /**
     * Send a notice — restricted to users who can update the parent lease.
     */
    public function create(User $user, Lease $lease): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }
}
