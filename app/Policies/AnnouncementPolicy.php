<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use App\Support\ManagerScopeHelper;

class AnnouncementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('announcements.VIEW');
    }

    public function view(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.VIEW')
            && ManagerScopeHelper::userCanAccessModel($user, $announcement);
    }

    public function create(User $user): bool
    {
        return $user->can('announcements.CREATE');
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.UPDATE')
            && ManagerScopeHelper::userCanAccessModel($user, $announcement);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.DELETE')
            && ManagerScopeHelper::userCanAccessModel($user, $announcement);
    }

    public function restore(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.RESTORE')
            && ManagerScopeHelper::userCanAccessModel($user, $announcement);
    }

    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return $user->can('announcements.FORCE_DELETE')
            && ManagerScopeHelper::userCanAccessModel($user, $announcement);
    }
}
