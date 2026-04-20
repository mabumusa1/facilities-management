<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\UnitCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitCategory extends Model
{
    /** @use HasFactory<UnitCategoryFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_unit_categories';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'icon',
    ];

    public function types(): HasMany
    {
        return $this->hasMany(UnitType::class, 'category_id');
    }
}
