<?php

namespace App\Models;

use App\Enums\DeductionReason;
use Database\Factories\MoveOutDeductionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoveOutDeduction extends Model
{
    /** @use HasFactory<MoveOutDeductionFactory> */
    use HasFactory;

    protected $fillable = [
        'move_out_id',
        'label_en',
        'label_ar',
        'amount',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'reason' => DeductionReason::class,
            'amount' => 'decimal:2',
        ];
    }

    public function moveOut(): BelongsTo
    {
        return $this->belongsTo(MoveOut::class);
    }
}
