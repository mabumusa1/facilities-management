<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use App\Enums\LeaseNoticeType;
use Database\Factories\LeaseNoticeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaseNotice extends Model
{
    /** @use HasFactory<LeaseNoticeFactory> */
    use BelongsToAccountTenant, HasFactory;

    protected $fillable = [
        'lease_id',
        'tenant_id',
        'sent_by',
        'type',
        'subject_en',
        'body_en',
        'subject_ar',
        'body_ar',
        'sent_at',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'type' => LeaseNoticeType::class,
            'sent_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Lease, $this> */
    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /** @return BelongsTo<Resident, $this> */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'tenant_id');
    }

    /** @return BelongsTo<User, $this> */
    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
