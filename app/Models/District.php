<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\DistrictFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class District extends Model
{
    /** @use HasFactory<DistrictFactory> */
    use HasBilingualName, HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'city_id',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
