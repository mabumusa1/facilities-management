<?php

namespace Tests\Feature;

use App\Enums\RolesEnum;
use App\Models\User;
use App\Policies\CommunityPolicy;
use App\Policies\LeasePolicy;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
    }

    public function test_permissions_seeder_creates_expected_count(): void
    {
        // 63 subjects × 6 actions = 378
        $this->assertGreaterThanOrEqual(378, Permission::count());
    }

    public function test_all_seven_roles_are_created(): void
    {
        foreach (RolesEnum::cases() as $role) {
            $this->assertTrue(Role::where('name', $role->value)->exists(), "Role {$role->value} should exist");
        }
    }

    public function test_account_admin_has_all_permissions(): void
    {
        $role = Role::findByName(RolesEnum::ACCOUNT_ADMINS->value, 'web');

        $this->assertEquals(Permission::count(), $role->permissions->count());
    }

    public function test_admin_does_not_have_super_admin_permissions(): void
    {
        $role = Role::findByName(RolesEnum::ADMINS->value, 'web');

        $this->assertFalse($role->hasPermissionTo('superAdmins.VIEW'));
        $this->assertFalse($role->hasPermissionTo('accountAdmins.VIEW'));
    }

    public function test_professional_cannot_create_lease(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->assertFalse($user->can('leases.CREATE'));
    }

    public function test_tenant_can_view_requests(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::TENANTS->value);

        $this->assertTrue($user->can('requests.VIEW'));
    }

    public function test_tenant_can_create_requests(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::TENANTS->value);

        $this->assertTrue($user->can('requests.CREATE'));
    }

    public function test_dependent_has_minimal_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::DEPENDENTS->value);

        $this->assertTrue($user->can('dashboard.VIEW'));
        $this->assertTrue($user->can('announcements.VIEW'));
        $this->assertFalse($user->can('leases.CREATE'));
        $this->assertFalse($user->can('communities.DELETE'));
    }

    public function test_community_policy_allows_admin(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $policy = new CommunityPolicy;
        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->create($user));
    }

    public function test_community_policy_denies_professional(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $policy = new CommunityPolicy;
        $this->assertFalse($policy->viewAny($user));
        $this->assertFalse($policy->create($user));
    }

    public function test_lease_policy_denies_professional_create(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $policy = new LeasePolicy;
        $this->assertFalse($policy->create($user));
    }

    public function test_roles_seeder_is_idempotent(): void
    {
        $initialPermCount = Permission::count();
        $initialRoleCount = Role::count();

        $this->seed(RolesSeeder::class);

        $this->assertEquals($initialPermCount, Permission::count());
        $this->assertEquals($initialRoleCount, Role::count());
    }
}
