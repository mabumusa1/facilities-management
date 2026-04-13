<?php

namespace Database\Factories;

use App\Models\Amenity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Amenity>
 */
class AmenityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amenities = [
            'Swimming Pool', 'Gym', 'Parking', 'Security', 'Playground',
            'Garden', 'Balcony', 'Concierge', 'Spa', 'Sauna',
        ];

        return [
            'name' => fake()->unique()->randomElement($amenities),
            'name_ar' => null,
            'description' => fake()->sentence(),
            'icon' => fake()->optional()->word(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the amenity is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create an amenity with an icon.
     */
    public function withIcon(string $icon): static
    {
        return $this->state(fn (array $attributes) => [
            'icon' => $icon,
        ]);
    }
}
