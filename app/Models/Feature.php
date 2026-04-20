<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\FeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    /** @use HasFactory<FeatureFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_features';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'type',
        'icon',
    ];

    /** @return BelongsToMany<Unit, $this> */
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'feature_unit');
    }
}
