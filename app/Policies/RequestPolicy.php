<?php

namespace App\Policies;

use App\Concerns\ChecksTenantOwnership;
use App\Models\Request;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class RequestPolicy
{
    use ChecksTenantOwnership;

    public function viewAny(User $user): bool
    {
        return $user->can('managerRequests.VIEW');
    }

    public function view(User $user, Request $request): bool
    {
        return $user->can('managerRequests.VIEW')
            && $this->belongsToCurrentTenant($request)
            && ManagerScopeHelper::userCanAccessModel($user, $request);
    }

    public function create(User $user): bool
    {
        return $user->can('managerRequests.CREATE');
    }

    public function update(User $user, Request $request): bool
    {
        return $user->can('managerRequests.UPDATE')
            && $this->belongsToCurrentTenant($request)
            && ManagerScopeHelper::userCanAccessModel($user, $request);
    }

    public function delete(User $user, Request $request): bool
    {
        return $user->can('managerRequests.DELETE')
            && $this->belongsToCurrentTenant($request)
            && ManagerScopeHelper::userCanAccessModel($user, $request);
    }

    public function restore(User $user, Request $request): bool
    {
        return $user->can('managerRequests.RESTORE')
            && $this->belongsToCurrentTenant($request)
            && ManagerScopeHelper::userCanAccessModel($user, $request);
    }

    public function forceDelete(User $user, Request $request): bool
    {
        return $user->can('managerRequests.FORCE_DELETE')
            && $this->belongsToCurrentTenant($request)
            && ManagerScopeHelper::userCanAccessModel($user, $request);
    }
}
