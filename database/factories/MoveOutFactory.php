<?php

namespace Database\Factories;

use App\Enums\MoveOutReason;
use App\Models\Lease;
use App\Models\MoveOut;
use App\Models\Status;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MoveOut>
 */
class MoveOutFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lease_id' => Lease::factory(),
            'move_out_date' => fake()->dateTimeBetween('now', '+3 months'),
            'reason' => fake()->randomElement(MoveOutReason::cases()),
            'status_id' => Status::factory()->state(['type' => 'move_out']),
            'initiated_by' => User::factory(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
