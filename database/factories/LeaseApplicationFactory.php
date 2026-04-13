<?php

namespace Database\Factories;

use App\Models\LeaseApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaseApplication>
 */
class LeaseApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $durationMonths = fake()->numberBetween(6, 24);

        return [
            'application_number' => 'APP-'.now()->format('Y').'-'.str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'status' => LeaseApplication::STATUS_DRAFT,
            'applicant_name' => fake()->name(),
            'applicant_email' => fake()->unique()->safeEmail(),
            'applicant_phone' => fake()->phoneNumber(),
            'applicant_type' => fake()->randomElement(['individual', 'company']),
            'company_name' => fake()->optional(0.3)->company(),
            'national_id' => fake()->optional(0.7)->numerify('##########'),
            'commercial_registration' => fake()->optional(0.3)->numerify('CR##########'),
            'quoted_rental_amount' => fake()->numberBetween(10000, 100000),
            'security_deposit' => fake()->numberBetween(5000, 50000),
            'proposed_start_date' => $startDate,
            'proposed_end_date' => (clone $startDate)->modify("+{$durationMonths} months"),
            'proposed_duration_months' => $durationMonths,
            'special_terms' => fake()->optional(0.3)->paragraph(),
            'notes' => fake()->optional(0.5)->paragraph(),
            'source' => fake()->randomElement([
                LeaseApplication::SOURCE_WALK_IN,
                LeaseApplication::SOURCE_WEBSITE,
                LeaseApplication::SOURCE_REFERRAL,
                LeaseApplication::SOURCE_MARKETPLACE,
            ]),
        ];
    }

    /**
     * Indicate that the application is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeaseApplication::STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Indicate that the application is under review.
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeaseApplication::STATUS_REVIEW,
        ]);
    }

    /**
     * Indicate that the application is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeaseApplication::STATUS_APPROVED,
            'reviewed_at' => now(),
            'review_notes' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the application is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeaseApplication::STATUS_REJECTED,
            'reviewed_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the application is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeaseApplication::STATUS_CANCELLED,
        ]);
    }

    /**
     * Indicate that the application is on hold.
     */
    public function onHold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => LeaseApplication::STATUS_ON_HOLD,
        ]);
    }

    /**
     * Indicate that the application has a quote sent.
     */
    public function withQuoteSent(): static
    {
        return $this->state(fn (array $attributes) => [
            'quote_sent_at' => now(),
            'quote_expires_at' => now()->addDays(30),
        ]);
    }

    /**
     * Indicate that the application is for a company.
     */
    public function forCompany(): static
    {
        return $this->state(fn (array $attributes) => [
            'applicant_type' => 'company',
            'company_name' => fake()->company(),
            'commercial_registration' => fake()->numerify('CR##########'),
        ]);
    }

    /**
     * Indicate that the application is for an individual.
     */
    public function forIndividual(): static
    {
        return $this->state(fn (array $attributes) => [
            'applicant_type' => 'individual',
            'company_name' => null,
            'commercial_registration' => null,
            'national_id' => fake()->numerify('##########'),
        ]);
    }
}
