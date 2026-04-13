<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityBooking>
 */
class FacilityBookingFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $bookingDate = fake()->dateTimeBetween('now', '+30 days');
        $startTime = fake()->time('H:i');
        $durationMinutes = fake()->randomElement([30, 60, 90, 120, 180]);
        $startDateTime = Carbon::parse($startTime);
        $endTime = $startDateTime->copy()->addMinutes($durationMinutes)->format('H:i');

        return [
            'tenant_id' => Tenant::factory(),
            'facility_id' => Facility::factory(),
            'contact_id' => Contact::factory(),
            'unit_id' => fn (array $attributes) => Unit::factory()->create([
                'tenant_id' => $attributes['tenant_id'],
            ])->id,
            'status_id' => fn () => Status::factory()->create([
                'domain' => 'facility_booking',
                'slug' => 'facility_booking_pending',
                'name' => 'Pending Approval',
            ])->id,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $durationMinutes,
            'total_price' => fake()->randomFloat(2, 50, 500),
            'notes' => fake()->optional(0.5)->sentence(),
            'special_requests' => fake()->optional(0.3)->sentence(),
        ];
    }

    /**
     * Indicate that the booking is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::factory()->create([
                'domain' => 'facility_booking',
                'slug' => 'facility_booking_pending',
                'name' => 'Pending Approval',
            ])->id,
        ]);
    }

    /**
     * Indicate that the booking is approved/booked.
     */
    public function booked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::factory()->create([
                'domain' => 'facility_booking',
                'slug' => 'facility_booking_booked',
                'name' => 'Booked',
            ])->id,
            'approved_by' => Contact::factory(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the booking is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::factory()->create([
                'domain' => 'facility_booking',
                'slug' => 'facility_booking_scheduled',
                'name' => 'Scheduled',
            ])->id,
            'approved_by' => Contact::factory(),
            'approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the booking is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::factory()->create([
                'domain' => 'facility_booking',
                'slug' => 'facility_booking_completed',
                'name' => 'Completed',
            ])->id,
            'approved_by' => Contact::factory(),
            'approved_at' => now()->subDays(2),
            'checked_in_at' => now()->subDay(),
            'checked_in_by' => Contact::factory(),
            'checked_out_at' => now()->subHours(2),
            'checked_out_by' => Contact::factory(),
        ]);
    }

    /**
     * Indicate that the booking is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::factory()->create([
                'domain' => 'facility_booking',
                'slug' => 'facility_booking_rejected',
                'name' => 'Rejected',
            ])->id,
            'cancellation_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the booking is canceled.
     */
    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::factory()->create([
                'domain' => 'facility_booking',
                'slug' => 'facility_booking_canceled',
                'name' => 'Canceled',
            ])->id,
            'canceled_at' => now(),
            'cancellation_reason' => fake()->sentence(),
        ]);
    }
}
