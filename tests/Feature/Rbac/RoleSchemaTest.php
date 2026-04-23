<?php

namespace Tests\Feature\Rbac;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use App\Enums\RoleType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Tests\TestCase;

class RoleSchemaTest extends TestCase
{
    use LazilyRefreshDatabase;

    // -------------------------------------------------------------------------
    // Schema column assertions
    // -------------------------------------------------------------------------

    public function test_roles_table_has_extended_columns(): void
    {
        foreach (['name_ar', 'name_en', 'type', 'account_tenant_id'] as $column) {
            $this->assertTrue(
                Schema::hasColumn('roles', $column),
                "roles table is missing column: {$column}",
            );
        }
    }

    public function test_model_has_roles_table_has_scope_columns(): void
    {
        foreach (['community_id', 'building_id', 'service_type_id'] as $column) {
            $this->assertTrue(
                Schema::hasColumn('model_has_roles', $column),
                "model_has_roles table is missing column: {$column}",
            );
        }
    }

    public function test_permissions_table_has_subject_and_action_columns(): void
    {
        foreach (['subject', 'action'] as $column) {
            $this->assertTrue(
                Schema::hasColumn('permissions', $column),
                "permissions table is missing column: {$column}",
            );
        }
    }

    // -------------------------------------------------------------------------
    // Enum completeness
    // -------------------------------------------------------------------------

    public function test_permission_subject_enum_has_exactly_31_cases(): void
    {
        $this->assertCount(31, PermissionSubject::cases());
    }

    public function test_permission_action_enum_has_exactly_6_cases(): void
    {
        $this->assertCount(6, PermissionAction::cases());
    }

    // -------------------------------------------------------------------------
    // Happy-path model persistence
    // -------------------------------------------------------------------------

    public function test_role_persists_with_type_cast(): void
    {
        $role = Role::create([
            'name' => 'test-admin-role',
            'guard_name' => 'web',
            'name_ar' => 'اختبار',
            'name_en' => 'Test Admin',
            'type' => RoleType::AdminRole,
        ]);

        $fresh = Role::findById($role->id);

        $this->assertInstanceOf(Role::class, $fresh);
        $this->assertSame(RoleType::AdminRole, $fresh->type);
    }

    public function test_permission_persists_with_subject_and_action_cast(): void
    {
        $permission = Permission::create([
            'name' => PermissionSubject::Leases->value.'.'.PermissionAction::View->value,
            'guard_name' => 'web',
            'subject' => PermissionSubject::Leases,
            'action' => PermissionAction::View,
        ]);

        $fresh = Permission::findById($permission->id);

        $this->assertSame(PermissionSubject::Leases, $fresh->subject);
        $this->assertSame(PermissionAction::View, $fresh->action);
        $this->assertSame('leases.VIEW', $fresh->name);
    }

    // -------------------------------------------------------------------------
    // Tenant isolation — two tenants may share a role name
    // -------------------------------------------------------------------------

    public function test_two_tenants_can_share_same_role_name(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A']);
        $tenantB = Tenant::create(['name' => 'Tenant B']);

        $roleA = Role::create([
            'name' => 'admins',
            'guard_name' => 'web',
            'account_tenant_id' => $tenantA->id,
        ]);

        $roleB = Role::create([
            'name' => 'admins',
            'guard_name' => 'web',
            'account_tenant_id' => $tenantB->id,
        ]);

        $this->assertModelExists($roleA);
        $this->assertModelExists($roleB);
    }

    // -------------------------------------------------------------------------
    // Failure path — duplicate within same tenant triggers unique violation
    // -------------------------------------------------------------------------

    public function test_duplicate_role_name_within_same_tenant_throws_unique_violation(): void
    {
        $this->expectException(RoleAlreadyExists::class);

        $tenant = Tenant::create(['name' => 'Tenant Dup']);

        Role::create([
            'name' => 'admins',
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        Role::create([
            'name' => 'admins',
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);
    }

    // -------------------------------------------------------------------------
    // Edge case — BelongsToAccountTenant global scope filters by tenant
    // -------------------------------------------------------------------------

    public function test_role_global_scope_filters_by_current_tenant(): void
    {
        $tenantA = Tenant::create(['name' => 'Scope Tenant A']);
        $tenantB = Tenant::create(['name' => 'Scope Tenant B']);

        // Create roles outside of any current-tenant context so global scope is
        // not applied on creation (the trait only auto-fills when a tenant is
        // current, but we are setting the FK explicitly here).
        Role::create([
            'name' => 'scoped-role-a',
            'guard_name' => 'web',
            'account_tenant_id' => $tenantA->id,
        ]);

        Role::create([
            'name' => 'scoped-role-b',
            'guard_name' => 'web',
            'account_tenant_id' => $tenantB->id,
        ]);

        // Make tenant A "current" — the global scope should show only its role.
        $tenantA->makeCurrent();

        $names = Role::where('name', 'like', 'scoped-role-%')->pluck('name')->toArray();

        $this->assertContains('scoped-role-a', $names);
        $this->assertNotContains('scoped-role-b', $names);

        Tenant::forgetCurrent();
    }
}
