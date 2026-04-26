<?php

namespace App\Services\Leasing;

use App\Models\LeaseQuote;

/**
 * Creates a draft LeaseQuote from an accepted Marketplace offer payload.
 *
 * Called by Marketplace story #276 when a seller converts an accepted offer
 * to a leasing quote. The full implementation is deferred to #276; this
 * service class establishes the integration point and method signature.
 */
class QuoteFromMarketplaceService
{
    /**
     * Create a draft LeaseQuote pre-filled from a Marketplace offer.
     *
     * Expected payload keys:
     *  - unit_id           (int)    RF unit being leased
     *  - contact_id        (int)    Resident linked to the accepted offer (buyer)
     *  - agreed_price      (float)  Accepted offer's proposed price → rent_amount
     *  - payment_plan_type (string) Accepted offer's payment plan type
     *  - marketplace_unit_id (int)  Back-reference for attribution (#278)
     *  - account_tenant_id (int)    Owning tenant
     *  - status_id         (int)    Draft status ID
     *  - payment_frequency_id (int) Resolved from payment_plan_type
     *  - created_by_id     (int)    Admin performing the conversion
     *
     * @param  array<string, mixed>  $payload
     *
     * @todo Implement full conversion logic in story #276.
     */
    public function createFromPayload(array $payload): LeaseQuote
    {
        return LeaseQuote::create([
            'account_tenant_id' => $payload['account_tenant_id'],
            'unit_id' => $payload['unit_id'],
            'contact_id' => $payload['contact_id'],
            'rent_amount' => $payload['agreed_price'],
            'marketplace_unit_id' => $payload['marketplace_unit_id'] ?? null,
            'status_id' => $payload['status_id'],
            'payment_frequency_id' => $payload['payment_frequency_id'],
            'duration_months' => $payload['duration_months'] ?? 12,
            'start_date' => $payload['start_date'] ?? now()->toDateString(),
            'valid_until' => $payload['valid_until'] ?? now()->addDays(30),
            'created_by_id' => $payload['created_by_id'],
        ]);
    }
}
