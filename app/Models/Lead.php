<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use BelongsToAccountTenant;

    protected $table = 'rf_leads';

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'source_id',
        'status_id',
        'priority_id',
        'lead_owner_id',
        'interested',
        'lead_last_contact_at',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'lead_last_contact_at' => 'datetime',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function leadOwner(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'lead_owner_id');
    }
}
