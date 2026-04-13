<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\MarketplaceCustomer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceVisit>
 */
class MarketplaceVisitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $visitDate = fake()->dateTimeBetween('now', '+30 days');
        $startHour = fake()->numberBetween(9, 17);
        $duration = fake()->randomElement([30, 45, 60, 90, 120]);

        return [
            'tenant_id' => Tenant::factory(),
            'marketplace_unit_id' => MarketplaceUnit::factory(),
            'marketplace_customer_id' => MarketplaceCustomer::factory(),
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_visit_pending'],
                ['domain' => 'marketplace_visit', 'name' => 'Pending']
            )->id,
            'visit_date' => $visitDate,
            'visit_time' => sprintf('%02d:00:00', $startHour),
            'visit_end_time' => sprintf('%02d:%02d:00', $startHour + intdiv($duration, 60), $duration % 60),
            'duration_minutes' => $duration,
            'is_all_day' => false,
            'assigned_agent' => fake()->optional(0.7)->passthrough(Contact::factory()),
            'customer_notes' => fake()->optional(0.5)->sentence(),
        ];
    }

    /**
     * Indicate that the visit is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_visit_pending'],
                ['domain' => 'marketplace_visit', 'name' => 'Pending']
            )->id,
            'visit_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    /**
     * Indicate that the visit is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_visit_confirmed'],
                ['domain' => 'marketplace_visit', 'name' => 'Confirmed']
            )->id,
            'confirmed_at' => now()->subHours(fake()->numberBetween(1, 48)),
            'confirmed_by' => Contact::factory(),
            'visit_date' => fake()->dateTimeBetween('+1 day', '+14 days'),
        ]);
    }

    /**
     * Indicate that the visit is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_visit_completed'],
                ['domain' => 'marketplace_visit', 'name' => 'Completed']
            )->id,
            'visit_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'confirmed_at' => now()->subDays(fake()->numberBetween(2, 30)),
            'confirmed_by' => Contact::factory(),
            'completed_at' => now()->subDays(fake()->numberBetween(1, 7)),
            'outcome' => fake()->randomElement(['interested', 'not_interested', 'follow_up', 'offer_made']),
            'interest_level' => fake()->numberBetween(1, 10),
            'feedback' => fake()->optional(0.7)->paragraph(),
            'agent_notes' => fake()->optional(0.5)->sentence(),
        ]);
    }

    /**
     * Indicate that the visit is canceled.
     */
    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_visit_canceled'],
                ['domain' => 'marketplace_visit', 'name' => 'Canceled']
            )->id,
            'canceled_at' => now()->subHours(fake()->numberBetween(1, 72)),
            'cancellation_reason' => fake()->randomElement([
                'Customer request',
                'Schedule conflict',
                'Unit no longer available',
                'Agent unavailable',
                'Rescheduled',
            ]),
        ]);
    }

    /**
     * Indicate that the visit was a no-show.
     */
    public function noShow(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_visit_no_show'],
                ['domain' => 'marketplace_visit', 'name' => 'No Show']
            )->id,
            'visit_date' => fake()->dateTimeBetween('-14 days', '-1 day'),
            'confirmed_at' => now()->subDays(fake()->numberBetween(2, 14)),
        ]);
    }

    /**
     * Indicate that the visit is an all-day event.
     */
    public function allDay(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_all_day' => true,
            'visit_time' => null,
            'visit_end_time' => null,
            'duration_minutes' => 480, // 8 hours
        ]);
    }

    /**
     * Indicate that the visit is today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'visit_date' => today(),
        ]);
    }

    /**
     * Indicate that the visit has an assigned agent.
     */
    public function withAgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_agent' => Contact::factory(),
        ]);
    }
}
