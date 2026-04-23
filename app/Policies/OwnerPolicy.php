<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Owner;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class OwnerPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('owners.VIEW');
    }

    public function view(User $user, Owner $owner): bool
    {
        return $user->can('owners.VIEW')
            && $this->belongsToCurrentTenant($owner)
            && ManagerScopeHelper::userCanAccessModel($user, $owner);
    }

    public function create(User $user): bool
    {
        return $user->can('owners.CREATE');
    }

    public function update(User $user, Owner $owner): bool
    {
        return $user->can('owners.UPDATE')
            && $this->belongsToCurrentTenant($owner)
            && ManagerScopeHelper::userCanAccessModel($user, $owner);
    }

    public function delete(User $user, Owner $owner): bool
    {
        return $user->can('owners.DELETE')
            && $this->belongsToCurrentTenant($owner)
            && ManagerScopeHelper::userCanAccessModel($user, $owner);
    }

    public function restore(User $user, Owner $owner): bool
    {
        return $user->can('owners.RESTORE')
            && $this->belongsToCurrentTenant($owner)
            && ManagerScopeHelper::userCanAccessModel($user, $owner);
    }

    public function forceDelete(User $user, Owner $owner): bool
    {
        return $user->can('owners.FORCE_DELETE')
            && $this->belongsToCurrentTenant($owner)
            && ManagerScopeHelper::userCanAccessModel($user, $owner);
    }
}
