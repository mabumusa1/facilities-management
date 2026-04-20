<?php

namespace App\Models;

use App\Concerns\HasBilingualName;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setting extends Model
{
    /** @use HasFactory<SettingFactory> */
    use HasBilingualName, HasFactory;

    protected $table = 'rf_settings';

    protected $fillable = [
        'name',
        'name_ar',
        'name_en',
        'type',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}
