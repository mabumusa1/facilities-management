<?php

namespace Tests\Feature\Admin;

use App\Enums\RolesEnum;
use App\Enums\RoleType;
use App\Models\AccountMembership;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function createTenantAdmin(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Role Test Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ADMINS->value,
        ]);

        $user->assignRole(RolesEnum::ADMINS->value);

        return [$user, $tenant];
    }

    public function test_index_returns_roles_page(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.roles.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/roles/Index')
            ->has('roles.data')
            ->has('filters')
        );
    }

    public function test_index_returns_tenant_and_system_roles(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        // Create a custom tenant role
        $customRole = Role::withoutGlobalScopes()->create([
            'name' => 'custom-role-test',
            'name_en' => 'Custom Role',
            'name_ar' => 'دور مخصص',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.roles.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/roles/Index')
            ->where('roles.data', function ($data) use ($customRole) {
                $ids = array_column(collect($data)->toArray(), 'id');

                return in_array($customRole->id, $ids);
            })
        );

        $customRole->delete();
    }

    public function test_index_search_filter_returns_matching_roles(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'searchable-unique-role',
            'name_en' => 'SearchableUnique Role',
            'name_ar' => 'دور فريد',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.roles.index', ['search' => 'SearchableUnique']));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('roles.data', function ($data) use ($role) {
                $arr = collect($data)->toArray();

                return count($arr) >= 1 && in_array($role->id, array_column($arr, 'id'));
            })
            ->where('filters.search', 'SearchableUnique')
        );

        $role->delete();
    }

    public function test_index_type_filter_returns_matching_roles(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.roles.index', ['type' => 'userRole']));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('roles.data', function ($data) {
                foreach (collect($data)->toArray() as $role) {
                    if (isset($role['type']) && $role['type'] !== null && $role['type'] !== 'userRole') {
                        return false;
                    }
                }

                return true;
            })
            ->where('filters.type', 'userRole')
        );
    }

    public function test_store_creates_role_with_bilingual_names(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_en' => 'Brand New Role',
                'name_ar' => 'دور جديد',
                'type' => 'userRole',
            ]);

        $response->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'name_en' => 'Brand New Role',
            'name_ar' => 'دور جديد',
            'account_tenant_id' => $tenant->id,
        ]);
    }

    public function test_update_changes_bilingual_names(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'editable-role',
            'name_en' => 'Editable Role',
            'name_ar' => 'دور قابل للتعديل',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.roles.update', $role), [
                'name_en' => 'Updated Role EN',
                'name_ar' => 'الدور المحدث',
            ]);

        $response->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name_en' => 'Updated Role EN',
            'name_ar' => 'الدور المحدث',
        ]);

        $role->delete();
    }

    public function test_destroy_removes_custom_role(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'deletable-role',
            'name_en' => 'Deletable Role',
            'name_ar' => 'دور قابل للحذف',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('admin.roles.destroy', $role));

        $response->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_update_system_role_returns_403(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        // Find a system role (account_tenant_id IS NULL)
        $systemRole = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->first();

        $this->assertNotNull($systemRole, 'A system role must exist (run RolesSeeder)');

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.roles.update', $systemRole), [
                'name_en' => 'Hacked Name',
                'name_ar' => 'اسم مخترق',
            ]);

        $response->assertForbidden();
    }

    public function test_destroy_system_role_returns_403(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $systemRole = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->first();

        $this->assertNotNull($systemRole);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('admin.roles.destroy', $systemRole));

        $response->assertForbidden();
    }

    public function test_store_rejects_duplicate_name_en(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        // Create first role
        Role::withoutGlobalScopes()->create([
            'name' => 'duplicate-check-role',
            'name_en' => 'Duplicate Role EN',
            'name_ar' => 'دور مكرر',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_en' => 'Duplicate Role EN',
                'name_ar' => 'دور آخر',
                'type' => 'userRole',
            ]);

        $response->assertSessionHasErrors('name_en');
    }

    public function test_update_ignores_type_field(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'type-lock-role',
            'name_en' => 'Type Lock Role',
            'name_ar' => 'دور مقفل النوع',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.roles.update', $role), [
                'name_en' => 'Type Lock Role',
                'name_ar' => 'دور مقفل النوع',
                'type' => 'adminRole', // should be ignored
            ]);

        $role->refresh();
        $this->assertEquals(RoleType::UserRole, $role->type);

        $role->delete();
    }
}
