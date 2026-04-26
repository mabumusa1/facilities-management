<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use Database\Factories\RequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    /** @use HasFactory<RequestFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope, SoftDeletes;

    protected $table = 'rf_requests';

    /**
     * Auto-generate a unique request_code (SR-YYYY-NNNNN) on first create.
     *
     * Sequence is scoped per tenant per year. Resolves after
     * BelongsToAccountTenant sets account_tenant_id in the creating event.
     */
    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (! $model->request_code) {
                // Prefer the already-set account_tenant_id; fall back to current tenant.
                $tenantId = $model->account_tenant_id
                    ?? (Tenant::current()?->id);

                if (! $tenantId) {
                    return;
                }

                $year = now()->year;

                $count = static::withoutGlobalScopes()
                    ->where('account_tenant_id', $tenantId)
                    ->whereRaw('EXTRACT(YEAR FROM created_at) = ?', [$year])
                    ->whereNull('deleted_at')
                    ->count();

                $model->request_code = 'SR-'.$year.'-'.str_pad((string) ($count + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }

    protected function hasBuildingIdColumn(): bool
    {
        return true;
    }

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'service_category_id',
        'service_subcategory_id',
        'status_id',
        'unit_id',
        'community_id',
        'building_id',
        'professional_id',
        'requester_type',
        'requester_id',
        'title',
        'description',
        'request_code',
        'urgency',
        'room_location',
        'preferred_date',
        'preferred_time',
        'priority',
        'admin_notes',
        'resolved_at',
        'assigned_at',
        'completed_at',
        'scheduled_date',
        'completed_date',
        'sla_response_due_at',
        'sla_resolution_due_at',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'scheduled_date' => 'date',
            'completed_date' => 'date',
            'resolved_at' => 'datetime',
            'assigned_at' => 'datetime',
            'completed_at' => 'datetime',
            'sla_response_due_at' => 'datetime',
            'sla_resolution_due_at' => 'datetime',
        ];
    }

    /** @return HasMany<ServiceRequestMessage, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(ServiceRequestMessage::class, 'service_request_id');
    }

    /** @return HasMany<ServiceRequestTimelineEvent, $this> */
    public function timelineEvents(): HasMany
    {
        return $this->hasMany(ServiceRequestTimelineEvent::class, 'service_request_id');
    }

    /** @return BelongsTo<RequestCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(RequestCategory::class, 'category_id');
    }

    /** @return BelongsTo<RequestSubcategory, $this> */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(RequestSubcategory::class, 'subcategory_id');
    }

    /** @return BelongsTo<Status, $this> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /** @return MorphTo<Model, $this> */
    public function requester(): MorphTo
    {
        return $this->morphTo();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    /** @return BelongsTo<ServiceCategory, $this> */
    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /** @return BelongsTo<ServiceSubcategory, $this> */
    public function serviceSubcategory(): BelongsTo
    {
        return $this->belongsTo(ServiceSubcategory::class);
    }
}
