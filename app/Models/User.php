<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\ContactType;
use App\Enums\ManagerRole;
use App\Enums\ServiceManagerType;
use App\Multitenancy\HasScopedAccess;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'tenant_id', 'contact_type', 'manager_role', 'service_manager_type', 'is_all_communities', 'is_all_buildings', 'notification_preferences'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, HasScopedAccess, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'contact_type' => ContactType::class,
            'manager_role' => ManagerRole::class,
            'service_manager_type' => ServiceManagerType::class,
            'is_all_communities' => 'boolean',
            'is_all_buildings' => 'boolean',
            'notification_preferences' => 'array',
        ];
    }

    /**
     * Check if the user is a specific contact type.
     */
    public function isContactType(ContactType $type): bool
    {
        return $this->contact_type === $type;
    }

    /**
     * Check if the user is an owner.
     */
    public function isOwner(): bool
    {
        return $this->isContactType(ContactType::Owner);
    }

    /**
     * Check if the user is a tenant.
     */
    public function isTenant(): bool
    {
        return $this->isContactType(ContactType::Tenant);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->isContactType(ContactType::Admin);
    }

    /**
     * Check if the user is a professional.
     */
    public function isProfessional(): bool
    {
        return $this->isContactType(ContactType::Professional);
    }

    /**
     * Check if the user has a specific manager role.
     */
    public function hasManagerRole(ManagerRole $role): bool
    {
        return $this->manager_role === $role;
    }

    /**
     * Check if the user is a service manager of a specific type.
     */
    public function isServiceManagerOfType(ServiceManagerType $type): bool
    {
        return $this->manager_role === ManagerRole::ServiceManager
            && $this->service_manager_type === $type;
    }

    /**
     * Check if the user has full access to all communities.
     */
    public function hasAllCommunitiesAccess(): bool
    {
        return $this->is_all_communities === true;
    }

    /**
     * Check if the user has full access to all buildings.
     */
    public function hasAllBuildingsAccess(): bool
    {
        return $this->is_all_buildings === true;
    }

    /**
     * Check if the user has unrestricted scope access.
     */
    public function hasUnrestrictedAccess(): bool
    {
        return $this->hasAllCommunitiesAccess() && $this->hasAllBuildingsAccess();
    }

    /**
     * Get the tenant that owns the user.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the capabilities for this user based on their manager role.
     *
     * @return array<string>
     */
    public function getCapabilities(): array
    {
        if ($this->manager_role === null) {
            return [];
        }

        return $this->manager_role->capabilities();
    }

    /**
     * Check if the user has a specific capability.
     */
    public function hasCapability(string $capability): bool
    {
        return in_array($capability, $this->getCapabilities(), true);
    }

    /**
     * Check if the user can manage properties.
     */
    public function canManageProperties(): bool
    {
        return $this->hasCapability('manage-properties');
    }

    /**
     * Check if the user can manage leases.
     */
    public function canManageLeases(): bool
    {
        return $this->hasCapability('manage-leases');
    }

    /**
     * Check if the user can manage transactions.
     */
    public function canManageTransactions(): bool
    {
        return $this->hasCapability('manage-transactions');
    }

    /**
     * Check if the user can view financial reports.
     */
    public function canViewFinancialReports(): bool
    {
        return $this->hasCapability('view-financial-reports');
    }

    /**
     * Check if the user can manage service requests.
     */
    public function canManageServiceRequests(): bool
    {
        return $this->hasCapability('manage-service-requests');
    }

    /**
     * Check if the user can manage announcements.
     */
    public function canManageAnnouncements(): bool
    {
        return $this->hasCapability('manage-announcements');
    }

    /**
     * Check if the user can manage marketplace.
     */
    public function canManageMarketplace(): bool
    {
        return $this->hasCapability('manage-marketplace');
    }

    /**
     * Check if the user can manage settings.
     */
    public function canManageSettings(): bool
    {
        return $this->hasCapability('manage-settings');
    }

    /**
     * Check if the user can manage users.
     */
    public function canManageUsers(): bool
    {
        return $this->hasCapability('manage-users');
    }
}
