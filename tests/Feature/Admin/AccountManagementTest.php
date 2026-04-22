<?php

namespace Tests\Feature\Admin;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravelcm\Subscriptions\Models\Subscription;
use Tests\TestCase;

class AccountManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
    }

    /**
     * @return array{0: User, 1: Tenant}
     */
    private function createTenantMember(string $role): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Managed Account']);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => $role,
        ]);

        $user->assignRole($role);

        return [$user, $tenant];
    }

    public function test_admin_can_access_account_user_management_page(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.index'));

        $response->assertOk();
    }

    public function test_non_admin_cannot_access_account_user_management_page(): void
    {
        [$user, $tenant] = $this->createTenantMember(RolesEnum::TENANTS->value);

        $response = $this->actingAs($user)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.users.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_account_user(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.users.store'), [
                'name' => 'Managed User',
                'email' => 'managed-user@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
                'role' => RolesEnum::MANAGERS->value,
            ]);

        $response->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'managed-user@example.com',
            'name' => 'Managed User',
        ]);

        $managedUserId = (int) User::query()->where('email', 'managed-user@example.com')->value('id');

        $this->assertDatabaseHas('account_memberships', [
            'user_id' => $managedUserId,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::MANAGERS->value,
        ]);
    }

    public function test_admin_can_activate_and_cancel_subscription_for_current_tenant(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $activateResponse = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.subscriptions.activate', $tenant));

        $activateResponse->assertRedirect();

        $this->assertDatabaseHas(config('laravel-subscriptions.tables.subscriptions'), [
            'subscriber_type' => Tenant::class,
            'subscriber_id' => $tenant->id,
            'slug' => 'main',
        ]);

        $cancelResponse = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->post(route('admin.subscriptions.cancel', $tenant));

        $cancelResponse->assertRedirect();

        $subscription = Subscription::query()
            ->where('subscriber_type', Tenant::class)
            ->where('subscriber_id', $tenant->id)
            ->where('slug', 'main')
            ->latest('id')
            ->first();

        $this->assertNotNull($subscription);
        $this->assertNotNull($subscription?->canceled_at);
    }

    public function test_admin_can_view_subscription_management_page(): void
    {
        [$admin, $tenant] = $this->createTenantMember(RolesEnum::ADMINS->value);

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get(route('admin.subscriptions.index'));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('admin/subscriptions/Index')
                ->has('plan')
                ->has('accounts', 1)
            );
    }
}
