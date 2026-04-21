<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_system_settings';

    protected $fillable = [
        'key',
        'payload',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}
