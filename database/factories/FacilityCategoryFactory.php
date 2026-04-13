<?php

namespace Database\Factories;

use App\Models\FacilityCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityCategory>
 */
class FacilityCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Sports', 'Recreation', 'Community', 'Business', 'Wellness', 'Entertainment'];

        return [
            'name' => fake()->unique()->randomElement($categories),
            'name_ar' => null,
            'description' => fake()->sentence(),
            'icon' => fake()->optional()->word(),
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

    /**
     * Create a category with an icon.
     */
    public function withIcon(string $icon): static
    {
        return $this->state(fn (array $attributes) => [
            'icon' => $icon,
        ]);
    }
}
