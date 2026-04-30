<?php

namespace App\Models;

use App\Concerns\BelongsToAccountTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        'assigned_to_user_id',
        'interested',
        'lead_last_contact_at',
        'lost_reason',
        'notes',
        'account_tenant_id',
        'converted_contact_id',
        'converted_contact_type',
        'converted_at',
    ];

    protected function casts(): array
    {
        return [
            'lead_last_contact_at' => 'datetime',
            'converted_at' => 'datetime',
        ];
    }

    /**
     * Whether this lead has already been converted to a contact.
     */
    public function isConverted(): bool
    {
        return $this->converted_contact_id !== null;
    }

    /** @return MorphTo<Model, $this> */
    public function convertedContact(): MorphTo
    {
        return $this->morphTo();
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

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /** @return HasMany<LeadActivity, $this> */
    public function leadActivities(): HasMany
    {
        return $this->hasMany(LeadActivity::class);
    }
}
