<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Database\Factories\VisitorLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorLog extends Model
{
    /** @use HasFactory<VisitorLogFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_visitor_logs';

    protected $fillable = [
        'account_tenant_id',
        'invitation_id',
        'community_id',
        'visitor_name',
        'visitor_phone',
        'purpose',
        'gate_officer_id',
        'entry_at',
        'exit_at',
        'id_verified',
        'photo_path',
    ];

    protected $attributes = [
        'id_verified' => false,
    ];

    protected function casts(): array
    {
        return [
            'entry_at' => 'datetime',
            'exit_at' => 'datetime',
            'id_verified' => 'boolean',
        ];
    }

    /** @return BelongsTo<VisitorInvitation, $this> */
    public function invitation(): BelongsTo
    {
        return $this->belongsTo(VisitorInvitation::class, 'invitation_id');
    }

    /** @return BelongsTo<Community, $this> */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /** @return BelongsTo<User, $this> */
    public function gateOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gate_officer_id');
    }
}
