<?php

namespace Database\Factories;

use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceCategory>
 */
class ServiceCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name_en' => $this->faker->words(2, true),
            'name_ar' => $this->faker->words(2, true),
            'icon' => $this->faker->randomElement(['🔧', '💡', '❄️', '🧹', '🐜', '🔌', '🚿', '🏠']),
            'response_sla_hours' => $this->faker->randomElement([1, 2, 4, 8, 24]),
            'resolution_sla_hours' => $this->faker->randomElement([6, 12, 24, 48, 72]),
            'default_assignee_id' => null,
            'require_completion_photo' => false,
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactive']);
    }
}
