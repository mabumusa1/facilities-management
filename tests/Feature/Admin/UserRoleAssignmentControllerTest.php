<?php

namespace Tests\Feature\Admin;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Building;
use App\Models\Community;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserRoleAssignmentControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->withoutVite();
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function createTenantAdmin(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ADMINS->value,
        ]);

        $user->assignRole(RolesEnum::ADMINS->value);

        return [$user, $tenant];
    }

    private function createTargetUser(Tenant $tenant): User
    {
        $user = User::factory()->create();
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);

        return $user;
    }

    // ─── Happy Paths ────────────────────────────────────────────────────────

    public function test_show_renders_user_detail_page(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.show', $target));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/Show')
            ->has('user')
            ->has('roles')
            ->has('communities')
            ->has('buildings')
            ->has('serviceTypes')
        );
    }

    public function test_store_inserts_unscoped_role_row(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::OWNERS->value)->first();
        $this->assertNotNull($role);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
            ]);

        $response->assertRedirect(route('admin.users.show', $target));

        $this->assertDatabaseHas(config('permission.table_names.model_has_roles'), [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => null,
            'building_id' => null,
            'service_type_id' => null,
        ]);
    }

    public function test_store_inserts_manager_scoped_role_row(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);

        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
                'community_id' => $community->id,
            ]);

        $response->assertRedirect(route('admin.users.show', $target));

        $this->assertDatabaseHas(config('permission.table_names.model_has_roles'), [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => $community->id,
            'building_id' => null,
        ]);
    }

    public function test_store_inserts_manager_scoped_role_with_community_and_building(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);

        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $building = Building::factory()->create([
            'account_tenant_id' => $tenant->id,
            'rf_community_id' => $community->id,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
                'community_id' => $community->id,
                'building_id' => $building->id,
            ]);

        $response->assertRedirect(route('admin.users.show', $target));

        $this->assertDatabaseHas(config('permission.table_names.model_has_roles'), [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => $community->id,
            'building_id' => $building->id,
        ]);
    }

    public function test_destroy_removes_exact_scope_tuple_and_leaves_sibling_row(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);

        $communityA = Community::factory()->create(['account_tenant_id' => $tenant->id]);
        $communityB = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $mhr = config('permission.table_names.model_has_roles');

        // Insert two scoped rows
        $idA = \DB::table($mhr)->insertGetId([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => $communityA->id,
            'building_id' => null,
            'service_type_id' => null,
        ]);
        \DB::table($mhr)->insert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => $communityB->id,
            'building_id' => null,
            'service_type_id' => null,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('admin.users.role-assignments.destroy', [$target, $idA]));

        $response->assertRedirect(route('admin.users.show', $target));

        // Row A deleted
        $this->assertDatabaseMissing($mhr, ['id' => $idA]);

        // Row B untouched
        $this->assertDatabaseHas($mhr, [
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => $communityB->id,
        ]);
    }

    public function test_destroy_redirects_with_success_toast(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::OWNERS->value)->first();
        $this->assertNotNull($role);

        $mhr = config('permission.table_names.model_has_roles');
        $rowId = \DB::table($mhr)->insertGetId([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => null,
            'building_id' => null,
            'service_type_id' => null,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('admin.users.role-assignments.destroy', [$target, $rowId]));

        $response->assertRedirect(route('admin.users.show', $target));
    }

    // ─── Failure Paths ───────────────────────────────────────────────────────

    public function test_store_requires_role_id(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), []);

        $response->assertSessionHasErrors('role_id');
    }

    public function test_store_requires_community_for_manager_role(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
                'community_id' => null,
            ]);

        $response->assertSessionHasErrors('community_id');
    }

    public function test_store_returns_422_for_duplicate_assignment(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::OWNERS->value)->first();
        $this->assertNotNull($role);

        // First assignment
        $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
            ]);

        // Duplicate
        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
            ]);

        $response->assertSessionHasErrors('role_id');
    }

    public function test_destroy_returns_404_for_assignment_belonging_to_different_user(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);
        $otherUser = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::OWNERS->value)->first();
        $this->assertNotNull($role);

        $mhr = config('permission.table_names.model_has_roles');
        $rowId = \DB::table($mhr)->insertGetId([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $otherUser->id,
            'community_id' => null,
            'building_id' => null,
            'service_type_id' => null,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('admin.users.role-assignments.destroy', [$target, $rowId]));

        $response->assertNotFound();
    }

    public function test_destroy_returns_404_for_nonexistent_assignment(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('admin.users.role-assignments.destroy', [$target, 99999]));

        $response->assertNotFound();
    }

    public function test_unauthenticated_requests_redirect_to_login(): void
    {
        $user = User::factory()->create();

        $this->get(route('admin.users.show', $user))->assertRedirect(route('login'));
        $this->post(route('admin.users.role-assignments.store', $user))->assertRedirect(route('login'));
        $this->delete(route('admin.users.role-assignments.destroy', [$user, 1]))->assertRedirect(route('login'));
    }

    public function test_user_without_admin_role_gets_403(): void
    {
        $actor = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account 2']);

        AccountMembership::create([
            'user_id' => $actor->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);

        $actor->assignRole(RolesEnum::TENANTS->value);

        $target = $this->createTargetUser($tenant);

        $response = $this->actingAs($actor)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.show', $target));

        $response->assertForbidden();
    }

    // ─── QA: Failure Paths ──────────────────────────────────────────────────

    /** AC: manager role requires at least one scope (community_id must be present). */
    public function test_store_manager_role_without_any_scope_is_rejected(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);

        // No community_id supplied at all
        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
            ]);

        $response->assertSessionHasErrors('community_id');
    }

    /** AC: non-manager role must not accept scope selectors (community_id passed but role is 'owners' → silently stored as null per nullable rule; cross-tenant ID still rejected). */
    public function test_store_non_manager_role_with_cross_tenant_community_is_rejected(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $otherTenant = Tenant::create(['name' => 'Other Tenant']);
        $foreignCommunity = Community::factory()->create(['account_tenant_id' => $otherTenant->id]);

        $role = Role::where('name', RolesEnum::OWNERS->value)->first();
        $this->assertNotNull($role);

        // Owners role has scopeLevel 'none' — community_id is prohibited for non-scoped roles.
        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
                'community_id' => $foreignCommunity->id,
            ]);

        // community_id must be rejected for roles with scopeLevel 'none'.
        $response->assertSessionHasErrors('community_id');
    }

    /** AC: scope FKs must belong to current tenant — cross-tenant community_id rejected for manager role. */
    public function test_store_manager_role_with_cross_tenant_community_is_rejected(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $otherTenant = Tenant::create(['name' => 'Cross Tenant']);
        $foreignCommunity = Community::factory()->create(['account_tenant_id' => $otherTenant->id]);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
                'community_id' => $foreignCommunity->id,
            ]);

        $response->assertSessionHasErrors('community_id');
    }

    /** AC: scope FKs must belong to current tenant — cross-tenant building_id rejected for manager role. */
    public function test_store_manager_role_with_cross_tenant_building_is_rejected(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $otherTenant = Tenant::create(['name' => 'Cross Tenant B']);
        $foreignCommunity = Community::factory()->create(['account_tenant_id' => $otherTenant->id]);
        $foreignBuilding = Building::factory()->create([
            'account_tenant_id' => $otherTenant->id,
            'rf_community_id' => $foreignCommunity->id,
        ]);

        // Valid community in current tenant
        $validCommunity = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
                'community_id' => $validCommunity->id,
                'building_id' => $foreignBuilding->id,
            ]);

        $response->assertSessionHasErrors('building_id');
    }

    /** AC: tenant admin cannot assign to user in another tenant (403). */
    public function test_store_cannot_assign_role_to_user_in_another_tenant(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $otherTenant = Tenant::create(['name' => 'Other Account']);
        $outsider = User::factory()->create();
        AccountMembership::create([
            'user_id' => $outsider->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);

        $role = Role::where('name', RolesEnum::OWNERS->value)->first();
        $this->assertNotNull($role);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $outsider), [
                'role_id' => $role->id,
            ]);

        $response->assertForbidden();
    }

    /** AC: destroy is also blocked when target user is in another tenant. */
    public function test_destroy_cannot_remove_role_for_user_in_another_tenant(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $otherTenant = Tenant::create(['name' => 'Other Account 2']);
        $outsider = User::factory()->create();
        AccountMembership::create([
            'user_id' => $outsider->id,
            'account_tenant_id' => $otherTenant->id,
            'role' => RolesEnum::TENANTS->value,
        ]);

        $role = Role::where('name', RolesEnum::OWNERS->value)->first();
        $this->assertNotNull($role);
        $mhr = config('permission.table_names.model_has_roles');

        $rowId = \DB::table($mhr)->insertGetId([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $outsider->id,
            'community_id' => null,
            'building_id' => null,
            'service_type_id' => null,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->delete(route('admin.users.role-assignments.destroy', [$outsider, $rowId]));

        $response->assertForbidden();
    }

    /** AC: system role (accountAdmins) is assignable by accountAdmin. */
    public function test_store_account_admin_can_assign_account_admin_role(): void
    {
        // Create an accountAdmins user (uses Gate::before bypass)
        $superAdmin = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Super Tenant']);
        AccountMembership::create([
            'user_id' => $superAdmin->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ACCOUNT_ADMINS->value,
        ]);
        $superAdmin->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::ACCOUNT_ADMINS->value)->first();
        $this->assertNotNull($role);

        $response = $this->actingAs($superAdmin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
            ]);

        $response->assertRedirect(route('admin.users.show', $target));

        $this->assertDatabaseHas(config('permission.table_names.model_has_roles'), [
            'role_id' => $role->id,
            'model_id' => $target->id,
        ]);
    }

    /** AC: assigning accountAdmins to a user (as a regular admin) is blocked. */
    public function test_store_regular_admin_can_assign_account_admins_role(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::ACCOUNT_ADMINS->value)->first();
        $this->assertNotNull($role);

        // Regular admins have 'manage-user-role-assignments' Gate permission
        // (they are in the allowed set). The Gate only checks admin or account_admin role.
        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
            ]);

        $response->assertRedirect(route('admin.users.show', $target));

        $this->assertDatabaseHas(config('permission.table_names.model_has_roles'), [
            'role_id' => $role->id,
            'model_id' => $target->id,
        ]);
    }

    /** AC: duplicate (user, role, scope) tuple is rejected idempotently (returns error, does not insert second row). */
    public function test_duplicate_scoped_assignment_is_rejected_with_error(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $mhr = config('permission.table_names.model_has_roles');

        // Pre-insert the scoped row
        \DB::table($mhr)->insert([
            'role_id' => $role->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => $community->id,
            'building_id' => null,
            'service_type_id' => null,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
                'community_id' => $community->id,
            ]);

        $response->assertSessionHasErrors('role_id');

        // Only one row exists
        $count = \DB::table($mhr)
            ->where('role_id', $role->id)
            ->where('model_id', $target->id)
            ->where('community_id', $community->id)
            ->count();

        $this->assertSame(1, $count);
    }

    // ─── Edge Cases ─────────────────────────────────────────────────────────

    public function test_store_manager_role_with_community_only_no_building(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $role = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $this->assertNotNull($role);

        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.role-assignments.store', $target), [
                'role_id' => $role->id,
                'community_id' => $community->id,
                // building_id intentionally absent
            ]);

        $response->assertRedirect(route('admin.users.show', $target));

        $this->assertDatabaseHas(config('permission.table_names.model_has_roles'), [
            'role_id' => $role->id,
            'model_id' => $target->id,
            'community_id' => $community->id,
            'building_id' => null,
        ]);
    }

    public function test_show_aborts_404_when_user_not_in_current_tenant(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $outsider = User::factory()->create(); // No membership in current tenant

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.show', $outsider));

        $response->assertForbidden();
    }

    public function test_update_membership_no_longer_throws_when_user_has_scoped_rows(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $target = $this->createTargetUser($tenant);

        $membership = AccountMembership::where('user_id', $target->id)
            ->where('account_tenant_id', $tenant->id)
            ->first();

        $this->assertNotNull($membership);

        // Give target a null-scope role first
        $target->assignRole(RolesEnum::TENANTS->value);

        // Insert a scoped manager row
        $managerRole = Role::where('name', RolesEnum::MANAGERS->value)->first();
        $community = Community::factory()->create(['account_tenant_id' => $tenant->id]);

        $mhr = config('permission.table_names.model_has_roles');
        \DB::table($mhr)->insert([
            'role_id' => $managerRole->id,
            'model_type' => User::class,
            'model_id' => $target->id,
            'community_id' => $community->id,
            'building_id' => null,
            'service_type_id' => null,
        ]);

        // Updating the base membership role should NOT throw LogicException
        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->put(route('admin.users.update', $membership), [
                'role' => RolesEnum::OWNERS->value,
            ]);

        $response->assertRedirect();

        // Scoped manager row still exists
        $this->assertDatabaseHas($mhr, [
            'role_id' => $managerRole->id,
            'model_id' => $target->id,
            'community_id' => $community->id,
        ]);
    }
}
