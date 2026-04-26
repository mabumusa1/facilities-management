<?php

namespace App\Policies;

use App\Models\AccountMembership;
use App\Models\Facility;
use App\Models\Tenant;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class FacilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('facilities.VIEW');
    }

    /**
     * Residents can list facilities for their current tenant without the admin
     * `facilities.VIEW` permission. Any authenticated user who belongs to the
     * current tenant's account membership is considered a resident for this
     * purpose.
     */
    public function viewAnyAsResident(User $user): bool
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            return false;
        }

        return AccountMembership::query()
            ->where('user_id', $user->id)
            ->where('account_tenant_id', $tenant->id)
            ->exists();
    }

    /**
     * Residents can view a specific facility when they belong to the same
     * tenant that owns the facility.
     */
    public function viewAsResident(User $user, Facility $facility): bool
    {
        $tenant = Tenant::current();

        if (! $tenant || $facility->account_tenant_id !== $tenant->id) {
            return false;
        }

        return AccountMembership::query()
            ->where('user_id', $user->id)
            ->where('account_tenant_id', $tenant->id)
            ->exists();
    }

    /**
     * Residents can create a booking for themselves. Any tenant member may book.
     */
    public function bookOwn(User $user): bool
    {
        $tenant = Tenant::current();

        if (! $tenant) {
            return false;
        }

        return AccountMembership::query()
            ->where('user_id', $user->id)
            ->where('account_tenant_id', $tenant->id)
            ->exists();
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
