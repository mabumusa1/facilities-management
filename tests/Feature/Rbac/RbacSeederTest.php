<?php

namespace Tests\Feature\Rbac;

use App\Enums\PermissionAction;
use App\Enums\PermissionSubject;
use App\Enums\RoleType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RbacSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function runSeeder(): void
    {
        (new RbacSeeder)->run();
    }

    // ── Happy path ────────────────────────────────────────────────────────────

    public function test_seeder_creates_exactly_186_permissions(): void
    {
        $this->runSeeder();

        $count = Permission::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->count();

        $this->assertSame(186, $count);
    }

    public function test_seeder_creates_exactly_12_roles(): void
    {
        $this->runSeeder();

        $count = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->count();

        $this->assertSame(12, $count);
    }

    public function test_all_permissions_have_bilingual_names(): void
    {
        $this->runSeeder();

        $missing = Permission::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->where(function ($q) {
                $q->whereNull('name_en')
                    ->orWhere('name_en', '')
                    ->orWhereNull('name_ar')
                    ->orWhere('name_ar', '');
            })
            ->count();

        $this->assertSame(0, $missing, 'Every permission must have non-empty name_en and name_ar');
    }

    public function test_all_permissions_have_subject_and_action(): void
    {
        $this->runSeeder();

        $missing = Permission::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->where(function ($q) {
                $q->whereNull('subject')->orWhereNull('action');
            })
            ->count();

        $this->assertSame(0, $missing, 'Every permission must have subject and action set');
    }

    public function test_account_admin_has_all_186_permissions(): void
    {
        $this->runSeeder();

        $role = Role::withoutGlobalScopes()
            ->where('name', 'accountAdmins')
            ->whereNull('account_tenant_id')
            ->firstOrFail();

        $this->assertSame(186, $role->permissions()->count());
    }

    // ── Per-role permission count ranges ─────────────────────────────────────

    #[DataProvider('rolePermissionCounts')]
    public function test_each_role_has_expected_permission_count(string $roleName, int $expectedCount): void
    {
        $this->runSeeder();

        $role = Role::withoutGlobalScopes()
            ->where('name', $roleName)
            ->whereNull('account_tenant_id')
            ->firstOrFail();

        $this->assertSame(
            $expectedCount,
            $role->permissions()->count(),
            "Role '{$roleName}' has unexpected permission count"
        );
    }

    /** @return array<string, array{string, int}> */
    public static function rolePermissionCounts(): array
    {
        // accountAdmins: 31×6 = 186
        // admins: (31-3)×6 = 28×6 = 168
        // managers: 25 subjects × 6 = 150
        // owners: 11 subjects × 3 actions = 33
        // tenants: 13 subjects × 2 actions = 26
        // dependents: 3 subjects × 1 action = 3
        // professionals: 3 subjects × 2 actions = 6
        // Admins (AdminRole): 31×6 = 186
        // accountingManagers: 8 subjects × 6 = 48
        // serviceManagers: 8 subjects × 6 = 48
        // marketingManagers: 6 subjects × 6 = 36
        // salesAndLeasingManagers: 8 subjects × 6 = 48
        return [
            'accountAdmins' => ['accountAdmins', 186],
            'admins' => ['admins', 168],
            'managers' => ['managers', 150],
            'owners' => ['owners', 33],
            'tenants' => ['tenants', 26],
            'dependents' => ['dependents', 3],
            'professionals' => ['professionals', 6],
            'Admins' => ['Admins', 186],
            'accountingManagers' => ['accountingManagers', 48],
            'serviceManagers' => ['serviceManagers', 48],
            'marketingManagers' => ['marketingManagers', 36],
            'salesAndLeasingManagers' => ['salesAndLeasingManagers', 48],
        ];
    }

    // ── Idempotency ───────────────────────────────────────────────────────────

    public function test_running_seeder_twice_does_not_duplicate_permissions(): void
    {
        $this->runSeeder();
        $this->runSeeder();

        $count = Permission::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->count();

        $this->assertSame(186, $count);
    }

    public function test_running_seeder_twice_does_not_duplicate_roles(): void
    {
        $this->runSeeder();
        $this->runSeeder();

        $count = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->count();

        $this->assertSame(12, $count);
    }

    // ── Orphan reconciliation ─────────────────────────────────────────────────

    public function test_orphaned_system_permissions_are_removed_on_rerun(): void
    {
        // Insert a stale system-wide permission not in the 186-member set
        Permission::withoutGlobalScopes()->create([
            'name' => 'staleSubject.VIEW',
            'guard_name' => 'web',
            'account_tenant_id' => null,
        ]);

        $this->runSeeder();

        $exists = Permission::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->where('name', 'staleSubject.VIEW')
            ->exists();

        $this->assertFalse($exists, 'Stale system permission should be deleted by seeder');
    }

    public function test_tenant_custom_permissions_are_not_removed(): void
    {
        // Create a tenant and a custom permission scoped to it
        $tenant = Tenant::query()->create(['name' => 'Test Tenant']);

        Permission::withoutGlobalScopes()->create([
            'name' => 'customSubject.VIEW',
            'guard_name' => 'web',
            'account_tenant_id' => $tenant->id,
        ]);

        $this->runSeeder();

        $exists = Permission::withoutGlobalScopes()
            ->where('account_tenant_id', $tenant->id)
            ->where('name', 'customSubject.VIEW')
            ->exists();

        $this->assertTrue($exists, 'Tenant-scoped custom permission must not be deleted');
    }

    // ── Edge cases ────────────────────────────────────────────────────────────

    public function test_roles_have_correct_type(): void
    {
        $this->runSeeder();

        $userRole = Role::withoutGlobalScopes()
            ->where('name', 'accountAdmins')
            ->whereNull('account_tenant_id')
            ->firstOrFail();

        $adminRole = Role::withoutGlobalScopes()
            ->where('name', 'Admins')
            ->whereNull('account_tenant_id')
            ->firstOrFail();

        $this->assertSame(RoleType::UserRole, $userRole->type);
        $this->assertSame(RoleType::AdminRole, $adminRole->type);
    }

    public function test_roles_have_null_account_tenant_id(): void
    {
        $this->runSeeder();

        $hasNonNullTenant = Role::withoutGlobalScopes()
            ->whereIn('name', [
                'accountAdmins', 'admins', 'managers', 'owners', 'tenants',
                'dependents', 'professionals', 'Admins', 'accountingManagers',
                'serviceManagers', 'marketingManagers', 'salesAndLeasingManagers',
            ])
            ->whereNotNull('account_tenant_id')
            ->exists();

        $this->assertFalse($hasNonNullTenant, 'All 12 default roles must have account_tenant_id = NULL');
    }

    public function test_permission_name_format_is_subject_dot_action(): void
    {
        $this->runSeeder();

        // Spot-check a known permission
        $permission = Permission::withoutGlobalScopes()
            ->where('name', 'communities.VIEW')
            ->whereNull('account_tenant_id')
            ->first();

        $this->assertNotNull($permission);
        $this->assertSame(PermissionSubject::Communities, $permission->subject);
        $this->assertSame(PermissionAction::View, $permission->action);
        $this->assertNotEmpty($permission->name_en);
        $this->assertNotEmpty($permission->name_ar);
    }
}
