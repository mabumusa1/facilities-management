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

    /**
     * Manager can triage (view triage queue, assign technician, add internal notes).
     */
    public function triage(User $user): bool
    {
        return $user->can('managerRequests.UPDATE');
    }

    /**
     * Manager can assign a request to a technician (user).
     */
    public function assign(User $user, Request $request): bool
    {
        return $user->can('managerRequests.UPDATE')
            && $this->belongsToCurrentTenant($request)
            && ManagerScopeHelper::userCanAccessModel($user, $request);
    }

    /**
     * Manager can add an internal note to a request.
     */
    public function addInternalNote(User $user, Request $request): bool
    {
        return $user->can('managerRequests.UPDATE')
            && $this->belongsToCurrentTenant($request)
            && ManagerScopeHelper::userCanAccessModel($user, $request);
    }

    /**
     * Any authenticated tenant member (resident or manager) can submit their own request.
     */
    public function createOwn(User $user): bool
    {
        return $user->can('managerRequests.CREATE') || $user->memberships()->exists();
    }

    /**
     * A user can view their own submission.
     */
    public function viewOwn(User $user, Request $request): bool
    {
        return $this->belongsToCurrentTenant($request)
            && (string) $request->requester_type === $user::class
            && (int) $request->requester_id === $user->id;
    }

    /**
     * Any authenticated tenant member can list their own submitted requests.
     */
    public function viewAnyOwn(User $user): bool
    {
        return $user->memberships()->exists();
    }
}
