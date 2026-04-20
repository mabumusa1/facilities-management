<?php

namespace Database\Factories;

use App\Models\FeaturedService;
use App\Models\RequestSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeaturedService>
 */
class FeaturedServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subcategory_id' => RequestSubcategory::factory(),
            'title' => $this->faker->sentence(3),
            'title_ar' => $this->faker->sentence(3),
            'title_en' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'is_active' => true,
        ];
    }
}
