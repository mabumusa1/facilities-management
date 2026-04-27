<?php

namespace Tests\Feature\Settings;

use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class SessionManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Session Test Account']);

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);
    }

    private function createSessionRow(string $sessionId, string $userAgent = 'Mozilla/5.0', ?string $ipAddress = '127.0.0.1'): void
    {
        \DB::table('sessions')->insert([
            'id' => $sessionId,
            'user_id' => $this->user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'payload' => '{}',
            'last_activity' => time(),
        ]);
    }

    public function test_list_returns_active_sessions(): void
    {
        $sessionId = 'extra-session-123';
        $this->createSessionRow($sessionId);

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson('/settings/sessions');

        $response->assertOk();
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertGreaterThanOrEqual(1, count($data));

        $otherSessions = collect($data)->filter(fn ($s) => ! $s['is_current']);
        $this->assertNotEmpty($otherSessions);
    }

    public function test_only_current_session_when_no_others(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson('/settings/sessions');

        $response->assertOk();
        $data = $response->json();

        $others = collect($data)->filter(fn ($s) => ! $s['is_current']);
        $this->assertCount(0, $others);
    }

    public function test_revoke_single_session(): void
    {
        $this->createSessionRow('target123', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutMiddleware(RequirePassword::class)
            ->deleteJson('/settings/sessions/target123');

        $response->assertOk();

        $this->assertDatabaseMissing('sessions', ['id' => 'target123']);
    }

    public function test_cannot_revoke_current_session(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutMiddleware(RequirePassword::class)
            ->deleteJson('/settings/sessions/current-session');

        $response->assertStatus(404);
    }

    public function test_revoke_all_other_sessions(): void
    {
        $this->createSessionRow('sess1', 'ua1');
        $this->createSessionRow('sess2', 'ua2');

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutMiddleware(RequirePassword::class)
            ->postJson('/settings/sessions/revoke-all');

        $response->assertOk();

        $this->assertDatabaseMissing('sessions', ['id' => 'sess1']);
        $this->assertDatabaseMissing('sessions', ['id' => 'sess2']);
    }

    public function test_revoke_nonexistent_session_returns_404(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutMiddleware(RequirePassword::class)
            ->deleteJson('/settings/sessions/non-existent');

        $response->assertStatus(404);
    }

    public function test_cannot_access_another_users_session(): void
    {
        $otherUser = User::factory()->create();

        \DB::table('sessions')->insert([
            'id' => 'otheruser123',
            'user_id' => $otherUser->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'test',
            'payload' => '{}',
            'last_activity' => time(),
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutMiddleware(RequirePassword::class)
            ->deleteJson('/settings/sessions/otheruser123');

        $response->assertStatus(404);
    }

    public function test_unauthenticated_user_redirected(): void
    {
        $response = $this->getJson('/settings/sessions');

        $response->assertStatus(401);
    }

    public function test_list_includes_session_agent_info(): void
    {
        $this->createSessionRow('info-test', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', '192.168.1.1');

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson('/settings/sessions');

        $response->assertOk();
        $data = $response->json();

        $otherSession = collect($data)->firstWhere('is_current', false);
        $this->assertNotNull($otherSession);
        $this->assertArrayHasKey('agent', $otherSession);
        $this->assertArrayHasKey('browser', $otherSession['agent']);
        $this->assertArrayHasKey('platform', $otherSession['agent']);
        $this->assertArrayHasKey('device', $otherSession['agent']);
        $this->assertArrayHasKey('location', $otherSession);
        $this->assertArrayHasKey('last_activity', $otherSession);
        $this->assertArrayHasKey('last_activity_diff', $otherSession);
    }

    public function test_revoke_requires_password_confirmation(): void
    {
        $this->createSessionRow('needs-password');

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->deleteJson('/settings/sessions/needs-password');

        $response->assertStatus(423);
    }

    public function test_revoke_all_requires_password_confirmation(): void
    {
        $this->createSessionRow('sess-a');
        $this->createSessionRow('sess-b');

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->postJson('/settings/sessions/revoke-all');

        $response->assertStatus(423);
    }

    public function test_revoke_all_keeps_current_session_in_database(): void
    {
        $this->createSessionRow('keep-me');

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withoutMiddleware(RequirePassword::class)
            ->postJson('/settings/sessions/revoke-all');

        $response->assertOk();

        $this->assertDatabaseMissing('sessions', ['id' => 'keep-me']);
    }

    public function test_multiple_sessions_returned_ordered_by_last_activity(): void
    {
        $this->createSessionRow('old-session');
        \DB::table('sessions')->where('id', 'old-session')->update(['last_activity' => time() - 3600]);

        $this->createSessionRow('new-session');

        $response = $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->getJson('/settings/sessions');

        $response->assertOk();
        $data = $response->json();

        $nonCurrent = collect($data)->filter(fn ($s) => ! $s['is_current'])->values();

        $this->assertCount(2, $nonCurrent);

        $this->assertEquals('new-session', $nonCurrent[0]['id']);
    }
}
