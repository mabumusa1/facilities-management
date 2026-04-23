<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Professional;
use App\Models\User;

class ProfessionalPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('professionals.VIEW');
    }

    public function view(User $user, Professional $professional): bool
    {
        return $user->can('professionals.VIEW')
            && $this->belongsToCurrentTenant($professional);
    }

    public function create(User $user): bool
    {
        return $user->can('professionals.CREATE');
    }

    public function update(User $user, Professional $professional): bool
    {
        return $user->can('professionals.UPDATE')
            && $this->belongsToCurrentTenant($professional);
    }

    public function delete(User $user, Professional $professional): bool
    {
        return $user->can('professionals.DELETE')
            && $this->belongsToCurrentTenant($professional);
    }

    public function restore(User $user, Professional $professional): bool
    {
        return $user->can('professionals.RESTORE')
            && $this->belongsToCurrentTenant($professional);
    }

    public function forceDelete(User $user, Professional $professional): bool
    {
        return $user->can('professionals.FORCE_DELETE')
            && $this->belongsToCurrentTenant($professional);
    }
}
