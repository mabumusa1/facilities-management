<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\LeaseRenewalOfferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseRenewalOffer extends Model
{
    /** @use HasFactory<LeaseRenewalOfferFactory> */
    use BelongsToAccountTenant, HasFactory;

    /** Reserved rf_statuses.id values for the renewal state machine (type=renewal). */
    public const STATUS_DRAFT = 83;

    public const STATUS_SENT = 84;

    public const STATUS_VIEWED = 85;

    public const STATUS_ACCEPTED = 86;

    public const STATUS_REJECTED = 87;

    public const STATUS_EXPIRED = 88;

    protected $fillable = [
        'lease_id',
        'status_id',
        'new_start_date',
        'duration_months',
        'new_rent_amount',
        'payment_frequency',
        'contract_type_id',
        'valid_until',
        'message_en',
        'message_ar',
        'created_by',
        'decided_at',
        'decided_by',
        'converted_lease_id',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'new_start_date' => 'date',
            'valid_until' => 'date',
            'new_rent_amount' => 'decimal:2',
            'decided_at' => 'datetime',
            'duration_months' => 'integer',
        ];
    }

    /** @return BelongsTo<Lease, $this> */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /** @return BelongsTo<Status, $this> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /** @return BelongsTo<User, $this> */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return BelongsTo<User, $this> */
    public function decidedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /** @return BelongsTo<Setting, $this> */
    public function contractType(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'contract_type_id');
    }

    /** @return BelongsTo<Lease, $this> */
    public function convertedLease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'converted_lease_id');
    }
}
