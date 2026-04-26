<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Lease;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class LeasePolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('leases.VIEW');
    }

    public function view(User $user, Lease $lease): bool
    {
        return $user->can('leases.VIEW')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    public function create(User $user): bool
    {
        return $user->can('leases.CREATE');
    }

    public function update(User $user, Lease $lease): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    public function uploadKyc(User $user, Lease $lease): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    public function removeKycDocument(User $user, Lease $lease): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    public function submitForApproval(User $user, Lease $lease): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    /**
     * Approve a pending lease application.
     * Requires leases.APPROVE permission and manager-level community scope.
     */
    public function approve(User $user, Lease $lease): bool
    {
        return $user->can('leases.APPROVE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    /**
     * Reject a pending lease application.
     * Requires leases.APPROVE permission and manager-level community scope.
     */
    public function reject(User $user, Lease $lease): bool
    {
        return $user->can('leases.APPROVE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    public function delete(User $user, Lease $lease): bool
    {
        return $user->can('leases.DELETE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    public function restore(User $user, Lease $lease): bool
    {
        return $user->can('leases.RESTORE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }

    public function forceDelete(User $user, Lease $lease): bool
    {
        return $user->can('leases.FORCE_DELETE')
            && $this->belongsToCurrentTenant($lease)
            && ManagerScopeHelper::userCanAccessModel($user, $lease);
    }
}
