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
            'iso2' => fake()->unique()->countryCode(),
            'iso3' => fake()->unique()->countryISOAlpha3(),
            'name' => fake()->country(),
            'name_ar' => fake()->country(),
            'name_en' => fake()->country(),
            'dial' => fake()->numerify('###'),
            'currency' => fake()->currencyCode(),
            'capital' => fake()->city(),
            'continent' => fake()->randomElement(['AF', 'AS', 'EU', 'NA', 'SA', 'OC', 'AN']),
        ];
    }
}
