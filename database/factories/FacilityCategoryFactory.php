<?php

namespace Database\Factories;

use App\Models\FacilityCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityCategory>
 */
class FacilityCategoryFactory extends Factory
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
            'status' => true,
        ];
    }
}
