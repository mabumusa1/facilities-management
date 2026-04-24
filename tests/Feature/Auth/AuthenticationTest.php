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
}
