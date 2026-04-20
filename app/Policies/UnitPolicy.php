<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('properties.VIEW');
    }

    public function view(User $user, Unit $unit): bool
    {
        return $user->can('properties.VIEW')
            && $this->belongsToCurrentTenant($unit);
    }

    public function create(User $user): bool
    {
        return $user->can('properties.CREATE');
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->can('properties.UPDATE')
            && $this->belongsToCurrentTenant($unit);
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->can('properties.DELETE')
            && $this->belongsToCurrentTenant($unit);
    }

    public function restore(User $user, Unit $unit): bool
    {
        return $user->can('properties.RESTORE')
            && $this->belongsToCurrentTenant($unit);
    }

    public function forceDelete(User $user, Unit $unit): bool
    {
        return $user->can('properties.FORCE_DELETE')
            && $this->belongsToCurrentTenant($unit);
    }
}
