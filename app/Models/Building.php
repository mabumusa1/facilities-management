<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\BuildingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Building extends Model
{
    /** @use HasFactory<BuildingFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_buildings';

    protected $fillable = [
        'name',
        'rf_community_id',
        'city_id',
        'district_id',
        'account_tenant_id',
        'no_floors',
        'year_build',
        'map',
    ];

    protected $attributes = [
        'no_floors' => 0,
    ];

    protected function casts(): array
    {
        return [
            'map' => 'array',
        ];
    }

    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class, 'rf_community_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'rf_building_id');
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'photos');
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'documents');
    }
}
