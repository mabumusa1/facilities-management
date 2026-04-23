<?php

namespace App\Policies;

use App\Models\Facility;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('facilities.VIEW');
    }

    public function view(User $user, Facility $facility): bool
    {
        return $user->can('facilities.VIEW')
            && ManagerScopeHelper::userCanAccessModel($user, $facility);
    }

    public function create(User $user): bool
    {
        return $user->can('facilities.CREATE');
    }

    public function update(User $user, Facility $facility): bool
    {
        return $user->can('facilities.UPDATE')
            && ManagerScopeHelper::userCanAccessModel($user, $facility);
    }

    public function delete(User $user, Facility $facility): bool
    {
        return $user->can('facilities.DELETE')
            && ManagerScopeHelper::userCanAccessModel($user, $facility);
    }

    public function restore(User $user, Facility $facility): bool
    {
        return $user->can('facilities.RESTORE')
            && ManagerScopeHelper::userCanAccessModel($user, $facility);
    }

    public function forceDelete(User $user, Facility $facility): bool
    {
        return $user->can('facilities.FORCE_DELETE')
            && ManagerScopeHelper::userCanAccessModel($user, $facility);
    }
}
