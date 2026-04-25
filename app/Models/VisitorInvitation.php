<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\VisitorInvitationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class VisitorInvitation extends Model
{
    /** @use HasFactory<VisitorInvitationFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_visitor_invitations';

    protected $fillable = [
        'account_tenant_id',
        'community_id',
        'resident_id',
        'visitor_name',
        'visitor_phone',
        'visitor_purpose',
        'expected_at',
        'valid_until',
        'status',
        'notes',
        'qr_code_token',
        'qr_code_sent_via',
    ];

    protected $attributes = [
        'visitor_purpose' => 'visit',
        'status' => 'pending',
        'qr_code_sent_via' => 'none',
    ];

    protected function casts(): array
    {
        return [
            'expected_at' => 'datetime',
            'valid_until' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (VisitorInvitation $invitation) {
            if (empty($invitation->qr_code_token)) {
                $invitation->qr_code_token = (string) Str::uuid();
            }
        });
    }

    /** @return BelongsTo<Community, $this> */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /** @return BelongsTo<User, $this> */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resident_id');
    }

    /** @return HasMany<VisitorLog, $this> */
    public function logs(): HasMany
    {
        return $this->hasMany(VisitorLog::class, 'invitation_id');
    }
}
