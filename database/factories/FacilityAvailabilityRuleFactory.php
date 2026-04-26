<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityAvailabilityRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityAvailabilityRule>
 */
class FacilityAvailabilityRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'facility_id' => Facility::factory(),
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'open_time' => '06:00',
            'close_time' => '22:00',
            'slot_duration_minutes' => 60,
            'max_concurrent_bookings' => 1,
            'is_active' => true,
        ];
    }
}
