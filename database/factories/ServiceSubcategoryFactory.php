<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceSubcategory>
 */
class ServiceSubcategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_category_id' => ServiceCategory::factory(),
            'name_en' => $this->faker->words(2, true),
            'name_ar' => $this->faker->words(2, true),
            'response_sla_hours' => null,
            'resolution_sla_hours' => null,
            'status' => 'active',
        ];
    }

    public function withCustomSla(): static
    {
        return $this->state([
            'response_sla_hours' => $this->faker->randomElement([1, 2, 4]),
            'resolution_sla_hours' => $this->faker->randomElement([6, 12, 24]),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }
}
