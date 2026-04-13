<?php

namespace Tests\Feature;

use App\Models\MarketplaceSetting;
use App\Models\Tenant;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_marketplace_setting(): void
    {
        $setting = MarketplaceSetting::factory()->create();

        $this->assertDatabaseHas('marketplace_settings', [
            'id' => $setting->id,
        ]);
    }

    public function test_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $setting = MarketplaceSetting::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertInstanceOf(Tenant::class, $setting->tenant);
        $this->assertEquals($tenant->id, $setting->tenant->id);
    }

    public function test_for_tenant_creates_new_settings(): void
    {
        $tenant = Tenant::factory()->create();

        $setting = MarketplaceSetting::forTenant($tenant->id);

        $this->assertDatabaseHas('marketplace_settings', [
            'tenant_id' => $tenant->id,
        ]);
        $this->assertTrue($setting->is_active);
    }

    public function test_for_tenant_returns_existing_settings(): void
    {
        $tenant = Tenant::factory()->create();
        $existing = MarketplaceSetting::factory()->create([
            'tenant_id' => $tenant->id,
            'marketplace_name' => 'My Custom Marketplace',
        ]);

        $setting = MarketplaceSetting::forTenant($tenant->id);

        $this->assertEquals($existing->id, $setting->id);
        $this->assertEquals('My Custom Marketplace', $setting->marketplace_name);
    }

    public function test_get_defaults_returns_correct_values(): void
    {
        $defaults = MarketplaceSetting::getDefaults();

        $this->assertTrue($defaults['is_active']);
        $this->assertEquals(10.00, $defaults['default_deposit_percentage']);
        $this->assertTrue($defaults['deposit_required']);
        $this->assertEquals('full', $defaults['deposit_refund_policy']);
        $this->assertContains('cash', $defaults['accepted_payment_methods']);
        $this->assertTrue($defaults['allow_installments']);
        $this->assertTrue($defaults['allow_mortgage']);
    }

    public function test_calculate_deposit_returns_percentage_of_price(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'default_deposit_percentage' => 10.00,
            'minimum_deposit_amount' => null,
            'maximum_deposit_amount' => null,
        ]);

        $deposit = $setting->calculateDeposit(500000);

        $this->assertEquals(50000, $deposit);
    }

    public function test_calculate_deposit_respects_minimum(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'default_deposit_percentage' => 10.00,
            'minimum_deposit_amount' => 10000,
            'maximum_deposit_amount' => null,
        ]);

        $deposit = $setting->calculateDeposit(50000); // 10% = 5000, but min is 10000

        $this->assertEquals(10000, $deposit);
    }

    public function test_calculate_deposit_respects_maximum(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'default_deposit_percentage' => 10.00,
            'minimum_deposit_amount' => null,
            'maximum_deposit_amount' => 25000,
        ]);

        $deposit = $setting->calculateDeposit(500000); // 10% = 50000, but max is 25000

        $this->assertEquals(25000, $deposit);
    }

    public function test_calculate_agent_commission(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'agent_commission_percentage' => 2.50,
        ]);

        $commission = $setting->calculateAgentCommission(1000000);

        $this->assertEquals(25000, $commission);
    }

    public function test_calculate_platform_commission(): void
    {
        $setting = MarketplaceSetting::factory()->withPlatformCommission()->create([
            'platform_commission_percentage' => 1.00,
        ]);

        $commission = $setting->calculatePlatformCommission(1000000);

        $this->assertEquals(10000, $commission);
    }

    public function test_calculate_minimum_down_payment(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'minimum_down_payment_percentage' => 20.00,
        ]);

        $downPayment = $setting->calculateMinimumDownPayment(1000000);

        $this->assertEquals(200000, $downPayment);
    }

    public function test_is_payment_method_accepted_returns_true_for_accepted(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'accepted_payment_methods' => ['cash', 'bank_transfer', 'mortgage'],
        ]);

        $this->assertTrue($setting->isPaymentMethodAccepted('cash'));
        $this->assertTrue($setting->isPaymentMethodAccepted('mortgage'));
    }

    public function test_is_payment_method_accepted_returns_false_for_not_accepted(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'accepted_payment_methods' => ['cash', 'bank_transfer'],
        ]);

        $this->assertFalse($setting->isPaymentMethodAccepted('mortgage'));
        $this->assertFalse($setting->isPaymentMethodAccepted('crypto'));
    }

    public function test_is_installment_option_available_returns_true_for_available(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'allow_installments' => true,
            'installment_options' => [12, 24, 36, 48, 60],
            'max_installment_months' => 60,
        ]);

        $this->assertTrue($setting->isInstallmentOptionAvailable(24));
        $this->assertTrue($setting->isInstallmentOptionAvailable(60));
    }

    public function test_is_installment_option_available_returns_false_when_not_allowed(): void
    {
        $setting = MarketplaceSetting::factory()->noInstallments()->create();

        $this->assertFalse($setting->isInstallmentOptionAvailable(24));
    }

    public function test_is_installment_option_available_returns_false_for_exceeding_max(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'allow_installments' => true,
            'installment_options' => [12, 24, 36],
            'max_installment_months' => 36,
        ]);

        $this->assertFalse($setting->isInstallmentOptionAvailable(48));
    }

    public function test_is_installment_option_available_returns_false_for_unavailable_option(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'allow_installments' => true,
            'installment_options' => [12, 24, 36],
            'max_installment_months' => 60,
        ]);

        $this->assertFalse($setting->isInstallmentOptionAvailable(18));
    }

    public function test_is_visit_day_available_returns_true_for_available_day(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'visit_available_days' => ['sunday', 'monday', 'tuesday'],
        ]);

        $this->assertTrue($setting->isVisitDayAvailable('sunday'));
        $this->assertTrue($setting->isVisitDayAvailable('Monday')); // Case insensitive
    }

    public function test_is_visit_day_available_returns_false_for_unavailable_day(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'visit_available_days' => ['sunday', 'monday', 'tuesday'],
        ]);

        $this->assertFalse($setting->isVisitDayAvailable('friday'));
        $this->assertFalse($setting->isVisitDayAvailable('saturday'));
    }

    public function test_is_offer_acceptable_returns_true_when_no_minimum(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'min_offer_percentage' => null,
        ]);

        $this->assertTrue($setting->isOfferAcceptable(1, 1000000)); // Any offer is acceptable
    }

    public function test_is_offer_acceptable_returns_true_for_acceptable_offer(): void
    {
        $setting = MarketplaceSetting::factory()->autoRejectLowOffers()->create([
            'min_offer_percentage' => 80.00,
        ]);

        $this->assertTrue($setting->isOfferAcceptable(850000, 1000000)); // 85% is above 80%
    }

    public function test_is_offer_acceptable_returns_false_for_low_offer(): void
    {
        $setting = MarketplaceSetting::factory()->autoRejectLowOffers()->create([
            'min_offer_percentage' => 80.00,
        ]);

        $this->assertFalse($setting->isOfferAcceptable(700000, 1000000)); // 70% is below 80%
    }

    public function test_calculate_refund_amount_full_policy(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'deposit_refund_policy' => 'full',
        ]);

        $refund = $setting->calculateRefundAmount(50000, 5);

        $this->assertEquals(50000, $refund);
    }

    public function test_calculate_refund_amount_non_refundable_policy(): void
    {
        $setting = MarketplaceSetting::factory()->nonRefundable()->create();

        $refund = $setting->calculateRefundAmount(50000, 30);

        $this->assertEquals(0, $refund);
    }

    public function test_calculate_refund_amount_partial_policy_full_refund(): void
    {
        $setting = MarketplaceSetting::factory()->partialRefund()->create([
            'deposit_refund_days' => 14,
        ]);

        $refund = $setting->calculateRefundAmount(50000, 14);

        $this->assertEquals(50000, $refund); // Full refund if days >= refund_days
    }

    public function test_calculate_refund_amount_partial_policy_proportional(): void
    {
        $setting = MarketplaceSetting::factory()->partialRefund()->create([
            'deposit_refund_days' => 14,
        ]);

        $refund = $setting->calculateRefundAmount(50000, 7);

        // 7 days notice out of 14 = 50% refund = 25000
        $this->assertEquals(25000, $refund);
    }

    public function test_is_active_returns_true_for_active(): void
    {
        $setting = MarketplaceSetting::factory()->create(['is_active' => true]);

        $this->assertTrue($setting->isActive());
    }

    public function test_is_active_returns_false_for_inactive(): void
    {
        $setting = MarketplaceSetting::factory()->inactive()->create();

        $this->assertFalse($setting->isActive());
    }

    public function test_requires_deposit_returns_true_when_required(): void
    {
        $setting = MarketplaceSetting::factory()->create(['deposit_required' => true]);

        $this->assertTrue($setting->requiresDeposit());
    }

    public function test_requires_deposit_returns_false_when_not_required(): void
    {
        $setting = MarketplaceSetting::factory()->noDeposit()->create();

        $this->assertFalse($setting->requiresDeposit());
    }

    public function test_allows_mortgage_returns_true_when_allowed(): void
    {
        $setting = MarketplaceSetting::factory()->create(['allow_mortgage' => true]);

        $this->assertTrue($setting->allowsMortgage());
    }

    public function test_allows_mortgage_returns_false_when_not_allowed(): void
    {
        $setting = MarketplaceSetting::factory()->noMortgage()->create();

        $this->assertFalse($setting->allowsMortgage());
    }

    public function test_allows_counter_offers_returns_true_when_allowed(): void
    {
        $setting = MarketplaceSetting::factory()->create(['allow_counter_offers' => true]);

        $this->assertTrue($setting->allowsCounterOffers());
    }

    public function test_allows_counter_offers_returns_false_when_not_allowed(): void
    {
        $setting = MarketplaceSetting::factory()->noCounterOffers()->create();

        $this->assertFalse($setting->allowsCounterOffers());
    }

    public function test_has_auto_renewal_returns_true_when_enabled(): void
    {
        $setting = MarketplaceSetting::factory()->autoRenew()->create();

        $this->assertTrue($setting->hasAutoRenewal());
    }

    public function test_has_auto_renewal_returns_false_when_disabled(): void
    {
        $setting = MarketplaceSetting::factory()->create(['auto_renew_listings' => false]);

        $this->assertFalse($setting->hasAutoRenewal());
    }

    public function test_get_bank_account_display_returns_formatted_string(): void
    {
        $setting = MarketplaceSetting::factory()->withBankAccount()->create([
            'bank_name' => 'Al Rajhi Bank',
            'bank_account_number' => '1234567890',
            'bank_currency' => 'SAR',
        ]);

        $display = $setting->getBankAccountDisplay();

        $this->assertEquals('Al Rajhi Bank - 1234567890 (SAR)', $display);
    }

    public function test_get_bank_account_display_returns_null_when_incomplete(): void
    {
        $setting = MarketplaceSetting::factory()->create([
            'bank_name' => null,
            'bank_account_number' => null,
        ]);

        $this->assertNull($setting->getBankAccountDisplay());
    }

    public function test_can_activate_marketplace(): void
    {
        $setting = MarketplaceSetting::factory()->inactive()->create();

        $setting->activate();

        $this->assertTrue($setting->fresh()->isActive());
    }

    public function test_can_deactivate_marketplace(): void
    {
        $setting = MarketplaceSetting::factory()->create(['is_active' => true]);

        $setting->deactivate();

        $this->assertFalse($setting->fresh()->isActive());
    }

    public function test_can_update_bank_account(): void
    {
        $setting = MarketplaceSetting::factory()->create();

        $setting->updateBankAccount([
            'bank_name' => 'Saudi National Bank',
            'bank_account_number' => '9876543210',
            'bank_iban' => 'SA1234567890123456789012',
        ]);

        $fresh = $setting->fresh();
        $this->assertEquals('Saudi National Bank', $fresh->bank_name);
        $this->assertEquals('9876543210', $fresh->bank_account_number);
        $this->assertEquals('SA1234567890123456789012', $fresh->bank_iban);
    }

    public function test_can_update_deposit_settings(): void
    {
        $setting = MarketplaceSetting::factory()->create();

        $setting->updateDepositSettings([
            'default_deposit_percentage' => 15.00,
            'deposit_required' => false,
            'deposit_refund_policy' => 'partial',
        ]);

        $fresh = $setting->fresh();
        $this->assertEquals(15.00, $fresh->default_deposit_percentage);
        $this->assertFalse($fresh->deposit_required);
        $this->assertEquals('partial', $fresh->deposit_refund_policy);
    }

    public function test_can_update_commission_settings(): void
    {
        $setting = MarketplaceSetting::factory()->create();

        $setting->updateCommissionSettings([
            'agent_commission_percentage' => 3.50,
            'platform_commission_percentage' => 1.00,
            'commission_on_gross' => false,
        ]);

        $fresh = $setting->fresh();
        $this->assertEquals(3.50, $fresh->agent_commission_percentage);
        $this->assertEquals(1.00, $fresh->platform_commission_percentage);
        $this->assertFalse($fresh->commission_on_gross);
    }

    public function test_can_update_notification_preferences(): void
    {
        $setting = MarketplaceSetting::factory()->create();

        $setting->updateNotificationPreferences([
            'notify_on_new_inquiry' => false,
            'notify_on_new_offer' => false,
            'notification_recipients' => ['admin@example.com'],
        ]);

        $fresh = $setting->fresh();
        $this->assertFalse($fresh->notify_on_new_inquiry);
        $this->assertFalse($fresh->notify_on_new_offer);
        $this->assertEquals(['admin@example.com'], $fresh->notification_recipients);
    }

    public function test_tenant_has_unique_settings(): void
    {
        $tenant = Tenant::factory()->create();
        MarketplaceSetting::factory()->create(['tenant_id' => $tenant->id]);

        $this->expectException(QueryException::class);

        MarketplaceSetting::factory()->create(['tenant_id' => $tenant->id]);
    }

    public function test_soft_deletes_marketplace_setting(): void
    {
        $setting = MarketplaceSetting::factory()->create();

        $setting->delete();

        $this->assertSoftDeleted('marketplace_settings', ['id' => $setting->id]);
    }

    public function test_factory_inactive_state(): void
    {
        $setting = MarketplaceSetting::factory()->inactive()->create();

        $this->assertFalse($setting->is_active);
    }

    public function test_factory_no_deposit_state(): void
    {
        $setting = MarketplaceSetting::factory()->noDeposit()->create();

        $this->assertFalse($setting->deposit_required);
        $this->assertEquals(0, $setting->default_deposit_percentage);
    }

    public function test_factory_partial_refund_state(): void
    {
        $setting = MarketplaceSetting::factory()->partialRefund()->create();

        $this->assertEquals('partial', $setting->deposit_refund_policy);
    }

    public function test_factory_non_refundable_state(): void
    {
        $setting = MarketplaceSetting::factory()->nonRefundable()->create();

        $this->assertEquals('non_refundable', $setting->deposit_refund_policy);
    }

    public function test_factory_no_installments_state(): void
    {
        $setting = MarketplaceSetting::factory()->noInstallments()->create();

        $this->assertFalse($setting->allow_installments);
    }

    public function test_factory_no_mortgage_state(): void
    {
        $setting = MarketplaceSetting::factory()->noMortgage()->create();

        $this->assertFalse($setting->allow_mortgage);
    }

    public function test_factory_no_counter_offers_state(): void
    {
        $setting = MarketplaceSetting::factory()->noCounterOffers()->create();

        $this->assertFalse($setting->allow_counter_offers);
    }

    public function test_factory_auto_reject_low_offers_state(): void
    {
        $setting = MarketplaceSetting::factory()->autoRejectLowOffers()->create();

        $this->assertTrue($setting->auto_reject_low_offers);
        $this->assertEquals(80.00, $setting->min_offer_percentage);
    }

    public function test_factory_with_bank_account_state(): void
    {
        $setting = MarketplaceSetting::factory()->withBankAccount()->create();

        $this->assertNotNull($setting->bank_name);
        $this->assertNotNull($setting->bank_account_number);
        $this->assertNotNull($setting->bank_iban);
    }

    public function test_factory_with_legal_terms_state(): void
    {
        $setting = MarketplaceSetting::factory()->withLegalTerms()->create();

        $this->assertNotNull($setting->terms_and_conditions);
        $this->assertNotNull($setting->privacy_policy);
        $this->assertNotNull($setting->cancellation_policy);
    }

    public function test_factory_weekend_availability_state(): void
    {
        $setting = MarketplaceSetting::factory()->weekendAvailability()->create();

        $this->assertEquals(['friday', 'saturday'], $setting->visit_available_days);
    }

    public function test_factory_extended_hours_state(): void
    {
        $setting = MarketplaceSetting::factory()->extendedHours()->create();

        $this->assertEquals('07:00:00', $setting->visit_start_time);
        $this->assertEquals('22:00:00', $setting->visit_end_time);
        $this->assertEquals(90, $setting->visit_duration_minutes);
    }

    public function test_factory_high_commission_state(): void
    {
        $setting = MarketplaceSetting::factory()->highCommission()->create();

        $this->assertEquals(5.00, $setting->agent_commission_percentage);
        $this->assertEquals(2.00, $setting->platform_commission_percentage);
    }

    public function test_factory_no_notifications_state(): void
    {
        $setting = MarketplaceSetting::factory()->noNotifications()->create();

        $this->assertFalse($setting->notify_on_new_inquiry);
        $this->assertFalse($setting->notify_on_new_offer);
        $this->assertFalse($setting->notify_on_visit_scheduled);
        $this->assertFalse($setting->notify_on_offer_accepted);
    }
}
