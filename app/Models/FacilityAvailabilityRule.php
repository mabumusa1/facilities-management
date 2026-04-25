<?php

namespace App\Models;

use Database\Factories\FacilityAvailabilityRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityAvailabilityRule extends Model
{
    /** @use HasFactory<FacilityAvailabilityRuleFactory> */
    use HasFactory;

    protected $table = 'rf_facility_availability_rules';

    protected $fillable = [
        'facility_id',
        'day_of_week',
        'open_time',
        'close_time',
        'slot_duration_minutes',
        'max_concurrent_bookings',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'slot_duration_minutes' => 'integer',
            'max_concurrent_bookings' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Facility, $this> */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
