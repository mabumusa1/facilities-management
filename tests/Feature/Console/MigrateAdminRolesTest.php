<?php

namespace Tests\Feature\Console;

use App\Enums\AdminRole;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Tenant;
use Database\Seeders\RbacSeeder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class MigrateAdminRolesTest extends TestCase
{
    use LazilyRefreshDatabase;

    private string $morphType;

    protected function setUp(): void
    {
        parent::setUp();

        $map = Relation::morphMap();
        $morphType = array_search(Admin::class, $map);
        $this->morphType = $morphType !== false ? $morphType : Admin::class;
    }

    private function seedRoles(): void
    {
        (new RbacSeeder)->run();
    }

    private function createTenantWithAdmins(): Tenant
    {
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);

        foreach (AdminRole::cases() as $role) {
            Admin::factory()->create([
                'account_tenant_id' => $tenant->id,
                'role' => $role,
            ]);
        }

        return $tenant;
    }

    private function modelHasRolesCount(): int
    {
        return DB::table('model_has_roles')
            ->where('model_type', $this->morphType)
            ->count();
    }

    // ── Happy path ────────────────────────────────────────────────────────────

    public function test_command_inserts_one_row_per_admin_for_all_five_roles(): void
    {
        $this->seedRoles();
        $this->createTenantWithAdmins();

        $this->artisan('rbac:migrate-admin-roles')
            ->assertSuccessful();

        $this->assertSame(5, $this->modelHasRolesCount());
    }

    public function test_inserted_rows_have_correct_role_id_and_model_id(): void
    {
        $this->seedRoles();
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        $admin = Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
            'role' => AdminRole::Admins,
        ]);

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();

        $roleId = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->where('name', AdminRole::Admins->value)
            ->value('id');

        $this->assertDatabaseHas('model_has_roles', [
            'model_type' => $this->morphType,
            'model_id' => $admin->id,
            'role_id' => $roleId,
        ]);
    }

    // ── Idempotency ───────────────────────────────────────────────────────────

    public function test_running_command_twice_does_not_duplicate_rows(): void
    {
        $this->seedRoles();
        $this->createTenantWithAdmins();

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();
        $countAfterFirst = $this->modelHasRolesCount();

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();
        $countAfterSecond = $this->modelHasRolesCount();

        $this->assertSame($countAfterFirst, $countAfterSecond);
    }

    public function test_partial_idempotency_only_inserts_missing_rows(): void
    {
        $this->seedRoles();
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);

        $admins = collect(AdminRole::cases())->map(fn ($role) => Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
            'role' => $role,
        ]));

        // Manually insert one row first.
        $roleId = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->where('name', AdminRole::Admins->value)
            ->value('id');

        DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => $this->morphType,
            'model_id' => $admins->first()->id,
        ]);

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();

        $this->assertSame(5, $this->modelHasRolesCount());
    }

    // ── Null / invalid role handling ──────────────────────────────────────────

    /**
     * The null-role path exists in the command as a defensive code path.
     * The rf_admins.role column is currently NOT NULL in the schema, so this
     * path cannot be triggered without DDL changes (which auto-commit in Postgres
     * and would corrupt the test transaction). The invalid-role test below covers
     * the same skip-and-warn behaviour.
     */
    public function test_admin_with_null_role_is_skipped_via_invalid_role_path(): void
    {
        $this->seedRoles();
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        $admin = Admin::factory()->create(['account_tenant_id' => $tenant->id]);

        // Update to an unrecognised value (cannot set NULL due to NOT NULL constraint).
        DB::table('rf_admins')->where('id', $admin->id)->update(['role' => 'unrecognised']);

        Log::spy();

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();

        $this->assertSame(0, $this->modelHasRolesCount());

        Log::shouldHaveReceived('warning')
            ->withArgs(fn (string $msg) => str_contains($msg, (string) $admin->id));
    }

    public function test_admin_with_invalid_role_is_skipped_and_command_succeeds(): void
    {
        $this->seedRoles();
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        $admin = Admin::factory()->create(['account_tenant_id' => $tenant->id]);

        DB::table('rf_admins')->where('id', $admin->id)->update(['role' => 'bogusRole']);

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();

        $this->assertSame(0, $this->modelHasRolesCount());
    }

    // ── Dry-run ───────────────────────────────────────────────────────────────

    public function test_dry_run_does_not_write_any_rows(): void
    {
        $this->seedRoles();
        $this->createTenantWithAdmins();

        $this->artisan('rbac:migrate-admin-roles', ['--dry-run' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('Dry-run mode');

        $this->assertSame(0, $this->modelHasRolesCount());
    }

    // ── Missing roles (RbacSeeder not run) ───────────────────────────────────

    public function test_command_fails_when_roles_are_not_seeded(): void
    {
        // No seeder run — roles table is empty.
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
            'role' => AdminRole::Admins,
        ]);

        $this->artisan('rbac:migrate-admin-roles')
            ->assertFailed();
    }

    // ── Inserted rows reference system roles (account_tenant_id IS NULL) ─────

    public function test_inserted_rows_point_to_system_roles_with_null_tenant(): void
    {
        $this->seedRoles();
        $tenant = $this->createTenantWithAdmins();

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();

        // Every role_id referenced in model_has_roles must belong to a system role.
        $roleIds = DB::table('model_has_roles')
            ->where('model_type', $this->morphType)
            ->pluck('role_id')
            ->unique()
            ->all();

        foreach ($roleIds as $roleId) {
            $this->assertDatabaseHas('roles', [
                'id' => $roleId,
                'account_tenant_id' => null,
            ]);
        }
    }

    // ── Already-assigned admin: skip, not upsert ──────────────────────────────

    public function test_already_assigned_admin_is_skipped_not_duplicated(): void
    {
        $this->seedRoles();
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        $admin = Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
            'role' => AdminRole::Admins,
        ]);

        // Pre-insert the assignment manually.
        $roleId = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->where('name', AdminRole::Admins->value)
            ->value('id');

        DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => $this->morphType,
            'model_id' => $admin->id,
        ]);

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();

        // Still only 1 row — no duplication.
        $this->assertSame(
            1,
            DB::table('model_has_roles')
                ->where('model_type', $this->morphType)
                ->where('model_id', $admin->id)
                ->where('role_id', $roleId)
                ->count(),
        );
    }

    // ── Summary output counts ─────────────────────────────────────────────────

    public function test_summary_output_contains_inserted_count(): void
    {
        $this->seedRoles();
        $this->createTenantWithAdmins(); // 5 admins

        $this->artisan('rbac:migrate-admin-roles')
            ->assertSuccessful()
            ->expectsOutputToContain('5 inserted');
    }

    public function test_summary_output_contains_skipped_existing_count(): void
    {
        $this->seedRoles();
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        $admin = Admin::factory()->create([
            'account_tenant_id' => $tenant->id,
            'role' => AdminRole::Admins,
        ]);

        $roleId = Role::withoutGlobalScopes()
            ->whereNull('account_tenant_id')
            ->where('name', AdminRole::Admins->value)
            ->value('id');

        DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => $this->morphType,
            'model_id' => $admin->id,
        ]);

        $this->artisan('rbac:migrate-admin-roles')
            ->assertSuccessful()
            ->expectsOutputToContain('1 skipped (already existed)');
    }

    public function test_summary_output_contains_invalid_skipped_count(): void
    {
        $this->seedRoles();
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        $admin = Admin::factory()->create(['account_tenant_id' => $tenant->id]);

        DB::table('rf_admins')->where('id', $admin->id)->update(['role' => 'bogusRole']);

        $this->artisan('rbac:migrate-admin-roles')
            ->assertSuccessful()
            ->expectsOutputToContain('1 skipped (null/invalid role)');
    }

    // ── Dry-run reports counts but writes nothing ─────────────────────────────

    public function test_dry_run_outputs_would_be_inserted_count(): void
    {
        $this->seedRoles();
        $this->createTenantWithAdmins(); // 5 admins

        $this->artisan('rbac:migrate-admin-roles', ['--dry-run' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('5 inserted')
            ->expectsOutputToContain('Dry-run mode');

        $this->assertSame(0, $this->modelHasRolesCount());
    }

    // ── Invalid role is logged with admin ID ─────────────────────────────────

    public function test_invalid_role_warning_log_contains_admin_id(): void
    {
        $this->seedRoles();
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        $admin = Admin::factory()->create(['account_tenant_id' => $tenant->id]);

        DB::table('rf_admins')->where('id', $admin->id)->update(['role' => 'unknownRole']);

        Log::spy();

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();

        Log::shouldHaveReceived('warning')
            ->withArgs(fn (string $msg) => str_contains($msg, (string) $admin->id));
    }

    // ── Large dataset: chunked processing integrity ───────────────────────────

    public function test_large_dataset_all_rows_inserted_across_chunks(): void
    {
        $this->seedRoles();

        // Create 210 admins (crosses the 200-row chunk boundary).
        $tenant = Tenant::create(['name' => 'Test Tenant '.uniqid()]);
        $roles = AdminRole::cases();
        $roleCount = count($roles);

        for ($i = 0; $i < 210; $i++) {
            Admin::factory()->create([
                'account_tenant_id' => $tenant->id,
                'role' => $roles[$i % $roleCount],
            ]);
        }

        $this->artisan('rbac:migrate-admin-roles')->assertSuccessful();

        $this->assertSame(210, $this->modelHasRolesCount());
    }
}
