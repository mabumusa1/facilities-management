<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents marketplace configuration settings for a tenant.
 */
#[Fillable([
    'tenant_id',
    'marketplace_name',
    'marketplace_description',
    'contact_email',
    'contact_phone',
    'is_active',
    'default_deposit_percentage',
    'minimum_deposit_amount',
    'maximum_deposit_amount',
    'deposit_required',
    'deposit_refund_policy',
    'deposit_refund_days',
    'deposit_terms',
    'accepted_payment_methods',
    'allow_installments',
    'installment_options',
    'max_installment_months',
    'minimum_down_payment_percentage',
    'allow_mortgage',
    'payment_terms_text',
    'bank_name',
    'bank_account_name',
    'bank_account_number',
    'bank_iban',
    'bank_swift_code',
    'bank_branch',
    'bank_currency',
    'agent_commission_percentage',
    'platform_commission_percentage',
    'commission_on_gross',
    'default_listing_days',
    'max_listing_days',
    'auto_renew_listings',
    'featured_listing_days',
    'featured_listing_fee',
    'visit_available_days',
    'visit_start_time',
    'visit_end_time',
    'visit_duration_minutes',
    'min_visit_notice_hours',
    'offer_validity_days',
    'allow_counter_offers',
    'max_negotiation_rounds',
    'auto_reject_low_offers',
    'min_offer_percentage',
    'notify_on_new_inquiry',
    'notify_on_new_offer',
    'notify_on_visit_scheduled',
    'notify_on_offer_accepted',
    'notification_recipients',
    'terms_and_conditions',
    'privacy_policy',
    'cancellation_policy',
])]
class MarketplaceSetting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'marketplace_settings';

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'default_deposit_percentage' => 'decimal:2',
            'minimum_deposit_amount' => 'decimal:2',
            'maximum_deposit_amount' => 'decimal:2',
            'deposit_required' => 'boolean',
            'accepted_payment_methods' => 'array',
            'allow_installments' => 'boolean',
            'installment_options' => 'array',
            'minimum_down_payment_percentage' => 'decimal:2',
            'allow_mortgage' => 'boolean',
            'agent_commission_percentage' => 'decimal:2',
            'platform_commission_percentage' => 'decimal:2',
            'commission_on_gross' => 'boolean',
            'auto_renew_listings' => 'boolean',
            'featured_listing_fee' => 'decimal:2',
            'visit_available_days' => 'array',
            'allow_counter_offers' => 'boolean',
            'auto_reject_low_offers' => 'boolean',
            'min_offer_percentage' => 'decimal:2',
            'notify_on_new_inquiry' => 'boolean',
            'notify_on_new_offer' => 'boolean',
            'notify_on_visit_scheduled' => 'boolean',
            'notify_on_offer_accepted' => 'boolean',
            'notification_recipients' => 'array',
        ];
    }

    /**
     * Get the tenant that owns these settings.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get or create settings for a tenant.
     */
    public static function forTenant(int $tenantId): self
    {
        return static::firstOrCreate(
            ['tenant_id' => $tenantId],
            static::getDefaults()
        );
    }

    /**
     * Get default settings.
     */
    public static function getDefaults(): array
    {
        return [
            'is_active' => true,
            'default_deposit_percentage' => 10.00,
            'deposit_required' => true,
            'deposit_refund_policy' => 'full',
            'deposit_refund_days' => 30,
            'accepted_payment_methods' => ['cash', 'bank_transfer', 'mortgage'],
            'allow_installments' => true,
            'installment_options' => [12, 24, 36, 48, 60],
            'max_installment_months' => 60,
            'minimum_down_payment_percentage' => 20.00,
            'allow_mortgage' => true,
            'bank_currency' => 'SAR',
            'agent_commission_percentage' => 2.00,
            'platform_commission_percentage' => 0.00,
            'commission_on_gross' => true,
            'default_listing_days' => 90,
            'max_listing_days' => 365,
            'auto_renew_listings' => false,
            'featured_listing_days' => 30,
            'visit_available_days' => ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday'],
            'visit_duration_minutes' => 60,
            'min_visit_notice_hours' => 24,
            'offer_validity_days' => 7,
            'allow_counter_offers' => true,
            'max_negotiation_rounds' => 5,
            'auto_reject_low_offers' => false,
            'notify_on_new_inquiry' => true,
            'notify_on_new_offer' => true,
            'notify_on_visit_scheduled' => true,
            'notify_on_offer_accepted' => true,
        ];
    }

    /**
     * Calculate deposit amount for a given price.
     */
    public function calculateDeposit(float $price): float
    {
        $deposit = $price * ($this->default_deposit_percentage / 100);

        if ($this->minimum_deposit_amount !== null && $deposit < $this->minimum_deposit_amount) {
            $deposit = $this->minimum_deposit_amount;
        }

        if ($this->maximum_deposit_amount !== null && $deposit > $this->maximum_deposit_amount) {
            $deposit = $this->maximum_deposit_amount;
        }

        return round($deposit, 2);
    }

    /**
     * Calculate commission for a given sale amount.
     */
    public function calculateAgentCommission(float $saleAmount): float
    {
        return round($saleAmount * ($this->agent_commission_percentage / 100), 2);
    }

    /**
     * Calculate platform commission for a given sale amount.
     */
    public function calculatePlatformCommission(float $saleAmount): float
    {
        return round($saleAmount * ($this->platform_commission_percentage / 100), 2);
    }

    /**
     * Calculate minimum down payment for a given price.
     */
    public function calculateMinimumDownPayment(float $price): float
    {
        return round($price * ($this->minimum_down_payment_percentage / 100), 2);
    }

    /**
     * Check if a payment method is accepted.
     */
    public function isPaymentMethodAccepted(string $method): bool
    {
        return in_array($method, $this->accepted_payment_methods ?? []);
    }

    /**
     * Check if installment option is available.
     */
    public function isInstallmentOptionAvailable(int $months): bool
    {
        if (! $this->allow_installments) {
            return false;
        }

        if ($months > $this->max_installment_months) {
            return false;
        }

        return in_array($months, $this->installment_options ?? []);
    }

    /**
     * Check if a day is available for visits.
     */
    public function isVisitDayAvailable(string $day): bool
    {
        return in_array(strtolower($day), array_map('strtolower', $this->visit_available_days ?? []));
    }

    /**
     * Check if an offer percentage is acceptable.
     */
    public function isOfferAcceptable(float $offerAmount, float $listingPrice): bool
    {
        if ($this->min_offer_percentage === null) {
            return true;
        }

        $minAcceptable = $listingPrice * ($this->min_offer_percentage / 100);

        return $offerAmount >= $minAcceptable;
    }

    /**
     * Get deposit refund amount based on policy.
     */
    public function calculateRefundAmount(float $depositAmount, int $daysBeforeVisit): float
    {
        if ($this->deposit_refund_policy === 'full') {
            return $depositAmount;
        }

        if ($this->deposit_refund_policy === 'non_refundable') {
            return 0;
        }

        // Partial refund - proportional to days notice
        if ($daysBeforeVisit >= $this->deposit_refund_days) {
            return $depositAmount;
        }

        $refundPercentage = ($daysBeforeVisit / $this->deposit_refund_days) * 100;

        return round($depositAmount * ($refundPercentage / 100), 2);
    }

    /**
     * Check if marketplace is active.
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Check if deposit is required.
     */
    public function requiresDeposit(): bool
    {
        return $this->deposit_required;
    }

    /**
     * Check if mortgage is allowed.
     */
    public function allowsMortgage(): bool
    {
        return $this->allow_mortgage;
    }

    /**
     * Check if counter offers are allowed.
     */
    public function allowsCounterOffers(): bool
    {
        return $this->allow_counter_offers;
    }

    /**
     * Check if listings auto-renew.
     */
    public function hasAutoRenewal(): bool
    {
        return $this->auto_renew_listings;
    }

    /**
     * Get bank account display string.
     */
    public function getBankAccountDisplay(): ?string
    {
        if (! $this->bank_name || ! $this->bank_account_number) {
            return null;
        }

        return sprintf(
            '%s - %s (%s)',
            $this->bank_name,
            $this->bank_account_number,
            $this->bank_currency
        );
    }

    /**
     * Activate the marketplace.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the marketplace.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Update bank account details.
     */
    public function updateBankAccount(array $details): void
    {
        $this->update([
            'bank_name' => $details['bank_name'] ?? $this->bank_name,
            'bank_account_name' => $details['bank_account_name'] ?? $this->bank_account_name,
            'bank_account_number' => $details['bank_account_number'] ?? $this->bank_account_number,
            'bank_iban' => $details['bank_iban'] ?? $this->bank_iban,
            'bank_swift_code' => $details['bank_swift_code'] ?? $this->bank_swift_code,
            'bank_branch' => $details['bank_branch'] ?? $this->bank_branch,
            'bank_currency' => $details['bank_currency'] ?? $this->bank_currency,
        ]);
    }

    /**
     * Update deposit settings.
     */
    public function updateDepositSettings(array $settings): void
    {
        $this->update([
            'default_deposit_percentage' => $settings['default_deposit_percentage'] ?? $this->default_deposit_percentage,
            'minimum_deposit_amount' => $settings['minimum_deposit_amount'] ?? $this->minimum_deposit_amount,
            'maximum_deposit_amount' => $settings['maximum_deposit_amount'] ?? $this->maximum_deposit_amount,
            'deposit_required' => $settings['deposit_required'] ?? $this->deposit_required,
            'deposit_refund_policy' => $settings['deposit_refund_policy'] ?? $this->deposit_refund_policy,
            'deposit_refund_days' => $settings['deposit_refund_days'] ?? $this->deposit_refund_days,
            'deposit_terms' => $settings['deposit_terms'] ?? $this->deposit_terms,
        ]);
    }

    /**
     * Update commission settings.
     */
    public function updateCommissionSettings(array $settings): void
    {
        $this->update([
            'agent_commission_percentage' => $settings['agent_commission_percentage'] ?? $this->agent_commission_percentage,
            'platform_commission_percentage' => $settings['platform_commission_percentage'] ?? $this->platform_commission_percentage,
            'commission_on_gross' => $settings['commission_on_gross'] ?? $this->commission_on_gross,
        ]);
    }

    /**
     * Update notification preferences.
     */
    public function updateNotificationPreferences(array $preferences): void
    {
        $this->update([
            'notify_on_new_inquiry' => $preferences['notify_on_new_inquiry'] ?? $this->notify_on_new_inquiry,
            'notify_on_new_offer' => $preferences['notify_on_new_offer'] ?? $this->notify_on_new_offer,
            'notify_on_visit_scheduled' => $preferences['notify_on_visit_scheduled'] ?? $this->notify_on_visit_scheduled,
            'notify_on_offer_accepted' => $preferences['notify_on_offer_accepted'] ?? $this->notify_on_offer_accepted,
            'notification_recipients' => $preferences['notification_recipients'] ?? $this->notification_recipients,
        ]);
    }
}
