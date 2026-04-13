<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Represents a bookable facility within a community (gym, pool, meeting room, etc.).
 */
#[Fillable([
    'tenant_id',
    'community_id',
    'category_id',
    'name_en',
    'name_ar',
    'description_en',
    'description_ar',
    'gender',
    'booking_type',
    'operating_days',
    'opening_time',
    'closing_time',
    'capacity',
    'price_per_hour',
    'price_per_day',
    'price_per_session',
    'requires_approval',
    'is_active',
    'booking_duration_minutes',
    'max_advance_booking_days',
    'rules_en',
    'rules_ar',
])]
class Facility extends Model
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
            'operating_days' => 'array',
            'price_per_hour' => 'decimal:2',
            'price_per_day' => 'decimal:2',
            'price_per_session' => 'decimal:2',
            'requires_approval' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the tenant that owns this facility.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the community this facility belongs to.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the category of this facility.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }

    /**
     * Get the bookings for this facility.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class);
    }

    /**
     * Check if the facility is active.
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Check if bookings require approval.
     */
    public function requiresApproval(): bool
    {
        return $this->requires_approval === true;
    }

    /**
     * Check if the facility operates on a given day.
     */
    public function operatesOnDay(string $day): bool
    {
        return in_array(strtolower($day), array_map('strtolower', $this->operating_days ?? []));
    }

    /**
     * Get the price based on booking type.
     */
    public function getPrice(string $bookingType): ?float
    {
        return match ($bookingType) {
            'hourly' => $this->price_per_hour,
            'daily' => $this->price_per_day,
            'session' => $this->price_per_session,
            default => null,
        };
    }

    /**
     * Activate the facility.
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Deactivate the facility.
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }
}
