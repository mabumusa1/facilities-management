<?php

namespace Tests\Feature;

use App\Enums\ContactType;
use App\Enums\ManagerRole;
use App\Enums\Permission;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerRolesCapabilitiesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_role_has_all_capabilities(): void
    {
        $capabilities = ManagerRole::Admin->capabilities();

        $this->assertContains('manage-properties', $capabilities);
        $this->assertContains('manage-leases', $capabilities);
        $this->assertContains('manage-transactions', $capabilities);
        $this->assertContains('view-financial-reports', $capabilities);
        $this->assertContains('manage-service-requests', $capabilities);
        $this->assertContains('manage-announcements', $capabilities);
        $this->assertContains('manage-marketplace', $capabilities);
        $this->assertContains('manage-settings', $capabilities);
        $this->assertContains('manage-users', $capabilities);
    }

    public function test_accounting_manager_has_limited_capabilities(): void
    {
        $capabilities = ManagerRole::AccountingManager->capabilities();

        $this->assertContains('manage-transactions', $capabilities);
        $this->assertContains('view-financial-reports', $capabilities);
        $this->assertNotContains('manage-properties', $capabilities);
        $this->assertNotContains('manage-leases', $capabilities);
        $this->assertNotContains('manage-users', $capabilities);
    }

    public function test_service_manager_has_service_request_capability(): void
    {
        $capabilities = ManagerRole::ServiceManager->capabilities();

        $this->assertContains('manage-service-requests', $capabilities);
        $this->assertNotContains('manage-properties', $capabilities);
        $this->assertNotContains('manage-transactions', $capabilities);
    }

    public function test_marketing_manager_has_marketing_capabilities(): void
    {
        $capabilities = ManagerRole::MarketingManager->capabilities();

        $this->assertContains('manage-announcements', $capabilities);
        $this->assertContains('manage-marketplace', $capabilities);
        $this->assertNotContains('manage-properties', $capabilities);
        $this->assertNotContains('manage-transactions', $capabilities);
    }

    public function test_sales_leasing_manager_has_property_capabilities(): void
    {
        $capabilities = ManagerRole::SalesAndLeasingManager->capabilities();

        $this->assertContains('manage-properties', $capabilities);
        $this->assertContains('manage-leases', $capabilities);
        $this->assertContains('manage-marketplace', $capabilities);
        $this->assertNotContains('manage-transactions', $capabilities);
        $this->assertNotContains('manage-users', $capabilities);
    }

    public function test_user_can_check_capabilities(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);

        $this->assertTrue($user->hasCapability('manage-properties'));
        $this->assertTrue($user->hasCapability('manage-users'));
        $this->assertTrue($user->canManageProperties());
        $this->assertTrue($user->canManageUsers());
    }

    public function test_user_without_manager_role_has_no_capabilities(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Tenant,
            'manager_role' => null,
        ]);

        $this->assertEmpty($user->getCapabilities());
        $this->assertFalse($user->hasCapability('manage-properties'));
        $this->assertFalse($user->canManageProperties());
    }

    public function test_accounting_user_can_only_manage_transactions(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::AccountingManager,
        ]);

        $this->assertTrue($user->canManageTransactions());
        $this->assertTrue($user->canViewFinancialReports());
        $this->assertFalse($user->canManageProperties());
        $this->assertFalse($user->canManageLeases());
        $this->assertFalse($user->canManageUsers());
    }

    public function test_permission_enum_has_all_required_permissions(): void
    {
        $permissions = Permission::values();

        $this->assertContains('manage-properties', $permissions);
        $this->assertContains('manage-leases', $permissions);
        $this->assertContains('manage-transactions', $permissions);
        $this->assertContains('manage-service-requests', $permissions);
        $this->assertContains('manage-announcements', $permissions);
        $this->assertContains('manage-marketplace', $permissions);
        $this->assertContains('manage-settings', $permissions);
        $this->assertContains('manage-users', $permissions);
    }

    public function test_permission_enum_groups_are_correct(): void
    {
        $this->assertEquals('properties', Permission::ManageProperties->group());
        $this->assertEquals('leases', Permission::ManageLeases->group());
        $this->assertEquals('transactions', Permission::ManageTransactions->group());
        $this->assertEquals('service-requests', Permission::ManageServiceRequests->group());
        $this->assertEquals('announcements', Permission::ManageAnnouncements->group());
        $this->assertEquals('marketplace', Permission::ManageMarketplace->group());
        $this->assertEquals('settings', Permission::ManageSettings->group());
        $this->assertEquals('users', Permission::ManageUsers->group());
    }

    public function test_permission_by_group_returns_correct_permissions(): void
    {
        $propertyPermissions = Permission::byGroup('properties');

        $this->assertContains(Permission::ManageProperties, $propertyPermissions);
        $this->assertContains(Permission::ViewProperties, $propertyPermissions);
        $this->assertContains(Permission::CreateProperties, $propertyPermissions);
        $this->assertContains(Permission::EditProperties, $propertyPermissions);
        $this->assertContains(Permission::DeleteProperties, $propertyPermissions);
    }

    public function test_user_scope_fields_work_correctly(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
            'is_all_communities' => true,
            'is_all_buildings' => true,
        ]);

        $this->assertTrue($user->hasAllCommunitiesAccess());
        $this->assertTrue($user->hasAllBuildingsAccess());
        $this->assertTrue($user->hasUnrestrictedAccess());
    }

    public function test_user_with_restricted_scope(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::ServiceManager,
            'is_all_communities' => false,
            'is_all_buildings' => false,
        ]);

        $this->assertFalse($user->hasAllCommunitiesAccess());
        $this->assertFalse($user->hasAllBuildingsAccess());
        $this->assertFalse($user->hasUnrestrictedAccess());
    }

    public function test_user_with_partial_scope(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::ServiceManager,
            'is_all_communities' => true,
            'is_all_buildings' => false,
        ]);

        $this->assertTrue($user->hasAllCommunitiesAccess());
        $this->assertFalse($user->hasAllBuildingsAccess());
        $this->assertFalse($user->hasUnrestrictedAccess());
    }

    public function test_capability_matrix_for_all_roles(): void
    {
        $capabilityMatrix = [
            [
                'role' => ManagerRole::Admin,
                'capabilities' => [
                    'manage-properties' => true,
                    'manage-leases' => true,
                    'manage-transactions' => true,
                    'manage-service-requests' => true,
                    'manage-announcements' => true,
                    'manage-marketplace' => true,
                    'manage-settings' => true,
                    'manage-users' => true,
                ],
            ],
            [
                'role' => ManagerRole::AccountingManager,
                'capabilities' => [
                    'manage-properties' => false,
                    'manage-leases' => false,
                    'manage-transactions' => true,
                    'view-financial-reports' => true,
                    'manage-service-requests' => false,
                    'manage-users' => false,
                ],
            ],
            [
                'role' => ManagerRole::ServiceManager,
                'capabilities' => [
                    'manage-properties' => false,
                    'manage-service-requests' => true,
                    'manage-transactions' => false,
                ],
            ],
            [
                'role' => ManagerRole::MarketingManager,
                'capabilities' => [
                    'manage-properties' => false,
                    'manage-announcements' => true,
                    'manage-marketplace' => true,
                    'manage-transactions' => false,
                ],
            ],
            [
                'role' => ManagerRole::SalesAndLeasingManager,
                'capabilities' => [
                    'manage-properties' => true,
                    'manage-leases' => true,
                    'manage-marketplace' => true,
                    'manage-transactions' => false,
                    'manage-users' => false,
                ],
            ],
        ];

        foreach ($capabilityMatrix as $matrixItem) {
            $role = $matrixItem['role'];
            $expectedCapabilities = $matrixItem['capabilities'];

            $user = User::factory()->create([
                'contact_type' => ContactType::Admin,
                'manager_role' => $role,
            ]);

            foreach ($expectedCapabilities as $capability => $expected) {
                $this->assertEquals(
                    $expected,
                    $user->hasCapability($capability),
                    "Role {$role->label()} should ".($expected ? 'have' : 'not have')." capability: {$capability}"
                );
            }
        }
    }
}
