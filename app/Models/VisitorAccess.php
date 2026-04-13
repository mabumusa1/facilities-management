<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a visitor access request for a property.
 */
#[Fillable([
    'tenant_id',
    'unit_id',
    'building_id',
    'community_id',
    'requested_by',
    'status_id',
    'visitor_name',
    'visitor_email',
    'visitor_phone',
    'visitor_id_number',
    'visitor_vehicle_plate',
    'visit_start_date',
    'visit_start_time',
    'visit_end_date',
    'visit_end_time',
    'access_type',
    'purpose',
    'notes',
    'approved_by',
    'approved_at',
    'rejection_reason',
])]
class VisitorAccess extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visit_start_date' => 'date',
            'visit_end_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Get the tenant that owns this visitor access.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the unit this visitor access is for.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get the building this visitor access is for.
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Get the community this visitor access is for.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the contact who requested this access.
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'requested_by');
    }

    /**
     * Get the status of this visitor access.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the contact who approved this access.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'approved_by');
    }

    /**
     * Check if the visitor access is pending.
     */
    public function isPending(): bool
    {
        return $this->status->slug === 'visitor_pending';
    }

    /**
     * Check if the visitor access is approved.
     */
    public function isApproved(): bool
    {
        return $this->status->slug === 'visitor_approved';
    }

    /**
     * Check if the visitor access is denied.
     */
    public function isDenied(): bool
    {
        return $this->status->slug === 'visitor_denied';
    }

    /**
     * Check if the visit is currently active.
     */
    public function isActive(): bool
    {
        $now = now();
        $startDate = $this->visit_start_date;
        $endDate = $this->visit_end_date ?? $startDate;

        return $this->isApproved() && $now->between($startDate, $endDate->endOfDay());
    }

    /**
     * Approve the visitor access request.
     */
    public function approve(?int $approvedBy = null): void
    {
        $status = Status::where('domain', 'visitor')->where('slug', 'visitor_approved')->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'approved_by' => $approvedBy,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Deny the visitor access request.
     */
    public function deny(?string $reason = null, ?int $deniedBy = null): void
    {
        $status = Status::where('domain', 'visitor')->where('slug', 'visitor_denied')->first();

        $this->update([
            'status_id' => $status?->id ?? $this->status_id,
            'approved_by' => $deniedBy,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
