<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\MarketplaceVisit;
use App\Models\User;

class MarketplaceVisitPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('marketPlaceVisits.VIEW');
    }

    public function view(User $user, MarketplaceVisit $marketplaceVisit): bool
    {
        return $user->can('marketPlaceVisits.VIEW')
            && $this->belongsToCurrentTenant($marketplaceVisit);
    }

    public function create(User $user): bool
    {
        return $user->can('marketPlaceVisits.CREATE');
    }

    public function update(User $user, MarketplaceVisit $marketplaceVisit): bool
    {
        return $user->can('marketPlaceVisits.UPDATE')
            && $this->belongsToCurrentTenant($marketplaceVisit);
    }

    public function delete(User $user, MarketplaceVisit $marketplaceVisit): bool
    {
        return $user->can('marketPlaceVisits.DELETE')
            && $this->belongsToCurrentTenant($marketplaceVisit);
    }

    public function restore(User $user, MarketplaceVisit $marketplaceVisit): bool
    {
        return $user->can('marketPlaceVisits.RESTORE')
            && $this->belongsToCurrentTenant($marketplaceVisit);
    }

    public function forceDelete(User $user, MarketplaceVisit $marketplaceVisit): bool
    {
        return $user->can('marketPlaceVisits.FORCE_DELETE')
            && $this->belongsToCurrentTenant($marketplaceVisit);
    }
}
