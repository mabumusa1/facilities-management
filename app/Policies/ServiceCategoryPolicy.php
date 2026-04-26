<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\ServiceCategory;
use App\Models\User;

class ServiceCategoryPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('settings.VIEW');
    }

    public function view(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('settings.VIEW')
            && $this->belongsToCurrentTenant($serviceCategory);
    }

    public function create(User $user): bool
    {
        return $user->can('settings.CREATE');
    }

    public function update(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('settings.UPDATE')
            && $this->belongsToCurrentTenant($serviceCategory);
    }

    public function delete(User $user, ServiceCategory $serviceCategory): bool
    {
        return $user->can('settings.DELETE')
            && $this->belongsToCurrentTenant($serviceCategory);
    }
}
