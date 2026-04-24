<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\AppSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tenant-level application / appearance configuration.
 *
 * Single-row-per-tenant. All new per-tenant appearance settings (favicon,
 * login background, sidebar label overrides) live here. Each new setting
 * requires a migration column — this is intentional; the typed schema is
 * preferred over a key-value bag for settings that consuming code reads directly.
 */
class AppSetting extends Model
{
    /** @use HasFactory<AppSettingFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_app_settings';

    protected $fillable = [
        'account_tenant_id',
        'sidebar_label_overrides',
        'favicon_path',
        'login_bg_path',
    ];

    protected function casts(): array
    {
        return [
            'sidebar_label_overrides' => 'array',
        ];
    }
}
