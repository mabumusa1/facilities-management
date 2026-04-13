<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaseApplication extends Model
{
    use HasFactory, SoftDeletes;

    // Status constants
    public const STATUS_DRAFT = 'draft';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_REVIEW = 'review';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_ON_HOLD = 'on_hold';

    // Application source constants
    public const SOURCE_WALK_IN = 'walk_in';

    public const SOURCE_WEBSITE = 'website';

    public const SOURCE_REFERRAL = 'referral';

    public const SOURCE_MARKETPLACE = 'marketplace';

    // Allowed state transitions
    protected static array $allowedTransitions = [
        self::STATUS_DRAFT => [self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
        self::STATUS_IN_PROGRESS => [self::STATUS_REVIEW, self::STATUS_ON_HOLD, self::STATUS_CANCELLED],
        self::STATUS_REVIEW => [self::STATUS_APPROVED, self::STATUS_REJECTED, self::STATUS_IN_PROGRESS],
        self::STATUS_ON_HOLD => [self::STATUS_IN_PROGRESS, self::STATUS_CANCELLED],
        self::STATUS_APPROVED => [], // Terminal state, only conversion to lease is allowed
        self::STATUS_REJECTED => [], // Terminal state
        self::STATUS_CANCELLED => [], // Terminal state
    ];

    protected $fillable = [
        'tenant_id',
        'application_number',
        'status',
        'applicant_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'applicant_type',
        'company_name',
        'national_id',
        'commercial_registration',
        'community_id',
        'building_id',
        'quoted_rental_amount',
        'security_deposit',
        'proposed_start_date',
        'proposed_end_date',
        'proposed_duration_months',
        'special_terms',
        'notes',
        'quote_pdf_url',
        'quote_sent_at',
        'quote_expires_at',
        'reviewed_by_id',
        'reviewed_at',
        'review_notes',
        'rejection_reason',
        'converted_lease_id',
        'converted_at',
        'created_by_id',
        'assigned_to_id',
        'source',
    ];

    protected $casts = [
        'quoted_rental_amount' => 'decimal:2',
        'security_deposit' => 'decimal:2',
        'proposed_start_date' => 'date',
        'proposed_end_date' => 'date',
        'proposed_duration_months' => 'integer',
        'quote_sent_at' => 'datetime',
        'quote_expires_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    // Relationships
    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'applicant_id');
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'lease_application_units')
            ->withPivot(['proposed_rental_amount', 'net_area', 'meter_cost'])
            ->withTimestamps();
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'reviewed_by_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'created_by_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'assigned_to_id');
    }

    public function convertedLease(): BelongsTo
    {
        return $this->belongsTo(Lease::class, 'converted_lease_id');
    }

    public function stateHistory(): HasMany
    {
        return $this->hasMany(LeaseApplicationStateHistory::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopeByTenantOrg(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_DRAFT,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW,
            self::STATUS_ON_HOLD,
        ]);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereIn('status', [
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_CANCELLED,
        ]);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeNotConverted(Builder $query): Builder
    {
        return $query->whereNull('converted_lease_id');
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('application_number', 'like', "%{$search}%")
                ->orWhere('applicant_name', 'like', "%{$search}%")
                ->orWhere('applicant_email', 'like', "%{$search}%")
                ->orWhere('applicant_phone', 'like', "%{$search}%");
        });
    }

    // State Machine Methods
    public function canTransitionTo(string $newStatus): bool
    {
        $allowed = self::$allowedTransitions[$this->status] ?? [];

        return in_array($newStatus, $allowed);
    }

    public function getAllowedTransitions(): array
    {
        return self::$allowedTransitions[$this->status] ?? [];
    }

    public static function getStatusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_REVIEW => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_CANCELLED => 'Cancelled',
            self::STATUS_ON_HOLD => 'On Hold',
        ];
    }

    public function getStatusLabel(): string
    {
        return self::getStatusLabels()[$this->status] ?? $this->status;
    }

    // Helper Methods
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isUnderReview(): bool
    {
        return $this->status === self::STATUS_REVIEW;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isOnHold(): bool
    {
        return $this->status === self::STATUS_ON_HOLD;
    }

    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_IN_PROGRESS,
            self::STATUS_REVIEW,
            self::STATUS_ON_HOLD,
        ]);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_CANCELLED,
        ]);
    }

    public function isConverted(): bool
    {
        return $this->converted_lease_id !== null;
    }

    public function canBeConverted(): bool
    {
        return $this->isApproved() && ! $this->isConverted();
    }

    public function isQuoteExpired(): bool
    {
        return $this->quote_expires_at && $this->quote_expires_at->isPast();
    }

    public function generateApplicationNumber(): string
    {
        $year = now()->format('Y');
        $count = self::whereYear('created_at', now()->year)->count() + 1;

        return sprintf('APP-%s-%05d', $year, $count);
    }
}
