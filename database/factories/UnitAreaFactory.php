<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\UnitArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitArea>
 */
class UnitAreaFactory extends Factory
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
            'type' => $this->faker->randomElement(['gross', 'net', 'balcony', 'terrace']),
            'size' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
