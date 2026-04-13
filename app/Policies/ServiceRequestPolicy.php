<?php

namespace App\Policies;

use App\Models\ServiceRequest;
use App\Models\User;

class ServiceRequestPolicy
{
    /**
     * Determine whether the user can view any service requests.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the service request.
     */
    public function view(User $user, ServiceRequest $serviceRequest): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create service requests.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the service request.
     */
    public function update(User $user, ServiceRequest $serviceRequest): bool
    {
        // Admins can update any request
        // Requesters can update their own requests
        return $user->hasRole('admin') || $serviceRequest->requester_id === $user->id;
    }

    /**
     * Determine whether the user can delete the service request.
     */
    public function delete(User $user, ServiceRequest $serviceRequest): bool
    {
        // Only admins can delete requests
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the service request.
     */
    public function restore(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the service request.
     */
    public function forceDelete(User $user, ServiceRequest $serviceRequest): bool
    {
        return $user->hasRole('admin');
    }
}
