<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\MarketplaceUnit;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class MarketplaceUnitPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('marketPlaces.VIEW');
    }

    public function view(User $user, MarketplaceUnit $marketplaceUnit): bool
    {
        return $user->can('marketPlaces.VIEW')
            && $this->belongsToCurrentTenant($marketplaceUnit)
            && ManagerScopeHelper::userCanAccessModel($user, $marketplaceUnit);
    }

    public function create(User $user): bool
    {
        return $user->can('marketPlaces.CREATE');
    }

    public function update(User $user, MarketplaceUnit $marketplaceUnit): bool
    {
        return $user->can('marketPlaces.UPDATE')
            && $this->belongsToCurrentTenant($marketplaceUnit)
            && ManagerScopeHelper::userCanAccessModel($user, $marketplaceUnit);
    }

    public function delete(User $user, MarketplaceUnit $marketplaceUnit): bool
    {
        return $user->can('marketPlaces.DELETE')
            && $this->belongsToCurrentTenant($marketplaceUnit)
            && ManagerScopeHelper::userCanAccessModel($user, $marketplaceUnit);
    }

    public function restore(User $user, MarketplaceUnit $marketplaceUnit): bool
    {
        return $user->can('marketPlaces.RESTORE')
            && $this->belongsToCurrentTenant($marketplaceUnit)
            && ManagerScopeHelper::userCanAccessModel($user, $marketplaceUnit);
    }

    public function forceDelete(User $user, MarketplaceUnit $marketplaceUnit): bool
    {
        return $user->can('marketPlaces.FORCE_DELETE')
            && $this->belongsToCurrentTenant($marketplaceUnit)
            && ManagerScopeHelper::userCanAccessModel($user, $marketplaceUnit);
    }
}
