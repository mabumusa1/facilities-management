<?php

namespace Database\Factories;

use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Status>
 */
class StatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'name_ar' => fake()->word(),
            'name_en' => fake()->word(),
            'priority' => fake()->numberBetween(1, 10),
            'type' => fake()->randomElement(['request', 'lease', 'unit', 'transaction', 'visitor_access', 'facility_booking']),
        ];
    }
}
