<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->streetName(),
            'name_ar' => fake()->streetName(),
            'name_en' => fake()->streetName(),
            'city_id' => City::factory(),
        ];
    }
}
