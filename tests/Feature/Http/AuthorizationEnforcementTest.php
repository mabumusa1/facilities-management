<?php

namespace Tests\Feature\Http;

use App\Enums\RolesEnum;
use App\Http\Middleware\HandleInertiaRequests;
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

    // ── AC: Unauthenticated user is redirected (not 403) ─────────────────────

    public function test_unauthenticated_user_is_redirected_from_communities_index(): void
    {
        $this->get(route('communities.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_is_redirected_from_leases_index(): void
    {
        $this->get(route('leases.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_is_redirected_from_transactions_index(): void
    {
        $this->get(route('transactions.index'))
            ->assertRedirect(route('login'));
    }

    // ── AC: Fine-grained — VIEW-only user can read but cannot write ───────────

    public function test_dependent_with_view_only_can_access_facilities_index(): void
    {
        // Dependents have FacilityBookings.VIEW + Facilities.VIEW only
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::DEPENDENTS->value);

        $this->tenantGet($user, route('facilities.index'))
            ->assertOk();
    }

    public function test_dependent_with_view_only_cannot_access_facilities_create(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::DEPENDENTS->value);

        $this->tenantGet($user, route('facilities.create'))
            ->assertForbidden();
    }

    public function test_owner_with_view_create_update_cannot_delete_leases(): void
    {
        // Owners have VIEW+CREATE+UPDATE on Leases but NOT DELETE
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::OWNERS->value);

        $this->assertTrue($user->can('leases.VIEW'));
        $this->assertTrue($user->can('leases.CREATE'));
        $this->assertTrue($user->can('leases.UPDATE'));
        $this->assertFalse($user->can('leases.DELETE'));
    }

    public function test_tenant_with_view_create_cannot_update_leases(): void
    {
        // Tenants have VIEW+CREATE on Leases but NOT UPDATE
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::TENANTS->value);

        $this->assertTrue($user->can('leases.VIEW'));
        $this->assertTrue($user->can('leases.CREATE'));
        $this->assertFalse($user->can('leases.UPDATE'));
        $this->assertFalse($user->can('leases.DELETE'));
    }

    // ── AC: Gate::before — accountAdmins bypass all Gate::define subjects ─────

    public function test_account_admin_bypasses_all_non_model_gate_subjects(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $nonModelSubjects = [
            'reports', 'settings', 'companyProfile',
            'invoiceSettings', 'leaseSettings', 'directories',
            'suggestions', 'complaints', 'homeServices',
            'neighbourhoodServices', 'visitorAccess',
        ];

        foreach ($nonModelSubjects as $subject) {
            $this->assertTrue(
                $user->can("{$subject}.VIEW"),
                "accountAdmins should bypass Gate::before for {$subject}.VIEW"
            );
        }
    }

    // ── AC: Inertia 403 JSON shape with correct message ───────────────────────

    public function test_inertia_request_returns_json_403_with_english_message(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->withoutMiddleware(HandleInertiaRequests::class)
            ->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withHeaders(['X-Inertia' => 'true', 'Accept' => 'application/json'])
            ->get(route('communities.index'))
            ->assertStatus(403)
            ->assertJson(['message' => __('errors.forbidden', [], 'en')]);
    }

    public function test_inertia_request_returns_json_403_with_arabic_message(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        // Send X-Locale header so SetLocale middleware switches app locale to Arabic.
        $this->withoutMiddleware(HandleInertiaRequests::class)
            ->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->withHeaders(['X-Inertia' => 'true', 'Accept' => 'application/json', 'X-Locale' => 'ar'])
            ->get(route('communities.index'))
            ->assertStatus(403)
            ->assertJson(['message' => __('errors.forbidden', [], 'ar')]);
    }

    public function test_arabic_forbidden_message_is_non_empty_and_differs_from_english(): void
    {
        $this->assertNotEmpty(__('errors.forbidden', [], 'ar'));
        $this->assertNotSame(__('errors.forbidden', [], 'en'), __('errors.forbidden', [], 'ar'));
    }

    // ── AC: Multi-area authorization (properties, leasing, accounting, facilities, communication) ──

    public function test_professional_cannot_access_leases_index(): void
    {
        // Professionals have no Leases permission
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->tenantGet($user, route('leases.index'))
            ->assertForbidden();
    }

    public function test_professional_cannot_access_transactions_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->tenantGet($user, route('transactions.index'))
            ->assertForbidden();
    }

    public function test_professional_cannot_access_announcements_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->tenantGet($user, route('announcements.index'))
            ->assertForbidden();
    }

    public function test_professional_cannot_access_facility_bookings_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->tenantGet($user, route('facility-bookings.index'))
            ->assertForbidden();
    }

    public function test_manager_can_access_leases_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::MANAGERS->value);

        $this->tenantGet($user, route('leases.index'))
            ->assertOk();
    }

    public function test_manager_can_access_transactions_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::MANAGERS->value);

        $this->tenantGet($user, route('transactions.index'))
            ->assertOk();
    }

    public function test_manager_can_access_announcements_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::MANAGERS->value);

        $this->tenantGet($user, route('announcements.index'))
            ->assertOk();
    }

    public function test_manager_can_access_facilities_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::MANAGERS->value);

        $this->tenantGet($user, route('facilities.index'))
            ->assertOk();
    }

    public function test_manager_can_access_facility_bookings_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::MANAGERS->value);

        $this->tenantGet($user, route('facility-bookings.index'))
            ->assertOk();
    }

    // ── AC: Non-model gate subjects blocked for unpermitted roles ─────────────

    public function test_professional_cannot_access_non_model_gate_subjects(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->assertFalse($user->can('reports.VIEW'));
        $this->assertFalse($user->can('companyProfile.VIEW'));
        $this->assertFalse($user->can('invoiceSettings.VIEW'));
        $this->assertFalse($user->can('leaseSettings.VIEW'));
        $this->assertFalse($user->can('directories.VIEW'));
        $this->assertFalse($user->can('visitorAccess.VIEW'));
    }

    public function test_manager_can_access_non_model_gate_subjects_in_their_scope(): void
    {
        // Managers have reports, directories, suggestions, complaints, visitorAccess, homeServices, neighbourhoodServices
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::MANAGERS->value);

        $this->assertTrue($user->can('reports.VIEW'));
        $this->assertTrue($user->can('directories.VIEW'));
        $this->assertTrue($user->can('suggestions.VIEW'));
        $this->assertTrue($user->can('complaints.VIEW'));
        $this->assertTrue($user->can('visitorAccess.VIEW'));
        $this->assertTrue($user->can('homeServices.VIEW'));
        $this->assertTrue($user->can('neighbourhoodServices.VIEW'));
    }

    // ── QA: Unauthenticated redirects — additional areas ─────────────────────

    public function test_unauthenticated_user_is_redirected_from_buildings_index(): void
    {
        $this->get(route('buildings.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_is_redirected_from_facilities_index(): void
    {
        $this->get(route('facilities.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_is_redirected_from_announcements_index(): void
    {
        $this->get(route('announcements.index'))
            ->assertRedirect(route('login'));
    }

    public function test_unauthenticated_user_is_redirected_from_facility_bookings_index(): void
    {
        $this->get(route('facility-bookings.index'))
            ->assertRedirect(route('login'));
    }

    // ── QA: Properties area (buildings, units) forbidden for unpermitted role ─

    public function test_professional_cannot_access_buildings_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->tenantGet($user, route('buildings.index'))
            ->assertForbidden();
    }

    public function test_professional_cannot_access_units_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        $this->tenantGet($user, route('units.index'))
            ->assertForbidden();
    }

    public function test_dependent_cannot_access_buildings_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::DEPENDENTS->value);

        $this->tenantGet($user, route('buildings.index'))
            ->assertForbidden();
    }

    // ── QA: Super-admin HTTP bypass — buildings and units areas ───────────────

    public function test_account_admin_can_access_buildings_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $this->tenantGet($user, route('buildings.index'))
            ->assertOk();
    }

    public function test_account_admin_can_access_units_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $this->tenantGet($user, route('units.index'))
            ->assertOk();
    }

    // ── QA: Manager authorized in buildings area ───────────────────────────────

    public function test_manager_can_access_buildings_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::MANAGERS->value);

        $this->tenantGet($user, route('buildings.index'))
            ->assertOk();
    }

    // ── QA: VIEW-only role blocked from write routes via HTTP ─────────────────

    public function test_dependent_with_view_only_cannot_post_to_facility_bookings_store(): void
    {
        // Dependents have FacilityBookings.VIEW but NOT FacilityBookings.CREATE.
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::DEPENDENTS->value);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('facility-bookings.store'), [])
            ->assertForbidden();
    }

    public function test_dependent_with_view_only_cannot_post_to_announcements_store(): void
    {
        // Dependents have Announcements.VIEW but NOT Announcements.CREATE.
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::DEPENDENTS->value);

        $this->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('announcements.store'), [])
            ->assertForbidden();
    }

    // ── QA: Non-Inertia 403 returns HTTP 403, not a redirect or 500 ──────────

    public function test_non_inertia_forbidden_returns_403_not_redirect(): void
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::PROFESSIONALS->value);

        // No X-Inertia header — handler returns null, Laravel default 403 applies.
        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('buildings.index'));

        $response->assertStatus(403);
        $response->assertDontSee('302');
    }

    // ── QA: Gate::define non-model — admins role also has full access ─────────

    public function test_admins_role_can_access_all_non_model_gate_subjects(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Admins');

        $nonModelSubjects = [
            'reports', 'settings', 'companyProfile',
            'invoiceSettings', 'leaseSettings', 'directories',
            'suggestions', 'complaints', 'homeServices',
            'neighbourhoodServices', 'visitorAccess',
        ];

        foreach ($nonModelSubjects as $subject) {
            $this->assertTrue(
                $user->can("{$subject}.VIEW"),
                "Admins should have {$subject}.VIEW permission"
            );
        }
    }
}
