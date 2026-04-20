<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\UnitRoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitRoom extends Model
{
    /** @use HasFactory<UnitRoomFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_unit_rooms';

    protected $fillable = [
        'unit_id',
        'name',
        'name_ar',
        'name_en',
        'count',
    ];

    /** @return BelongsTo<Unit, $this> */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
