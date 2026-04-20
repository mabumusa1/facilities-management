<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            $tenant = Tenant::create([
                'name' => $input['name']."'s Account",
                'domain' => null,
                'database' => null,
            ]);

            AccountMembership::create([
                'user_id' => $user->id,
                'account_tenant_id' => $tenant->id,
                'role' => RolesEnum::ACCOUNT_ADMINS->value,
            ]);

            $user->assignRole(RolesEnum::ACCOUNT_ADMINS);

            session()->put('tenant_id', $tenant->id);
            $tenant->makeCurrent();

            return $user;
        });
    }
}
