<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\MarketplaceCustomer;
use App\Models\MarketplaceOffer;
use App\Models\MarketplaceUnit;
use App\Models\MarketplaceVisit;
use App\Models\Status;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceOffer>
 */
class MarketplaceOfferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $offerAmount = fake()->randomFloat(2, 100000, 5000000);

        return [
            'tenant_id' => Tenant::factory(),
            'marketplace_unit_id' => MarketplaceUnit::factory(),
            'marketplace_customer_id' => MarketplaceCustomer::factory(),
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_draft'],
                ['domain' => 'marketplace_offer', 'name' => 'Draft']
            )->id,
            'offer_reference' => 'OFF-'.strtoupper(fake()->bothify('??????')),
            'offer_type' => fake()->randomElement(['purchase', 'booking', 'lease']),
            'offer_amount' => $offerAmount,
            'currency' => 'SAR',
            'payment_method' => fake()->optional(0.7)->randomElement(['cash', 'mortgage', 'installments', 'mixed']),
            'installment_months' => fn (array $attributes) => $attributes['payment_method'] === 'installments' ? fake()->randomElement([12, 24, 36, 48, 60]) : null,
            'down_payment_percentage' => fake()->optional(0.5)->randomFloat(2, 10, 50),
            'conditions' => fake()->optional(0.3)->sentence(),
            'customer_message' => fake()->optional(0.5)->paragraph(),
            'valid_until' => fake()->optional(0.7)->dateTimeBetween('+7 days', '+30 days'),
            'assigned_agent' => fake()->optional(0.6)->passthrough(Contact::factory()),
            'negotiation_rounds' => 0,
            'is_counter_offer' => false,
        ];
    }

    /**
     * Indicate that the offer is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_draft'],
                ['domain' => 'marketplace_offer', 'name' => 'Draft']
            )->id,
            'submitted_at' => null,
        ]);
    }

    /**
     * Indicate that the offer is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_submitted'],
                ['domain' => 'marketplace_offer', 'name' => 'Submitted']
            )->id,
            'submitted_at' => now()->subDays(fake()->numberBetween(1, 7)),
        ]);
    }

    /**
     * Indicate that the offer is in negotiation.
     */
    public function negotiating(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_negotiating'],
                ['domain' => 'marketplace_offer', 'name' => 'Negotiating']
            )->id,
            'submitted_at' => now()->subDays(fake()->numberBetween(3, 14)),
            'assigned_agent' => Contact::factory(),
            'negotiation_rounds' => fake()->numberBetween(1, 5),
        ]);
    }

    /**
     * Indicate that the offer is under review.
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_review'],
                ['domain' => 'marketplace_offer', 'name' => 'Under Review']
            )->id,
            'submitted_at' => now()->subDays(fake()->numberBetween(5, 14)),
            'assigned_agent' => Contact::factory(),
        ]);
    }

    /**
     * Indicate that the offer is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_approved'],
                ['domain' => 'marketplace_offer', 'name' => 'Approved']
            )->id,
            'submitted_at' => now()->subDays(fake()->numberBetween(7, 21)),
            'reviewed_at' => now()->subDays(fake()->numberBetween(1, 7)),
            'reviewed_by' => Contact::factory(),
            'approved_at' => now()->subDays(fake()->numberBetween(1, 3)),
            'approved_by' => Contact::factory(),
        ]);
    }

    /**
     * Indicate that the offer is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_rejected'],
                ['domain' => 'marketplace_offer', 'name' => 'Rejected']
            )->id,
            'submitted_at' => now()->subDays(fake()->numberBetween(7, 21)),
            'reviewed_at' => now()->subDays(fake()->numberBetween(1, 7)),
            'reviewed_by' => Contact::factory(),
            'rejected_at' => now()->subDays(fake()->numberBetween(1, 3)),
            'rejection_reason' => fake()->randomElement([
                'Price too low',
                'Unqualified buyer',
                'Unit already sold',
                'Financing not approved',
            ]),
        ]);
    }

    /**
     * Indicate that the offer is accepted.
     */
    public function accepted(): static
    {
        $offerAmount = fake()->randomFloat(2, 100000, 5000000);

        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_accepted'],
                ['domain' => 'marketplace_offer', 'name' => 'Accepted']
            )->id,
            'offer_amount' => $offerAmount,
            'submitted_at' => now()->subDays(fake()->numberBetween(14, 30)),
            'approved_at' => now()->subDays(fake()->numberBetween(7, 14)),
            'approved_by' => Contact::factory(),
            'accepted_at' => now()->subDays(fake()->numberBetween(1, 7)),
            'final_amount' => $offerAmount,
        ]);
    }

    /**
     * Indicate that the offer is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_cancelled'],
                ['domain' => 'marketplace_offer', 'name' => 'Cancelled']
            )->id,
            'cancelled_at' => now()->subDays(fake()->numberBetween(1, 14)),
            'rejection_reason' => fake()->optional(0.7)->randomElement([
                'Customer withdrew',
                'Found another property',
                'Financial issues',
            ]),
        ]);
    }

    /**
     * Indicate that the offer is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_expired'],
                ['domain' => 'marketplace_offer', 'name' => 'Expired']
            )->id,
            'valid_until' => now()->subDays(fake()->numberBetween(1, 14)),
            'expired_at' => now()->subDays(fake()->numberBetween(1, 7)),
        ]);
    }

    /**
     * Indicate that the offer has a signed contract.
     */
    public function contracted(): static
    {
        $offerAmount = fake()->randomFloat(2, 100000, 5000000);

        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_contracted'],
                ['domain' => 'marketplace_offer', 'name' => 'Contracted']
            )->id,
            'offer_amount' => $offerAmount,
            'final_amount' => $offerAmount,
            'submitted_at' => now()->subDays(fake()->numberBetween(21, 60)),
            'approved_at' => now()->subDays(fake()->numberBetween(14, 30)),
            'accepted_at' => now()->subDays(fake()->numberBetween(7, 21)),
            'contract_reference' => 'CNT-'.strtoupper(fake()->bothify('??????')),
            'contract_signed_at' => now()->subDays(fake()->numberBetween(1, 14)),
            'contract_signed_by' => Contact::factory(),
        ]);
    }

    /**
     * Indicate that the offer is completed.
     */
    public function completed(): static
    {
        $offerAmount = fake()->randomFloat(2, 100000, 5000000);

        return $this->state(fn (array $attributes) => [
            'status_id' => fn () => Status::firstOrCreate(
                ['slug' => 'marketplace_offer_completed'],
                ['domain' => 'marketplace_offer', 'name' => 'Completed']
            )->id,
            'offer_amount' => $offerAmount,
            'final_amount' => $offerAmount,
            'submitted_at' => now()->subDays(fake()->numberBetween(30, 90)),
            'approved_at' => now()->subDays(fake()->numberBetween(21, 60)),
            'accepted_at' => now()->subDays(fake()->numberBetween(14, 45)),
            'contract_signed_at' => now()->subDays(fake()->numberBetween(7, 30)),
            'completed_at' => now()->subDays(fake()->numberBetween(1, 14)),
        ]);
    }

    /**
     * Indicate that a deposit was paid.
     */
    public function withDeposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_deposit' => fake()->randomFloat(2, 5000, 50000),
            'deposit_paid_at' => now()->subDays(fake()->numberBetween(1, 14)),
            'deposit_payment_reference' => 'DEP-'.strtoupper(fake()->bothify('??????')),
        ]);
    }

    /**
     * Indicate that this is a counter offer.
     */
    public function counterOffer(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_counter_offer' => true,
            'parent_offer_id' => MarketplaceOffer::factory(),
            'negotiation_rounds' => fake()->numberBetween(1, 5),
        ]);
    }

    /**
     * Indicate this is a purchase offer.
     */
    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'offer_type' => 'purchase',
        ]);
    }

    /**
     * Indicate this is a booking offer.
     */
    public function booking(): static
    {
        return $this->state(fn (array $attributes) => [
            'offer_type' => 'booking',
            'booking_deposit' => fake()->randomFloat(2, 5000, 25000),
        ]);
    }

    /**
     * Indicate this is a lease offer.
     */
    public function lease(): static
    {
        return $this->state(fn (array $attributes) => [
            'offer_type' => 'lease',
            'offer_amount' => fake()->randomFloat(2, 5000, 50000), // Monthly rent
        ]);
    }

    /**
     * Indicate offer is linked to a visit.
     */
    public function fromVisit(): static
    {
        return $this->state(fn (array $attributes) => [
            'marketplace_visit_id' => MarketplaceVisit::factory(),
        ]);
    }
}
