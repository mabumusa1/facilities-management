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
            'currency' => 'SAR',
            'type' => $this->faker->randomElement(['gym', 'pool', 'hall', 'court', 'other']),
            'pricing_mode' => $this->faker->randomElement(['free', 'per_session', 'per_hour']),
            'requires_booking' => $this->faker->boolean(),
            'booking_horizon_days' => 14,
            'cancellation_hours_before' => 2,
            'min_booking_duration_minutes' => 30,
            'max_booking_duration_minutes' => null,
            'contract_required' => false,
            'notes' => null,
        ];
    }
}
