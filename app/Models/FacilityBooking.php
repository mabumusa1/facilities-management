<?php

namespace App\Models;

use Database\Factories\FacilityBookingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityBooking extends Model
{
    /** @use HasFactory<FacilityBookingFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'rf_facility_bookings';

    protected $fillable = [
        'facility_id',
        'status_id',
        'booker_type',
        'booker_id',
        'booking_date',
        'start_time',
        'end_time',
        'number_of_guests',
        'notes',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Facility, $this> */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /** @return BelongsTo<Status, $this> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /** @return MorphTo<Model, $this> */
    public function booker(): MorphTo
    {
        return $this->morphTo();
    }
}
