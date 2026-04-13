<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseApplicationStateHistory extends Model
{
    protected $table = 'lease_application_state_history';

    protected $fillable = [
        'lease_application_id',
        'from_status',
        'to_status',
        'changed_by_id',
        'notes',
    ];

    public function leaseApplication(): BelongsTo
    {
        return $this->belongsTo(LeaseApplication::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'changed_by_id');
    }

    public function getFromStatusLabel(): string
    {
        if (! $this->from_status) {
            return 'None';
        }

        return LeaseApplication::getStatusLabels()[$this->from_status] ?? $this->from_status;
    }

    public function getToStatusLabel(): string
    {
        return LeaseApplication::getStatusLabels()[$this->to_status] ?? $this->to_status;
    }
}
