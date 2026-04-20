<?php

namespace Database\Factories;

use App\Models\UnitCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitCategory>
 */
class UnitCategoryFactory extends Factory
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
            'icon' => fake()->randomElement(['residential', 'commercial', 'industrial', 'mixed']),
        ];
    }
}
