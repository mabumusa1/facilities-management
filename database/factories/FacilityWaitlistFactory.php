<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityWaitlist;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityWaitlist>
 */
class FacilityWaitlistFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+30 days');
        $end = clone $start;
        $end->modify('+60 minutes');

        return [
            'facility_id' => Facility::factory(),
            'resident_id' => Resident::factory(),
            'requested_start_at' => $start,
            'requested_end_at' => $end,
            'notified_at' => null,
            'ttl_expires_at' => null,
        ];
    }
}
