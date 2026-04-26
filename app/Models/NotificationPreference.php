<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_notification_preferences';

    protected $fillable = [
        'account_tenant_id',
        'trigger_key',
        'domain',
        'email_enabled',
        'sms_enabled',
        'email_template',
        'sms_template',
    ];

    protected function casts(): array
    {
        return [
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'email_template' => 'array',
            'sms_template' => 'array',
        ];
    }
}
