<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Building;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class BuildingPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('buildings.VIEW');
    }

    public function view(User $user, Building $building): bool
    {
        return $user->can('buildings.VIEW')
            && $this->belongsToCurrentTenant($building)
            && ManagerScopeHelper::userCanAccessModel($user, $building);
    }

    public function create(User $user): bool
    {
        return $user->can('buildings.CREATE');
    }

    public function update(User $user, Building $building): bool
    {
        return $user->can('buildings.UPDATE')
            && $this->belongsToCurrentTenant($building)
            && ManagerScopeHelper::userCanAccessModel($user, $building);
    }

    public function delete(User $user, Building $building): bool
    {
        return $user->can('buildings.DELETE')
            && $this->belongsToCurrentTenant($building)
            && ManagerScopeHelper::userCanAccessModel($user, $building);
    }

    public function restore(User $user, Building $building): bool
    {
        return $user->can('buildings.RESTORE')
            && $this->belongsToCurrentTenant($building)
            && ManagerScopeHelper::userCanAccessModel($user, $building);
    }

    public function forceDelete(User $user, Building $building): bool
    {
        return $user->can('buildings.FORCE_DELETE')
            && $this->belongsToCurrentTenant($building)
            && ManagerScopeHelper::userCanAccessModel($user, $building);
    }
}
