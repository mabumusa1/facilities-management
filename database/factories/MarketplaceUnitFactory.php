<?php

namespace Database\Factories;

use App\Models\MarketplaceUnit;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceUnit>
 */
class MarketplaceUnitFactory extends Factory
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
            'listing_type' => $this->faker->randomElement(['rent', 'sale']),
            'price' => $this->faker->randomFloat(2, 10000, 500000),
            'is_active' => true,
        ];
    }
}
