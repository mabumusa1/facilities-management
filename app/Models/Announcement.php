<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Concerns\HasManagerScope;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use BelongsToAccountTenant, HasFactory, HasManagerScope, SoftDeletes;

    protected $table = 'rf_announcements';

    protected function hasBuildingIdColumn(): bool
    {
        return true;
    }

    protected $fillable = [
        'community_id',
        'building_id',
        'title',
        'content',
        'status',
        'published_at',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Community, $this> */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class, 'community_id');
    }

    /** @return BelongsTo<Building, $this> */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }
}
