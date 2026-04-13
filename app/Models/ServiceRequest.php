<?php

namespace App\Models;

use App\Events\ServiceRequest\ServiceRequestAccepted;
use App\Events\ServiceRequest\ServiceRequestAssigned;
use App\Events\ServiceRequest\ServiceRequestCanceled;
use App\Events\ServiceRequest\ServiceRequestCompleted;
use App\Events\ServiceRequest\ServiceRequestInProgress;
use App\Events\ServiceRequest\ServiceRequestRejected;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'status_id',
        'community_id',
        'building_id',
        'unit_id',
        'requester_id',
        'requester_type',
        'professional_id',
        'assigned_by',
        'request_number',
        'title',
        'description',
        'priority',
        'scheduled_date',
        'scheduled_time',
        'is_all_day',
        'accepted_at',
        'started_at',
        'completed_at',
        'canceled_at',
        'estimated_cost',
        'actual_cost',
        'currency',
        'attachments',
        'notes',
        'admin_notes',
        'professional_notes',
        'rejection_reason',
        'cancellation_reason',
        'rating',
        'feedback',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'scheduled_time' => 'datetime:H:i',
        'is_all_day' => 'boolean',
        'accepted_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'canceled_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'attachments' => 'array',
    ];

    // Relationships

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestSubcategory::class, 'subcategory_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'requester_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'professional_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'assigned_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'created_by');
    }

    public function stateHistory(): HasMany
    {
        return $this->hasMany(ServiceRequestStateHistory::class);
    }

    // Query Scopes

    public function scopeNew(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->whereIn('slug', ['request_new', 'request_new_visitor']));
    }

    public function scopeAssigned(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->where('slug', 'request_assigned'));
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->where('slug', 'request_in_progress'));
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->where('slug', 'request_completed'));
    }

    public function scopeCanceled(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->where('slug', 'request_canceled'));
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->whereHas('status', fn ($q) => $q->whereIn('slug', ['request_rejected', 'request_rejected_visitor']));
    }

    public function scopeByCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySubcategory(Builder $query, int $subcategoryId): Builder
    {
        return $query->where('subcategory_id', $subcategoryId);
    }

    public function scopeByRequester(Builder $query, int $requesterId): Builder
    {
        return $query->where('requester_id', $requesterId);
    }

    public function scopeByProfessional(Builder $query, int $professionalId): Builder
    {
        return $query->where('professional_id', $professionalId);
    }

    public function scopeByUnit(Builder $query, int $unitId): Builder
    {
        return $query->where('unit_id', $unitId);
    }

    public function scopeByBuilding(Builder $query, int $buildingId): Builder
    {
        return $query->where('building_id', $buildingId);
    }

    public function scopeByCommunity(Builder $query, int $communityId): Builder
    {
        return $query->where('community_id', $communityId);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeScheduledBetween(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('scheduled_date', [$startDate, $endDate]);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('scheduled_date', '<', now())
            ->whereNull('completed_at')
            ->whereNull('canceled_at');
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('professional_id');
    }

    public function scopeRated(Builder $query): Builder
    {
        return $query->whereNotNull('rating');
    }

    // Helper Methods

    public function isNew(): bool
    {
        return $this->status && in_array($this->status->slug, ['request_new', 'request_new_visitor']);
    }

    public function isAssigned(): bool
    {
        return $this->status && $this->status->slug === 'request_assigned';
    }

    public function isAccepted(): bool
    {
        return $this->status && $this->status->slug === 'request_accepted';
    }

    public function isInProgress(): bool
    {
        return $this->status && $this->status->slug === 'request_in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status && $this->status->slug === 'request_completed';
    }

    public function isCanceled(): bool
    {
        return $this->status && $this->status->slug === 'request_canceled';
    }

    public function isRejected(): bool
    {
        return $this->status && in_array($this->status->slug, ['request_rejected', 'request_rejected_visitor']);
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_date &&
            $this->scheduled_date->isPast() &&
            ! $this->completed_at &&
            ! $this->canceled_at;
    }

    public function hasAttachments(): bool
    {
        return ! empty($this->attachments);
    }

    public function hasRating(): bool
    {
        return $this->rating !== null;
    }

    protected function recordStateChange(int $toStatusId, int $fromStatusId, ?int $changedBy = null, ?string $notes = null, ?array $metadata = null): void
    {
        ServiceRequestStateHistory::create([
            'service_request_id' => $this->id,
            'from_status_id' => $fromStatusId,
            'to_status_id' => $toStatusId,
            'changed_by' => $changedBy,
            'notes' => $notes,
            'metadata' => $metadata,
        ]);
    }

    public function markAsAssigned(int $professionalId, ?int $assignedBy = null): void
    {
        $status = Status::where('domain', 'request')->where('slug', 'request_assigned')->first();
        $newStatusId = $status?->id ?? $this->status_id;
        $oldStatusId = $this->status_id;

        $this->update([
            'professional_id' => $professionalId,
            'assigned_by' => $assignedBy,
            'status_id' => $newStatusId,
        ]);

        $this->recordStateChange($newStatusId, $oldStatusId, $assignedBy, 'Request assigned to professional');
        ServiceRequestAssigned::dispatch($this);
    }

    public function markAsAccepted(): void
    {
        $status = Status::where('domain', 'request')->where('slug', 'request_accepted')->first();
        $newStatusId = $status?->id ?? $this->status_id;
        $oldStatusId = $this->status_id;

        $this->update([
            'status_id' => $newStatusId,
            'accepted_at' => now(),
        ]);

        $this->recordStateChange($newStatusId, $oldStatusId, $this->professional_id, 'Request accepted by professional');
        ServiceRequestAccepted::dispatch($this);
    }

    public function markAsInProgress(): void
    {
        $status = Status::where('domain', 'request')->where('slug', 'request_in_progress')->first();
        $newStatusId = $status?->id ?? $this->status_id;
        $oldStatusId = $this->status_id;

        $this->update([
            'status_id' => $newStatusId,
            'started_at' => now(),
        ]);

        $this->recordStateChange($newStatusId, $oldStatusId, $this->professional_id, 'Work started on request');
        ServiceRequestInProgress::dispatch($this);
    }

    public function markAsCompleted(?float $actualCost = null): void
    {
        $status = Status::where('domain', 'request')->where('slug', 'request_completed')->first();
        $newStatusId = $status?->id ?? $this->status_id;
        $oldStatusId = $this->status_id;

        $this->update([
            'status_id' => $newStatusId,
            'completed_at' => now(),
            'actual_cost' => $actualCost ?? $this->actual_cost,
        ]);

        $this->recordStateChange($newStatusId, $oldStatusId, $this->professional_id, 'Request completed', ['actual_cost' => $actualCost]);
        ServiceRequestCompleted::dispatch($this);
    }

    public function markAsCanceled(string $reason): void
    {
        $status = Status::where('domain', 'request')->where('slug', 'request_canceled')->first();
        $newStatusId = $status?->id ?? $this->status_id;
        $oldStatusId = $this->status_id;

        $this->update([
            'status_id' => $newStatusId,
            'canceled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        $this->recordStateChange($newStatusId, $oldStatusId, null, $reason);
        ServiceRequestCanceled::dispatch($this);
    }

    public function markAsRejected(string $reason): void
    {
        $status = Status::where('domain', 'request')->where('slug', 'request_rejected')->first();
        $newStatusId = $status?->id ?? $this->status_id;
        $oldStatusId = $this->status_id;

        $this->update([
            'status_id' => $newStatusId,
            'rejection_reason' => $reason,
        ]);

        $this->recordStateChange($newStatusId, $oldStatusId, $this->professional_id, $reason);
        ServiceRequestRejected::dispatch($this);
    }

    public function addRating(int $rating, ?string $feedback = null): void
    {
        if ($rating < 1 || $rating > 5) {
            throw new \InvalidArgumentException('Rating must be between 1 and 5');
        }

        $this->update([
            'rating' => $rating,
            'feedback' => $feedback,
        ]);
    }

    public function generateRequestNumber(): string
    {
        $prefix = 'SR';
        $date = now()->format('Ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $date, $sequence);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ServiceRequest $request) {
            if (empty($request->request_number)) {
                $request->request_number = $request->generateRequestNumber();
            }
        });
    }
}
