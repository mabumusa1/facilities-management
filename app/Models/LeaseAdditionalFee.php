<?php

namespace App\Models;

use Database\Factories\LeaseAdditionalFeeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseAdditionalFee extends Model
{
    /** @use HasFactory<LeaseAdditionalFeeFactory> */
    use HasFactory;

    protected $table = 'rf_lease_additional_fees';

    protected $fillable = [
        'lease_id',
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

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
