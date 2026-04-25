<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\ReceiptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    /** @use HasFactory<ReceiptFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_receipts';

    protected $fillable = [
        'transaction_id',
        'account_tenant_id',
        'status',
        'pdf_path',
        'sent_at',
        'sent_to_name',
        'sent_to_email',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
