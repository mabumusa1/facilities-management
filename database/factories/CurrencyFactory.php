<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word().' Dollar',
            'name_ar' => null,
            'code' => fake()->unique()->currencyCode(),
            'symbol' => fake()->randomElement(['$', '€', '£', '¥', '₹', 'د.إ', 'ر.س']),
            'decimal_places' => 2,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the currency is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Create a currency with no decimal places.
     */
    public function noDecimals(): static
    {
        return $this->state(fn (array $attributes) => [
            'decimal_places' => 0,
        ]);
    }
}
