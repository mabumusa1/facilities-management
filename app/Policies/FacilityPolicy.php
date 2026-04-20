<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('facilities.VIEW');
    }

    public function view(User $user, Facility $facility): bool
    {
        return $user->can('facilities.VIEW');
    }

    public function create(User $user): bool
    {
        return $user->can('facilities.CREATE');
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->can('facilities.UPDATE');
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->can('facilities.DELETE');
    }

    public function restore(User $user, Facility $facility): bool
    {
        return $user->can('facilities.RESTORE');
    }

    public function forceDelete(User $user, Facility $facility): bool
    {
        return $user->can('facilities.FORCE_DELETE');
    }
}
