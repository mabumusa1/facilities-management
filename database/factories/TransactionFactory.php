<?php

namespace Database\Factories;

use App\Models\Status;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => 1,
            'type_id' => 1,
            'status_id' => Status::factory()->state(['type' => 'transaction']),
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'due_on' => fake()->dateTimeBetween('-6 months', '+6 months'),
            'is_paid' => false,
            'is_old' => false,
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['is_paid' => true]);
    }
}
