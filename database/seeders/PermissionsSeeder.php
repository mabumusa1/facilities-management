<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /** @var list<string> */
    private array $subjects = [
        'communities', 'buildings', 'properties', 'leases', 'transactions',
        'payments', 'requests', 'announcements', 'facilities', 'bookings',
        'tenants', 'owners', 'admins', 'managers', 'professionals',
        'settings', 'statuses', 'categories', 'types', 'reports',
        'dashboard', 'contacts', 'offers', 'offerRequests', 'visitorAccess',
        'marketPlaces', 'bookingUnits', 'roles', 'permissions',
        'accountAdmins', 'accountSettings', 'serviceSettings',
        'leaseSettings', 'salesSettings', 'invoiceSettings',
        'managerRequests', 'directories', 'notifications',
        'images', 'tools', 'forms', 'questions', 'answers',
        'suggestions', 'homeServices', 'neighbourhoodServices',
        'schedules', 'rates', 'plans', 'subscriptions',
        'countries', 'cities', 'districts', 'languages',
        'termsAndConditions', 'privacyPolicies', 'profiles',
        'contactUs', 'superAdmins', 'visitorSettings',
        'sub-categories', 'bookingUnitsComments', 'bookingUnitsDiscounts',
        'leaseStatementReports', 'maintenanceRequestReports',
        'tenantReports', 'systemReports',
    ];

    /** @var list<string> */
    private array $actions = [
        'VIEW', 'CREATE', 'UPDATE', 'DELETE', 'RESTORE', 'FORCE_DELETE',
    ];

    public function run(): void
    {
        $permissions = [];
        $now = now();

        foreach ($this->subjects as $subject) {
            foreach ($this->actions as $action) {
                $permissions[] = [
                    'name' => "{$subject}.{$action}",
                    'guard_name' => 'web',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Special-case permissions that do not apply to all subjects
        $permissions[] = [
            'name' => 'transactions.SEND_RECEIPT',
            'guard_name' => 'web',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // Batch upsert for speed
        Permission::query()->upsert(
            $permissions,
            ['name', 'guard_name'],
            ['updated_at'],
        );

        // Reset Spatie cache
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
