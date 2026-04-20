<?php

namespace Database\Factories;

use App\Models\RequestCategory;
use App\Models\RequestSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestSubcategory>
 */
class RequestSubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => RequestCategory::factory(),
            'name' => $this->faker->word(),
            'name_ar' => $this->faker->word(),
            'name_en' => $this->faker->word(),
            'status' => true,
            'is_all_day' => $this->faker->boolean(),
        ];
    }
}
