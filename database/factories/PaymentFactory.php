<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'amount' => fake()->randomFloat(2, 500, 25000),
            'payment_date' => fake()->date(),
            'payment_method' => fake()->randomElement(['cash', 'bank_transfer', 'cheque', 'online']),
            'reference' => fake()->optional()->numerify('PAY-######'),
        ];
    }
}
