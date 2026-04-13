<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Contact;
use App\Models\Status;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\VisitorAccess;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VisitorAccess>
 */
class VisitorAccessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+30 days');
        $endDate = fake()->optional(0.7)->dateTimeBetween($startDate, '+37 days');

        return [
            'tenant_id' => Tenant::factory(),
            'unit_id' => Unit::factory(),
            'building_id' => fn (array $attributes) => Unit::find($attributes['unit_id'])?->building_id,
            'community_id' => fn (array $attributes) => Building::find($attributes['building_id'])?->community_id,
            'requested_by' => Contact::factory(),
            'status_id' => Status::factory(),
            'visitor_name' => fake()->name(),
            'visitor_email' => fake()->optional(0.8)->safeEmail(),
            'visitor_phone' => fake()->optional(0.8)->phoneNumber(),
            'visitor_id_number' => fake()->optional(0.6)->numerify('##########'),
            'visitor_vehicle_plate' => fake()->optional(0.5)->bothify('??? ###'),
            'visit_start_date' => $startDate,
            'visit_start_time' => fake()->optional(0.7)->time(),
            'visit_end_date' => $endDate,
            'visit_end_time' => $endDate ? fake()->optional(0.7)->time() : null,
            'access_type' => fake()->randomElement(['one-time', 'recurring', 'permanent']),
            'purpose' => fake()->optional(0.8)->sentence(),
            'notes' => fake()->optional(0.4)->paragraph(),
        ];
    }

    /**
     * Indicate that the visitor access is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'visitor')
                ->where('slug', 'visitor_pending')
                ->first()?->id ?? $attributes['status_id'],
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the visitor access is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'visitor')
                ->where('slug', 'visitor_approved')
                ->first()?->id ?? $attributes['status_id'],
            'approved_by' => Contact::factory(),
            'approved_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Indicate that the visitor access is denied.
     */
    public function denied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => Status::where('domain', 'visitor')
                ->where('slug', 'visitor_denied')
                ->first()?->id ?? $attributes['status_id'],
            'approved_by' => Contact::factory(),
            'approved_at' => fake()->dateTimeBetween('-7 days', 'now'),
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
