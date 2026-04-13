<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisitorAccess;

class VisitorAccessPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VisitorAccess $visitorAccess): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VisitorAccess $visitorAccess): bool
    {
        // Admins can update any visitor access
        // Requesters can update their own pending requests
        return $user->role === 'admin' ||
            ($visitorAccess->requested_by === $user->id && $visitorAccess->isPending());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VisitorAccess $visitorAccess): bool
    {
        // Only admins can delete visitor access requests
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, VisitorAccess $visitorAccess): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, VisitorAccess $visitorAccess): bool
    {
        return $user->role === 'admin';
    }
}
