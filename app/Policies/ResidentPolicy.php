<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Resident;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class ResidentPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('tenants.VIEW');
    }

    public function view(User $user, Resident $resident): bool
    {
        return $user->can('tenants.VIEW')
            && $this->belongsToCurrentTenant($resident)
            && ManagerScopeHelper::userCanAccessModel($user, $resident);
    }

    public function create(User $user): bool
    {
        return $user->can('tenants.CREATE');
    }

    public function update(User $user, Resident $resident): bool
    {
        return $user->can('tenants.UPDATE')
            && $this->belongsToCurrentTenant($resident)
            && ManagerScopeHelper::userCanAccessModel($user, $resident);
    }

    public function delete(User $user, Resident $resident): bool
    {
        return $user->can('tenants.DELETE')
            && $this->belongsToCurrentTenant($resident)
            && ManagerScopeHelper::userCanAccessModel($user, $resident);
    }

    public function restore(User $user, Resident $resident): bool
    {
        return $user->can('tenants.RESTORE')
            && $this->belongsToCurrentTenant($resident)
            && ManagerScopeHelper::userCanAccessModel($user, $resident);
    }

    public function forceDelete(User $user, Resident $resident): bool
    {
        return $user->can('tenants.FORCE_DELETE')
            && $this->belongsToCurrentTenant($resident)
            && ManagerScopeHelper::userCanAccessModel($user, $resident);
    }
}
