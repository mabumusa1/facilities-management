<?php

namespace App\Models;

use App\Enums\InspectionCondition;
use Database\Factories\MoveOutRoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MoveOutRoom extends Model
{
    /** @use HasFactory<MoveOutRoomFactory> */
    use HasFactory;

    protected $fillable = [
        'move_out_id',
        'name',
        'condition',
        'notes',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'condition' => InspectionCondition::class,
            'sort_order' => 'integer',
        ];
    }

    public function moveOut(): BelongsTo
    {
        return $this->belongsTo(MoveOut::class);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('collection', 'inspection_photos');
    }
}
