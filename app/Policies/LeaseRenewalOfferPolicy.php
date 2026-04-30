<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\LeaseRenewalOffer;
use App\Models\User;

class LeaseRenewalOfferPolicy
{
    use ChecksTenantOwnership;

    /** All renewal offer views require leases.VIEW permission scoped to the tenant. */
    public function viewAny(User $user): bool
    {
        return $user->can('leases.VIEW');
    }

    public function view(User $user, LeaseRenewalOffer $offer): bool
    {
        return $user->can('leases.VIEW') && $this->belongsToCurrentTenant($offer);
    }

    public function create(User $user): bool
    {
        return $user->can('leases.CREATE');
    }

    public function send(User $user, LeaseRenewalOffer $offer): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($offer)
            && (int) $offer->status_id === LeaseRenewalOffer::STATUS_DRAFT;
    }

    public function recordDecision(User $user, LeaseRenewalOffer $offer): bool
    {
        $allowedStatuses = [
            LeaseRenewalOffer::STATUS_SENT,
            LeaseRenewalOffer::STATUS_VIEWED,
        ];

        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($offer)
            && in_array((int) $offer->status_id, $allowedStatuses, strict: true);
    }

    public function convert(User $user, LeaseRenewalOffer $offer): bool
    {
        return $user->can('leases.CREATE')
            && $this->belongsToCurrentTenant($offer)
            && (int) $offer->status_id === LeaseRenewalOffer::STATUS_ACCEPTED;
    }
}
