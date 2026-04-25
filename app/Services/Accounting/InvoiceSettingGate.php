<?php

namespace App\Services\Accounting;

use App\Models\InvoiceSetting;

/**
 * Checks whether the tenant's InvoiceSetting is sufficiently configured
 * to generate and send receipts / invoices.
 *
 * Shared by #186 (receipt gate), #188 (activation gate), and #190 (invoice generation gate).
 * Result is memoised per-request via once() to avoid repeated DB hits.
 */
class InvoiceSettingGate
{
    /**
     * Returns true when InvoiceSetting has a non-empty company_name AND instructions.
     * Logo is not enforced for MVP (nullable, future concern).
     */
    public function isComplete(): bool
    {
        return once(function (): bool {
            $setting = InvoiceSetting::first();

            if ($setting === null) {
                return false;
            }

            return filled($setting->company_name) && filled($setting->instructions);
        });
    }
}
