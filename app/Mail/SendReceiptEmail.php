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
    ) {}

    public function envelope(): Envelope
    {
        $setting = InvoiceSetting::first();
        $subject = $setting?->company_name
            ? "Receipt from {$setting->company_name}"
            : 'Your Receipt';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'receipts.receipt-email');
    }
}
