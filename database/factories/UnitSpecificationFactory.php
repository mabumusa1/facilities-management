<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\UnitSpecification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitSpecification>
 */
class UnitSpecificationFactory extends Factory
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
            'key' => $this->faker->randomElement(['bedrooms', 'bathrooms', 'parking', 'balcony']),
            'value' => (string) $this->faker->numberBetween(1, 5),
        ];
    }
}
