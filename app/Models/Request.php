<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use Database\Factories\RequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    /** @use HasFactory<RequestFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope, SoftDeletes;

    protected $table = 'rf_requests';

    protected function hasBuildingIdColumn(): bool
    {
        return true;
    }

    protected $fillable = [
        'category_id',
        'subcategory_id',
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
        'preferred_date',
        'preferred_time',
        'priority',
        'admin_notes',
        'resolved_at',
        'assigned_at',
        'completed_at',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'resolved_at' => 'datetime',
            'assigned_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
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
}
