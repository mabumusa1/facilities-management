<?php

namespace Database\Factories;

use App\Models\Dependent;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dependent>
 */
class DependentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dependable_type' => Resident::class,
            'dependable_id' => Resident::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'birthdate' => $this->faker->date(),
            'relationship' => $this->faker->randomElement(['spouse', 'child', 'parent', 'sibling']),
        ];
    }
}
