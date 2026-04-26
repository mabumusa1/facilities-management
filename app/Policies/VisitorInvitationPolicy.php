<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisitorInvitation;

class VisitorInvitationPolicy
{
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
