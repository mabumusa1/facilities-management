<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\FacilityWaitlist;
use App\Models\User;

class FacilityWaitlistPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('facilityBookings.VIEW');
    }

    public function create(User $user): bool
    {
        return $user->can('facilityBookings.CREATE');
    }

    public function delete(User $user, FacilityWaitlist $facilityWaitlist): bool
    {
        return $user->can('facilityBookings.DELETE')
            && $this->belongsToCurrentTenant($facilityWaitlist->facility);
    }
}
