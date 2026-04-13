<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->country(),
            'name_ar' => fake()->optional()->country(),
            'iso2' => fake()->unique()->countryCode(),
            'iso3' => fake()->unique()->countryISOAlpha3(),
            'dial_code' => '+'.fake()->numberBetween(1, 999),
            'currency_code' => fake()->currencyCode(),
            'capital' => fake()->city(),
            'continent' => fake()->randomElement(['AF', 'AS', 'EU', 'NA', 'SA', 'OC', 'AN']),
            'flag_emoji' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the country is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
