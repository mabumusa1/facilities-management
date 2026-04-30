<?php

namespace App\Models;

use Database\Factories\LeaseAmendmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseAmendment extends Model
{
    /** @use HasFactory<LeaseAmendmentFactory> */
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'amended_by',
        'reason',
        'changes',
        'addendum_media_id',
        'amendment_number',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'amendment_number' => 'integer',
        ];
    }

    /** @return BelongsTo<Lease, $this> */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /** @return BelongsTo<User, $this> */
    public function amendedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'amended_by');
    }
}
