<?php

namespace Tests\Feature\Auth;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Inertia\Testing\AssertableInertia;
use Laravel\Fortify\Features;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function createUserWithTenant(array $overrides = []): User
    {
        $user = User::factory()->create($overrides);

        $tenant = Tenant::create([
            'name' => $user->name."'s Account",
        ]);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ACCOUNT_ADMINS->value,
        ]);

        return $user;
    }

    /**
     * Create a user with a tenant and assign a specific Spatie role.
     * Requires RolesSeeder to have been run first.
     */
    protected function createUserWithTenantAndRole(RolesEnum $role, array $overrides = []): User
    {
        $user = $this->createUserWithTenant($overrides);
        $user->assignRole($role->value);

        return $user;
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = $this->createUserWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_login_sets_tenant_in_session(): void
    {
        $user = $this->createUserWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $tenant = $user->tenants()->first();
        $response->assertSessionHas('tenant_id', $tenant->id);
    }

    public function test_users_with_two_factor_enabled_are_redirected_to_two_factor_challenge(): void
    {
        $this->skipUnlessFortifyHas(Features::twoFactorAuthentication());

        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
        ]);

        $user = $this->createUserWithTenant();

        $user->forceFill([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.login'));
        $response->assertSessionHas('login.id', $user->id);
        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = $this->createUserWithTenant();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = $this->createUserWithTenant();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_users_are_rate_limited(): void
    {
        $user = $this->createUserWithTenant();

        RateLimiter::increment(md5('login'.implode('|', [$user->email, '127.0.0.1'])), amount: 5);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertTooManyRequests();
    }

    public function test_user_without_tenant_is_redirected_to_login(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    // ─── Role-based post-login redirect tests (Gap 1 — #235) ─────────────────

    /**
     * @test Happy path: admin role redirects to dashboard.
     */
    public function test_admin_user_is_redirected_to_dashboard_after_login(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::ADMINS);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /**
     * @test Happy path: account admin role redirects to dashboard.
     */
    public function test_account_admin_user_is_redirected_to_dashboard_after_login(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::ACCOUNT_ADMINS);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /**
     * @test Happy path: manager role redirects to dashboard.
     */
    public function test_manager_user_is_redirected_to_dashboard_after_login(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::MANAGERS);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /**
     * @test Owner role falls back to /dashboard until owner portal route lands (#225).
     */
    public function test_owner_user_is_redirected_to_dashboard_fallback_after_login(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::OWNERS);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        // TODO: assert redirect to route('owner.index') once the owner-portal story lands
        $response->assertRedirect(config('fortify.home'));
    }

    /**
     * @test Resident (tenant) role falls back to /dashboard until resident portal route lands.
     */
    public function test_tenant_user_is_redirected_to_dashboard_fallback_after_login(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::TENANTS);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        // TODO: assert redirect to route('resident.index') once the resident-portal story lands
        $response->assertRedirect(config('fortify.home'));
    }

    /**
     * @test Multi-role user: admin takes priority over tenant role.
     */
    public function test_user_with_admin_and_tenant_roles_is_redirected_to_dashboard(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenant();
        // Assign both roles — admin should win per priority order in LoginResponse
        $user->assignRole(RolesEnum::ADMINS->value);
        $user->assignRole(RolesEnum::TENANTS->value);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /**
     * @test No-role user falls back to fortify.home (/dashboard).
     */
    public function test_user_with_no_role_is_redirected_to_fortify_home(): void
    {
        $user = $this->createUserWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(config('fortify.home'));
    }

    // ─── QA: Failure paths and edge cases (story #235 ACs) ───────────────────

    /**
     * AC3 — Failure path: wrong password returns session error on the 'email' key.
     * Fortify returns a redirect (not JSON 422) with flashed session errors for
     * non-JSON login requests. The error must appear on 'email', not 'password',
     * so no account-specific information (which field is wrong) is revealed.
     */
    public function test_invalid_credentials_return_session_error_on_email_key(): void
    {
        $user = $this->createUserWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'definitely-wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * AC3 — Failure path: unknown email returns the same 'email' session error as
     * a wrong password. Fortify must not distinguish the two cases so no account
     * existence information is leaked to the caller.
     */
    public function test_unknown_email_returns_same_email_error_as_wrong_password(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => 'nobody@nowhere.example',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * AC3 — Failure path: submitting without an email field returns a validation
     * error on 'email' before the credential check is even attempted.
     */
    public function test_missing_email_field_returns_validation_error(): void
    {
        $response = $this->post(route('login.store'), [
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * AC3 — Failure path: submitting without a password field returns a validation
     * error on 'password'.
     */
    public function test_missing_password_field_returns_validation_error(): void
    {
        $user = $this->createUserWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * AC4 — Failure path: after 5 failed attempts the 6th returns HTTP 429.
     * When Fortify uses a named rate limiter ('login' key in config/fortify.php),
     * the throttle is enforced by Laravel's ThrottleRequests middleware, which
     * returns a bare 429 with no session errors. The frontend must detect this via
     * the 429 HTTP status (not session errors) to display the throttle amber banner.
     *
     * Note: The TL design (Gap 7 / R2) proposed detecting 'seconds' in errors.email,
     * but that only applies when Fortify's EnsureLoginIsNotThrottled pipeline action
     * fires (i.e. when config('fortify.limiters.login') is null). With a named
     * limiter the ThrottleRequests middleware intercepts before any Fortify logic.
     */
    public function test_throttled_login_returns_429_after_five_failed_attempts(): void
    {
        $user = $this->createUserWithTenant();

        // Exhaust the 5-attempt-per-minute limit with wrong credentials.
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt must be throttled.
        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertTooManyRequests();
        $this->assertGuest();
    }

    /**
     * AC5 — Edge case: submitting with remember=1 writes a persistent remember_token
     * to the users table so the session survives a browser restart.
     */
    public function test_remember_me_sets_a_persistent_remember_token(): void
    {
        $user = $this->createUserWithTenant();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $this->assertAuthenticated();
        $this->assertNotNull($user->fresh()->remember_token);
    }

    /**
     * AC5 — Edge case (counterpart): without remember=1 no persistent token is
     * written, confirming opt-in behaviour.
     */
    public function test_login_without_remember_me_does_not_set_remember_token(): void
    {
        $user = $this->createUserWithTenant();
        $user->forceFill(['remember_token' => null])->save();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertNull($user->fresh()->remember_token);
    }

    /**
     * AC6 — Login page Inertia component renders with required branding props.
     * companyLogoUrl is a Vue layout prop (not an Inertia page prop) and defaults
     * to undefined/null in AuthSimpleLayout.vue until story #225 ships. This test
     * confirms the Inertia login view resolves the correct component and exposes the
     * canResetPassword / canRegister props that control optional UI elements.
     */
    public function test_login_page_inertia_component_renders_with_required_props(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('auth/Login')
                ->has('canResetPassword')
                ->has('canRegister')
        );
    }

    /**
     * AC2 — Tenant boundary: a resident (TENANTS role) who is authenticated cannot
     * access an admin-only route. The admin.manage middleware returns 403.
     * Verifies the RBAC fence is in place independently of the redirect logic.
     */
    public function test_resident_cannot_access_admin_only_routes(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::TENANTS);
        $tenant = $user->tenants()->first();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    /**
     * AC2 — Tenant boundary: a property owner cannot access admin-only routes.
     */
    public function test_owner_cannot_access_admin_only_routes(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::OWNERS);
        $tenant = $user->tenants()->first();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    /**
     * AC2 — Tenant boundary: a property manager cannot access admin-only routes.
     */
    public function test_manager_cannot_access_admin_only_routes(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::MANAGERS);
        $tenant = $user->tenants()->first();

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    /**
     * Edge case (known gap): the story requires that unverified users cannot access
     * protected routes. The 'verified' middleware is wired to the dashboard route
     * group, but the User model does NOT implement MustVerifyEmail (it is commented
     * out in app/Models/User.php). Laravel's EnsureEmailIsVerified middleware only
     * redirects when the user implements MustVerifyEmail — without the interface,
     * the middleware is a no-op and unverified users reach the dashboard.
     *
     * This test documents the current (broken) behaviour. Story #237 is expected to
     * fix this by reinstating the MustVerifyEmail interface on User. Once that lands,
     * this test assertion should change to assertRedirect(route('verification.notice')).
     *
     * @see https://github.com/mabumusa1/facilities-management/issues/237
     */
    public function test_unverified_user_can_access_dashboard_because_must_verify_email_is_not_implemented(): void
    {
        $user = User::factory()->unverified()->create();

        $tenant = Tenant::create(['name' => $user->name."'s Account"]);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ACCOUNT_ADMINS->value,
        ]);

        // Login itself succeeds.
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticated();

        // BUG: unverified user reaches the dashboard because MustVerifyEmail
        // interface is not implemented — 'verified' middleware is bypassed.
        // Expected (per story AC): assertRedirect(route('verification.notice'))
        $protectedResponse = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('dashboard'));

        $protectedResponse->assertOk();
    }

    /**
     * Edge case: unauthenticated GET to a protected route redirects to /login.
     * Confirms the auth middleware is in place independently of the login flow.
     */
    public function test_unauthenticated_request_to_protected_route_redirects_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
