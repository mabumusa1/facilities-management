<?php

namespace App\Models;

use Database\Factories\TransactionAdditionalFeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionAdditionalFee extends Model
{
    /** @use HasFactory<TransactionAdditionalFeeFactory> */
    use HasFactory;

    protected $table = 'rf_transaction_additional_fees';

    protected $fillable = [
        'transaction_id',
        'name',
        'amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
