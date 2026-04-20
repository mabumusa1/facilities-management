<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('announcements.VIEW');
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.VIEW');
    }

    public function create(User $user): bool
    {
        return $user->can('announcements.CREATE');
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.UPDATE');
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.DELETE');
    }

    public function restore(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.RESTORE');
    }

    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.FORCE_DELETE');
    }
}
