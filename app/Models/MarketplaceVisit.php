<?php

namespace App\Models;

use Database\Factories\MarketplaceVisitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceVisit extends Model
{
    /** @use HasFactory<MarketplaceVisitFactory> */
    use HasFactory;

    protected $table = 'rf_marketplace_visits';

    protected $fillable = [
        'marketplace_unit_id',
        'status_id',
        'visitor_name',
        'visitor_phone',
        'scheduled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<MarketplaceUnit, $this> */
    public function marketplaceUnit(): BelongsTo
    {
        return $this->belongsTo(MarketplaceUnit::class);
    }

    /** @return BelongsTo<Status, $this> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }
}
