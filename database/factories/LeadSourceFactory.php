<?php

namespace Database\Factories;

use App\Models\LeadSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeadSource>
 */
class LeadSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'name_ar' => $this->faker->word(),
            'name_en' => $this->faker->word(),
        ];
    }
}
