<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\AmenityFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    /** @use HasFactory<AmenityFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_amenities';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'icon',
    ];

    /** @return BelongsToMany<Community, $this> */
    public function communities(): BelongsToMany
    {
        return $this->belongsToMany(Community::class, 'community_amenities');
    }
}
