<?php

namespace Tests\Feature\Http\Reports;

use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\Community;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OperationalReportTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;
    private Tenant $tenant;
    private Community $community;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Report Test']);
        $this->tenant->makeCurrent();
        AccountMembership::create([
            'user_id' => $this->user->id, 'account_tenant_id' => $this->tenant->id, 'role' => 'account_admins',
        ]);
        $this->ensureAccountAdminsRoleExists();
        $this->user->assignRole('accountAdmins');
        $this->actingAs($this->user);
        $this->withSession(['tenant_id' => $this->tenant->id]);
        $this->community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);
        Building::factory()->create(['rf_community_id' => $this->community->id, 'account_tenant_id' => $this->tenant->id]);
        Unit::factory()->count(3)->create(['rf_community_id' => $this->community->id, 'status' => 'available', 'account_tenant_id' => $this->tenant->id]);
        Unit::factory()->create(['rf_community_id' => $this->community->id, 'status' => 'occupied', 'account_tenant_id' => $this->tenant->id]);
    }

    protected function tearDown(): void { Tenant::forgetCurrent(); parent::tearDown(); }

    private function ensureAccountAdminsRoleExists(): void
    {
        if (! DB::table('roles')->where('name', 'accountAdmins')->where('guard_name', 'web')->exists()) {
            DB::table('roles')->insert(['name' => 'accountAdmins', 'guard_name' => 'web', 'name_en' => 'Account Admins', 'name_ar' => 'مدراء الحسابات', 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function test_occupancy_report(): void
    {
        $r = $this->getJson('/rf/reports/occupancy');
        $r->assertStatus(200);
        $this->assertSame(4, $r->json('data.total_units'));
        $this->assertSame(1, $r->json('data.occupied'));
        $this->assertEquals(25.0, (float) $r->json('data.occupancy_rate_pct'));
    }

    public function test_occupancy_by_community(): void
    {
        $r = $this->getJson('/rf/reports/occupancy?community_id=' . $this->community->id);
        $r->assertStatus(200);
        $this->assertGreaterThan(0, $r->json('data.total_units'));
    }

    public function test_portfolio_health(): void
    {
        $r = $this->getJson('/rf/reports/portfolio-health');
        $r->assertStatus(200);
        $this->assertNotEmpty($r->json('data'));
    }

    public function test_financial_summary(): void
    {
        Transaction::create(['account_tenant_id' => $this->tenant->id, 'direction' => 'money_in', 'amount' => 1000, 'is_paid' => 1, 'due_on' => now()]);
        $r = $this->getJson('/rf/reports/financial-summary');
        $r->assertStatus(200);
        $this->assertNotNull($r->json('data.revenue'));
    }

    public function test_financial_summary_date_range(): void
    {
        Transaction::create(['account_tenant_id' => $this->tenant->id, 'direction' => 'money_in', 'amount' => 500, 'is_paid' => 0, 'due_on' => now()]);
        $r = $this->getJson('/rf/reports/financial-summary?from=' . now()->subMonth()->format('Y-m-d') . '&to=' . now()->format('Y-m-d'));
        $r->assertStatus(200);
        $this->assertNotNull($r->json('data'));
    }

    public function test_lease_pipeline(): void
    {
        $r = $this->getJson('/rf/reports/lease-pipeline');
        $r->assertStatus(200);
        $this->assertNotNull($r->json('data'));
    }
}
