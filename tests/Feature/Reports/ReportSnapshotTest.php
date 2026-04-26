<?php

namespace Tests\Feature\Reports;

use App\Enums\ReportType;
use App\Models\AccountMembership;
use App\Models\ReportSnapshot;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ReportSnapshotTest extends TestCase
{
    use LazilyRefreshDatabase;

    // ── Happy path: data model ────────────────────────────────────────────────

    public function test_factory_creates_a_pending_snapshot(): void
    {
        $tenant = Tenant::create(['name' => 'Test Corp']);
        $user = User::factory()->create();

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $tenant->makeCurrent();

        $snapshot = ReportSnapshot::factory()->create([
            'requested_by_user_id' => $user->id,
        ]);

        $this->assertInstanceOf(ReportSnapshot::class, $snapshot);
        $this->assertSame('pending', $snapshot->status);
        $this->assertTrue($snapshot->isPending());
        $this->assertFalse($snapshot->isReady());
        $this->assertFalse($snapshot->hasFailed());
        $this->assertInstanceOf(ReportType::class, $snapshot->report_type);

        Tenant::forgetCurrent();
    }

    public function test_factory_ready_state_sets_payload_and_generated_at(): void
    {
        $tenant = Tenant::create(['name' => 'Ready Corp']);
        $tenant->makeCurrent();

        $snapshot = ReportSnapshot::factory()->ready()->create();

        $this->assertSame('ready', $snapshot->status);
        $this->assertTrue($snapshot->isReady());
        $this->assertNotNull($snapshot->generated_at);
        $this->assertNotNull($snapshot->payload);
        $this->assertArrayHasKey('total', $snapshot->payload);

        Tenant::forgetCurrent();
    }

    public function test_factory_failed_state_sets_error_message(): void
    {
        $tenant = Tenant::create(['name' => 'Failed Corp']);
        $tenant->makeCurrent();

        $snapshot = ReportSnapshot::factory()->failed()->create();

        $this->assertSame('failed', $snapshot->status);
        $this->assertTrue($snapshot->hasFailed());
        $this->assertNotNull($snapshot->error_message);
        $this->assertNull($snapshot->payload);

        Tenant::forgetCurrent();
    }

    public function test_snapshot_belongs_to_report_type_enum(): void
    {
        $tenant = Tenant::create(['name' => 'Enum Corp']);
        $tenant->makeCurrent();

        $snapshot = ReportSnapshot::factory()
            ->ofType(ReportType::FinancialSummary)
            ->create();

        $this->assertSame(ReportType::FinancialSummary, $snapshot->report_type);

        Tenant::forgetCurrent();
    }

    // ── Tenant isolation ──────────────────────────────────────────────────────

    public function test_tenant_a_cannot_read_tenant_b_snapshots(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A']);
        $tenantB = Tenant::create(['name' => 'Tenant B']);

        // Create snapshot under tenant B
        $tenantB->makeCurrent();
        $snapshotB = ReportSnapshot::factory()->ready()->create();
        Tenant::forgetCurrent();

        // Switch to tenant A — BelongsToAccountTenant global scope applies
        $tenantA->makeCurrent();
        $visible = ReportSnapshot::all();
        Tenant::forgetCurrent();

        $this->assertCount(0, $visible);
        $this->assertFalse($visible->contains('id', $snapshotB->id));
    }

    public function test_each_tenant_sees_only_its_own_snapshots(): void
    {
        $tenantA = Tenant::create(['name' => 'Isolation A']);
        $tenantB = Tenant::create(['name' => 'Isolation B']);

        $tenantA->makeCurrent();
        ReportSnapshot::factory()->count(2)->create();
        Tenant::forgetCurrent();

        $tenantB->makeCurrent();
        ReportSnapshot::factory()->count(3)->create();
        Tenant::forgetCurrent();

        $tenantA->makeCurrent();
        $tenantASnapshots = ReportSnapshot::all();
        Tenant::forgetCurrent();

        $tenantB->makeCurrent();
        $tenantBSnapshots = ReportSnapshot::all();
        Tenant::forgetCurrent();

        $this->assertCount(2, $tenantASnapshots);
        $this->assertCount(3, $tenantBSnapshots);
    }

    // ── RBAC gate ─────────────────────────────────────────────────────────────

    public function test_reports_view_gate_allows_user_with_permission(): void
    {
        (new RbacSeeder)->run();

        $tenant = Tenant::create(['name' => 'Gate Corp']);
        $user = User::factory()->create();

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $tenant->makeCurrent();
        $user->assignRole('accountAdmins');

        $this->actingAs($user);

        $this->assertTrue(Gate::allows('reports.VIEW'));

        Tenant::forgetCurrent();
    }

    public function test_reports_view_gate_denies_user_without_permission(): void
    {
        (new RbacSeeder)->run();

        $tenant = Tenant::create(['name' => 'No Access Corp']);
        $user = User::factory()->create();

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $tenant->makeCurrent();

        // User has no role assigned — no reports.VIEW permission
        $this->actingAs($user);

        $this->assertFalse(Gate::allows('reports.VIEW'));

        Tenant::forgetCurrent();
    }

    // ── ReportType enum ───────────────────────────────────────────────────────

    public function test_report_type_enum_has_all_six_cases(): void
    {
        $cases = ReportType::cases();

        $this->assertCount(6, $cases);

        $values = array_map(fn (ReportType $t) => $t->value, $cases);
        $this->assertContains('financial_summary', $values);
        $this->assertContains('occupancy', $values);
        $this->assertContains('lease_pipeline', $values);
        $this->assertContains('vat_return', $values);
        $this->assertContains('receivables_aging', $values);
        $this->assertContains('portfolio_health', $values);
    }

    public function test_snapshot_report_types_use_snapshot_pattern(): void
    {
        $this->assertTrue(ReportType::LeasePipeline->isSnapshot());
        $this->assertTrue(ReportType::PortfolioHealth->isSnapshot());
    }

    public function test_live_report_types_do_not_use_snapshot_pattern(): void
    {
        $this->assertFalse(ReportType::FinancialSummary->isSnapshot());
        $this->assertFalse(ReportType::Occupancy->isSnapshot());
        $this->assertFalse(ReportType::VatReturn->isSnapshot());
        $this->assertFalse(ReportType::ReceivablesAging->isSnapshot());
    }
}
