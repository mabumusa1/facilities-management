<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\LeaseQuote;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\Status;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseQuote>
 */
class LeaseQuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quote_number' => null,
            'unit_id' => Unit::factory(),
            'contact_id' => Resident::factory(),
            'contract_type_id' => null,
            'status_id' => Status::factory()->state(['type' => 'lease_quote']),
            'duration_months' => fake()->numberBetween(1, 24),
            'start_date' => fake()->dateTimeBetween('now', '+1 month'),
            'rent_amount' => fake()->randomFloat(2, 1000, 50000),
            'payment_frequency_id' => Setting::factory()->state(['type' => 'payment_frequency']),
            'security_deposit' => fake()->randomFloat(2, 0, 10000),
            'additional_charges' => null,
            'special_conditions' => null,
            'valid_until' => fake()->dateTimeBetween('+7 days', '+30 days'),
            'version' => 1,
            'parent_quote_id' => null,
            'marketplace_unit_id' => null,
            'created_by_id' => Admin::factory(),
        ];
    }

    /**
     * Draft state — the initial status for new quotes.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_number' => null,
            'status_id' => Status::factory()->state(['type' => 'lease_quote', 'name_en' => 'draft']),
        ]);
    }

    /**
     * Expired state — valid_until is in the past.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'valid_until' => fake()->dateTimeBetween('-30 days', '-1 day'),
            'status_id' => Status::factory()->state(['type' => 'lease_quote', 'name_en' => 'expired']),
        ]);
    }
}
