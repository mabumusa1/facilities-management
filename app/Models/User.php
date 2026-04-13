<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\ContactType;
use App\Enums\ManagerRole;
use App\Enums\ServiceManagerType;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'contact_type', 'manager_role', 'service_manager_type'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, TwoFactorAuthenticatable;

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
}
