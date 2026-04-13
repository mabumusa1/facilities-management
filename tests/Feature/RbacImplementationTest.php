<?php

namespace Tests\Feature;

use App\Enums\ContactType;
use App\Enums\ManagerRole;
use App\Models\User;
use App\Services\PermissionGenerator;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RbacImplementationTest extends TestCase
{
    use RefreshDatabase;

    protected PermissionGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generator = new PermissionGenerator;
    }

    public function test_permission_generator_generates_all_permissions(): void
    {
        $permissions = $this->generator->generateAllPermissions();

        // Should have permissions for all subjects with their applicable actions
        $this->assertNotEmpty($permissions);
        $this->assertContains('view-communities', $permissions);
        $this->assertContains('create-buildings', $permissions);
        $this->assertContains('edit-units', $permissions);
        $this->assertContains('delete-facilities', $permissions);
        $this->assertContains('manage-leases', $permissions);
    }

    public function test_permission_generator_groups_by_module(): void
    {
        $permissionsByModule = $this->generator->getPermissionsByModule();

        $this->assertArrayHasKey('properties', $permissionsByModule);
        $this->assertArrayHasKey('contacts', $permissionsByModule);
        $this->assertArrayHasKey('leasing', $permissionsByModule);
        $this->assertArrayHasKey('transactions', $permissionsByModule);
        $this->assertArrayHasKey('service-requests', $permissionsByModule);
        $this->assertArrayHasKey('operations', $permissionsByModule);
        $this->assertArrayHasKey('marketplace', $permissionsByModule);
        $this->assertArrayHasKey('settings', $permissionsByModule);
    }

    public function test_permission_generator_creates_permissions_in_database(): void
    {
        $count = $this->generator->syncPermissionsToDatabase();

        $this->assertGreaterThan(0, $count);
        $this->assertDatabaseHas('permissions', ['name' => 'view-communities']);
        $this->assertDatabaseHas('permissions', ['name' => 'create-buildings']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage-users']);
    }

    public function test_permission_generator_maps_capabilities_to_permissions(): void
    {
        $propertyPermissions = $this->generator->mapCapabilityToPermissions('manage-properties');

        $this->assertContains('view-communities', $propertyPermissions);
        $this->assertContains('create-communities', $propertyPermissions);
        $this->assertContains('view-buildings', $propertyPermissions);
        $this->assertContains('view-units', $propertyPermissions);
    }

    public function test_permission_generator_gets_permissions_for_admin_role(): void
    {
        $permissions = $this->generator->getPermissionsForRole(ManagerRole::Admin);

        // Admin should have all module permissions
        $this->assertContains('view-communities', $permissions);
        $this->assertContains('manage-leases', $permissions);
        $this->assertContains('manage-transactions', $permissions);
        $this->assertContains('manage-users', $permissions);
    }

    public function test_permission_generator_gets_limited_permissions_for_accounting_role(): void
    {
        $permissions = $this->generator->getPermissionsForRole(ManagerRole::AccountingManager);

        // Accounting should have transaction permissions but not property
        $this->assertContains('view-transactions', $permissions);
        $this->assertContains('view-financial-reports', $permissions);
        $this->assertNotContains('view-communities', $permissions);
        $this->assertNotContains('manage-leases', $permissions);
    }

    public function test_seeder_creates_all_permissions_and_roles(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        // Check permissions exist
        $this->assertDatabaseHas('permissions', ['name' => 'view-communities']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage-users']);

        // Check roles exist
        $this->assertDatabaseHas('roles', ['name' => 'Admins']);
        $this->assertDatabaseHas('roles', ['name' => 'accountingManagers']);
        $this->assertDatabaseHas('roles', ['name' => 'serviceManagers']);
    }

    public function test_admin_role_has_all_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $adminRole = Role::findByName('Admins');
        $permissions = $adminRole->permissions->pluck('name')->toArray();

        // Admin should have permissions from all capabilities
        $this->assertContains('view-communities', $permissions);
        $this->assertContains('manage-leases', $permissions);
        $this->assertContains('manage-transactions', $permissions);
        $this->assertContains('manage-users', $permissions);
    }

    public function test_accounting_role_has_limited_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $accountingRole = Role::findByName('accountingManagers');
        $permissions = $accountingRole->permissions->pluck('name')->toArray();

        // Should have transaction permissions
        $this->assertContains('view-transactions', $permissions);
        $this->assertContains('view-financial-reports', $permissions);

        // Should NOT have property or user permissions
        $this->assertNotContains('view-communities', $permissions);
        $this->assertNotContains('manage-users', $permissions);
    }

    public function test_service_manager_role_has_service_request_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $serviceRole = Role::findByName('serviceManagers');
        $permissions = $serviceRole->permissions->pluck('name')->toArray();

        // Should have service request permissions
        $this->assertContains('view-service-requests', $permissions);
        $this->assertContains('close-service-requests', $permissions);
        $this->assertContains('assign-service-requests', $permissions);

        // Should NOT have property or transaction permissions
        $this->assertNotContains('view-communities', $permissions);
        $this->assertNotContains('manage-transactions', $permissions);
    }

    public function test_user_can_be_assigned_role_with_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);

        $user->assignRole('Admins');

        $this->assertTrue($user->hasRole('Admins'));
        $this->assertTrue($user->hasPermissionTo('view-communities'));
        $this->assertTrue($user->hasPermissionTo('manage-users'));
    }

    public function test_user_with_accounting_role_cannot_manage_properties(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::AccountingManager,
        ]);

        $user->assignRole('accountingManagers');

        $this->assertTrue($user->hasRole('accountingManagers'));
        $this->assertTrue($user->hasPermissionTo('view-transactions'));
        $this->assertFalse($user->hasPermissionTo('view-communities'));
        $this->assertFalse($user->hasPermissionTo('manage-users'));
    }

    public function test_permission_middleware_blocks_unauthorized_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::AccountingManager,
        ]);
        $user->assignRole('accountingManagers');

        // Accounting manager should not have permission to view communities
        $response = $this->actingAs($user)->get('/test-permission-communities');

        $response->assertStatus(403);
    }

    public function test_permission_middleware_allows_authorized_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);
        $user->assignRole('Admins');

        // Admin should have permission to view communities
        $response = $this->actingAs($user)->get('/test-permission-communities');

        $response->assertStatus(200);
    }

    public function test_capability_middleware_blocks_unauthorized_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::AccountingManager,
        ]);

        // Accounting manager should not have manage-properties capability
        $response = $this->actingAs($user)->get('/test-capability-properties');

        $response->assertStatus(403);
    }

    public function test_capability_middleware_allows_authorized_user(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);

        // Admin should have manage-properties capability
        $response = $this->actingAs($user)->get('/test-capability-properties');

        $response->assertStatus(200);
    }

    public function test_permission_count_for_each_role(): void
    {
        $summary = $this->generator->getRolePermissionSummary();

        // Admin should have the most permissions
        $this->assertArrayHasKey('Admin', $summary);
        $this->assertGreaterThan(0, $summary['Admin']);

        // All roles should have some permissions
        foreach ($summary as $role => $count) {
            $this->assertGreaterThan(0, $count, "Role {$role} should have permissions");
        }

        // Admin should have more permissions than Accounting Manager
        $this->assertGreaterThan(
            $summary['Accounting Manager'],
            $summary['Admin']
        );
    }

    public function test_get_module_permissions(): void
    {
        $propertyPermissions = $this->generator->getModulePermissions('properties');

        $this->assertNotEmpty($propertyPermissions);
        $this->assertContains('view-communities', $propertyPermissions);
        $this->assertContains('view-buildings', $propertyPermissions);
        $this->assertContains('view-units', $propertyPermissions);
        $this->assertContains('view-facilities', $propertyPermissions);
    }

    public function test_all_permissions_have_correct_format(): void
    {
        $permissions = $this->generator->generateAllPermissions();

        foreach ($permissions as $permission) {
            $this->assertMatchesRegularExpression(
                '/^[a-z]+-[a-z-]+$/',
                $permission,
                "Permission '{$permission}' should match format 'action-subject'"
            );
        }
    }
}
