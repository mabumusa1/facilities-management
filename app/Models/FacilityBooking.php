<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use App\Support\ManagerScopeHelper;
use Database\Factories\FacilityBookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacilityBooking extends Model
{
    /** @use HasFactory<FacilityBookingFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope, SoftDeletes;

    protected $table = 'rf_facility_bookings';

    /**
     * FacilityBookings: filter via facility_id → rf_facilities.community_id.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeForManager(Builder $query, User $user): Builder
    {
        $scopes = ManagerScopeHelper::scopesForUser($user);

        if ($scopes['is_unrestricted']) {
            return $query;
        }

        $communityIds = $scopes['community_ids'];

        if (empty($communityIds)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn(
            $this->getTable().'.facility_id',
            fn ($sub) => $sub
                ->select('id')
                ->from('rf_facilities')
                ->whereIn('community_id', $communityIds)
        );
    }

    protected $fillable = [
        'facility_id',
        'account_tenant_id',
        'status_id',
        'booker_type',
        'booker_id',
        'booked_by_type',
        'booking_date',
        'start_time',
        'end_time',
        'start_at',
        'end_at',
        'number_of_guests',
        'notes',
        'approved_at',
        'cancelled_at',
        'cancellation_reason',
        'cancellation_by_type',
        'invoice_id',
        'contract_document_id',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'approved_at' => 'datetime',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'cancelled_at' => 'immutable_datetime',
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

    /**
     * Scope bookings that overlap a given time range for a facility.
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOverlapping(Builder $query, int $facilityId, string $start, string $end): Builder
    {
        return $query
            ->where('facility_id', $facilityId)
            ->where('start_at', '<', $end)
            ->where('end_at', '>', $start);
    }
}
