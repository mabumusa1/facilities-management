<?php

namespace Database\Factories;

use App\Models\Unit;
use App\Models\UnitRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UnitRoom>
 */
class UnitRoomFactory extends Factory
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
            'name' => $this->faker->randomElement(['Bedroom', 'Living Room', 'Kitchen', 'Bathroom']),
            'name_ar' => $this->faker->word(),
            'name_en' => $this->faker->word(),
            'count' => $this->faker->numberBetween(1, 3),
        ];
    }
}
