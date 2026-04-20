<?php

namespace App\Models;

use Database\Factories\WorkingDayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkingDay extends Model
{
    /** @use HasFactory<WorkingDayFactory> */
    use HasFactory;

    protected $table = 'rf_working_days';

    protected $fillable = [
        'subcategory_id',
        'day',
        'start',
        'end',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<RequestSubcategory, $this> */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(RequestSubcategory::class, 'subcategory_id');
    }
}
