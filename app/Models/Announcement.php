<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'tenant_id',
    'created_by',
    'title',
    'description',
    'start_date',
    'start_time',
    'end_date',
    'end_time',
    'is_visible',
    'notify_user_types',
    'community_ids',
    'building_ids',
    'priority',
    'status',
])]
class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_visible' => 'boolean',
            'notify_user_types' => 'array',
            'community_ids' => 'array',
            'building_ids' => 'array',
        ];
    }

    /**
     * Get the tenant that owns the announcement.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the user who created the announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include active announcements.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('is_visible', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope a query to only include visible announcements.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    /**
     * Scope a query to filter by tenant.
     */
    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Check if the announcement is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->is_visible
            && $this->start_date <= now()
            && $this->end_date >= now();
    }

    /**
     * Check if the announcement has expired.
     */
    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Check if the announcement is scheduled for the future.
     */
    public function isScheduled(): bool
    {
        return $this->start_date > now();
    }
}
