<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequestStateHistory extends Model
{
    protected $fillable = [
        'service_request_id',
        'from_status_id',
        'to_status_id',
        'changed_by',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'from_status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'to_status_id');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'changed_by');
    }
}
