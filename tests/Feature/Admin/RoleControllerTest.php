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

    // -------------------------------------------------------------------------
    // AC: Unauthenticated requests are redirected
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_access_index(): void
    {
        $this->get(route('admin.roles.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_store_role(): void
    {
        $this->post(route('admin.roles.store'), [
            'name_en' => 'New Role',
            'name_ar' => 'دور جديد',
            'type' => 'userRole',
        ])->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_update_role(): void
    {
        $role = Role::withoutGlobalScopes()->whereNull('account_tenant_id')->first();

        $this->put(route('admin.roles.update', $role), [
            'name_en' => 'X',
            'name_ar' => 'X',
        ])->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_cannot_delete_role(): void
    {
        $role = Role::withoutGlobalScopes()->whereNull('account_tenant_id')->first();

        $this->delete(route('admin.roles.destroy', $role))
            ->assertRedirect(route('login'));
    }

    // -------------------------------------------------------------------------
    // AC: Non-admin (no permission) is blocked
    // -------------------------------------------------------------------------

    public function test_non_admin_cannot_view_roles_index(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Non Admin Tenant']);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);
        $user->assignRole(RolesEnum::TENANTS->value);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_non_admin_cannot_store_role(): void
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Non Admin Store Tenant']);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);
        $user->assignRole(RolesEnum::TENANTS->value);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_en' => 'Should Fail',
                'name_ar' => 'يجب ان يفشل',
                'type' => 'userRole',
            ])->assertForbidden();
    }

    public function test_non_admin_cannot_update_custom_role(): void
    {
        [$admin, $ownerTenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'non-admin-update-check',
            'name_en' => 'Non Admin Update',
            'name_ar' => 'تحديث غير الادمن',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $ownerTenant->id,
        ]);

        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Non Admin Update Tenant']);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);
        $user->assignRole(RolesEnum::TENANTS->value);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.roles.update', $role), [
                'name_en' => 'Hijacked',
                'name_ar' => 'مخترق',
            ])->assertForbidden();

        $role->delete();
    }

    public function test_non_admin_cannot_delete_custom_role(): void
    {
        [$admin, $ownerTenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'non-admin-delete-check',
            'name_en' => 'Non Admin Delete',
            'name_ar' => 'حذف غير الادمن',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $ownerTenant->id,
        ]);

        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Non Admin Delete Tenant']);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);
        $user->assignRole(RolesEnum::TENANTS->value);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('admin.roles.destroy', $role))
            ->assertForbidden();

        $role->delete();
    }

    // -------------------------------------------------------------------------
    // AC: Validation — bilingual names required
    // -------------------------------------------------------------------------

    public function test_store_requires_name_en(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_ar' => 'دور بدون انجليزي',
                'type' => 'userRole',
            ])->assertSessionHasErrors('name_en');
    }

    public function test_store_requires_name_ar(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_en' => 'Role Without Arabic',
                'type' => 'userRole',
            ])->assertSessionHasErrors('name_ar');
    }

    public function test_store_requires_type(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_en' => 'Role Without Type',
                'name_ar' => 'دور بدون نوع',
            ])->assertSessionHasErrors('type');
    }

    public function test_store_rejects_invalid_type_value(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_en' => 'Bad Type Role',
                'name_ar' => 'دور نوع خاطئ',
                'type' => 'invalidType',
            ])->assertSessionHasErrors('type');
    }

    public function test_update_requires_name_en(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'update-validation-en',
            'name_en' => 'Update Validation EN',
            'name_ar' => 'تحقق من التحديث',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.roles.update', $role), [
                'name_ar' => 'تحقق من التحديث',
            ])->assertSessionHasErrors('name_en');

        $role->delete();
    }

    public function test_update_requires_name_ar(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'update-validation-ar',
            'name_en' => 'Update Validation AR',
            'name_ar' => 'تحقق من التحديث العربي',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.roles.update', $role), [
                'name_en' => 'Update Validation AR',
            ])->assertSessionHasErrors('name_ar');

        $role->delete();
    }

    // -------------------------------------------------------------------------
    // AC: Duplicate name_ar validation on store
    // -------------------------------------------------------------------------

    public function test_store_rejects_duplicate_name_ar(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        Role::withoutGlobalScopes()->create([
            'name' => 'dup-ar-check',
            'name_en' => 'Dup AR Check EN',
            'name_ar' => 'مكرر عربي',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_en' => 'Different EN Name',
                'name_ar' => 'مكرر عربي',
                'type' => 'userRole',
            ])->assertSessionHasErrors('name_ar');
    }

    // -------------------------------------------------------------------------
    // AC: Duplicate name_en validation on update
    // -------------------------------------------------------------------------

    public function test_update_rejects_duplicate_name_en_of_another_role(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        Role::withoutGlobalScopes()->create([
            'name' => 'existing-en-dup',
            'name_en' => 'Existing EN Duplicate',
            'name_ar' => 'موجود مكرر',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $roleToUpdate = Role::withoutGlobalScopes()->create([
            'name' => 'role-to-update-dup',
            'name_en' => 'Role To Update',
            'name_ar' => 'دور للتحديث',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.roles.update', $roleToUpdate), [
                'name_en' => 'Existing EN Duplicate',
                'name_ar' => 'دور للتحديث',
            ])->assertSessionHasErrors('name_en');

        $roleToUpdate->delete();
    }

    // -------------------------------------------------------------------------
    // AC: Update with same names should pass (self-unique ignore)
    // -------------------------------------------------------------------------

    public function test_update_with_same_names_passes_uniqueness(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $role = Role::withoutGlobalScopes()->create([
            'name' => 'self-unique-role',
            'name_en' => 'Self Unique Role',
            'name_ar' => 'دور فريد ذاتي',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.roles.update', $role), [
                'name_en' => 'Self Unique Role',
                'name_ar' => 'دور فريد ذاتي',
            ])->assertRedirect(route('admin.roles.index'));

        $role->delete();
    }

    // -------------------------------------------------------------------------
    // AC: Tenant isolation — tenant B cannot modify tenant A's custom roles
    // -------------------------------------------------------------------------

    public function test_tenant_b_admin_cannot_update_tenant_a_custom_role(): void
    {
        [$adminA, $tenantA] = $this->createTenantAdmin();

        $roleA = Role::withoutGlobalScopes()->create([
            'name' => 'tenant-a-exclusive-role',
            'name_en' => 'Tenant A Role',
            'name_ar' => 'دور المستأجر أ',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenantA->id,
        ]);

        // Create admin for a different tenant
        $userB = User::factory()->create();
        $tenantB = Tenant::create(['name' => 'Tenant B']);
        AccountMembership::create([
            'user_id' => $userB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => RolesEnum::ADMINS->value,
        ]);
        $userB->assignRole(RolesEnum::ADMINS->value);

        $this->actingAs($userB)
            ->withSession(['tenant_id' => $tenantB->id])
            ->put(route('admin.roles.update', $roleA), [
                'name_en' => 'Hijacked By B',
                'name_ar' => 'مخترق من ب',
            ])->assertForbidden();

        $roleA->delete();
    }

    public function test_tenant_b_admin_cannot_delete_tenant_a_custom_role(): void
    {
        [$adminA, $tenantA] = $this->createTenantAdmin();

        $roleA = Role::withoutGlobalScopes()->create([
            'name' => 'tenant-a-delete-isolation',
            'name_en' => 'Tenant A Delete Role',
            'name_ar' => 'دور حذف المستأجر أ',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenantA->id,
        ]);

        $userB = User::factory()->create();
        $tenantB = Tenant::create(['name' => 'Tenant B Delete']);
        AccountMembership::create([
            'user_id' => $userB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => RolesEnum::ADMINS->value,
        ]);
        $userB->assignRole(RolesEnum::ADMINS->value);

        $this->actingAs($userB)
            ->withSession(['tenant_id' => $tenantB->id])
            ->delete(route('admin.roles.destroy', $roleA))
            ->assertForbidden();

        $roleA->delete();
    }

    // -------------------------------------------------------------------------
    // AC: Tenant isolation — index does not expose other tenants' custom roles
    // -------------------------------------------------------------------------

    public function test_index_does_not_show_other_tenants_custom_roles(): void
    {
        [$adminA, $tenantA] = $this->createTenantAdmin();

        $roleA = Role::withoutGlobalScopes()->create([
            'name' => 'tenant-a-secret-role',
            'name_en' => 'Tenant A Secret Role',
            'name_ar' => 'دور سري للمستأجر أ',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenantA->id,
        ]);

        $userB = User::factory()->create();
        $tenantB = Tenant::create(['name' => 'Tenant B Index']);
        AccountMembership::create([
            'user_id' => $userB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => RolesEnum::ADMINS->value,
        ]);
        $userB->assignRole(RolesEnum::ADMINS->value);

        $this->actingAs($userB)
            ->withSession(['tenant_id' => $tenantB->id])
            ->get(route('admin.roles.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('roles.data', function ($data) use ($roleA) {
                    $ids = array_column(collect($data)->toArray(), 'id');

                    return ! in_array($roleA->id, $ids);
                })
            );

        $roleA->delete();
    }

    // -------------------------------------------------------------------------
    // Edge: max-length bilingual names accepted
    // -------------------------------------------------------------------------

    public function test_store_accepts_max_length_bilingual_names(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $maxEn = str_repeat('A', 255);
        $maxAr = str_repeat('أ', 255);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.roles.store'), [
                'name_en' => $maxEn,
                'name_ar' => $maxAr,
                'type' => 'userRole',
            ])->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'name_en' => $maxEn,
            'account_tenant_id' => $tenant->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Edge: same name_en/name_ar allowed across different tenants (not duplicates)
    // -------------------------------------------------------------------------

    public function test_same_name_allowed_in_different_tenants(): void
    {
        [$adminA, $tenantA] = $this->createTenantAdmin();

        Role::withoutGlobalScopes()->create([
            'name' => 'cross-tenant-name-a',
            'name_en' => 'Shared Name Role',
            'name_ar' => 'دور الاسم المشترك',
            'type' => RoleType::UserRole,
            'guard_name' => 'web',
            'account_tenant_id' => $tenantA->id,
        ]);

        $userB = User::factory()->create();
        $tenantB = Tenant::create(['name' => 'Cross Tenant B']);
        AccountMembership::create([
            'user_id' => $userB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => RolesEnum::ADMINS->value,
        ]);
        $userB->assignRole(RolesEnum::ADMINS->value);

        $this->actingAs($userB)
            ->withSession(['tenant_id' => $tenantB->id])
            ->post(route('admin.roles.store'), [
                'name_en' => 'Shared Name Role',
                'name_ar' => 'دور الاسم المشترك',
                'type' => 'userRole',
            ])->assertRedirect(route('admin.roles.index'));

        $this->assertDatabaseHas('roles', [
            'name_en' => 'Shared Name Role',
            'account_tenant_id' => $tenantB->id,
        ]);
    }
}
