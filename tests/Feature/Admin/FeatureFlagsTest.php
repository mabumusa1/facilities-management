<?php

namespace Tests\Feature\Admin;

use App\Enums\FeatureFlag;
use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\FeatureFlagAuditLog;
use App\Models\FeatureFlagOverride;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FeatureFlagsTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesSeeder::class);
    }

    private function createSuperAdmin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(RolesEnum::ACCOUNT_ADMINS->value);

        return $user;
    }

    private function createTenantAdmin(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Managed Account']);
        $user->assignRole(RolesEnum::ADMINS->value);

        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => RolesEnum::ADMINS->value,
        ]);

        return [$user, $tenant];
    }

    public function test_super_admin_can_view_tenant_detail_page(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);

        $response = $this->actingAs($superAdmin)
            ->get("/admin/subscriptions/{$tenant->id}");

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('admin/subscriptions/Show')
                ->where('tenant.id', $tenant->id)
                ->where('tenant.name', 'Test Tenant')
        );
    }

    public function test_super_admin_can_view_feature_flags_index(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);

        $response = $this->actingAs($superAdmin)
            ->getJson("/admin/subscriptions/{$tenant->id}/features");

        $response->assertOk();
        $response->assertJsonCount(count(FeatureFlag::cases()));
    }

    public function test_feature_flags_index_returns_correct_structure(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);

        $response = $this->actingAs($superAdmin)
            ->getJson("/admin/subscriptions/{$tenant->id}/features");

        $response->assertOk();
        $data = $response->json();

        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        $first = $data[0];
        $this->assertArrayHasKey('key', $first);
        $this->assertArrayHasKey('label_en', $first);
        $this->assertArrayHasKey('label_ar', $first);
        $this->assertArrayHasKey('enabled', $first);
        $this->assertArrayHasKey('in_tier', $first);
        $this->assertArrayHasKey('plan_name', $first);
    }

    public function test_non_super_admin_is_denied_on_tenant_detail(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->get("/admin/subscriptions/{$tenant->id}");

        $response->assertRedirect(route('admin.subscriptions.index'));
    }

    public function test_non_super_admin_is_denied_on_features_index(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->getJson("/admin/subscriptions/{$tenant->id}/features");

        $response->assertForbidden();
    }

    public function test_super_admin_can_enable_a_feature(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);
        $flagKey = FeatureFlag::MarketplaceModule->value;

        $response = $this->actingAs($superAdmin)
            ->patchJson("/admin/subscriptions/{$tenant->id}/features/{$flagKey}", [
                'enabled' => true,
            ]);

        $response->assertOk();
        $response->assertJson([
            'key' => $flagKey,
            'enabled' => true,
        ]);

        $this->assertDatabaseHas('feature_flag_overrides', [
            'account_tenant_id' => $tenant->id,
            'flag_key' => $flagKey,
            'enabled' => true,
        ]);
    }

    public function test_super_admin_can_disable_a_feature(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);
        $flagKey = FeatureFlag::FacilitiesManagement->value;

        $response = $this->actingAs($superAdmin)
            ->patchJson("/admin/subscriptions/{$tenant->id}/features/{$flagKey}", [
                'enabled' => false,
            ]);

        $response->assertOk();
        $response->assertJson([
            'key' => $flagKey,
            'enabled' => false,
        ]);

        $this->assertDatabaseHas('feature_flag_overrides', [
            'account_tenant_id' => $tenant->id,
            'flag_key' => $flagKey,
            'enabled' => false,
        ]);
    }

    public function test_toggle_creates_audit_log_entry(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);
        $flagKey = FeatureFlag::CommunicationHub->value;

        $this->actingAs($superAdmin)
            ->patchJson("/admin/subscriptions/{$tenant->id}/features/{$flagKey}", [
                'enabled' => false,
            ]);

        $this->assertDatabaseHas('feature_flag_audit_logs', [
            'account_tenant_id' => $tenant->id,
            'user_id' => $superAdmin->id,
            'flag_key' => $flagKey,
            'action' => 'disabled',
        ]);
    }

    public function test_toggle_rejects_invalid_flag_key(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);

        $response = $this->actingAs($superAdmin)
            ->patchJson("/admin/subscriptions/{$tenant->id}/features/nonexistent_flag", [
                'enabled' => true,
            ]);

        $response->assertStatus(422);
    }

    public function test_toggle_requires_boolean_enabled(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);
        $flagKey = FeatureFlag::DocumentVault->value;

        $response = $this->actingAs($superAdmin)
            ->patchJson("/admin/subscriptions/{$tenant->id}/features/{$flagKey}", [
                'enabled' => 'not-a-bool',
            ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['enabled']);
    }

    public function test_enable_disable_enable_roundtrip(): void
    {
        $superAdmin = $this->createSuperAdmin();
        $tenant = Tenant::create(['name' => 'Test Tenant']);
        $flagKey = FeatureFlag::ReportsAndAnalytics->value;
        $url = "/admin/subscriptions/{$tenant->id}/features/{$flagKey}";

        $this->actingAs($superAdmin)
            ->patchJson($url, ['enabled' => false])
            ->assertOk()
            ->assertJson(['enabled' => false]);

        $this->actingAs($superAdmin)
            ->patchJson($url, ['enabled' => true])
            ->assertOk()
            ->assertJson(['enabled' => true]);

        $logCount = FeatureFlagAuditLog::query()
            ->where('account_tenant_id', $tenant->id)
            ->where('flag_key', $flagKey)
            ->count();

        $this->assertEquals(2, $logCount);
    }

    public function test_non_super_admin_cannot_toggle_features(): void
    {
        [$admin, $tenant] = $this->createTenantAdmin();
        $flagKey = FeatureFlag::MarketplaceModule->value;

        $response = $this->actingAs($admin)
            ->withSession(['tenant_id' => $tenant->id])
            ->patchJson("/admin/subscriptions/{$tenant->id}/features/{$flagKey}", [
                'enabled' => true,
            ]);

        $response->assertForbidden();
    }

    public function test_nonexistent_tenant_returns_404_on_features(): void
    {
        $superAdmin = $this->createSuperAdmin();

        $response = $this->actingAs($superAdmin)
            ->getJson('/admin/subscriptions/99999/features');

        $response->assertNotFound();
    }
}
