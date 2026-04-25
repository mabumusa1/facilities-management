<?php

namespace App\Policies;

use App\Models\FacilityWaitlist;
use App\Models\User;

class FacilityWaitlistPolicy
{
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
        return $user->can('facilityBookings.DELETE');
    }
}
