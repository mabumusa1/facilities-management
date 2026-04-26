<?php

namespace Database\Factories;

use App\Models\OwnerRegistration;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OwnerRegistration>
 */
class OwnerRegistrationFactory extends Factory
{
    protected $model = OwnerRegistration::class;

    public function definition(): array
    {
        return [
            'account_tenant_id' => fn () => Tenant::create(['name' => fake()->unique()->company()])->id,
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending']);
    }
}
