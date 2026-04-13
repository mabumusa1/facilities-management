<?php

namespace Database\Seeders;

use App\Enums\ManagerRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Property permissions
            'manage-properties',
            'view-properties',
            'create-properties',
            'edit-properties',
            'delete-properties',

            // Lease permissions
            'manage-leases',
            'view-leases',
            'create-leases',
            'edit-leases',
            'delete-leases',

            // Transaction permissions
            'manage-transactions',
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',

            // Financial reports permissions
            'view-financial-reports',

            // Service request permissions
            'manage-service-requests',
            'view-service-requests',
            'create-service-requests',
            'edit-service-requests',
            'close-service-requests',
            'assign-service-requests',

            // Announcement permissions
            'manage-announcements',
            'view-announcements',
            'create-announcements',
            'edit-announcements',
            'delete-announcements',

            // Marketplace permissions
            'manage-marketplace',
            'view-marketplace',
            'create-marketplace-listings',
            'edit-marketplace-listings',
            'delete-marketplace-listings',

            // Settings permissions
            'manage-settings',
            'view-settings',

            // User management permissions
            'manage-users',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        foreach (ManagerRole::cases() as $managerRole) {
            $role = Role::create(['name' => $managerRole->key()]);

            // Get capabilities for this role
            $capabilities = $managerRole->capabilities();

            // Assign permissions based on capabilities
            foreach ($capabilities as $capability) {
                $role->givePermissionTo($capability);
            }
        }
    }
}
