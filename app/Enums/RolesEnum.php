<?php

namespace App\Enums;

enum RolesEnum: string
{
    case ACCOUNT_ADMINS = 'accountAdmins';
    case ADMINS = 'admins';
    case MANAGERS = 'managers';
    case OWNERS = 'owners';
    case TENANTS = 'tenants';
    case DEPENDENTS = 'dependents';
    case PROFESSIONALS = 'professionals';

    public function label(): string
    {
        return match ($this) {
            self::ACCOUNT_ADMINS => 'Super admin with all permissions (Account Owner)',
            self::ADMINS => 'Admin users with configurable permissions',
            self::MANAGERS => 'Manager users (service, accounting, security managers)',
            self::OWNERS => 'Property owners',
            self::TENANTS => 'Tenant users',
            self::DEPENDENTS => 'Dependents/family members of owners or tenants',
            self::PROFESSIONALS => 'Service professionals/technicians',
        };
    }

    /**
     * Returns the scope level for this role.
     *
     * 'none'           — global role, no scope selectors needed.
     * 'manager'        — requires community; building is optional.
     * 'serviceManager' — requires community + service type; building is optional.
     */
    public function scopeLevel(): string
    {
        return match ($this) {
            self::MANAGERS => 'manager',
            default => 'none',
        };
    }
}
