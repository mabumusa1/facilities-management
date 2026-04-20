<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Lease;
use App\Models\User;

class LeasePolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('leases.VIEW');
    }

    public function view(User $user, Lease $lease): bool
    {
        return $user->can('leases.VIEW')
            && $this->belongsToCurrentTenant($lease);
    }

    public function create(User $user): bool
    {
        return $user->can('leases.CREATE');
    }

    public function update(User $user, Lease $lease): bool
    {
        return $user->can('leases.UPDATE')
            && $this->belongsToCurrentTenant($lease);
    }

    public function delete(User $user, Lease $lease): bool
    {
        return $user->can('leases.DELETE')
            && $this->belongsToCurrentTenant($lease);
    }

    public function restore(User $user, Lease $lease): bool
    {
        return $user->can('leases.RESTORE')
            && $this->belongsToCurrentTenant($lease);
    }

    public function forceDelete(User $user, Lease $lease): bool
    {
        return $user->can('leases.FORCE_DELETE')
            && $this->belongsToCurrentTenant($lease);
    }
}
