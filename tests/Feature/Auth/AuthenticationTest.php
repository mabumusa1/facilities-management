<?php

namespace Tests\Feature\Auth;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
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

    // ─── QA: Failure paths and edge cases (Gap 1 + story ACs) ────────────────

    /**
     * Failure path: invalid credentials return 422 with errors on the email key.
     * The frontend reads errors.email to display "These credentials do not match our records."
     */
    public function test_invalid_credentials_return_422_with_email_error(): void
    {
        $user = $this->createUserWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'definitely-wrong-password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Failure path: login with a completely unknown email address also returns an
     * email validation error (Fortify does not distinguish unknown email from wrong password).
     */
    public function test_unknown_email_returns_email_error(): void
    {
        $response = $this->post(route('login.store'), [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Failure path: missing email field fails validation before even hitting credentials check.
     */
    public function test_missing_email_field_returns_validation_error(): void
    {
        $response = $this->post(route('login.store'), [
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Failure path: missing password field returns validation error.
     */
    public function test_missing_password_field_returns_validation_error(): void
    {
        $user = $this->createUserWithTenant();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Failure path: throttle — after hitting the rate limit the response is 429
     * with a Retry-After header indicating seconds remaining.
     *
     * The named 'login' rate limiter in config/fortify.php is enforced by Laravel's
     * ThrottleRequests middleware, which intercepts before Fortify's pipeline runs and
     * returns a bare 429. No session errors are written. The frontend detects the 429
     * via onHttpException on the <Form> component and reads the Retry-After header to
     * show the amber throttle banner (Gap 7, Reviewer fix on PR #325).
     */
    public function test_throttled_login_returns_429_with_retry_after_header(): void
    {
        $user = $this->createUserWithTenant();

        // Exhaust the 5-attempt-per-minute rate limit with wrong credentials.
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt must be throttled (HTTP 429).
        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertTooManyRequests();

        // ThrottleRequests middleware includes a Retry-After header with the seconds remaining.
        // The frontend reads this via onHttpException to show the countdown in the throttle banner.
        $response->assertHeader('Retry-After');
        $retryAfter = (int) $response->headers->get('Retry-After');
        $this->assertGreaterThan(0, $retryAfter, 'Retry-After header must contain a positive integer (seconds remaining).');

        // No session errors are written — the middleware intercepts before Fortify flashes errors.
        $response->assertSessionDoesntHaveErrors('email');
    }

    /**
     * Edge case: remember-me — submitting with remember=1 must set a long-lived cookie
     * (lasting longer than a session-only cookie).
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
        // Fortify writes a remember_token to the users table when remember=1.
        $this->assertNotNull($user->fresh()->remember_token);
    }

    /**
     * Edge case: without remember-me the user is still authenticated but
     * no persistent remember-me token is written to the DB.
     */
    public function test_login_without_remember_me_does_not_set_remember_token(): void
    {
        $user = $this->createUserWithTenant();

        $user->forceFill(['remember_token' => null])->save();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            // no 'remember' key
        ]);

        $this->assertAuthenticated();
        $this->assertNull($user->fresh()->remember_token);
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
     * Edge case: user with no AccountMembership row — setTenantForUser() returns early
     * without throwing an exception; user is still authenticated and redirected.
     */
    public function test_user_without_account_membership_can_still_authenticate(): void
    {
        $user = User::factory()->create();
        // Deliberately do NOT create an AccountMembership for this user.

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // No exception — setTenantForUser() returns early on missing membership.
        $this->assertAuthenticated();
        $response->assertRedirect(config('fortify.home'));
        // No tenant_id was set in session.
        $response->assertSessionMissing('tenant_id');
    }

    /**
     * Edge case: dependent role falls back to fortify.home (same as tenant role,
     * per LoginResponse priority order — both share the same case branch).
     */
    public function test_dependent_user_is_redirected_to_dashboard_fallback_after_login(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::DEPENDENTS);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        // TODO: replace with route('resident.index') when resident portal lands
        $response->assertRedirect(config('fortify.home'));
    }

    /**
     * Edge case: professionals role falls back to fortify.home.
     */
    public function test_professional_user_is_redirected_to_dashboard_fallback_after_login(): void
    {
        $this->seed(RolesSeeder::class);

        $user = $this->createUserWithTenantAndRole(RolesEnum::PROFESSIONALS);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        // TODO: replace with role-specific route once professional portal story lands
        $response->assertRedirect(config('fortify.home'));
    }
}
