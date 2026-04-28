<?php

namespace Tests\Feature\Admin;

use App\Enums\RolesEnum;
use App\Mail\UserInvitationMail;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function createTenantMember(string $role): array
    {
        $user = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        $tenant = Tenant::create(['name' => 'Managed Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => $role,
        ]);

        $user->assignRole($role);

        return [$user, $tenant];
    }

    public function test_admin_can_invite_user(): void
    {
        Mail::fake();

        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.store'), [
                'first_name' => 'Jana',
                'last_name' => 'Al-Ali',
                'email' => 'jana@example.com',
                'role' => RolesEnum::MANAGERS->value,
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'jana@example.com',
            'name' => 'Jana Al-Ali',
            'status' => User::STATUS_INVITATION_PENDING,
        ]);

        $invitedUser = User::query()->where('email', 'jana@example.com')->first();
        $this->assertNotNull($invitedUser);
        $this->assertNotNull($invitedUser->invitation_token);
        $this->assertNotNull($invitedUser->invitation_expires_at);
        $this->assertTrue($invitedUser->isInvitationPending());

        Mail::assertQueued(UserInvitationMail::class, function (UserInvitationMail $mail) use ($invitedUser) {
            return $mail->user->id === $invitedUser->id;
        });

        $this->assertDatabaseHas('account_memberships', [
            'user_id' => $invitedUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);
    }

    public function test_cannot_invite_duplicate_email(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.store'), [
                'first_name' => 'Duplicate',
                'last_name' => 'User',
                'email' => 'existing@example.com',
                'role' => RolesEnum::ADMINS->value,
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_invitation_validation_requires_fields(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.store'), []);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'email', 'role']);
    }

    public function test_invited_user_can_set_password_and_activate(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $plainToken = Str::random(40);
        $user = User::factory()->create([
            'email' => 'invited@example.com',
            'status' => User::STATUS_INVITATION_PENDING,
            'invitation_token' => hash('sha256', $plainToken),
            'invitation_expires_at' => now()->addHours(72),
        ]);

        $response = $this->post(route('set-password.store'), [
            'token' => $plainToken,
            'password' => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        $user->refresh();

        $this->assertEquals(User::STATUS_ACTIVE, $user->status);
        $this->assertNull($user->invitation_token);
        $this->assertNotNull($user->email_verified_at);
        $this->assertAuthenticatedAs($user);
    }

    public function test_expired_invitation_token_shows_error(): void
    {
        $plainToken = Str::random(40);
        User::factory()->create([
            'email' => 'expired@example.com',
            'status' => User::STATUS_INVITATION_PENDING,
            'invitation_token' => hash('sha256', $plainToken),
            'invitation_expires_at' => now()->subHour(),
        ]);

        $this->withoutVite();

        $response = $this->get(route('set-password.create', ['token' => $plainToken]));

        $response->assertOk();
    }

    public function test_cannot_set_password_with_invalid_token(): void
    {
        $response = $this->post(route('set-password.store'), [
            'token' => 'invalid-token-12345',
            'password' => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        $response->assertStatus(410);
    }

    public function test_admin_can_deactivate_user(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.deactivate', ['user' => $targetUser->id]));

        $response->assertRedirect();

        $targetUser->refresh();
        $this->assertEquals(User::STATUS_DEACTIVATED, $targetUser->status);
        $this->assertTrue($targetUser->isDeactivated());
    }

    public function test_admin_cannot_deactivate_self(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.deactivate', ['user' => $admin->id]));

        $response->assertForbidden();
    }

    public function test_admin_can_reactivate_user(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create(['status' => User::STATUS_DEACTIVATED]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.reactivate', ['user' => $targetUser->id]));

        $response->assertRedirect();

        $targetUser->refresh();
        $this->assertEquals(User::STATUS_ACTIVE, $targetUser->status);
        $this->assertTrue($targetUser->isActive());
    }

    public function test_cannot_reactivate_non_deactivated_user(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.reactivate', ['user' => $targetUser->id]));

        $response->assertStatus(400);
    }

    public function test_admin_can_trigger_password_reset(): void
    {
        Mail::fake();

        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.send-password-reset', ['user' => $targetUser->id]));

        $response->assertRedirect();

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $targetUser->email,
        ]);
    }

    public function test_non_admin_cannot_invite_user(): void
    {
        [$tenant, $tenantModel] = $this->createTenantMember(RolesEnum::TENANTS->value);

        $response = $this->actingAs($tenant)
            ->withSession(['tenant_id' => $tenantModel->id])
            ->post(route('admin.users.store'), [
                'first_name' => 'Jana',
                'last_name' => 'Al-Ali',
                'email' => 'jana@example.com',
                'role' => RolesEnum::MANAGERS->value,
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_resend_invitation(): void
    {
        Mail::fake();

        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create([
            'status' => User::STATUS_INVITATION_PENDING,
            'invitation_token' => hash('sha256', Str::random(40)),
            'invitation_expires_at' => now()->addHours(12),
        ]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $oldToken = $targetUser->invitation_token;

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.resend-invitation', ['user' => $targetUser->id]));

        $response->assertRedirect();

        $targetUser->refresh();
        $this->assertNotEquals($oldToken, $targetUser->invitation_token);
    }

    public function test_admin_can_revoke_invitation(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create([
            'status' => User::STATUS_INVITATION_PENDING,
            'invitation_token' => hash('sha256', Str::random(40)),
            'invitation_expires_at' => now()->addHours(72),
        ]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.revoke-invitation', ['user' => $targetUser->id]));

        $response->assertRedirect();

        $targetUser->refresh();
        $this->assertNull($targetUser->invitation_token);
        $this->assertNull($targetUser->invitation_expires_at);
    }

    public function test_admin_can_view_users_index_page(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $this->withoutVite();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.index'));

        $response->assertOk();
    }

    public function test_deactivated_user_cannot_login(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create([
            'status' => User::STATUS_ACTIVE,
            'email_verified_at' => now(),
        ]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.deactivate', ['user' => $targetUser->id]));

        $targetUser->refresh();
        $this->assertEquals(User::STATUS_DEACTIVATED, $targetUser->status);

        auth()->logout();

        $response = $this->post(route('login'), [
            'email' => $targetUser->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_cannot_reactivate_already_active_user(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.reactivate', ['user' => $targetUser->id]));

        $response->assertStatus(400);
    }

    public function test_cannot_deactivate_already_deactivated_user(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create(['status' => User::STATUS_DEACTIVATED]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.deactivate', ['user' => $targetUser->id]));

        $response->assertStatus(400);
    }

    public function test_cannot_invite_with_invalid_email_format(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.store'), [
                'first_name' => 'Bad',
                'last_name' => 'Email',
                'email' => 'not-an-email',
                'role' => RolesEnum::MANAGERS->value,
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_cannot_set_password_with_mismatched_confirmation(): void
    {
        $plainToken = Str::random(40);
        User::factory()->create([
            'email' => 'invited@example.com',
            'status' => User::STATUS_INVITATION_PENDING,
            'invitation_token' => hash('sha256', $plainToken),
            'invitation_expires_at' => now()->addHours(72),
        ]);

        $response = $this->post(route('set-password.store'), [
            'token' => $plainToken,
            'password' => 'SecurePass1!',
            'password_confirmation' => 'WrongPass1!',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_cannot_set_password_without_password(): void
    {
        $plainToken = Str::random(40);
        User::factory()->create([
            'email' => 'invited@example.com',
            'status' => User::STATUS_INVITATION_PENDING,
            'invitation_token' => hash('sha256', $plainToken),
            'invitation_expires_at' => now()->addHours(72),
        ]);

        $response = $this->post(route('set-password.store'), [
            'token' => $plainToken,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_non_admin_cannot_access_admin_user_index_page(): void
    {
        [$manager, $tenant] = $this->createTenantMember(RolesEnum::MANAGERS->value);

        $this->withoutVite();

        $response = $this->actingAs($manager)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_cannot_revoke_already_used_invitation(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create([
            'status' => User::STATUS_ACTIVE,
            'invitation_token' => null,
            'invitation_expires_at' => null,
        ]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.revoke-invitation', ['user' => $targetUser->id]));

        $response->assertStatus(400);
    }

    public function test_cannot_set_password_with_expired_token(): void
    {
        $plainToken = Str::random(40);
        User::factory()->create([
            'email' => 'expired@example.com',
            'status' => User::STATUS_INVITATION_PENDING,
            'invitation_token' => hash('sha256', $plainToken),
            'invitation_expires_at' => now()->subHour(),
        ]);

        $response = $this->post(route('set-password.store'), [
            'token' => $plainToken,
            'password' => 'SecurePass1!',
            'password_confirmation' => 'SecurePass1!',
        ]);

        $response->assertStatus(410);
    }

    public function test_cannot_revoke_invitation_for_non_pending_user(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $targetUser = User::factory()->create([
            'status' => User::STATUS_DEACTIVATED,
            'invitation_token' => null,
            'invitation_expires_at' => null,
        ]);
        AccountMembership::create([
            'user_id' => $targetUser->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.revoke-invitation', ['user' => $targetUser->id]));

        $response->assertStatus(400);
    }

    public function test_admin_cannot_deactivate_user_from_another_tenant(): void
    {
        // Tenant A: admin + their own target user
        [$adminA, $tenantA] = $this->createTenantMember(RolesEnum::ADMINS->value);

        // Tenant B: a completely separate tenant with their own user
        $tenantB = Tenant::create(['name' => 'Other Account']);
        $userInTenantB = User::factory()->create(['status' => User::STATUS_ACTIVE]);
        AccountMembership::create([
            'user_id' => $userInTenantB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);

        // Admin A tries to deactivate a user who belongs to Tenant B
        $response = $this->actingAs($adminA)
            ->withSession(['tenant_id' => $tenantA->id])
            ->post(route('admin.users.deactivate', ['user' => $userInTenantB->id]));

        $response->assertForbidden();

        $userInTenantB->refresh();
        $this->assertEquals(User::STATUS_ACTIVE, $userInTenantB->status);
    }
}
