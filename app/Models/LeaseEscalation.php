<?php

namespace App\Models;

use App\Enums\LeaseEscalationType;
use Database\Factories\LeaseEscalationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseEscalation extends Model
{
    /** @use HasFactory<LeaseEscalationFactory> */
    use HasFactory;

    protected $table = 'rf_lease_escalations';

    protected $fillable = [
        'lease_id',
        'year',
        'type',
        'value',
        'new_amount',
    ];

    protected function casts(): array
    {
        return [
            'type' => LeaseEscalationType::class,
            'value' => 'decimal:2',
            'new_amount' => 'decimal:2',
        ];
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
