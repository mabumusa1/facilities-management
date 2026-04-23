<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('roles.VIEW');
    }

    public function create(User $user): bool
    {
        return $user->can('roles.CREATE');
    }

    public function update(User $user, Role $role): bool
    {
        return ! $role->isSystemRole()
            && $user->can('roles.UPDATE')
            && $this->belongsToCurrentTenant($role);
    }

    public function delete(User $user, Role $role): bool
    {
        return ! $role->isSystemRole()
            && $user->can('roles.DELETE')
            && $this->belongsToCurrentTenant($role);
    }

    public function view(User $user, Role $role): bool
    {
        if ($role->isSystemRole()) {
            return $user->can('roles.VIEW');
        }

        return $user->can('roles.VIEW')
            && $this->belongsToCurrentTenant($role);
    }

    public function managePermissions(User $user, Role $role): bool
    {
        return ! $role->isSystemRole()
            && $user->can('roles.UPDATE')
            && $this->belongsToCurrentTenant($role);
    }
}
