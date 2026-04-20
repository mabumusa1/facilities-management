<?php

namespace App\Models;

use Database\Factories\MarketplaceUnitFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MarketplaceUnit extends Model
{
    /** @use HasFactory<MarketplaceUnitFactory> */
    use HasFactory;

    protected $table = 'rf_marketplace_units';

    protected $fillable = [
        'unit_id',
        'listing_type',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Unit, $this> */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /** @return HasMany<MarketplaceVisit, $this> */
    public function visits(): HasMany
    {
        return $this->hasMany(MarketplaceVisit::class);
    }
}
