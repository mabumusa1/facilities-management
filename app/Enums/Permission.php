<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Permission enum defining all available permissions in the system.
 *
 * Organized by capability area for clarity.
 */
enum Permission: string
{
    // Property permissions
    case ManageProperties = 'manage-properties';
    case ViewProperties = 'view-properties';
    case CreateProperties = 'create-properties';
    case EditProperties = 'edit-properties';
    case DeleteProperties = 'delete-properties';

    // Lease permissions
    case ManageLeases = 'manage-leases';
    case ViewLeases = 'view-leases';
    case CreateLeases = 'create-leases';
    case EditLeases = 'edit-leases';
    case DeleteLeases = 'delete-leases';

    // Transaction permissions
    case ManageTransactions = 'manage-transactions';
    case ViewTransactions = 'view-transactions';
    case CreateTransactions = 'create-transactions';
    case EditTransactions = 'edit-transactions';
    case DeleteTransactions = 'delete-transactions';

    // Financial reports permissions
    case ViewFinancialReports = 'view-financial-reports';

    // Service request permissions
    case ManageServiceRequests = 'manage-service-requests';
    case ViewServiceRequests = 'view-service-requests';
    case CreateServiceRequests = 'create-service-requests';
    case EditServiceRequests = 'edit-service-requests';
    case CloseServiceRequests = 'close-service-requests';
    case AssignServiceRequests = 'assign-service-requests';

    // Announcement permissions
    case ManageAnnouncements = 'manage-announcements';
    case ViewAnnouncements = 'view-announcements';
    case CreateAnnouncements = 'create-announcements';
    case EditAnnouncements = 'edit-announcements';
    case DeleteAnnouncements = 'delete-announcements';

    // Marketplace permissions
    case ManageMarketplace = 'manage-marketplace';
    case ViewMarketplace = 'view-marketplace';
    case CreateMarketplaceListings = 'create-marketplace-listings';
    case EditMarketplaceListings = 'edit-marketplace-listings';
    case DeleteMarketplaceListings = 'delete-marketplace-listings';

    // Settings permissions
    case ManageSettings = 'manage-settings';
    case ViewSettings = 'view-settings';

    // User management permissions
    case ManageUsers = 'manage-users';
    case ViewUsers = 'view-users';
    case CreateUsers = 'create-users';
    case EditUsers = 'edit-users';
    case DeleteUsers = 'delete-users';

    /**
     * Get human-readable label for the permission.
     */
    public function label(): string
    {
        return match ($this) {
            self::ManageProperties => 'Manage Properties',
            self::ViewProperties => 'View Properties',
            self::CreateProperties => 'Create Properties',
            self::EditProperties => 'Edit Properties',
            self::DeleteProperties => 'Delete Properties',
            self::ManageLeases => 'Manage Leases',
            self::ViewLeases => 'View Leases',
            self::CreateLeases => 'Create Leases',
            self::EditLeases => 'Edit Leases',
            self::DeleteLeases => 'Delete Leases',
            self::ManageTransactions => 'Manage Transactions',
            self::ViewTransactions => 'View Transactions',
            self::CreateTransactions => 'Create Transactions',
            self::EditTransactions => 'Edit Transactions',
            self::DeleteTransactions => 'Delete Transactions',
            self::ViewFinancialReports => 'View Financial Reports',
            self::ManageServiceRequests => 'Manage Service Requests',
            self::ViewServiceRequests => 'View Service Requests',
            self::CreateServiceRequests => 'Create Service Requests',
            self::EditServiceRequests => 'Edit Service Requests',
            self::CloseServiceRequests => 'Close Service Requests',
            self::AssignServiceRequests => 'Assign Service Requests',
            self::ManageAnnouncements => 'Manage Announcements',
            self::ViewAnnouncements => 'View Announcements',
            self::CreateAnnouncements => 'Create Announcements',
            self::EditAnnouncements => 'Edit Announcements',
            self::DeleteAnnouncements => 'Delete Announcements',
            self::ManageMarketplace => 'Manage Marketplace',
            self::ViewMarketplace => 'View Marketplace',
            self::CreateMarketplaceListings => 'Create Marketplace Listings',
            self::EditMarketplaceListings => 'Edit Marketplace Listings',
            self::DeleteMarketplaceListings => 'Delete Marketplace Listings',
            self::ManageSettings => 'Manage Settings',
            self::ViewSettings => 'View Settings',
            self::ManageUsers => 'Manage Users',
            self::ViewUsers => 'View Users',
            self::CreateUsers => 'Create Users',
            self::EditUsers => 'Edit Users',
            self::DeleteUsers => 'Delete Users',
        };
    }

    /**
     * Get the capability group this permission belongs to.
     */
    public function group(): string
    {
        return match ($this) {
            self::ManageProperties,
            self::ViewProperties,
            self::CreateProperties,
            self::EditProperties,
            self::DeleteProperties => 'properties',

            self::ManageLeases,
            self::ViewLeases,
            self::CreateLeases,
            self::EditLeases,
            self::DeleteLeases => 'leases',

            self::ManageTransactions,
            self::ViewTransactions,
            self::CreateTransactions,
            self::EditTransactions,
            self::DeleteTransactions,
            self::ViewFinancialReports => 'transactions',

            self::ManageServiceRequests,
            self::ViewServiceRequests,
            self::CreateServiceRequests,
            self::EditServiceRequests,
            self::CloseServiceRequests,
            self::AssignServiceRequests => 'service-requests',

            self::ManageAnnouncements,
            self::ViewAnnouncements,
            self::CreateAnnouncements,
            self::EditAnnouncements,
            self::DeleteAnnouncements => 'announcements',

            self::ManageMarketplace,
            self::ViewMarketplace,
            self::CreateMarketplaceListings,
            self::EditMarketplaceListings,
            self::DeleteMarketplaceListings => 'marketplace',

            self::ManageSettings,
            self::ViewSettings => 'settings',

            self::ManageUsers,
            self::ViewUsers,
            self::CreateUsers,
            self::EditUsers,
            self::DeleteUsers => 'users',
        };
    }

    /**
     * Get all permissions as string values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get permissions by group.
     *
     * @return array<self>
     */
    public static function byGroup(string $group): array
    {
        return array_filter(
            self::cases(),
            fn (self $permission) => $permission->group() === $group
        );
    }
}
