<?php

namespace Database\Factories;

use App\Models\Receipt;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Receipt>
 */
class ReceiptFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'status' => 'generated',
            'pdf_path' => null,
            'sent_at' => null,
            'sent_to_name' => null,
            'sent_to_email' => null,
        ];
    }

    public function settingsIncomplete(): static
    {
        return $this->state(fn () => ['status' => 'settings_incomplete']);
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => 'generated',
            'sent_at' => now(),
            'sent_to_name' => fake()->name(),
            'sent_to_email' => fake()->safeEmail(),
        ]);
    }
}
