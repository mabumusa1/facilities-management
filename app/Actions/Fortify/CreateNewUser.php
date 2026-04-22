<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use App\Support\DefaultSubscriptionPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input): User
    {
        $resolvedName = $this->resolvedName($input);

        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => $this->passwordRules(),
            'tenant_name' => ['required', 'string', 'max:255'],
            'terms' => ['accepted'],
            'name' => ['nullable', 'string', 'max:255'],
        ], [
            'first_name.required' => __('The first name field is required.'),
            'last_name.required' => __('The last name field is required.'),
            'phone_number.required' => __('The phone number field is required.'),
            'tenant_name.required' => __('The account name field is required.'),
            'terms.accepted' => __('You must accept the terms and conditions.'),
        ])->validate();

        return DB::transaction(function () use ($input, $resolvedName) {
            $user = User::create([
                'name' => $resolvedName,
                'email' => $input['email'],
                'phone_number' => $input['phone_number'],
                'password' => $input['password'],
            ]);

            $tenant = Tenant::create([
                'name' => $input['tenant_name'],
                'domain' => null,
                'database' => null,
            ]);

            $plan = DefaultSubscriptionPlan::ensure();
            $tenant->newPlanSubscription('main', $plan);

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

    /**
     * @param  array<string, mixed>  $input
     */
    private function resolvedName(array $input): string
    {
        $fullName = trim(sprintf(
            '%s %s',
            $input['first_name'] ?? '',
            $input['last_name'] ?? '',
        ));

        if ($fullName !== '') {
            return $fullName;
        }

        return $input['name'] ?? '';
    }
}
