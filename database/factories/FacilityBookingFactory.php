<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use App\Models\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityBooking>
 */
class FacilityBookingFactory extends Factory
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
            'status_id' => Status::factory()->state(['type' => 'facility_booking']),
            'booker_type' => Resident::class,
            'booker_id' => Resident::factory(),
            'booking_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'start_time' => '10:00',
            'end_time' => '12:00',
            'number_of_guests' => $this->faker->numberBetween(1, 20),
            'notes' => $this->faker->sentence(),
        ];
    }
}
