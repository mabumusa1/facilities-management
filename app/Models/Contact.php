<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    /** @use HasFactory<ContactFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'contact_type',
        'first_name',
        'last_name',
        'email',
        'image',
        'georgian_birthdate',
        'gender',
        'national_id',
        'nationality',
        'phone_number',
        'national_phone_number',
        'phone_country_code',
        'active',
        'account_creation_date',
        'last_active',
        'source',
        'accepted_invite',
        'relation',
        'relation_key',
    ];

    protected $casts = [
        'georgian_birthdate' => 'date',
        'account_creation_date' => 'datetime',
        'last_active' => 'datetime',
        'active' => 'boolean',
        'accepted_invite' => 'boolean',
    ];

    protected $appends = ['name'];

    // Accessors

    public function getNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    // Relationships

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Units owned by this contact (for owners) or occupied by this contact (for tenants).
     */
    public function units(): BelongsToMany
    {
        if ($this->contact_type === 'owner') {
            return $this->belongsToMany(Unit::class, 'contact_unit')
                ->withTimestamps();
        }

        // For tenants, get units through leases
        return $this->belongsToMany(Unit::class, 'leases')
            ->withTimestamps();
    }

    /**
     * Leases for this contact (primarily for tenants).
     */
    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class, 'tenant_id');
    }

    /**
     * Active service requests created by this contact.
     */
    public function activeRequests(): HasMany
    {
        return $this->hasMany(Request::class)->where('status', 'active');
    }

    /**
     * All requests created by this contact.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    /**
     * Transactions associated with this contact.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Documents uploaded by this contact (primarily for tenants).
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    // Scopes

    public function scopeOwners($query)
    {
        return $query->where('contact_type', 'owner');
    }

    public function scopeTenants($query)
    {
        return $query->where('contact_type', 'tenant');
    }

    public function scopeAdmins($query)
    {
        return $query->where('contact_type', 'admin');
    }

    public function scopeProfessionals($query)
    {
        return $query->where('contact_type', 'professional');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('active', false);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%")
                ->orWhere('national_id', 'like', "%{$search}%");
        });
    }

    public function scopeInvited($query)
    {
        return $query->where('accepted_invite', true);
    }

    public function scopePendingInvitation($query)
    {
        return $query->where('accepted_invite', false)
            ->whereNotNull('source');
    }

    // Helper Methods

    public function isOwner(): bool
    {
        return $this->contact_type === 'owner';
    }

    public function isTenant(): bool
    {
        return $this->contact_type === 'tenant';
    }

    public function isAdmin(): bool
    {
        return $this->contact_type === 'admin';
    }

    public function isProfessional(): bool
    {
        return $this->contact_type === 'professional';
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function activate(): bool
    {
        return $this->update(['active' => true]);
    }

    public function deactivate(): bool
    {
        return $this->update(['active' => false]);
    }

    public function hasAcceptedInvite(): bool
    {
        return $this->accepted_invite;
    }

    /**
     * Mark the invitation as accepted and update last active timestamp.
     */
    public function acceptInvitation(): bool
    {
        return $this->update([
            'accepted_invite' => true,
            'last_active' => now(),
        ]);
    }

    /**
     * Update the last active timestamp.
     */
    public function updateLastActive(): bool
    {
        return $this->update(['last_active' => now()]);
    }

    /**
     * Get the full phone number with country code.
     */
    public function getFullPhoneNumber(): string
    {
        return $this->phone_number;
    }

    /**
     * Get the local phone number without country code.
     */
    public function getLocalPhoneNumber(): string
    {
        return $this->national_phone_number;
    }
}
