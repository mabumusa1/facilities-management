<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\FacilityBooking;
use App\Models\User;

class FacilityBookingPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('facilityBookings.VIEW');
    }

    public function view(User $user, FacilityBooking $facilityBooking): bool
    {
        return $user->can('facilityBookings.VIEW')
            && $this->belongsToCurrentTenant($facilityBooking);
    }

    public function create(User $user): bool
    {
        return $user->can('facilityBookings.CREATE');
    }

    public function update(User $user, FacilityBooking $facilityBooking): bool
    {
        return $user->can('facilityBookings.UPDATE')
            && $this->belongsToCurrentTenant($facilityBooking);
    }

    public function delete(User $user, FacilityBooking $facilityBooking): bool
    {
        return $user->can('facilityBookings.DELETE')
            && $this->belongsToCurrentTenant($facilityBooking);
    }

    public function restore(User $user, FacilityBooking $facilityBooking): bool
    {
        return $user->can('facilityBookings.RESTORE')
            && $this->belongsToCurrentTenant($facilityBooking);
    }

    public function forceDelete(User $user, FacilityBooking $facilityBooking): bool
    {
        return $user->can('facilityBookings.FORCE_DELETE')
            && $this->belongsToCurrentTenant($facilityBooking);
    }
}
