<?php

namespace Database\Factories;

use App\Enums\InspectionCondition;
use App\Models\MoveOut;
use App\Models\MoveOutRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MoveOutRoom>
 */
class MoveOutRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'move_out_id' => MoveOut::factory(),
            'name' => fake()->randomElement(['Living Room', 'Kitchen', 'Bedroom 1', 'Bedroom 2', 'Bathroom']),
            'condition' => fake()->randomElement(InspectionCondition::cases()),
            'notes' => fake()->optional()->sentence(),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
