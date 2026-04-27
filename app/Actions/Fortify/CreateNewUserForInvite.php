<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\AccountMembership;
use App\Models\InviteCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUserForInvite implements CreatesNewUsers
{
    use PasswordValidationRules;

    public function create(array $input): User
    {
        Validator::make($input, [
            'code' => ['required', 'string', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        $inviteCode = InviteCode::where('code', $input['code'])->first();

        if (! $inviteCode) {
            throw ValidationException::withMessages([
                'code' => [__('This invite code is invalid or has already been used.')],
            ]);
        }

        if ($inviteCode->isUsed()) {
            throw ValidationException::withMessages([
                'code' => [__('This invite code is invalid or has already been used.')],
            ]);
        }

        if ($inviteCode->isExpired()) {
            throw ValidationException::withMessages([
                'code' => [__('This invite code has expired. Please contact your property manager.')],
            ]);
        }

        return DB::transaction(function () use ($input, $inviteCode) {
            $user = User::create([
                'name' => $input['name'] ?? 'Resident',
                'email' => $input['email'] ?? $inviteCode->code.'@placeholder.local',
                'password' => $input['password'],
            ]);

            if ($inviteCode->tenant_id) {
                AccountMembership::create([
                    'user_id' => $user->id,
                    'account_tenant_id' => $inviteCode->tenant_id,
                    'role' => 'resident',
                ]);

                session()->put('tenant_id', $inviteCode->tenant_id);
            }

            $inviteCode->update([
                'used_by' => $user->id,
                'used_at' => now(),
            ]);

            return $user;
        });
    }
}
