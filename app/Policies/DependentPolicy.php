<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Dependent;
use App\Models\User;

class DependentPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('dependents.VIEW');
    }

    public function view(User $user, Dependent $dependent): bool
    {
        return $user->can('dependents.VIEW')
            && $this->belongsToCurrentTenant($dependent);
    }

    public function create(User $user): bool
    {
        return $user->can('dependents.CREATE');
    }

    public function update(User $user, Dependent $dependent): bool
    {
        return $user->can('dependents.UPDATE')
            && $this->belongsToCurrentTenant($dependent);
    }

    public function delete(User $user, Dependent $dependent): bool
    {
        return $user->can('dependents.DELETE')
            && $this->belongsToCurrentTenant($dependent);
    }

    public function restore(User $user, Dependent $dependent): bool
    {
        return $user->can('dependents.RESTORE')
            && $this->belongsToCurrentTenant($dependent);
    }

    public function forceDelete(User $user, Dependent $dependent): bool
    {
        return $user->can('dependents.FORCE_DELETE')
            && $this->belongsToCurrentTenant($dependent);
    }
}
