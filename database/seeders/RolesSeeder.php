<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionsSeeder::class);

        // Account Admin gets all permissions
        $accountAdmin = Role::findOrCreate(RolesEnum::ACCOUNT_ADMINS->value, 'web');
        $accountAdmin->syncPermissions(Permission::all());

        // Admin gets most permissions except super-admin-level
        $admin = Role::findOrCreate(RolesEnum::ADMINS->value, 'web');
        $adminPermissions = Permission::query()
            ->where('name', 'not like', 'superAdmins.%')
            ->where('name', 'not like', 'accountAdmins.%')
            ->where('name', 'not like', 'accountSettings.%')
            ->pluck('name');
        $admin->syncPermissions($adminPermissions);

        // Manager gets operational permissions
        $manager = Role::findOrCreate(RolesEnum::MANAGERS->value, 'web');
        $managerSubjects = [
            'communities', 'buildings', 'properties', 'leases', 'transactions',
            'payments', 'requests', 'announcements', 'facilities', 'bookings',
            'tenants', 'owners', 'settings', 'statuses', 'categories', 'types',
            'reports', 'dashboard', 'contacts', 'managerRequests', 'directories',
            'notifications', 'images', 'schedules', 'visitorAccess',
            'marketPlaces', 'bookingUnits', 'serviceSettings',
        ];
        $managerPermissions = Permission::query()
            ->where(function ($q) use ($managerSubjects) {
                foreach ($managerSubjects as $subject) {
                    $q->orWhere('name', 'like', "{$subject}.%");
                }
            })
            ->pluck('name');
        $manager->syncPermissions($managerPermissions);

        // Owner gets limited read + own-resource permissions
        $owner = Role::findOrCreate(RolesEnum::OWNERS->value, 'web');
        $ownerSubjects = [
            'dashboard', 'communities', 'buildings', 'properties', 'leases',
            'transactions', 'payments', 'requests', 'announcements',
            'facilities', 'bookings', 'reports', 'profiles',
        ];
        $ownerPermissions = Permission::query()
            ->where(function ($q) use ($ownerSubjects) {
                foreach ($ownerSubjects as $subject) {
                    $q->orWhere('name', 'like', "{$subject}.%");
                }
            })
            ->whereIn('name', collect($ownerSubjects)->flatMap(fn ($s) => [
                "{$s}.VIEW", "{$s}.CREATE", "{$s}.UPDATE",
            ])->toArray())
            ->pluck('name');
        $owner->syncPermissions($ownerPermissions);

        // Tenant gets self-service permissions
        $tenant = Role::findOrCreate(RolesEnum::TENANTS->value, 'web');
        $tenantSubjects = [
            'dashboard', 'requests', 'announcements', 'facilities',
            'bookings', 'profiles', 'visitorAccess', 'marketPlaces',
        ];
        $tenantPermissions = Permission::query()
            ->where(function ($q) use ($tenantSubjects) {
                foreach ($tenantSubjects as $subject) {
                    $q->orWhere('name', 'like', "{$subject}.%");
                }
            })
            ->whereIn('name', collect($tenantSubjects)->flatMap(fn ($s) => [
                "{$s}.VIEW", "{$s}.CREATE",
            ])->toArray())
            ->pluck('name');
        $tenant->syncPermissions($tenantPermissions);

        // Dependent gets read-only subset
        $dependent = Role::findOrCreate(RolesEnum::DEPENDENTS->value, 'web');
        $dependent->syncPermissions([
            'dashboard.VIEW', 'announcements.VIEW', 'facilities.VIEW',
            'bookings.VIEW', 'bookings.CREATE', 'profiles.VIEW',
        ]);

        // Professional gets service-related permissions
        $professional = Role::findOrCreate(RolesEnum::PROFESSIONALS->value, 'web');
        $professional->syncPermissions([
            'dashboard.VIEW', 'requests.VIEW', 'requests.UPDATE',
            'schedules.VIEW', 'schedules.UPDATE', 'profiles.VIEW', 'profiles.UPDATE',
        ]);
    }
}
