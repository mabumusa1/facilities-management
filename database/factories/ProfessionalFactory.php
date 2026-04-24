<?php

namespace Database\Factories;

use App\Models\Professional;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Professional>
 */
class ProfessionalFactory extends Factory
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
            'national_phone_number' => null,
            'phone_country_code' => 'SA',
            'active' => true,
        ];
    }
}
