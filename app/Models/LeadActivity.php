<?php

namespace App\Models;

use Database\Factories\LeadActivityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    /** @use HasFactory<LeadActivityFactory> */
    use HasFactory;

    protected $table = 'lead_activities';

    /**
     * Activity entries are immutable — no updated_at.
     */
    public const UPDATED_AT = null;

    /**
     * Valid activity types.
     */
    public const TYPE_ASSIGNED = 'assigned';

    public const TYPE_UNASSIGNED = 'unassigned';

    public const TYPE_STATUS_CHANGE = 'status_change';

    public const TYPE_NOTE = 'note';

    public const TYPE_CONVERTED = 'converted';

    protected $fillable = [
        'lead_id',
        'user_id',
        'type',
        'data',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Lead, $this> */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
