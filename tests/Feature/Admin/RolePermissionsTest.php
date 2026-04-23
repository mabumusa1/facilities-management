<?php

namespace Tests\Feature\Admin;

use App\Enums\RolesEnum;
use App\Enums\RoleType;
use App\Models\AccountMembership;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RolePermissionsTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesSeeder::class);
    }

    /**
     * @return array{0: User, 1: Tenant, 2: Role}
     */
    private function createTenantAdminAndCustomRole(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Permissions Test Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ADMINS->value,
        ]);

        $user->assignRole(RolesEnum::ADMINS->value);

        $role = Role::create([
            'name' => 'custom-perm-role-'.uniqid(),
            'name_en' => 'Custom Perm Role',
            'name_ar' => 'دور مخصص',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        return [$user, $tenant, $role];
    }

    public function test_permissions_page_renders_for_tenant_admin(): void
    {
        [$admin, $tenant, $role] = $this->createTenantAdminAndCustomRole();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.roles.permissions', $role));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/roles/Permissions')
            ->has('role')
            ->where('role.id', $role->id)
            ->where('role.is_system', false)
            ->has('subjects')
            ->has('actions')
            ->has('presets')
        );
    }

    public function test_permissions_page_for_system_role_shows_is_system_true(): void
    {
        [$admin, $tenant] = array_slice($this->createTenantAdminAndCustomRole(), 0, 2);

        /** @var Role $systemRole */
        $systemRole = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->whereNotNull('name')
            ->firstOrFail();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.roles.permissions', $systemRole));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('role.is_system', true)
        );
    }

    public function test_sync_permissions_replaces_role_permissions(): void
    {
        [$admin, $tenant, $role] = $this->createTenantAdminAndCustomRole();

        $permissionNames = ['communities.VIEW', 'communities.CREATE'];

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson(route('admin.roles.permissions.sync', $role), [
                'permissions' => $permissionNames,
            ]);

        $response->assertOk();
        $response->assertJson(['message' => 'Permissions saved']);

        $role->refresh()->load('permissions');

        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermissionTo('communities.VIEW'));
        $this->assertTrue($role->hasPermissionTo('communities.CREATE'));
    }

    public function test_sync_permissions_empty_removes_all(): void
    {
        [$admin, $tenant, $role] = $this->createTenantAdminAndCustomRole();

        $permission = Permission::withoutGlobalScopes()->where('name', 'communities.VIEW')->first();
        if ($permission) {
            $role->permissions()->sync([$permission->id]);
        }

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson(route('admin.roles.permissions.sync', $role), [
                'permissions' => [],
            ]);

        $response->assertOk();

        $role->refresh();
        $this->assertCount(0, $role->permissions);
    }

    public function test_sync_permissions_validates_permission_names(): void
    {
        [$admin, $tenant, $role] = $this->createTenantAdminAndCustomRole();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson(route('admin.roles.permissions.sync', $role), [
                'permissions' => ['fake.PERMISSION'],
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['permissions.0']);
    }

    public function test_sync_permissions_blocked_for_system_role(): void
    {
        [$admin, $tenant] = array_slice($this->createTenantAdminAndCustomRole(), 0, 2);

        $systemRole = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->whereNotNull('name')
            ->firstOrFail();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->putJson(route('admin.roles.permissions.sync', $systemRole), [
                'permissions' => [],
            ]);

        $response->assertForbidden();
    }

    public function test_unauthenticated_user_cannot_view_permissions_page(): void
    {
        [, , $role] = $this->createTenantAdminAndCustomRole();

        $response = $this->get(route('admin.roles.permissions', $role));

        $response->assertRedirect();
    }

    public function test_user_without_roles_view_cannot_access_permissions_page(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Other Account']);

        [, , $role] = $this->createTenantAdminAndCustomRole();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.roles.permissions', $role));

        $response->assertForbidden();
    }
}
