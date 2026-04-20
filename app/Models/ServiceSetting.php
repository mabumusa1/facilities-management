<?php

namespace App\Models;

use Database\Factories\ServiceSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSetting extends Model
{
    /** @use HasFactory<ServiceSettingFactory> */
    use HasFactory;

    protected $table = 'rf_service_settings';

    protected $fillable = [
        'category_id',
        'visibilities',
        'permissions',
        'submit_request_before_type',
        'submit_request_before_value',
        'capacity_type',
        'capacity_value',
    ];

    protected function casts(): array
    {
        return [
            'visibilities' => 'array',
            'permissions' => 'array',
        ];
    }

    /** @return BelongsTo<RequestCategory, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(RequestCategory::class, 'category_id');
    }
}
