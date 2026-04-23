<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Admin;
use App\Models\User;

class AdminPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('admins.VIEW');
    }

    public function view(User $user, Admin $admin): bool
    {
        return $user->can('admins.VIEW')
            && $this->belongsToCurrentTenant($admin);
    }

    public function create(User $user): bool
    {
        return $user->can('admins.CREATE');
    }

    public function update(User $user, Admin $admin): bool
    {
        return $user->can('admins.UPDATE')
            && $this->belongsToCurrentTenant($admin);
    }

    public function delete(User $user, Admin $admin): bool
    {
        return $user->can('admins.DELETE')
            && $this->belongsToCurrentTenant($admin);
    }

    public function restore(User $user, Admin $admin): bool
    {
        return $user->can('admins.RESTORE')
            && $this->belongsToCurrentTenant($admin);
    }

    public function forceDelete(User $user, Admin $admin): bool
    {
        return $user->can('admins.FORCE_DELETE')
            && $this->belongsToCurrentTenant($admin);
    }
}
