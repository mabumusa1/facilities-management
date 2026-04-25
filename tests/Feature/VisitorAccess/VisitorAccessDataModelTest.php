<?php

namespace Tests\Feature\VisitorAccess;

use App\Enums\AdminRole;
use App\Models\Community;
use App\Models\User;
use App\Models\VisitorAccessSetting;
use App\Models\VisitorInvitation;
use App\Models\VisitorLog;
use Database\Seeders\VisitorAccessSettingsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class VisitorAccessDataModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Happy path: VisitorInvitation is created with auto-generated QR token,
     * correct defaults, and relations resolve correctly.
     */
    public function test_visitor_invitation_can_be_created_with_defaults(): void
    {
        $community = Community::factory()->create();
        $resident = User::factory()->create();

        $invitation = VisitorInvitation::factory()->create([
            'community_id' => $community->id,
            'resident_id' => $resident->id,
            'visitor_name' => 'John Doe',
            'visitor_purpose' => 'delivery',
        ]);

        $this->assertModelExists($invitation);
        $this->assertSame('pending', $invitation->status);
        $this->assertSame('none', $invitation->qr_code_sent_via);
        $this->assertNotEmpty($invitation->qr_code_token);
        $this->assertSame($community->id, $invitation->community_id);
        $this->assertSame($resident->id, $invitation->resident_id);
    }

    /**
     * Happy path: QR token is auto-generated on creation and is unique per record.
     */
    public function test_visitor_invitation_auto_generates_unique_qr_token(): void
    {
        $invitationA = VisitorInvitation::factory()->create();
        $invitationB = VisitorInvitation::factory()->create();

        $this->assertNotEmpty($invitationA->qr_code_token);
        $this->assertNotEmpty($invitationB->qr_code_token);
        $this->assertNotSame($invitationA->qr_code_token, $invitationB->qr_code_token);
    }

    /**
     * Happy path: VisitorInvitation factory states (active, used, expired, cancelled) work correctly.
     */
    public function test_visitor_invitation_factory_states(): void
    {
        $this->assertSame('active', VisitorInvitation::factory()->active()->create()->status);
        $this->assertSame('used', VisitorInvitation::factory()->used()->create()->status);
        $this->assertSame('expired', VisitorInvitation::factory()->expired()->create()->status);
        $this->assertSame('cancelled', VisitorInvitation::factory()->cancelled()->create()->status);
    }

    /**
     * Happy path: VisitorLog (walk-in) can be created without an invitation.
     */
    public function test_visitor_log_walk_in_can_be_created_without_invitation(): void
    {
        $community = Community::factory()->create();
        $officer = User::factory()->create();

        $log = VisitorLog::factory()->create([
            'community_id' => $community->id,
            'gate_officer_id' => $officer->id,
            'invitation_id' => null,
            'visitor_name' => 'Jane Walk-in',
        ]);

        $this->assertModelExists($log);
        $this->assertNull($log->invitation_id);
        $this->assertFalse($log->id_verified);
        $this->assertNull($log->exit_at);
    }

    /**
     * Happy path: VisitorLog linked to an invitation resolves the relation.
     */
    public function test_visitor_log_can_be_linked_to_an_invitation(): void
    {
        $invitation = VisitorInvitation::factory()->active()->create();

        $log = VisitorLog::factory()->withInvitation()->create([
            'invitation_id' => $invitation->id,
            'community_id' => $invitation->community_id,
        ]);

        $this->assertModelExists($log);
        $this->assertSame($invitation->id, $log->invitation_id);
        $this->assertTrue($invitation->is($log->invitation));
    }

    /**
     * Happy path: VisitorLog factory states (withExit, idVerified) work correctly.
     */
    public function test_visitor_log_factory_states(): void
    {
        $logWithExit = VisitorLog::factory()->withExit()->create();
        $this->assertNotNull($logWithExit->exit_at);

        $logIdVerified = VisitorLog::factory()->idVerified()->create();
        $this->assertTrue($logIdVerified->id_verified);
    }

    /**
     * Happy path: VisitorAccessSetting is created with correct defaults.
     */
    public function test_visitor_access_setting_created_with_defaults(): void
    {
        $community = Community::factory()->create();

        $settings = VisitorAccessSetting::factory()->create([
            'community_id' => $community->id,
        ]);

        $this->assertModelExists($settings);
        $this->assertFalse($settings->require_id_verification);
        $this->assertTrue($settings->allow_walk_in);
        $this->assertSame(1440, $settings->qr_expiry_minutes);
        $this->assertSame(1, $settings->max_uses_per_invitation);
    }

    /**
     * Happy path: VisitorAccessSetting is unique per community (DB constraint).
     */
    public function test_visitor_access_setting_is_unique_per_community(): void
    {
        $community = Community::factory()->create();

        VisitorAccessSetting::factory()->create(['community_id' => $community->id]);

        $this->expectException(QueryException::class);

        VisitorAccessSetting::factory()->create(['community_id' => $community->id]);
    }

    /**
     * Happy path: VisitorAccessSetting factory states work correctly.
     */
    public function test_visitor_access_setting_factory_states(): void
    {
        $requiresId = VisitorAccessSetting::factory()->requiresIdVerification()->create();
        $this->assertTrue($requiresId->require_id_verification);

        $noWalkIn = VisitorAccessSetting::factory()->noWalkIn()->create();
        $this->assertFalse($noWalkIn->allow_walk_in);
    }

    /**
     * Happy path: Seeder creates one settings row per community, idempotent.
     */
    public function test_visitor_access_settings_seeder_creates_one_row_per_community(): void
    {
        $communityA = Community::factory()->create();
        $communityB = Community::factory()->create();

        $this->seed(VisitorAccessSettingsSeeder::class);

        $this->assertDatabaseHas('rf_visitor_access_settings', ['community_id' => $communityA->id]);
        $this->assertDatabaseHas('rf_visitor_access_settings', ['community_id' => $communityB->id]);

        // Running again should not throw (upsert idempotent)
        $this->seed(VisitorAccessSettingsSeeder::class);

        $this->assertSame(
            2,
            VisitorAccessSetting::withoutGlobalScopes()->whereIn('community_id', [$communityA->id, $communityB->id])->count()
        );
    }

    /**
     * Happy path: AdminRole enum includes GateOfficers case.
     */
    public function test_admin_role_enum_has_gate_officers_case(): void
    {
        $this->assertSame('gateOfficers', AdminRole::GateOfficers->value);

        $fromValue = AdminRole::from('gateOfficers');
        $this->assertSame(AdminRole::GateOfficers, $fromValue);
    }

    /**
     * Happy path: VisitorInvitation has-many VisitorLogs relation works.
     */
    public function test_visitor_invitation_has_many_logs_relation(): void
    {
        $invitation = VisitorInvitation::factory()->active()->create();

        VisitorLog::factory()->count(2)->create([
            'invitation_id' => $invitation->id,
            'community_id' => $invitation->community_id,
        ]);

        $this->assertCount(2, $invitation->logs);
    }
}
