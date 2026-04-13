<?php

declare(strict_types=1);

namespace App\Enums;

enum ContactType: string
{
    case Owner = 'owner';
    case Tenant = 'tenant';
    case Admin = 'admin';
    case Professional = 'professional';

    /**
     * Get the display label for the contact type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Tenant => 'Tenant',
            self::Admin => 'Admin',
            self::Professional => 'Professional',
        };
    }

    /**
     * Get the Arabic label for the contact type.
     */
    public function labelAr(): string
    {
        return match ($this) {
            self::Owner => 'مالك',
            self::Tenant => 'مستأجر',
            self::Admin => 'مدير',
            self::Professional => 'مزود خدمة',
        };
    }

    /**
     * Get the API endpoint for the contact type.
     */
    public function endpoint(): string
    {
        return match ($this) {
            self::Owner => 'rf/owners',
            self::Tenant => 'rf/tenants',
            self::Admin => 'rf/admins',
            self::Professional => 'rf/professionals',
        };
    }

    /**
     * Get all values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
