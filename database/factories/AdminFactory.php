<?php

namespace Database\Factories;

use App\Enums\AdminRole;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Admin>
 */
class AdminFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'phone_country_code' => 'SA',
            'role' => fake()->randomElement(AdminRole::cases()),
            'active' => true,
        ];
    }
}
