<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('settings.VIEW');
    }

    public function view(User $user, Setting $setting): bool
    {
        return $user->can('settings.VIEW');
    }

    public function create(User $user): bool
    {
        return $user->can('settings.CREATE');
    }

    public function update(User $user, Setting $setting): bool
    {
        return $user->can('settings.UPDATE');
    }

    public function delete(User $user, Setting $setting): bool
    {
        return $user->can('settings.DELETE');
    }
}
