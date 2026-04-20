<?php

namespace App\Models;

use Database\Factories\UnitSpecificationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitSpecification extends Model
{
    /** @use HasFactory<UnitSpecificationFactory> */
    use HasFactory;

    protected $table = 'rf_unit_specifications';

    protected $fillable = [
        'unit_id',
        'key',
        'value',
        'name_ar',
        'name_en',
    ];

    /** @return BelongsTo<Unit, $this> */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
