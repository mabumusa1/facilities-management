<?php

namespace Database\Factories;

use App\Models\MarketplaceOffer;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceOffer>
 */
class MarketplaceOfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'unit_id' => Unit::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->optional()->paragraph(),
            'discount_type' => fake()->randomElement(['percentage', 'fixed']),
            'discount_value' => fake()->randomFloat(2, 5, 40),
            'start_date' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('+1 week', '+2 months')->format('Y-m-d'),
            'is_active' => true,
        ];
    }
}
