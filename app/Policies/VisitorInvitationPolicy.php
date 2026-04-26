<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisitorInvitation;

class VisitorInvitationPolicy
{
    /**
     * Any authenticated resident may list their invitations.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Any authenticated resident may create an invitation.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Residents may view their own invitations only.
     */
    public function view(User $user, VisitorInvitation $visitorInvitation): bool
    {
        return $visitorInvitation->resident_id === $user->id;
    }

    /**
     * Residents may cancel only their own active invitations.
     */
    public function cancel(User $user, VisitorInvitation $visitorInvitation): bool
    {
        return $visitorInvitation->resident_id === $user->id
            && $visitorInvitation->status === 'active';
    }
}
