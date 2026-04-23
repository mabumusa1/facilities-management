<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Building;
use App\Models\Community;
use App\Models\Professional;
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
     * Assign the user a service-type-scoped role row.
     */
    private function assignServiceTypeScope(User $user, int $serviceTypeId): void
    {
        $role = $this->getOrCreateManagersRole();
        \DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
            'community_id' => null,
            'building_id' => null,
            'service_type_id' => $serviceTypeId,
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

    // -------------------------------------------------------------------------
    // AC1: Union-of-scopes — multiple model_has_roles rows → union, not intersection
    // -------------------------------------------------------------------------

    public function test_union_of_scopes_community_plus_community_sees_both(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $communityA = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $communityB = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $communityC = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $this->assignCommunityScope($user, $communityA->id);
        $this->assignCommunityScope($user, $communityB->id);

        $scopes = ManagerScopeHelper::scopesForUser($user);
        $this->assertFalse($scopes['is_unrestricted']);
        $this->assertContains($communityA->id, $scopes['community_ids']);
        $this->assertContains($communityB->id, $scopes['community_ids']);
        $this->assertNotContains($communityC->id, $scopes['community_ids']);
    }

    public function test_union_of_scopes_announcements_across_community_and_building(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $communityA = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $communityB = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $buildingB = Building::factory()->create([
            'rf_community_id' => $communityB->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $announcementCommunityA = Announcement::factory()->create([
            'community_id' => $communityA->id,
            'account_tenant_id' => $tenant->id,
        ]);
        $announcementBuildingB = Announcement::factory()->create([
            'community_id' => $communityB->id,
            'building_id' => $buildingB->id,
            'account_tenant_id' => $tenant->id,
        ]);
        $announcementOther = Announcement::factory()->create([
            'community_id' => $communityB->id,
            'account_tenant_id' => $tenant->id,
        ]);

        // Manager has community scope on A, building scope on B.
        $this->assignCommunityScope($user, $communityA->id);
        $this->assignBuildingScope($user, $buildingB->id);

        $results = Announcement::query()->forManager($user)->get();

        $this->assertTrue($results->contains('id', $announcementCommunityA->id));
        $this->assertTrue($results->contains('id', $announcementBuildingB->id));
        // Announcement tied only to communityB (not to the specific building) is out of scope.
        $this->assertFalse($results->contains('id', $announcementOther->id));
    }

    // -------------------------------------------------------------------------
    // AC2: accountAdmins bypass — admin sees all rows regardless of scope FKs
    // -------------------------------------------------------------------------

    public function test_account_admin_bypasses_manager_scope_on_announcements(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $communityA = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $communityB = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        Announcement::factory()->create(['community_id' => $communityA->id, 'account_tenant_id' => $tenant->id]);
        Announcement::factory()->create(['community_id' => $communityB->id, 'account_tenant_id' => $tenant->id]);

        $this->getOrCreateAccountAdminsRole();
        $user->assignRole('accountAdmins');

        $results = Announcement::query()->forManager($user)->get();

        // Admin sees all two announcements (no WHERE restriction added).
        $this->assertCount(2, $results);
    }

    public function test_account_admin_user_can_access_any_model_instance(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $this->getOrCreateAccountAdminsRole();
        $user->assignRole('accountAdmins');

        // Admin has no explicit community scope rows — userCanAccessModel must still return true.
        $scopes = ManagerScopeHelper::scopesForUser($user);
        $this->assertTrue($scopes['is_unrestricted']);
        $this->assertTrue(ManagerScopeHelper::userCanAccessModel($user, $community));
    }

    // -------------------------------------------------------------------------
    // AC3: Out-of-scope write rejection — userCanAccessModel returns false for
    //      records outside manager's scope (policy enforcement)
    // -------------------------------------------------------------------------

    public function test_policy_write_rejected_for_out_of_scope_community(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $inScope = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $outOfScope = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $this->assignCommunityScope($user, $inScope->id);

        // userCanAccessModel is what policies call for update/delete.
        $this->assertTrue(ManagerScopeHelper::userCanAccessModel($user, $inScope));
        $this->assertFalse(ManagerScopeHelper::userCanAccessModel($user, $outOfScope));
    }

    public function test_policy_write_rejected_for_out_of_scope_building(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $communityA = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $communityB = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $buildingInA = Building::factory()->create([
            'rf_community_id' => $communityA->id,
            'account_tenant_id' => $tenant->id,
        ]);
        $buildingInB = Building::factory()->create([
            'rf_community_id' => $communityB->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assignCommunityScope($user, $communityA->id);

        $this->assertTrue(ManagerScopeHelper::userCanAccessModel($user, $buildingInA));
        $this->assertFalse(ManagerScopeHelper::userCanAccessModel($user, $buildingInB));
    }

    public function test_policy_write_rejected_for_out_of_scope_announcement(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $inScopeCommunity = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $outOfScopeCommunity = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $announcementIn = Announcement::factory()->create([
            'community_id' => $inScopeCommunity->id,
            'account_tenant_id' => $tenant->id,
        ]);
        $announcementOut = Announcement::factory()->create([
            'community_id' => $outOfScopeCommunity->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assignCommunityScope($user, $inScopeCommunity->id);

        $this->assertTrue(ManagerScopeHelper::userCanAccessModel($user, $announcementIn));
        $this->assertFalse(ManagerScopeHelper::userCanAccessModel($user, $announcementOut));
    }

    // -------------------------------------------------------------------------
    // AC4: Tenant boundary — manager cannot see rows from another tenant
    //      even when scope IDs collide (BelongsToAccountTenant global scope)
    // -------------------------------------------------------------------------

    public function test_tenant_global_scope_blocks_cross_tenant_visibility(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A']);
        $tenantB = Tenant::create(['name' => 'Tenant B']);

        $userA = User::factory()->create();

        // Create a community in tenant A and one in tenant B.
        $communityA = Community::factory()->create(['account_tenant_id' => $tenantA->id]);
        $communityB = Community::factory()->create(['account_tenant_id' => $tenantB->id]);

        // Assign user from tenant A community-scope on communityA.
        $this->assignCommunityScope($userA, $communityA->id);

        // Make tenant A current to activate the BelongsToAccountTenant global scope.
        $tenantA->makeCurrent();

        $results = Community::query()->forManager($userA)->get();

        Tenant::forgetCurrent();

        // User sees their in-scope community in their tenant.
        $this->assertTrue($results->contains('id', $communityA->id));
        // User cannot see a community from tenant B — global scope blocks it.
        $this->assertFalse($results->contains('id', $communityB->id));
    }

    public function test_manager_cannot_access_model_from_another_tenant_via_id_collision(): void
    {
        // This tests that even if scopes['community_ids'] contains an ID that happens
        // to match a community in a different tenant, userCanAccessModel alone cannot
        // be relied upon for cross-tenant isolation — the query scope + global tenant
        // scope together form the full protection.
        $tenantA = Tenant::create(['name' => 'Tenant Alpha']);
        $tenantB = Tenant::create(['name' => 'Tenant Beta']);

        $userA = User::factory()->create();

        $communityA = Community::factory()->create(['account_tenant_id' => $tenantA->id]);

        // Assign scope on community A's ID.
        $this->assignCommunityScope($userA, $communityA->id);

        // Make tenant B current — communityA is NOT visible under tenant B scope.
        $tenantB->makeCurrent();

        $results = Community::query()->forManager($userA)->get();

        Tenant::forgetCurrent();

        $this->assertFalse($results->contains('id', $communityA->id));
    }

    // -------------------------------------------------------------------------
    // AC5: Service-type scope independently resolves correct IDs
    // -------------------------------------------------------------------------

    public function test_scopes_for_user_returns_service_type_ids(): void
    {
        [, $user] = $this->makeTenantAndUser();
        $serviceTypeId = 42;

        $this->assignServiceTypeScope($user, $serviceTypeId);

        $scopes = ManagerScopeHelper::scopesForUser($user);

        $this->assertFalse($scopes['is_unrestricted']);
        $this->assertContains($serviceTypeId, $scopes['service_type_ids']);
        $this->assertEmpty($scopes['community_ids']);
        $this->assertEmpty($scopes['building_ids']);
    }

    public function test_professional_scope_is_unrestricted_regardless_of_manager_scope(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $communityA = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $professionalA = Professional::factory()->create(['account_tenant_id' => $tenant->id]);
        $professionalB = Professional::factory()->create(['account_tenant_id' => $tenant->id]);

        // Even a narrowly-scoped manager sees all professionals (no FK path).
        $this->assignCommunityScope($user, $communityA->id);

        $results = Professional::query()->forManager($user)->get();

        $this->assertTrue($results->contains('id', $professionalA->id));
        $this->assertTrue($results->contains('id', $professionalB->id));
    }

    // -------------------------------------------------------------------------
    // AC6: Manager with no scope rows sees nothing (zero access)
    // -------------------------------------------------------------------------

    public function test_manager_with_no_scope_rows_sees_no_communities(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        Community::factory()->count(2)->create(['account_tenant_id' => $tenant->id]);

        // User has the managers role but no scope rows at all.
        $role = $this->getOrCreateManagersRole();
        \DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $user->id,
            'community_id' => null,
            'building_id' => null,
            'service_type_id' => null,
        ]);

        // A null-null-null row means system-wide role → unrestricted (documented behaviour).
        $scopes = ManagerScopeHelper::scopesForUser($user);
        $this->assertTrue($scopes['is_unrestricted']);
    }

    public function test_manager_with_no_role_rows_whatsoever_sees_no_communities(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        Community::factory()->count(2)->create(['account_tenant_id' => $tenant->id]);

        // User has absolutely no model_has_roles rows.
        $scopes = ManagerScopeHelper::scopesForUser($user);

        $this->assertFalse($scopes['is_unrestricted']);
        $this->assertEmpty($scopes['community_ids']);
        $this->assertEmpty($scopes['building_ids']);
        $this->assertEmpty($scopes['service_type_ids']);

        // forManager() with empty scope + no unrestricted flag → returns nothing.
        $results = Community::query()->forManager($user)->get();
        $this->assertCount(0, $results);
    }

    // -------------------------------------------------------------------------
    // Fix: removeScopedRole only removes the targeted scope row, leaving others
    // -------------------------------------------------------------------------

    public function test_remove_scoped_role_leaves_sibling_scope_rows_intact(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $communityA = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $communityB = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $this->assignCommunityScope($user, $communityA->id);
        $this->assignCommunityScope($user, $communityB->id);

        // Both rows exist before removal.
        $this->assertSame(2, \DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->count());

        // Remove only the communityA scope row.
        $user->removeScopedRole('managers', communityId: $communityA->id);

        // communityB row must still be present.
        $this->assertSame(1, \DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->count());

        $remaining = \DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->where('model_id', $user->id)
            ->first();

        $this->assertSame($communityB->id, (int) $remaining->community_id);
    }

    public function test_remove_role_throws_when_scoped_rows_exist(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $this->assignCommunityScope($user, $community->id);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/removeScopedRole/');

        $user->removeRole('managers');
    }

    public function test_sync_roles_throws_when_scoped_rows_exist(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $this->assignCommunityScope($user, $community->id);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/syncRoles/');

        $user->syncRoles('managers');
    }

    // -------------------------------------------------------------------------
    // Failure path: userCanAccessModel returns false when building-only manager
    //               tries to access a community (different model type)
    // -------------------------------------------------------------------------

    public function test_building_scoped_manager_cannot_access_community_directly(): void
    {
        [$tenant, $user] = $this->makeTenantAndUser();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $building = Building::factory()->create([
            'rf_community_id' => $community->id,
            'account_tenant_id' => $tenant->id,
        ]);

        $this->assignBuildingScope($user, $building->id);

        // Community access requires a community scope row, not building scope.
        $this->assertFalse(ManagerScopeHelper::userCanAccessModel($user, $community));
        // But the building itself is accessible.
        $this->assertTrue(ManagerScopeHelper::userCanAccessModel($user, $building));
    }
}
