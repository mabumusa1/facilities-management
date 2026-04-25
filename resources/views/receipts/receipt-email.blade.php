<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; background: #f9f9f9; }
        .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: #1a1a2e; color: #fff; padding: 24px 32px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; font-size: 14px; opacity: 0.8; }
        .body { padding: 32px; }
        .greeting { font-size: 16px; margin-bottom: 24px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .details-table th { background: #f3f4f6; text-align: left; padding: 10px 12px; font-size: 12px; text-transform: uppercase; color: #6b7280; }
        .details-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .amount-row td { font-weight: bold; font-size: 16px; }
        .footer { padding: 24px 32px; background: #f3f4f6; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        @php
            $setting = \App\Models\InvoiceSetting::first();
        @endphp
        <h1>{{ $setting?->company_name ?? config('app.name') }}</h1>
        @if($setting?->address)
            <p>{{ $setting->address }}</p>
        @endif
    </div>

    <div class="body">
        <p class="greeting">Dear {{ $payerName }},</p>
        <p>Please find below your payment receipt.</p>

        @php
            $transaction = $receipt->transaction()->with(['category', 'unit', 'lease'])->first();
        @endphp

        @if($transaction)
        <table class="details-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Transaction ID</td>
                    <td>#{{ $transaction->id }}</td>
                </tr>
                @if($transaction->category)
                <tr>
                    <td>Category</td>
                    <td>{{ $transaction->category->name_en ?? $transaction->category->name }}</td>
                </tr>
                @endif
                @if($transaction->unit)
                <tr>
                    <td>Unit</td>
                    <td>{{ $transaction->unit->name }}</td>
                </tr>
                @endif
                @if($transaction->payment_method)
                <tr>
                    <td>Payment Method</td>
                    <td>{{ ucwords(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                </tr>
                @endif
                @if($transaction->reference_number)
                <tr>
                    <td>Reference</td>
                    <td>{{ $transaction->reference_number }}</td>
                </tr>
                @endif
                <tr>
                    <td>Payment Date</td>
                    <td>{{ $transaction->due_on?->format('d M Y') ?? '—' }}</td>
                </tr>
                @if($transaction->tax_amount && $transaction->tax_amount > 0)
                <tr>
                    <td>Tax Amount</td>
                    <td>{{ number_format((float) $transaction->tax_amount, 2) }}</td>
                </tr>
                @endif
                <tr class="amount-row">
                    <td>Total Amount</td>
                    <td>{{ number_format((float) $transaction->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
        @endif

        <p>Thank you for your payment.</p>
    </div>

    <div class="footer">
        @if($setting?->vat_number)
            <p>VAT Number: {{ $setting->vat_number }}</p>
        @endif
        @if($setting?->instructions)
            <p>{{ $setting->instructions }}</p>
        @endif
        <p>This is an automated email. Please do not reply.</p>
    </div>
</div>
</body>
</html>
