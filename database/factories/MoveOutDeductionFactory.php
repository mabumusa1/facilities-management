<?php

namespace Database\Factories;

use App\Enums\DeductionReason;
use App\Models\MoveOut;
use App\Models\MoveOutDeduction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MoveOutDeduction>
 */
class MoveOutDeductionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'move_out_id' => MoveOut::factory(),
            'label_en' => fake()->words(2, true),
            'label_ar' => fake()->words(2, true),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'reason' => fake()->randomElement(DeductionReason::cases()),
        ];
    }
}
