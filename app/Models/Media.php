<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    protected $fillable = [
        'url',
        'name',
        'notes',
        'mediable_type',
        'mediable_id',
        'collection',
    ];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}
