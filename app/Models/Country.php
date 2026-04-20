<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasBilingualName, HasFactory;

    protected $fillable = [
        'iso2',
        'iso3',
        'name',
        'name_ar',
        'name_en',
        'dial',
        'currency',
        'capital',
        'continent',
        'unicode',
        'excel',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
