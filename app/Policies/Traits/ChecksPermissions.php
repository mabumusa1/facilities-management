<?php

declare(strict_types=1);

namespace App\Policies\Traits;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use App\Models\User;

/**
 * Trait that provides permission checking functionality for policies.
 */
trait ChecksPermissions
{
    /**
     * The permission subject for this policy.
     */
    abstract protected function subject(): PermissionSubject;

    /**
     * Check if the user has a specific permission for this subject.
     */
    protected function hasPermission(User $user, PermissionAction $action): bool
    {
        $permissionName = $this->subject()->permissionFor($action);

        return $user->hasPermissionTo($permissionName);
    }

    /**
     * Check if the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, PermissionAction::View);
    }

    /**
     * Check if the user can view the model.
     */
    public function view(User $user, mixed $model): bool
    {
        return $this->hasPermission($user, PermissionAction::View);
    }

    /**
     * Check if the user can create models.
     */
    public function create(User $user): bool
    {
        return $this->hasPermission($user, PermissionAction::Create);
    }

    /**
     * Check if the user can update the model.
     */
    public function update(User $user, mixed $model): bool
    {
        return $this->hasPermission($user, PermissionAction::Edit);
    }

    /**
     * Check if the user can delete the model.
     */
    public function delete(User $user, mixed $model): bool
    {
        return $this->hasPermission($user, PermissionAction::Delete);
    }

    /**
     * Check if the user can restore the model.
     */
    public function restore(User $user, mixed $model): bool
    {
        return $this->hasPermission($user, PermissionAction::Manage);
    }

    /**
     * Check if the user can permanently delete the model.
     */
    public function forceDelete(User $user, mixed $model): bool
    {
        return $this->hasPermission($user, PermissionAction::Manage);
    }
}
