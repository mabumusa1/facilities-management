<?php

declare(strict_types=1);

namespace App\Enums;

enum ManagerRole: int
{
    case Admin = 1;
    case AccountingManager = 2;
    case ServiceManager = 3;
    case MarketingManager = 4;
    case SalesAndLeasingManager = 5;

    /**
     * Get the role key identifier.
     */
    public function key(): string
    {
        return match ($this) {
            self::Admin => 'Admins',
            self::AccountingManager => 'accountingManagers',
            self::ServiceManager => 'serviceManagers',
            self::MarketingManager => 'marketingManagers',
            self::SalesAndLeasingManager => 'salesAndLeasingManagers',
        };
    }

    /**
     * Get the display label for the role.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::AccountingManager => 'Accounting Manager',
            self::ServiceManager => 'Service Manager',
            self::MarketingManager => 'Marketing Manager',
            self::SalesAndLeasingManager => 'Sales & Leasing Manager',
        };
    }

    /**
     * Get the Arabic label for the role.
     */
    public function labelAr(): string
    {
        return match ($this) {
            self::Admin => 'مدير',
            self::AccountingManager => 'مسؤول المالي',
            self::ServiceManager => 'مسؤول الخدمات',
            self::MarketingManager => 'مسؤول التسويق',
            self::SalesAndLeasingManager => 'مسؤول المبيعات والتأجير',
        };
    }

    /**
     * Get the capabilities for this role.
     *
     * @return array<string>
     */
    public function capabilities(): array
    {
        return match ($this) {
            self::Admin => [
                'manage-properties',
                'manage-leases',
                'manage-transactions',
                'view-financial-reports',
                'manage-service-requests',
                'manage-announcements',
                'manage-marketplace',
                'manage-settings',
                'manage-users',
            ],
            self::AccountingManager => [
                'manage-transactions',
                'view-financial-reports',
            ],
            self::ServiceManager => [
                'manage-service-requests',
            ],
            self::MarketingManager => [
                'manage-announcements',
                'manage-marketplace',
            ],
            self::SalesAndLeasingManager => [
                'manage-properties',
                'manage-leases',
                'manage-marketplace',
            ],
        };
    }

    /**
     * Get all values as an array.
     *
     * @return array<int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Create from key string.
     */
    public static function fromKey(string $key): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->key() === $key) {
                return $case;
            }
        }

        return null;
    }
}
