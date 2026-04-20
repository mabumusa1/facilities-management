<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Enums\MarketplaceType;
use Database\Factories\CommunityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Community extends Model
{
    /** @use HasFactory<CommunityFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_communities';

    protected $fillable = [
        'name',
        'country_id',
        'currency_id',
        'city_id',
        'district_id',
        'account_tenant_id',
        'sales_commission_rate',
        'rental_commission_rate',
        'map',
        'is_market_place',
        'is_buy',
        'community_marketplace_type',
        'is_off_plan_sale',
        'is_selected_property',
        'count_selected_property',
        'total_income',
    ];

    protected $attributes = [
        'is_market_place' => false,
        'is_buy' => false,
        'is_off_plan_sale' => false,
        'is_selected_property' => false,
        'count_selected_property' => 0,
        'total_income' => 0,
    ];

    protected function casts(): array
    {
        return [
            'map' => 'array',
            'is_market_place' => 'boolean',
            'is_buy' => 'boolean',
            'community_marketplace_type' => MarketplaceType::class,
            'is_off_plan_sale' => 'boolean',
            'is_selected_property' => 'boolean',
            'sales_commission_rate' => 'decimal:2',
            'rental_commission_rate' => 'decimal:2',
            'total_income' => 'decimal:2',
        ];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class, 'rf_community_id');
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'rf_community_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'photos');
    }

    /** @return BelongsToMany<Amenity, $this> */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'community_amenities');
    }
}
