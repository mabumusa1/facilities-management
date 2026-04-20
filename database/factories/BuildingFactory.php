<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Building>
 */
class BuildingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->numerify('Building ###'),
            'rf_community_id' => Community::factory(),
            'no_floors' => fake()->numberBetween(1, 30),
            'year_build' => fake()->optional()->year(),
        ];
    }
}
