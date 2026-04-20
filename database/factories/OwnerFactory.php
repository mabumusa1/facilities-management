<?php

namespace Database\Factories;

use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Owner>
 */
class OwnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'phone_country_code' => 'SA',
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
