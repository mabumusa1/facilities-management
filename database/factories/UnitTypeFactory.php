<?php

namespace Database\Factories;

use App\Models\UnitCategory;
use App\Models\UnitType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitType>
 */
class UnitTypeFactory extends Factory
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
            'icon' => fake()->word(),
            'category_id' => UnitCategory::factory(),
        ];
    }
}
