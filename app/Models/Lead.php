<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use BelongsToAccountTenant, HasFactory;

    protected $table = 'rf_leads';

    protected $fillable = [
        'name',
        'name_en',
        'name_ar',
        'first_name',
        'last_name',
        'phone_number',
        'phone_country_code',
        'email',
        'source_id',
        'status_id',
        'priority_id',
        'lead_owner_id',
        'interested',
        'lead_last_contact_at',
        'notes',
        'account_tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'lead_last_contact_at' => 'datetime',
        ];
    }

    /**
     * Return the display name based on the given locale.
     * Falls back to name_en, then the legacy name field.
     */
    public function displayName(string $locale = 'en'): string
    {
        if ($locale === 'ar' && $this->name_ar) {
            return $this->name_ar;
        }

        return $this->name_en ?? $this->name ?? '';
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
