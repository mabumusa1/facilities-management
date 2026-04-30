<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => $this->faker->name(),
            'name_ar' => $this->faker->name(),
            'name' => $this->faker->name(),
            'phone_country_code' => '+966',
            'phone_number' => $this->faker->numerify('5########'),
            'email' => $this->faker->safeEmail(),
            'notes' => null,
        ];
    }
}
