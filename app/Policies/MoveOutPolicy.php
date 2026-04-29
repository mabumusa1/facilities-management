<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Lease;
use App\Models\MoveOut;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class MoveOutPolicy
{
    use ChecksTenantOwnership;

    /**
     * Initiate a move-out: requires update permission on the parent lease.
     */
    public function create(User $user, Lease $lease): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    /**
     * View / update a move-out record.
     */
    public function view(User $user, MoveOut $moveOut): bool
    {
        return $user->can('leases.VIEW')
            && $this->belongsToCurrentTenant($moveOut);
    }

    /**
     * Update inspection or deductions on a move-out record.
     */
    public function update(User $user, MoveOut $moveOut): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($moveOut);
    }
}
