<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\FacilityCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilityCategory extends Model
{
    /** @use HasFactory<FacilityCategoryFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_facility_categories';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    /** @return HasMany<Facility, $this> */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class, 'category_id');
    }
}
