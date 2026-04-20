<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Community;
use App\Models\User;

class CommunityPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('communities.VIEW');
    }

    public function view(User $user, Community $community): bool
    {
        return $user->can('communities.VIEW')
            && $this->belongsToCurrentTenant($community);
    }

    public function create(User $user): bool
    {
        return $user->can('communities.CREATE');
    }

    public function update(User $user, Community $community): bool
    {
        return $user->can('communities.UPDATE')
            && $this->belongsToCurrentTenant($community);
    }

    public function delete(User $user, Community $community): bool
    {
        return $user->can('communities.DELETE')
            && $this->belongsToCurrentTenant($community);
    }

    public function restore(User $user, Community $community): bool
    {
        return $user->can('communities.RESTORE')
            && $this->belongsToCurrentTenant($community);
    }

    public function forceDelete(User $user, Community $community): bool
    {
        return $user->can('communities.FORCE_DELETE')
            && $this->belongsToCurrentTenant($community);
    }
}
