<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingsAuditLog extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_settings_audit_logs';

    protected $fillable = [
        'account_tenant_id', 'user_id', 'setting_group',
        'setting_key', 'old_value', 'new_value',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
