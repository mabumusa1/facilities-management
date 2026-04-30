<?php

namespace Database\Factories;

use App\Models\Lease;
use App\Models\LeaseRenewalOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseRenewalOffer>
 */
class LeaseRenewalOfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $newStartDate = fake()->dateTimeBetween('+1 month', '+2 years');

        return [
            'lease_id' => Lease::factory(),
            'status_id' => LeaseRenewalOffer::STATUS_DRAFT,
            'new_start_date' => $newStartDate,
            'duration_months' => fake()->randomElement([6, 12, 24]),
            'new_rent_amount' => fake()->randomFloat(2, 10000, 200000),
            'payment_frequency' => fake()->randomElement(['Annual', 'Semi-Annual', 'Quarterly']),
            'valid_until' => fake()->dateTimeBetween('now', '+3 months'),
            'message_en' => fake()->optional()->paragraph(),
            'message_ar' => fake()->optional()->paragraph(),
            'created_by' => User::factory(),
            'account_tenant_id' => null,
        ];
    }

    /** Transition to sent state. */
    public function sent(): static
    {
        return $this->state(['status_id' => LeaseRenewalOffer::STATUS_SENT]);
    }

    /** Transition to accepted state. */
    public function accepted(): static
    {
        return $this->state([
            'status_id' => LeaseRenewalOffer::STATUS_ACCEPTED,
            'decided_at' => now(),
        ]);
    }

    /** Transition to rejected state. */
    public function rejected(): static
    {
        return $this->state([
            'status_id' => LeaseRenewalOffer::STATUS_REJECTED,
            'decided_at' => now(),
        ]);
    }

    /** Transition to expired state. */
    public function expired(): static
    {
        return $this->state(['status_id' => LeaseRenewalOffer::STATUS_EXPIRED]);
    }
}
