<?php

namespace Database\Factories;

use App\Models\RequestCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestCategory>
 */
class RequestCategoryFactory extends Factory
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
            'description' => $this->faker->sentence(),
            'status' => true,
            'has_sub_categories' => $this->faker->boolean(),
        ];
    }
}
