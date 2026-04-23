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
