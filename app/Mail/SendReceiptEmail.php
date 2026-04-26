<?php

namespace App\Mail;

use App\Models\InvoiceSetting;
use App\Models\Receipt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendReceiptEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Receipt $receipt,
        public readonly string $payerName,
        public readonly ?InvoiceSetting $invoiceSetting,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->invoiceSetting?->company_name
            ? "Receipt from {$this->invoiceSetting->company_name}"
            : 'Your Receipt';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $transaction = $this->receipt->transaction->loadMissing(['category', 'unit', 'lease']);

        return new Content(
            view: 'receipts.receipt-email',
            with: [
                'setting' => $this->invoiceSetting,
                'transaction' => $transaction,
            ],
        );
    }
}
