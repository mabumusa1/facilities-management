<?php

namespace Tests\Feature;

use App\Enums\ContactType;
use App\Enums\ManagerRole;
use App\Enums\ServiceManagerType;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_professional_without_manager_type_redirected_to_no_access(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Professional,
            'manager_role' => null,
            'service_manager_type' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('no-access'));
    }

    public function test_professional_with_manager_type_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Professional,
            'manager_role' => ManagerRole::ServiceManager,
            'service_manager_type' => ServiceManagerType::HomeServiceRequests,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_no_access_page_is_accessible(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Professional,
            'manager_role' => null,
        ]);

        $response = $this->actingAs($user)->get('/no-access');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('auth/no-access'));
    }

    public function test_forbidden_page_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/forbidden');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('auth/forbidden'));
    }

    public function test_contact_type_middleware_allows_matching_type(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
        ]);

        $response = $this->actingAs($user)->get('/test-contact-type-admin-owner');

        $response->assertStatus(200);
    }

    public function test_contact_type_middleware_allows_alternative_type(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Owner,
        ]);

        $response = $this->actingAs($user)->get('/test-contact-type-admin-owner');

        $response->assertStatus(200);
    }

    public function test_contact_type_middleware_blocks_non_matching_type(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Tenant,
        ]);

        $response = $this->actingAs($user)->get('/test-contact-type-admin-owner');

        $response->assertStatus(403);
    }

    public function test_verified_user_middleware_allows_admin(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);

        $response = $this->actingAs($user)->get('/test-verified-user');

        $response->assertStatus(200);
    }

    public function test_verified_user_middleware_allows_owner(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Owner,
        ]);

        $response = $this->actingAs($user)->get('/test-verified-user');

        $response->assertStatus(200);
    }

    public function test_verified_user_middleware_allows_tenant(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Tenant,
        ]);

        $response = $this->actingAs($user)->get('/test-verified-user');

        $response->assertStatus(200);
    }

    public function test_verified_user_middleware_redirects_unverified_professional(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Professional,
            'manager_role' => null,
            'service_manager_type' => null,
        ]);

        $response = $this->actingAs($user)->get('/test-verified-user');

        $response->assertRedirect(route('no-access'));
    }

    public function test_verified_user_middleware_allows_verified_professional(): void
    {
        $user = User::factory()->create([
            'contact_type' => ContactType::Professional,
            'manager_role' => ManagerRole::ServiceManager,
            'service_manager_type' => ServiceManagerType::HomeServiceRequests,
        ]);

        $response = $this->actingAs($user)->get('/test-verified-user');

        $response->assertStatus(200);
    }

    public function test_user_contact_type_helper_methods(): void
    {
        $owner = User::factory()->create(['contact_type' => ContactType::Owner]);
        $tenant = User::factory()->create(['contact_type' => ContactType::Tenant]);
        $admin = User::factory()->create(['contact_type' => ContactType::Admin]);
        $professional = User::factory()->create(['contact_type' => ContactType::Professional]);

        $this->assertTrue($owner->isOwner());
        $this->assertFalse($owner->isTenant());

        $this->assertTrue($tenant->isTenant());
        $this->assertFalse($tenant->isAdmin());

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isProfessional());

        $this->assertTrue($professional->isProfessional());
        $this->assertFalse($professional->isOwner());
    }

    public function test_user_scope_access_methods(): void
    {
        $userWithFullAccess = User::factory()->create([
            'is_all_communities' => true,
            'is_all_buildings' => true,
        ]);

        $userWithPartialAccess = User::factory()->create([
            'is_all_communities' => true,
            'is_all_buildings' => false,
        ]);

        $userWithNoAccess = User::factory()->create([
            'is_all_communities' => false,
            'is_all_buildings' => false,
        ]);

        $this->assertTrue($userWithFullAccess->hasUnrestrictedAccess());
        $this->assertFalse($userWithPartialAccess->hasUnrestrictedAccess());
        $this->assertFalse($userWithNoAccess->hasUnrestrictedAccess());

        $this->assertTrue($userWithPartialAccess->hasAllCommunitiesAccess());
        $this->assertFalse($userWithPartialAccess->hasAllBuildingsAccess());
    }

    public function test_middleware_stack_works_correctly(): void
    {
        $admin = User::factory()->create([
            'contact_type' => ContactType::Admin,
            'manager_role' => ManagerRole::Admin,
        ]);
        $admin->assignRole('Admins');

        // Admin should pass all middleware checks
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/test-permission-communities');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/test-capability-properties');
        $response->assertStatus(200);
    }
}
