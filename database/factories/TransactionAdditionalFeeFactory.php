<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionAdditionalFee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionAdditionalFee>
 */
class TransactionAdditionalFeeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'name' => fake()->randomElement(['Late Fee', 'Admin Fee', 'Processing Fee']),
            'amount' => fake()->randomFloat(2, 50, 2000),
        ];
    }
}
