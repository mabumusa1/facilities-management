<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => FacilityCategory::factory(),
            'name' => $this->faker->word(),
            'name_ar' => $this->faker->word(),
            'name_en' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'capacity' => $this->faker->numberBetween(10, 100),
            'open_time' => '08:00',
            'close_time' => '22:00',
            'booking_fee' => $this->faker->randomFloat(2, 0, 500),
            'is_active' => true,
            'requires_approval' => $this->faker->boolean(),
        ];
    }
}
