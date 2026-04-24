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
            'first_name_ar' => null,
            'last_name' => fake()->lastName(),
            'last_name_ar' => null,
            'id_type' => null,
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

    public function withPhone(string $phone): static
    {
        return $this->state(fn () => ['national_phone_number' => $phone]);
    }

    public function arabicOnly(): static
    {
        return $this->state(fn () => [
            'first_name' => null,
            'last_name' => null,
            'first_name_ar' => fake()->firstName(),
            'last_name_ar' => fake()->lastName(),
        ]);
    }
}
