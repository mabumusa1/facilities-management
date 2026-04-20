<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\UnitTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitType extends Model
{
    /** @use HasFactory<UnitTypeFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_unit_types';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'icon',
        'category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(UnitCategory::class, 'category_id');
    }
}
