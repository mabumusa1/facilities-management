<?php

namespace Tests\Feature;

use App\Enums\ContactType;
use App\Enums\ManagerRole;
use App\Enums\ServiceManagerType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRolesAndContactTypesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_contact_type_enum_has_all_required_types(): void
    {
        $types = ContactType::cases();

        $this->assertCount(4, $types);
        $this->assertContains(ContactType::Owner, $types);
        $this->assertContains(ContactType::Tenant, $types);
        $this->assertContains(ContactType::Admin, $types);
        $this->assertContains(ContactType::Professional, $types);
    }

    public function test_contact_type_returns_correct_labels(): void
    {
        $this->assertEquals('Owner', ContactType::Owner->label());
        $this->assertEquals('Tenant', ContactType::Tenant->label());
        $this->assertEquals('Admin', ContactType::Admin->label());
        $this->assertEquals('Professional', ContactType::Professional->label());
    }

    public function test_contact_type_returns_correct_arabic_labels(): void
    {
        $this->assertEquals('مالك', ContactType::Owner->labelAr());
        $this->assertEquals('مستأجر', ContactType::Tenant->labelAr());
        $this->assertEquals('مدير', ContactType::Admin->labelAr());
        $this->assertEquals('مزود خدمة', ContactType::Professional->labelAr());
    }

    public function test_contact_type_returns_correct_endpoints(): void
    {
        $this->assertEquals('rf/owners', ContactType::Owner->endpoint());
        $this->assertEquals('rf/tenants', ContactType::Tenant->endpoint());
        $this->assertEquals('rf/admins', ContactType::Admin->endpoint());
        $this->assertEquals('rf/professionals', ContactType::Professional->endpoint());
    }

    public function test_manager_role_enum_has_all_required_roles(): void
    {
        $roles = ManagerRole::cases();

        $this->assertCount(5, $roles);
        $this->assertContains(ManagerRole::Admin, $roles);
        $this->assertContains(ManagerRole::AccountingManager, $roles);
        $this->assertContains(ManagerRole::ServiceManager, $roles);
        $this->assertContains(ManagerRole::MarketingManager, $roles);
        $this->assertContains(ManagerRole::SalesAndLeasingManager, $roles);
    }

    public function test_manager_role_returns_correct_keys(): void
    {
        $this->assertEquals('Admins', ManagerRole::Admin->key());
        $this->assertEquals('accountingManagers', ManagerRole::AccountingManager->key());
        $this->assertEquals('serviceManagers', ManagerRole::ServiceManager->key());
        $this->assertEquals('marketingManagers', ManagerRole::MarketingManager->key());
        $this->assertEquals('salesAndLeasingManagers', ManagerRole::SalesAndLeasingManager->key());
    }

    public function test_manager_role_from_key(): void
    {
        $this->assertEquals(ManagerRole::Admin, ManagerRole::fromKey('Admins'));
        $this->assertEquals(ManagerRole::AccountingManager, ManagerRole::fromKey('accountingManagers'));
        $this->assertNull(ManagerRole::fromKey('nonexistent'));
    }

    public function test_manager_role_has_correct_capabilities(): void
    {
        $adminCapabilities = ManagerRole::Admin->capabilities();

        $this->assertContains('manage-properties', $adminCapabilities);
        $this->assertContains('manage-leases', $adminCapabilities);
        $this->assertContains('manage-transactions', $adminCapabilities);
        $this->assertContains('manage-settings', $adminCapabilities);
        $this->assertContains('manage-users', $adminCapabilities);

        $accountingCapabilities = ManagerRole::AccountingManager->capabilities();
        $this->assertContains('manage-transactions', $accountingCapabilities);
        $this->assertContains('view-financial-reports', $accountingCapabilities);
        $this->assertNotContains('manage-properties', $accountingCapabilities);
    }

    public function test_service_manager_type_enum_has_all_required_types(): void
    {
        $types = ServiceManagerType::cases();

        $this->assertCount(4, $types);
        $this->assertContains(ServiceManagerType::HomeServiceRequests, $types);
        $this->assertContains(ServiceManagerType::CommonAreaServiceRequests, $types);
        $this->assertContains(ServiceManagerType::VisitorAccessRequests, $types);
        $this->assertContains(ServiceManagerType::FacilityBookingRequests, $types);
    }

    public function test_user_can_have_contact_type(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Owner,
        ]);

        $this->assertEquals(ContactType::Owner, $user->contact_type);
        $this->assertTrue($user->isOwner());
        $this->assertFalse($user->isTenant());
    }

    public function test_user_can_have_manager_role(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);

        $this->assertEquals(ManagerRole::Admin, $user->manager_role);
        $this->assertTrue($user->hasManagerRole(ManagerRole::Admin));
        $this->assertFalse($user->hasManagerRole(ManagerRole::AccountingManager));
    }

    public function test_user_can_be_service_manager_with_type(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::ServiceManager,
            'service_manager_type' => ServiceManagerType::HomeServiceRequests,
        ]);

        $this->assertTrue($user->isServiceManagerOfType(ServiceManagerType::HomeServiceRequests));
        $this->assertFalse($user->isServiceManagerOfType(ServiceManagerType::VisitorAccessRequests));
    }

    public function test_roles_are_seeded_correctly(): void
    {
        $this->assertDatabaseHas('roles', ['name' => 'Admins']);
        $this->assertDatabaseHas('roles', ['name' => 'accountingManagers']);
        $this->assertDatabaseHas('roles', ['name' => 'serviceManagers']);
        $this->assertDatabaseHas('roles', ['name' => 'marketingManagers']);
        $this->assertDatabaseHas('roles', ['name' => 'salesAndLeasingManagers']);
    }

    public function test_permissions_are_seeded_correctly(): void
    {
        // Check permissions generated from PermissionSubject enum
        $this->assertDatabaseHas('permissions', ['name' => 'view-communities']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage-communities']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-leases']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage-leases']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-transactions']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage-transactions']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-service-requests']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage-service-requests']);
        $this->assertDatabaseHas('permissions', ['name' => 'view-announcements']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage-announcements']);
    }

    public function test_admin_role_has_all_permissions(): void
    {
        $adminRole = Role::findByName('Admins');

        // Admin should have permissions from all modules based on capabilities
        $this->assertTrue($adminRole->hasPermissionTo('view-communities'));
        $this->assertTrue($adminRole->hasPermissionTo('view-leases'));
        $this->assertTrue($adminRole->hasPermissionTo('view-transactions'));
        $this->assertTrue($adminRole->hasPermissionTo('view-service-requests'));
        $this->assertTrue($adminRole->hasPermissionTo('view-announcements'));
        $this->assertTrue($adminRole->hasPermissionTo('manage-users'));
    }

    public function test_user_can_be_assigned_role_with_permissions(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);

        $user->assignRole('Admins');

        $this->assertTrue($user->hasRole('Admins'));
        $this->assertTrue($user->hasPermissionTo('view-communities'));
        $this->assertTrue($user->hasPermissionTo('manage-users'));
    }

    public function test_accounting_manager_has_limited_permissions(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::AccountingManager,
        ]);

        $user->assignRole('accountingManagers');

        $this->assertTrue($user->hasRole('accountingManagers'));
        $this->assertTrue($user->hasPermissionTo('view-transactions'));
        $this->assertTrue($user->hasPermissionTo('view-financial-reports'));
        $this->assertFalse($user->hasPermissionTo('view-communities'));
        $this->assertFalse($user->hasPermissionTo('manage-users'));
    }
}
