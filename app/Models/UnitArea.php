<?php

namespace App\Models;

use Database\Factories\UnitAreaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitArea extends Model
{
    /** @use HasFactory<UnitAreaFactory> */
    use HasFactory;

    protected $table = 'rf_unit_areas';

    protected $fillable = [
        'unit_id',
        'type',
        'name_ar',
        'name_en',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'decimal:2',
        ];
    }

    /** @return BelongsTo<Unit, $this> */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
