<?php

namespace App\Models;

use Database\Factories\FacilityWaitlistFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityWaitlist extends Model
{
    /** @use HasFactory<FacilityWaitlistFactory> */
    use HasFactory;

    protected $table = 'rf_facility_waitlist';

    protected $fillable = [
        'facility_id',
        'resident_id',
        'requested_start_at',
        'requested_end_at',
        'notified_at',
        'ttl_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_start_at' => 'datetime',
            'requested_end_at' => 'datetime',
            'notified_at' => 'datetime',
            'ttl_expires_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Facility, $this> */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /** @return BelongsTo<Resident, $this> */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * Scope waitlist entries for a specific slot ordered FIFO.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForSlot(Builder $query, int $facilityId, string $start, string $end): Builder
    {
        return $query
            ->where('facility_id', $facilityId)
            ->where('requested_start_at', $start)
            ->where('requested_end_at', $end)
            ->orderBy('created_at');
    }
}
