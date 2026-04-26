<?php

namespace Tests\Feature\VisitorAccess;

use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use App\Models\VisitorInvitation;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class VisitorInvitationControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Visitor Test Account']);

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($this->user);
    }

    // ── My Visitors (index) ──────────────────────────────────────────────────

    public function test_guests_are_redirected_from_my_visitors(): void
    {
        auth()->logout();

        $response = $this->get(route('visitor-access.invitations.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_my_visitors_page(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('visitor-access.invitations.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('visitor-access/MyVisitors'));
    }

    public function test_my_visitors_separates_active_and_past_invitations(): void
    {
        VisitorInvitation::factory()->active()->create([
            'resident_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        VisitorInvitation::factory()->used()->create([
            'resident_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('visitor-access.invitations.index'));

        $response->assertOk();
        $response->assertInertia(function ($page) {
            $page->component('visitor-access/MyVisitors')
                ->has('activeInvitations', 1)
                ->has('pastInvitations', 1);
        });
    }

    public function test_my_visitors_only_shows_current_users_invitations(): void
    {
        $otherUser = User::factory()->create();

        VisitorInvitation::factory()->active()->create([
            'resident_id' => $otherUser->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        VisitorInvitation::factory()->active()->create([
            'resident_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('visitor-access.invitations.index'));

        $response->assertInertia(function ($page) {
            $page->has('activeInvitations', 1);
        });
    }

    // ── Register form (create) ───────────────────────────────────────────────

    public function test_authenticated_user_can_view_register_form(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('visitor-access.invitations.create'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page->component('visitor-access/Register'));
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function test_resident_can_create_visitor_invitation(): void
    {
        $expectedAt = now()->addDay()->format('Y-m-d\TH:i');

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('visitor-access.invitations.store'), [
                'visitor_name' => 'Ahmed Mohammed',
                'visitor_purpose' => 'visit',
                'expected_at' => $expectedAt,
                'visitor_phone' => null,
            ]);

        $this->assertDatabaseHas('rf_visitor_invitations', [
            'resident_id' => $this->user->id,
            'visitor_name' => 'Ahmed Mohammed',
            'visitor_purpose' => 'visit',
            'status' => 'active',
        ]);

        $invitation = VisitorInvitation::query()
            ->where('resident_id', $this->user->id)
            ->where('visitor_name', 'Ahmed Mohammed')
            ->firstOrFail();

        $response->assertRedirect(route('visitor-access.invitations.show', $invitation));
    }

    public function test_invitation_has_qr_code_token_and_valid_until_set(): void
    {
        $expectedAt = now()->addDay()->format('Y-m-d\TH:i');

        $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('visitor-access.invitations.store'), [
                'visitor_name' => 'Fatima Ali',
                'visitor_purpose' => 'delivery',
                'expected_at' => $expectedAt,
            ]);

        $invitation = VisitorInvitation::query()
            ->where('visitor_name', 'Fatima Ali')
            ->firstOrFail();

        $this->assertNotEmpty($invitation->qr_code_token);
        $this->assertNotNull($invitation->valid_until);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('visitor-access.invitations.store'), []);

        $response->assertSessionHasErrors(['visitor_name', 'visitor_purpose', 'expected_at']);
    }

    public function test_store_rejects_past_expected_at(): void
    {
        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('visitor-access.invitations.store'), [
                'visitor_name' => 'Test Visitor',
                'visitor_purpose' => 'visit',
                'expected_at' => now()->subDay()->format('Y-m-d\TH:i'),
            ]);

        $response->assertSessionHasErrors(['expected_at']);
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function test_resident_can_view_their_own_invitation_qr(): void
    {
        $invitation = VisitorInvitation::factory()->active()->create([
            'resident_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('visitor-access.invitations.show', $invitation));

        $response->assertOk();
        $response->assertInertia(function ($page) use ($invitation) {
            $page->component('visitor-access/Show')
                ->where('invitation.id', $invitation->id)
                ->has('invitation.qr_svg');
        });
    }

    public function test_resident_cannot_view_another_users_invitation(): void
    {
        $otherUser = User::factory()->create();

        $invitation = VisitorInvitation::factory()->active()->create([
            'resident_id' => $otherUser->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('visitor-access.invitations.show', $invitation));

        $response->assertForbidden();
    }

    // ── Cancel ───────────────────────────────────────────────────────────────

    public function test_resident_can_cancel_their_active_invitation(): void
    {
        $invitation = VisitorInvitation::factory()->active()->create([
            'resident_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('visitor-access.invitations.cancel', $invitation));

        $response->assertRedirect(route('visitor-access.invitations.index'));

        $this->assertDatabaseHas('rf_visitor_invitations', [
            'id' => $invitation->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_resident_cannot_cancel_another_users_invitation(): void
    {
        $otherUser = User::factory()->create();

        $invitation = VisitorInvitation::factory()->active()->create([
            'resident_id' => $otherUser->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('visitor-access.invitations.cancel', $invitation));

        $response->assertForbidden();
    }

    public function test_resident_cannot_cancel_non_active_invitation(): void
    {
        $invitation = VisitorInvitation::factory()->used()->create([
            'resident_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'community_id' => null,
        ]);

        $response = $this
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('visitor-access.invitations.cancel', $invitation));

        $response->assertForbidden();
    }
}
