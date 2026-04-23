<?php

namespace Tests\Feature\Http;

use App\Enums\RolesEnum;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthorizationEnforcementTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
        $this->tenant = Tenant::create(['name' => 'Test Account']);
    }

    /**
     * Perform an authenticated GET request with the tenant session set.
     *
     * @param  array<string, mixed>  $headers
     */
    private function tenantGet(User $user, string $url, array $headers = []): TestResponse
    {
        return $this->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withHeaders($headers)
            ->get($url);
    }

    // ── Happy path: authorized user passes through ─────────────────────────────

    public function test_account_admin_can_access_communities_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $this->tenantGet($user, route('communities.index'))
            ->assertOk();
    }

    public function test_manager_with_permission_can_access_communities_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::MANAGERS->value);

        $this->tenantGet($user, route('communities.index'))
            ->assertOk();
    }

    // ── Failure path: unauthorized user receives 403 ──────────────────────────

    /** @return array<string, array{string}> */
    public static function protectedIndexRoutes(): array
    {
        return [
            'communities.index' => ['communities.index'],
            'communities.create' => ['communities.create'],
        ];
    }

    #[DataProvider('protectedIndexRoutes')]
    public function test_professional_cannot_access_communities_routes(string $routeName): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->tenantGet($user, route($routeName))
            ->assertForbidden();
    }

    public function test_tenant_cannot_access_communities_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::TENANTS->value);

        $this->tenantGet($user, route('communities.index'))
            ->assertForbidden();
    }

    public function test_dependent_cannot_access_communities_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::DEPENDENTS->value);

        $this->tenantGet($user, route('communities.index'))
            ->assertForbidden();
    }

    // ── Super-admin Gate::before bypass ───────────────────────────────────────

    public function test_account_admin_bypasses_policy_check_on_all_core_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        foreach ([route('communities.index'), route('communities.create')] as $url) {
            $this->tenantGet($user, $url)->assertOk();
        }
    }

    // ── Inertia 403 JSON response shape ───────────────────────────────────────

    public function test_non_inertia_request_returns_403_when_forbidden(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->tenantGet($user, route('communities.index'))
            ->assertForbidden();
    }

    // ── Gate::define non-model subjects (settings area) ──────────────────────

    public function test_account_admin_can_check_non_model_gate_ability(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $this->assertTrue($user->can('reports.VIEW'));
        $this->assertTrue($user->can('settings.UPDATE'));
        $this->assertTrue($user->can('companyProfile.CREATE'));
    }

    public function test_dependent_cannot_check_reports_gate_ability(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::DEPENDENTS->value);

        $this->assertFalse($user->can('reports.VIEW'));
        $this->assertFalse($user->can('settings.UPDATE'));
    }
}
