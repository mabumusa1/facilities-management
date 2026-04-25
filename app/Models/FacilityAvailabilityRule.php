<?php

namespace App\Models;

use Database\Factories\FacilityAvailabilityRuleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tenant scope is delegated through facility_id — this model carries no direct
 * account_tenant_id FK. Always join or load the parent Facility to enforce
 * tenant isolation; any query on this table alone will be cross-tenant.
 */
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
            'open_time' => 'datetime:H:i',
            'close_time' => 'datetime:H:i',
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
