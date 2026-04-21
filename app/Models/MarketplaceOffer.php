<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\MarketplaceOfferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceOffer extends Model
{
    /** @use HasFactory<MarketplaceOfferFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_marketplace_offers';

    protected $fillable = [
        'unit_id',
        'account_tenant_id',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $attributes = [
        'discount_type' => 'percentage',
        'discount_value' => 0,
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
