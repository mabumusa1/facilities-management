<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\VisitorAccessSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorAccessSetting extends Model
{
    /** @use HasFactory<VisitorAccessSettingFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_visitor_access_settings';

    protected $fillable = [
        'account_tenant_id',
        'community_id',
        'require_id_verification',
        'allow_walk_in',
        'qr_expiry_minutes',
        'max_uses_per_invitation',
    ];

    protected $attributes = [
        'require_id_verification' => false,
        'allow_walk_in' => true,
        'qr_expiry_minutes' => 1440,
        'max_uses_per_invitation' => 1,
    ];

    protected function casts(): array
    {
        return [
            'require_id_verification' => 'boolean',
            'allow_walk_in' => 'boolean',
            'qr_expiry_minutes' => 'integer',
            'max_uses_per_invitation' => 'integer',
        ];
    }

    /** @return BelongsTo<Community, $this> */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }
}
