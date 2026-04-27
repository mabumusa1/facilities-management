<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserStatusService
{
    public function deactivate(User $user, ?string $reason = null): void
    {
        DB::transaction(function () use ($user): void {
            $user->update(['status' => User::STATUS_DEACTIVATED]);

            $user->forceFill([
                'remember_token' => null,
            ])->save();

            DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        });
    }

    public function reactivate(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->update(['status' => User::STATUS_ACTIVE]);
        });
    }
}
