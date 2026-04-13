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
        $types = ['Studio', 'Apartment', 'Villa', 'Townhouse', 'Penthouse', 'Duplex', 'Loft'];

        return [
            'unit_category_id' => UnitCategory::factory(),
            'name' => fake()->unique()->randomElement($types),
            'name_ar' => null,
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the type is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a type for a specific category.
     */
    public function forCategory(UnitCategory $category): static
    {
        return $this->state(fn (array $attributes) => [
            'unit_category_id' => $category->id,
        ]);
    }
}
