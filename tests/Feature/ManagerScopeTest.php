<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use App\Models\Tenant;
use App\Models\User;
use App\Support\ManagerScopeHelper;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ManagerScopeTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * Create a tenant and a user for test use.
     *
     * @return array{Tenant, User}
     */
    private function makeTenantAndUser(): array
    {
        $tenant = Tenant::create(['name' => 'Test Tenant']);
        $user = User::factory()->create();

        return [$tenant, $user];
    }

    /**
     * Get or create the managers role for tests.
     */
    private function getOrCreateManagersRole(): object
    {
        $role = \DB::table('roles')->where('name', 'managers')->where('guard_name', 'web')->first();

        if ($role === null) {
            $id = \DB::table('roles')->insertGetId([
                'name' => 'managers',
                'guard_name' => 'web',
                'name_en' => 'Managers',
                'name_ar' => 'مديرون',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $role = \DB::table('roles')->find($id);
        }

        return $role;
    }

    /**
     * Get or create the accountAdmins role for tests.
     */
    private function getOrCreateAccountAdminsRole(): object
    {
        $role = \DB::table('roles')->where('name', 'accountAdmins')->where('guard_name', 'web')->first();

        if ($role === null) {
            $id = \DB::table('roles')->insertGetId([
                'name' => 'accountAdmins',
                'guard_name' => 'web',
                'name_en' => 'Account Admins',
                'name_ar' => 'مدراء الحسابات',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $role = \DB::table('roles')->find($id);
        }

        return $role;
    }

    /**
     * Assign the user a community-scoped role row (simulates a community manager).
     */
    private function assignCommunityScope(User $user, int $communityId): void
    {
        $role = $this->getOrCreateManagersRole();
        \DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
            'community_id' => $communityId,
            'building_id' => null,
            'service_type_id' => null,
        ]);
    }

    /**
     * Assign the user a building-scoped role row (simulates a building manager).
     */
    private function assignBuildingScope(User $user, int $buildingId): void
    {
        $role = $this->getOrCreateManagersRole();
        \DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
            'community_id' => null,
            'building_id' => $buildingId,
            'service_type_id' => null,
        ]);
    }

    // -------------------------------------------------------------------------
    // scopesForUser helpers
    // -------------------------------------------------------------------------

    public function test_scopes_for_user_returns_unrestricted_for_account_admin(): void
    {
        [, $user] = $this->makeTenantAndUser();
        $this->getOrCreateAccountAdminsRole();
        $user->assignRole('accountAdmins');

        $scopes = ManagerScopeHelper::scopesForUser($user);

        $this->assertTrue($scopes['is_unrestricted']);
    }

    public function test_scopes_for_user_returns_community_ids_for_community_scoped_user(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $this->assignCommunityScope($user, $community->id);

        $scopes = ManagerScopeHelper::scopesForUser($user);

        $this->assertFalse($scopes['is_unrestricted']);
        $this->assertContains($community->id, $scopes['community_ids']);
        $this->assertEmpty($scopes['building_ids']);
    }

    public function test_scopes_for_user_returns_building_ids_for_building_scoped_user(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $building = Building::factory()->create([
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assignBuildingScope($user, $building->id);

        $scopes = ManagerScopeHelper::scopesForUser($user);

        $this->assertFalse($scopes['is_unrestricted']);
        $this->assertContains($building->id, $scopes['building_ids']);
        $this->assertEmpty($scopes['community_ids']);
    }

    // -------------------------------------------------------------------------
    // Community scope
    // -------------------------------------------------------------------------

    public function test_community_scope_filters_by_community_id(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $inScope = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $outOfScope = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $this->assignCommunityScope($user, $inScope->id);

        $results = Community::query()->forManager($user)->get();

        $this->assertTrue($results->contains('id', $inScope->id));
        $this->assertFalse($results->contains('id', $outOfScope->id));
    }

    public function test_community_scope_returns_all_for_unrestricted_user(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        Community::factory()->count(3)->create(['account_tenant_id' => $tenant->id]);
        $this->getOrCreateAccountAdminsRole();
        $user->assignRole('accountAdmins');

        $results = Community::query()->forManager($user)->get();

        $this->assertCount(3, $results);
    }

    // -------------------------------------------------------------------------
    // Building scope
    // -------------------------------------------------------------------------

    public function test_building_scope_filters_by_building_id(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $inScope = Building::factory()->create([
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);
        $outOfScope = Building::factory()->create([
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assignBuildingScope($user, $inScope->id);

        $results = Building::query()->forManager($user)->get();

        $this->assertTrue($results->contains('id', $inScope->id));
        $this->assertFalse($results->contains('id', $outOfScope->id));
    }

    public function test_building_scope_includes_buildings_via_community_scope(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $building = Building::factory()->create([
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assignCommunityScope($user, $community->id);

        $results = Building::query()->forManager($user)->get();

        $this->assertTrue($results->contains('id', $building->id));
    }

    // -------------------------------------------------------------------------
    // Announcement scope
    // -------------------------------------------------------------------------

    public function test_announcement_scope_filters_by_community_id(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $inScope = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $outOfScope = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $announcementIn = Announcement::factory()->create([
            'community_id' => $inScope->id,
            'account_tenant_id' => $tenant->id,
        ]);
        $announcementOut = Announcement::factory()->create([
            'community_id' => $outOfScope->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assignCommunityScope($user, $inScope->id);

        $results = Announcement::query()->forManager($user)->get();

        $this->assertTrue($results->contains('id', $announcementIn->id));
        $this->assertFalse($results->contains('id', $announcementOut->id));
    }

    // -------------------------------------------------------------------------
    // userCanAccessModel
    // -------------------------------------------------------------------------

    public function test_user_can_access_model_allows_community_in_scope(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $this->assignCommunityScope($user, $community->id);

        $this->assertTrue(ManagerScopeHelper::userCanAccessModel($user, $community));
    }

    public function test_user_can_access_model_denies_community_out_of_scope(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $inScope = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $outOfScope = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $this->assignCommunityScope($user, $inScope->id);

        $this->assertFalse(ManagerScopeHelper::userCanAccessModel($user, $outOfScope));
    }

    public function test_user_can_access_model_allows_everything_for_account_admin(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $this->getOrCreateAccountAdminsRole();
        $user->assignRole('accountAdmins');

        $this->assertTrue(ManagerScopeHelper::userCanAccessModel($user, $community));
    }

    // -------------------------------------------------------------------------
    // Union of scopes
    // -------------------------------------------------------------------------

    public function test_building_scope_union_of_community_and_building_rows(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $communityA = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $communityB = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $buildingInCommunityA = Building::factory()->create([
            'rf_community_id' => $communityA->id,
            'account_tenant_id' => $tenant->id,
        ]);
        $buildingInCommunityB = Building::factory()->create([
            'rf_community_id' => $communityB->id,
            'account_tenant_id' => $tenant->id,
        ]);

        // User has community scope on A and building scope on a specific building in B.
        $this->assignCommunityScope($user, $communityA->id);
        $this->assignBuildingScope($user, $buildingInCommunityB->id);

        $results = Building::query()->forManager($user)->get();

        $this->assertTrue($results->contains('id', $buildingInCommunityA->id));
        $this->assertTrue($results->contains('id', $buildingInCommunityB->id));
    }
}
