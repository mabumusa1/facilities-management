<?php

namespace App\Models;

use Database\Factories\RequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    /** @use HasFactory<RequestFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'rf_requests';

    protected $fillable = [
        'category_id',
        'subcategory_id',
        'status_id',
        'requester_type',
        'requester_id',
        'title',
        'description',
        'preferred_date',
        'preferred_time',
        'priority',
        'admin_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'resolved_at' => 'datetime',
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
}
