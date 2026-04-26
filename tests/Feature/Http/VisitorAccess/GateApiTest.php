<?php

namespace Tests\Feature\Http\VisitorAccess;

use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\Tenant;
use App\Models\User;
use App\Models\VisitorAccessSetting;
use App\Models\VisitorInvitation;
use App\Models\VisitorLog;
use Carbon\Carbon;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class GateApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private Community $community;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Gate Test']);
        $this->tenant->makeCurrent();

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->ensureAccountAdminsRoleExists();
        $this->user->assignRole('accountAdmins');

        $this->actingAs($this->user);
        $this->withSession(['tenant_id' => $this->tenant->id]);

        $this->community = Community::factory()->create(['account_tenant_id' => $this->tenant->id]);

        VisitorAccessSetting::create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'allow_walk_in' => true,
            'max_uses_per_invitation' => 2,
        ]);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    private function ensureAccountAdminsRoleExists(): void
    {
        if (! DB::table('roles')->where('name', 'accountAdmins')->where('guard_name', 'web')->exists()) {
            DB::table('roles')->insert([
                'name' => 'accountAdmins', 'guard_name' => 'web',
                'name_en' => 'Account Admins', 'name_ar' => 'مدراء الحسابات',
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function createInvitation(array $overrides = []): VisitorInvitation
    {
        return VisitorInvitation::create(array_merge([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'resident_id' => $this->user->id,
            'visitor_name' => 'John Doe',
            'visitor_phone' => '0501234567',
            'visitor_purpose' => 'visit',
            'expected_at' => now(),
            'valid_until' => now()->addDay(),
            'status' => 'pending',
        ], $overrides));
    }

    // -------------------------------------------------------------------------
    // Happy paths — check-in
    // -------------------------------------------------------------------------

    public function test_check_in_with_valid_qr_creates_log_entry(): void
    {
        $invitation = $this->createInvitation();

        $response = $this->postJson('/rf/gate/check-in', [
            'qr_code_token' => $invitation->qr_code_token,
            'id_verified' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.type', 'check_in');
        $response->assertJsonPath('data.visitor_name', 'John Doe');

        $this->assertDatabaseHas('rf_visitor_logs', [
            'invitation_id' => $invitation->id,
            'visitor_name' => 'John Doe',
        ]);
        $this->assertSame('used', $invitation->fresh()->status);
    }

    public function test_check_out_mark_departed(): void
    {
        $invitation = $this->createInvitation();
        $log = VisitorLog::create([
            'account_tenant_id' => $this->tenant->id,
            'invitation_id' => $invitation->id,
            'community_id' => $this->community->id,
            'visitor_name' => 'Jane Smith',
            'entry_at' => now()->subHour(),
            'gate_officer_id' => $this->user->id,
        ]);

        $response = $this->postJson('/rf/gate/check-out', ['log_id' => $log->id]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.log_id', $log->id);
        $this->assertNotNull($log->fresh()->exit_at);
    }

    public function test_walk_in_creates_log_without_invitation(): void
    {
        $response = $this->postJson('/rf/gate/check-in', [
            'qr_code_token' => 'nonexistent-token',
            'visitor_name' => 'Walk In Visitor',
            'visitor_phone' => '0509999999',
            'community_id' => $this->community->id,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.type', 'walk_in');

        $this->assertDatabaseHas('rf_visitor_logs', [
            'visitor_name' => 'Walk In Visitor',
            'purpose' => 'walk_in',
        ]);
    }

    // -------------------------------------------------------------------------
    // Failure paths
    // -------------------------------------------------------------------------

    public function test_check_in_with_expired_invitation_fails(): void
    {
        $invitation = $this->createInvitation([
            'valid_until' => now()->subDay(),
            'status' => 'pending',
        ]);

        $response = $this->postJson('/rf/gate/check-in', [
            'qr_code_token' => $invitation->qr_code_token,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['qr_code_token']);
    }

    public function test_check_in_with_cancelled_invitation_fails(): void
    {
        $invitation = $this->createInvitation(['status' => 'cancelled']);

        $response = $this->postJson('/rf/gate/check-in', [
            'qr_code_token' => $invitation->qr_code_token,
        ]);

        $response->assertStatus(422);
    }

    public function test_double_check_in_is_prevented(): void
    {
        $invitation = $this->createInvitation();

        // First check-in
        $this->postJson('/rf/gate/check-in', ['qr_code_token' => $invitation->qr_code_token]);

        // Second check-in (same visitor hasn't checked out)
        $response = $this->postJson('/rf/gate/check-in', ['qr_code_token' => $invitation->qr_code_token]);

        $response->assertStatus(422);
    }

    public function test_check_out_already_departed_fails(): void
    {
        $log = VisitorLog::create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'visitor_name' => 'Test',
            'entry_at' => now()->subHour(),
            'exit_at' => now()->subMinutes(30),
            'gate_officer_id' => $this->user->id,
        ]);

        $response = $this->postJson('/rf/gate/check-out', ['log_id' => $log->id]);

        $response->assertStatus(422);
    }

    public function test_walk_in_without_name_fails(): void
    {
        $response = $this->postJson('/rf/gate/check-in', [
            'qr_code_token' => 'nonexistent',
            'community_id' => $this->community->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['visitor_name']);
    }

    // -------------------------------------------------------------------------
    // Gate view + reporting
    // -------------------------------------------------------------------------

    public function test_gate_view_returns_todays_visitors(): void
    {
        $this->createInvitation(['visitor_name' => 'Today Visitor', 'expected_at' => now()]);

        $response = $this->getJson('/rf/gate/today');

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_log_report_with_date_range(): void
    {
        VisitorLog::create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'visitor_name' => 'Logged Visitor',
            'entry_at' => now()->subDays(2),
            'gate_officer_id' => $this->user->id,
        ]);

        $response = $this->getJson('/rf/gate/log-report?from=' . now()->subDays(3)->format('Y-m-d') . '&to=' . now()->format('Y-m-d'));

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_overstay_report_detects_long_stays(): void
    {
        VisitorLog::create([
            'account_tenant_id' => $this->tenant->id,
            'community_id' => $this->community->id,
            'visitor_name' => 'Still Here',
            'entry_at' => now()->subHours(25),
            'gate_officer_id' => $this->user->id,
        ]);

        $response = $this->getJson('/rf/gate/overstay');

        $response->assertStatus(200);
        $this->assertSame(1, $response->json('meta.total_overstays'));
    }

    public function test_max_scan_enforcement(): void
    {
        $invitation = $this->createInvitation();

        // First check-in
        $this->postJson('/rf/gate/check-in', ['qr_code_token' => $invitation->qr_code_token]);

        // Check out
        $logId = VisitorLog::where('invitation_id', $invitation->id)->value('id');
        $this->postJson('/rf/gate/check-out', ['log_id' => $logId]);

        // Second check-in (max is 2, this should pass)
        $response = $this->postJson('/rf/gate/check-in', ['qr_code_token' => $invitation->qr_code_token]);
        $response->assertStatus(200);

        // Third scan should fail (max 2 reached)
        // Need to check out first for the double check-in guard
        $logId2 = VisitorLog::where('invitation_id', $invitation->id)->latest('id')->value('id');
        $this->postJson('/rf/gate/check-out', ['log_id' => $logId2]);

        $response3 = $this->postJson('/rf/gate/check-in', ['qr_code_token' => $invitation->qr_code_token]);
        $response3->assertStatus(422);
    }
}
