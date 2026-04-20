<?php

namespace App\Policies;

use App\Models\Request;
use App\Models\User;

class RequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('requests.VIEW');
    }

    public function view(User $user, Request $request): bool
    {
        return $user->can('requests.VIEW');
    }

    public function create(User $user): bool
    {
        return $user->can('requests.CREATE');
    }

    public function update(User $user, Request $request): bool
    {
        return $user->can('requests.UPDATE');
    }

    public function delete(User $user, Request $request): bool
    {
        return $user->can('requests.DELETE');
    }

    public function restore(User $user, Request $request): bool
    {
        return $user->can('requests.RESTORE');
    }

    public function forceDelete(User $user, Request $request): bool
    {
        return $user->can('requests.FORCE_DELETE');
    }
}
