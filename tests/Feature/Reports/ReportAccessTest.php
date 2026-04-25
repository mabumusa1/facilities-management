<?php

namespace Tests\Feature\Reports;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * HTTP-layer 403 tests for the reports module.
 *
 * Verifies that Gate::authorize('reports.VIEW') in ReportsController
 * is resolved by Spatie's Gate::before hook — no Gate::define() needed.
 */
class ReportAccessTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
        $this->tenant = Tenant::create(['name' => 'Access Test Account']);

        $this->withoutVite();
    }

    /**
     * Create a user with an AccountMembership but no role (no reports.VIEW).
     */
    private function userWithoutReportsPermission(): User
    {
        $user = User::factory()->create();

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'tenants',
        ]);

        return $user;
    }

    /**
     * Create a user with the accountAdmins role (has reports.VIEW via Spatie).
     */
    private function userWithReportsPermission(): User
    {
        $user = User::factory()->create();

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        return $user;
    }

    private function tenantGet(User $user, string $url): TestResponse
    {
        return $this->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get($url);
    }

    private function tenantPost(User $user, string $url, mixed $data = []): TestResponse
    {
        return $this->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson($url, $data);
    }

    // ── 403 for users without reports.VIEW ───────────────────────────────────

    public function test_user_without_reports_permission_is_forbidden_on_reports_page(): void
    {
        $user = $this->userWithoutReportsPermission();

        $this->tenantGet($user, route('dashboard.reports'))
            ->assertForbidden();
    }

    public function test_user_without_reports_permission_is_forbidden_on_system_reports_page(): void
    {
        $user = $this->userWithoutReportsPermission();

        $this->tenantGet($user, route('dashboard.system-reports'))
            ->assertForbidden();
    }

    public function test_user_without_reports_permission_is_forbidden_on_report_load_endpoint(): void
    {
        $user = $this->userWithoutReportsPermission();

        $this->tenantPost($user, route('report.load'), ['reportId' => 'collections'])
            ->assertForbidden();
    }

    public function test_user_without_reports_permission_is_forbidden_on_report_settings_endpoint(): void
    {
        $user = $this->userWithoutReportsPermission();

        $this->tenantGet($user, route('report.settings'))
            ->assertForbidden();
    }

    public function test_user_without_reports_permission_is_forbidden_on_reports_expenses_endpoint(): void
    {
        $user = $this->userWithoutReportsPermission();

        $this->tenantGet($user, route('reports.expenses'))
            ->assertForbidden();
    }

    // ── 200 for users with reports.VIEW (Spatie resolves via Gate::before) ────

    public function test_account_admin_can_access_reports_page(): void
    {
        $user = $this->userWithReportsPermission();

        $this->tenantGet($user, route('dashboard.reports'))
            ->assertOk();
    }

    public function test_account_admin_can_access_report_settings_endpoint(): void
    {
        $user = $this->userWithReportsPermission();

        $this->tenantGet($user, route('report.settings'))
            ->assertOk();
    }
}
