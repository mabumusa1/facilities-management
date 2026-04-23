<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\MarketplaceOffer;
use App\Models\User;

class MarketplaceOfferPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('marketPlaceBookings.VIEW');
    }

    public function view(User $user, MarketplaceOffer $marketplaceOffer): bool
    {
        return $user->can('marketPlaceBookings.VIEW')
            && $this->belongsToCurrentTenant($marketplaceOffer);
    }

    public function create(User $user): bool
    {
        return $user->can('marketPlaceBookings.CREATE');
    }

    public function update(User $user, MarketplaceOffer $marketplaceOffer): bool
    {
        return $user->can('marketPlaceBookings.UPDATE')
            && $this->belongsToCurrentTenant($marketplaceOffer);
    }

    public function delete(User $user, MarketplaceOffer $marketplaceOffer): bool
    {
        return $user->can('marketPlaceBookings.DELETE')
            && $this->belongsToCurrentTenant($marketplaceOffer);
    }

    public function restore(User $user, MarketplaceOffer $marketplaceOffer): bool
    {
        return $user->can('marketPlaceBookings.RESTORE')
            && $this->belongsToCurrentTenant($marketplaceOffer);
    }

    public function forceDelete(User $user, MarketplaceOffer $marketplaceOffer): bool
    {
        return $user->can('marketPlaceBookings.FORCE_DELETE')
            && $this->belongsToCurrentTenant($marketplaceOffer);
    }
}
