<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\ServiceRequestTimelineEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ServiceRequestTimelineEvent extends Model
{
    /** @use HasFactory<ServiceRequestTimelineEventFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_service_request_timeline_events';

    /**
     * Timeline events are immutable — no updated_at.
     */
    public const UPDATED_AT = null;

    /**
     * Valid event types for a service request timeline.
     *
     * @var string[]
     */
    public const EVENT_TYPES = [
        'submitted',
        'assigned',
        'accepted',
        'in_progress',
        'pending_parts',
        'resolved',
        'rated',
        'reopened',
        'closed',
        'pending_review',
    ];

    protected $fillable = [
        'service_request_id',
        'event_type',
        'actor_type',
        'actor_id',
        'metadata',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Request, $this> */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(Request::class, 'service_request_id');
    }

    /** @return MorphTo<Model, $this> */
    public function actor(): MorphTo
    {
        return $this->morphTo();
    }
}
