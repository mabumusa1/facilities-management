<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Permission subjects that actions can be performed on.
 *
 * Based on the 23 identified subjects in the Atar system.
 */
enum PermissionSubject: string
{
    // Properties
    case Communities = 'communities';
    case Buildings = 'buildings';
    case Units = 'units';
    case Facilities = 'facilities';

    // Contacts
    case Owners = 'owners';
    case Tenants = 'tenants';
    case Admins = 'admins';
    case Professionals = 'professionals';

    // Leasing
    case Leases = 'leases';
    case SubLeases = 'sub-leases';
    case Quotes = 'quotes';
    case Applications = 'applications';

    // Transactions
    case Transactions = 'transactions';
    case Invoices = 'invoices';
    case Payments = 'payments';
    case FinancialReports = 'financial-reports';

    // Service Requests
    case ServiceRequests = 'service-requests';
    case ServiceCategories = 'service-categories';

    // Operations
    case VisitorAccess = 'visitor-access';
    case FacilityBookings = 'facility-bookings';

    // Marketplace
    case Marketplace = 'marketplace';
    case MarketplaceListings = 'marketplace-listings';

    // Settings & Communication
    case Settings = 'settings';
    case Announcements = 'announcements';
    case Notifications = 'notifications';
    case Reports = 'reports';
    case Users = 'users';

    /**
     * Get the display label for the subject.
     */
    public function label(): string
    {
        return match ($this) {
            self::Communities => 'Communities',
            self::Buildings => 'Buildings',
            self::Units => 'Units',
            self::Facilities => 'Facilities',
            self::Owners => 'Owners',
            self::Tenants => 'Tenants',
            self::Admins => 'Admins',
            self::Professionals => 'Professionals',
            self::Leases => 'Leases',
            self::SubLeases => 'Sub-leases',
            self::Quotes => 'Quotes',
            self::Applications => 'Applications',
            self::Transactions => 'Transactions',
            self::Invoices => 'Invoices',
            self::Payments => 'Payments',
            self::FinancialReports => 'Financial Reports',
            self::ServiceRequests => 'Service Requests',
            self::ServiceCategories => 'Service Categories',
            self::VisitorAccess => 'Visitor Access',
            self::FacilityBookings => 'Facility Bookings',
            self::Marketplace => 'Marketplace',
            self::MarketplaceListings => 'Marketplace Listings',
            self::Settings => 'Settings',
            self::Announcements => 'Announcements',
            self::Notifications => 'Notifications',
            self::Reports => 'Reports',
            self::Users => 'Users',
        };
    }

    /**
     * Get the module this subject belongs to.
     */
    public function module(): string
    {
        return match ($this) {
            self::Communities,
            self::Buildings,
            self::Units,
            self::Facilities => 'properties',

            self::Owners,
            self::Tenants,
            self::Admins,
            self::Professionals => 'contacts',

            self::Leases,
            self::SubLeases,
            self::Quotes,
            self::Applications => 'leasing',

            self::Transactions,
            self::Invoices,
            self::Payments,
            self::FinancialReports => 'transactions',

            self::ServiceRequests,
            self::ServiceCategories => 'service-requests',

            self::VisitorAccess,
            self::FacilityBookings => 'operations',

            self::Marketplace,
            self::MarketplaceListings => 'marketplace',

            self::Settings,
            self::Announcements,
            self::Notifications,
            self::Reports,
            self::Users => 'settings',
        };
    }

    /**
     * Get the applicable actions for this subject.
     *
     * @return array<PermissionAction>
     */
    public function applicableActions(): array
    {
        return match ($this) {
            // Most subjects support full CRUD + manage
            self::Communities,
            self::Buildings,
            self::Units,
            self::Facilities,
            self::Owners,
            self::Tenants,
            self::Admins,
            self::Professionals,
            self::Leases,
            self::SubLeases,
            self::Transactions,
            self::MarketplaceListings,
            self::Announcements,
            self::Users => [
                PermissionAction::View,
                PermissionAction::Create,
                PermissionAction::Edit,
                PermissionAction::Delete,
                PermissionAction::Manage,
            ],

            // Service requests have additional actions
            self::ServiceRequests => [
                PermissionAction::View,
                PermissionAction::Create,
                PermissionAction::Edit,
                PermissionAction::Delete,
                PermissionAction::Manage,
                PermissionAction::Close,
                PermissionAction::Assign,
            ],

            // Read-only subjects
            self::FinancialReports,
            self::Reports => [
                PermissionAction::View,
            ],

            // Settings and system
            self::Settings,
            self::Notifications => [
                PermissionAction::View,
                PermissionAction::Edit,
                PermissionAction::Manage,
            ],

            // Other subjects
            default => [
                PermissionAction::View,
                PermissionAction::Create,
                PermissionAction::Edit,
                PermissionAction::Delete,
                PermissionAction::Manage,
            ],
        };
    }

    /**
     * Generate permission string for an action on this subject.
     */
    public function permissionFor(PermissionAction $action): string
    {
        return "{$action->value}-{$this->value}";
    }

    /**
     * Get all permissions for this subject.
     *
     * @return array<string>
     */
    public function allPermissions(): array
    {
        return array_map(
            fn (PermissionAction $action) => $this->permissionFor($action),
            $this->applicableActions()
        );
    }

    /**
     * Get subjects by module.
     *
     * @return array<self>
     */
    public static function byModule(string $module): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $subject) => $subject->module() === $module
        ));
    }

    /**
     * Get all available modules.
     *
     * @return array<string>
     */
    public static function modules(): array
    {
        return array_unique(array_map(
            fn (self $subject) => $subject->module(),
            self::cases()
        ));
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
