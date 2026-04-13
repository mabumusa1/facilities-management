<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ManagerRole;
use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Generates and manages permissions and roles for the RBAC system.
 */
class PermissionGenerator
{
    /**
     * Generate all permissions from the PermissionSubject and PermissionAction enums.
     *
     * @return array<string>
     */
    public function generateAllPermissions(): array
    {
        $permissions = [];

        foreach (PermissionSubject::cases() as $subject) {
            foreach ($subject->applicableActions() as $action) {
                $permissions[] = $subject->permissionFor($action);
            }
        }

        return $permissions;
    }

    /**
     * Get permissions by module.
     *
     * @return array<string, array<string>>
     */
    public function getPermissionsByModule(): array
    {
        $permissionsByModule = [];

        foreach (PermissionSubject::cases() as $subject) {
            $module = $subject->module();

            if (! isset($permissionsByModule[$module])) {
                $permissionsByModule[$module] = [];
            }

            foreach ($subject->applicableActions() as $action) {
                $permissionsByModule[$module][] = $subject->permissionFor($action);
            }
        }

        return $permissionsByModule;
    }

    /**
     * Create all permissions in the database.
     *
     * @return int The number of permissions created
     */
    public function syncPermissionsToDatabase(): int
    {
        $permissions = $this->generateAllPermissions();
        $created = 0;

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
            $created++;
        }

        return $created;
    }

    /**
     * Get permissions for a specific manager role based on capabilities.
     *
     * @return array<string>
     */
    public function getPermissionsForRole(ManagerRole $role): array
    {
        $capabilities = $role->capabilities();
        $permissions = [];

        foreach ($capabilities as $capability) {
            $permissions = array_merge($permissions, $this->mapCapabilityToPermissions($capability));
        }

        return array_unique($permissions);
    }

    /**
     * Map a capability string to its corresponding permissions.
     *
     * @return array<string>
     */
    public function mapCapabilityToPermissions(string $capability): array
    {
        return match ($capability) {
            'manage-properties' => $this->getModulePermissions('properties'),
            'manage-leases' => $this->getModulePermissions('leasing'),
            'manage-transactions' => $this->getModulePermissions('transactions'),
            'view-financial-reports' => [
                PermissionSubject::FinancialReports->permissionFor(PermissionAction::View),
                PermissionSubject::Reports->permissionFor(PermissionAction::View),
            ],
            'manage-service-requests' => $this->getModulePermissions('service-requests'),
            'manage-announcements' => [
                PermissionSubject::Announcements->permissionFor(PermissionAction::View),
                PermissionSubject::Announcements->permissionFor(PermissionAction::Create),
                PermissionSubject::Announcements->permissionFor(PermissionAction::Edit),
                PermissionSubject::Announcements->permissionFor(PermissionAction::Delete),
                PermissionSubject::Announcements->permissionFor(PermissionAction::Manage),
            ],
            'manage-marketplace' => $this->getModulePermissions('marketplace'),
            'manage-settings' => [
                PermissionSubject::Settings->permissionFor(PermissionAction::View),
                PermissionSubject::Settings->permissionFor(PermissionAction::Edit),
                PermissionSubject::Settings->permissionFor(PermissionAction::Manage),
            ],
            'manage-users' => [
                PermissionSubject::Users->permissionFor(PermissionAction::View),
                PermissionSubject::Users->permissionFor(PermissionAction::Create),
                PermissionSubject::Users->permissionFor(PermissionAction::Edit),
                PermissionSubject::Users->permissionFor(PermissionAction::Delete),
                PermissionSubject::Users->permissionFor(PermissionAction::Manage),
                PermissionSubject::Admins->permissionFor(PermissionAction::View),
                PermissionSubject::Admins->permissionFor(PermissionAction::Create),
                PermissionSubject::Admins->permissionFor(PermissionAction::Edit),
                PermissionSubject::Admins->permissionFor(PermissionAction::Delete),
                PermissionSubject::Admins->permissionFor(PermissionAction::Manage),
            ],
            default => [],
        };
    }

    /**
     * Get all permissions for a module.
     *
     * @return array<string>
     */
    public function getModulePermissions(string $module): array
    {
        $permissions = [];
        $subjects = PermissionSubject::byModule($module);

        foreach ($subjects as $subject) {
            $permissions = array_merge($permissions, $subject->allPermissions());
        }

        return $permissions;
    }

    /**
     * Create all roles and assign permissions.
     */
    public function syncRolesToDatabase(): void
    {
        foreach (ManagerRole::cases() as $managerRole) {
            $role = Role::firstOrCreate([
                'name' => $managerRole->key(),
                'guard_name' => 'web',
            ]);

            $permissions = $this->getPermissionsForRole($managerRole);
            $role->syncPermissions($permissions);
        }
    }

    /**
     * Get a summary of roles and their permission counts.
     *
     * @return array<string, int>
     */
    public function getRolePermissionSummary(): array
    {
        $summary = [];

        foreach (ManagerRole::cases() as $role) {
            $summary[$role->label()] = count($this->getPermissionsForRole($role));
        }

        return $summary;
    }
}
