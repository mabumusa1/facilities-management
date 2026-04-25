<?php

namespace App\Services\Accounting;

use App\Mail\SendReceiptEmail;
use App\Models\Receipt;
use App\Models\Transaction;
use Illuminate\Support\Facades\Mail;

/**
 * Manages receipt generation and delivery.
 *
 * PDF generation requires a PDF package (spatie/laravel-pdf or similar).
 * Until that package is added via a dependency approval, pdf_path is stored
 * as null and the Download/Email flows are deferred.
 */
class ReceiptService
{
    public function __construct(private InvoiceSettingGate $gate) {}

    public function canGenerate(): bool
    {
        return $this->gate->isComplete();
    }

    /**
     * Create (or update) the Receipt record for a transaction.
     * When InvoiceSetting is complete, status is 'generated'; otherwise 'settings_incomplete'.
     */
    public function generateOrBlock(Transaction $transaction): Receipt
    {
        $status = $this->canGenerate() ? 'generated' : 'settings_incomplete';

        /** @var Receipt $receipt */
        $receipt = Receipt::updateOrCreate(
            ['transaction_id' => $transaction->id],
            [
                'status' => $status,
                'pdf_path' => null, // PDF generation requires a package — deferred
                'account_tenant_id' => $transaction->account_tenant_id,
            ],
        );

        return $receipt;
    }

    /**
     * Send the receipt PDF to the payer via email.
     * Updates sent_at, sent_to_name, and sent_to_email after dispatch.
     */
    public function send(Receipt $receipt): void
    {
        $transaction = $receipt->transaction()->with('assignee')->first();

        $payerName = $this->resolvePayerName($transaction);
        $payerEmail = $this->resolvePayerEmail($transaction);

        Mail::to($payerEmail)->queue(new SendReceiptEmail($receipt, $payerName));

        $receipt->update([
            'sent_at' => now(),
            'sent_to_name' => $payerName,
            'sent_to_email' => $payerEmail,
        ]);
    }

    private function resolvePayerName(?Transaction $transaction): string
    {
        if ($transaction === null || $transaction->assignee === null) {
            return '';
        }

        $assignee = $transaction->assignee;

        if (isset($assignee->name)) {
            return (string) $assignee->name;
        }

        $firstName = (string) ($assignee->first_name ?? '');
        $lastName = (string) ($assignee->last_name ?? '');

        return trim("{$firstName} {$lastName}");
    }

    private function resolvePayerEmail(?Transaction $transaction): string
    {
        if ($transaction === null || $transaction->assignee === null) {
            return '';
        }

        return (string) ($transaction->assignee->email ?? '');
    }
}
