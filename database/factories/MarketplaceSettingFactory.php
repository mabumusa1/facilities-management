<?php

namespace Database\Factories;

use App\Models\MarketplaceSetting;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketplaceSetting>
 */
class MarketplaceSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'marketplace_name' => fake()->company().' Marketplace',
            'marketplace_description' => fake()->paragraph(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'is_active' => true,
            'default_deposit_percentage' => 10.00,
            'minimum_deposit_amount' => fake()->optional(0.5)->randomFloat(2, 1000, 10000),
            'maximum_deposit_amount' => fake()->optional(0.5)->randomFloat(2, 50000, 500000),
            'deposit_required' => true,
            'deposit_refund_policy' => 'full',
            'deposit_refund_days' => 30,
            'deposit_terms' => fake()->optional(0.5)->paragraph(),
            'accepted_payment_methods' => ['cash', 'bank_transfer', 'mortgage'],
            'allow_installments' => true,
            'installment_options' => [12, 24, 36, 48, 60],
            'max_installment_months' => 60,
            'minimum_down_payment_percentage' => 20.00,
            'allow_mortgage' => true,
            'payment_terms_text' => fake()->optional(0.5)->paragraph(),
            'bank_currency' => 'SAR',
            'agent_commission_percentage' => 2.00,
            'platform_commission_percentage' => 0.00,
            'commission_on_gross' => true,
            'default_listing_days' => 90,
            'max_listing_days' => 365,
            'auto_renew_listings' => false,
            'featured_listing_days' => 30,
            'featured_listing_fee' => fake()->optional(0.5)->randomFloat(2, 100, 1000),
            'visit_available_days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'],
            'visit_start_time' => '09:00:00',
            'visit_end_time' => '18:00:00',
            'visit_duration_minutes' => 60,
            'min_visit_notice_hours' => 24,
            'offer_validity_days' => 7,
            'allow_counter_offers' => true,
            'max_negotiation_rounds' => 5,
            'auto_reject_low_offers' => false,
            'min_offer_percentage' => fake()->optional(0.3)->randomFloat(2, 70, 95),
            'notify_on_new_inquiry' => true,
            'notify_on_new_offer' => true,
            'notify_on_visit_scheduled' => true,
            'notify_on_offer_accepted' => true,
            'notification_recipients' => fake()->optional(0.5)->passthrough([fake()->email(), fake()->email()]),
        ];
    }

    /**
     * Indicate that the marketplace is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that deposits are not required.
     */
    public function noDeposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'deposit_required' => false,
            'default_deposit_percentage' => 0,
        ]);
    }

    /**
     * Indicate partial refund policy.
     */
    public function partialRefund(): static
    {
        return $this->state(fn (array $attributes) => [
            'deposit_refund_policy' => 'partial',
            'deposit_refund_days' => 14,
        ]);
    }

    /**
     * Indicate non-refundable deposit policy.
     */
    public function nonRefundable(): static
    {
        return $this->state(fn (array $attributes) => [
            'deposit_refund_policy' => 'non_refundable',
        ]);
    }

    /**
     * Indicate installments are not allowed.
     */
    public function noInstallments(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_installments' => false,
            'installment_options' => [],
        ]);
    }

    /**
     * Indicate mortgage is not allowed.
     */
    public function noMortgage(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_mortgage' => false,
            'accepted_payment_methods' => ['cash', 'bank_transfer'],
        ]);
    }

    /**
     * Indicate counter offers are not allowed.
     */
    public function noCounterOffers(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_counter_offers' => false,
            'max_negotiation_rounds' => 0,
        ]);
    }

    /**
     * Indicate auto-reject low offers is enabled.
     */
    public function autoRejectLowOffers(): static
    {
        return $this->state(fn (array $attributes) => [
            'auto_reject_low_offers' => true,
            'min_offer_percentage' => 80.00,
        ]);
    }

    /**
     * Indicate listings auto-renew.
     */
    public function autoRenew(): static
    {
        return $this->state(fn (array $attributes) => [
            'auto_renew_listings' => true,
        ]);
    }

    /**
     * Include bank account details.
     */
    public function withBankAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'bank_name' => fake()->randomElement(['Al Rajhi Bank', 'Saudi National Bank', 'Riyad Bank']),
            'bank_account_name' => fake()->company(),
            'bank_account_number' => fake()->numerify('##########'),
            'bank_iban' => 'SA'.fake()->numerify('## #### #### #### #### ####'),
            'bank_swift_code' => fake()->regexify('[A-Z]{4}SARI[A-Z]{3}'),
            'bank_branch' => fake()->city().' Branch',
            'bank_currency' => 'SAR',
        ]);
    }

    /**
     * Include platform commission.
     */
    public function withPlatformCommission(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform_commission_percentage' => fake()->randomFloat(2, 0.5, 3),
        ]);
    }

    /**
     * Include legal terms.
     */
    public function withLegalTerms(): static
    {
        return $this->state(fn (array $attributes) => [
            'terms_and_conditions' => fake()->paragraphs(3, true),
            'privacy_policy' => fake()->paragraphs(3, true),
            'cancellation_policy' => fake()->paragraphs(2, true),
        ]);
    }

    /**
     * Weekend availability (Friday and Saturday).
     */
    public function weekendAvailability(): static
    {
        return $this->state(fn (array $attributes) => [
            'visit_available_days' => ['friday', 'saturday'],
        ]);
    }

    /**
     * Extended hours availability.
     */
    public function extendedHours(): static
    {
        return $this->state(fn (array $attributes) => [
            'visit_start_time' => '07:00:00',
            'visit_end_time' => '22:00:00',
            'visit_duration_minutes' => 90,
        ]);
    }

    /**
     * High commission setup.
     */
    public function highCommission(): static
    {
        return $this->state(fn (array $attributes) => [
            'agent_commission_percentage' => 5.00,
            'platform_commission_percentage' => 2.00,
        ]);
    }

    /**
     * All notifications disabled.
     */
    public function noNotifications(): static
    {
        return $this->state(fn (array $attributes) => [
            'notify_on_new_inquiry' => false,
            'notify_on_new_offer' => false,
            'notify_on_visit_scheduled' => false,
            'notify_on_offer_accepted' => false,
            'notification_recipients' => [],
        ]);
    }
}
