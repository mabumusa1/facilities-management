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
        $categories = ['Residential', 'Commercial', 'Industrial', 'Mixed Use', 'Retail', 'Office', 'Warehouse', 'Storage', 'Hospitality', 'Healthcare'];

        return [
            'name' => fake()->randomElement($categories).' '.fake()->numberBetween(1, 9999),
            'name_ar' => null,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
