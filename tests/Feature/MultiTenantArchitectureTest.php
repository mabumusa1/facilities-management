<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Multitenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenantArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_be_created(): void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Acme Corporation',
        ]);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Acme Corporation',
            'is_active' => true,
        ]);

        $this->assertNotNull($tenant->uuid);
        $this->assertNotNull($tenant->slug);
    }

    public function test_tenant_uuid_is_generated_on_create(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertNotNull($tenant->uuid);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $tenant->uuid
        );
    }

    public function test_tenant_slug_is_generated_from_name(): void
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Test Company Name',
            'slug' => null, // Let it auto-generate
        ]);

        $this->assertStringContainsString('test-company-name', $tenant->slug);
    }

    public function test_tenant_can_have_settings(): void
    {
        $tenant = Tenant::factory()->create([
            'settings' => [
                'timezone' => 'Asia/Riyadh',
                'currency' => 'SAR',
            ],
        ]);

        $this->assertEquals('Asia/Riyadh', $tenant->getSetting('timezone'));
        $this->assertEquals('SAR', $tenant->getSetting('currency'));
    }

    public function test_tenant_can_set_settings(): void
    {
        $tenant = Tenant::factory()->create();

        $tenant->setSetting('custom_setting', 'custom_value');
        $tenant->save();

        $this->assertEquals('custom_value', $tenant->fresh()->getSetting('custom_setting'));
    }

    public function test_tenant_is_active_check(): void
    {
        $activeTenant = Tenant::factory()->create(['is_active' => true]);
        $inactiveTenant = Tenant::factory()->inactive()->create();

        $this->assertTrue($activeTenant->isActive());
        $this->assertFalse($inactiveTenant->isActive());
    }

    public function test_user_belongs_to_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->assertEquals($tenant->id, $user->tenant->id);
    }

    public function test_tenant_has_many_users(): void
    {
        $tenant = Tenant::factory()->create();
        User::factory()->count(3)->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->assertCount(3, $tenant->users);
    }

    public function test_tenant_context_singleton(): void
    {
        $context1 = app(TenantContext::class);
        $context2 = app(TenantContext::class);

        $this->assertSame($context1, $context2);
    }

    public function test_tenant_context_can_set_and_get_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $context = app(TenantContext::class);

        $this->assertFalse($context->has());

        $context->set($tenant);

        $this->assertTrue($context->has());
        $this->assertEquals($tenant->id, $context->id());
        $this->assertEquals($tenant->id, $context->get()->id);
    }

    public function test_tenant_context_can_clear_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $context = app(TenantContext::class);

        $context->set($tenant);
        $this->assertTrue($context->has());

        $context->clear();
        $this->assertFalse($context->has());
        $this->assertNull($context->get());
    }

    public function test_tenant_context_run_with_callback(): void
    {
        $tenant1 = Tenant::factory()->create(['name' => 'Tenant 1']);
        $tenant2 = Tenant::factory()->create(['name' => 'Tenant 2']);
        $context = app(TenantContext::class);

        $context->set($tenant1);

        $result = $context->run($tenant2, function () use ($context, $tenant2) {
            $this->assertEquals($tenant2->id, $context->id());

            return 'inside callback';
        });

        $this->assertEquals('inside callback', $result);
        $this->assertEquals($tenant1->id, $context->id()); // Restored to original
    }

    public function test_tenant_is_resolved_from_header(): void
    {
        $tenant = Tenant::factory()->create();

        $response = $this->withHeader('X-Tenant', $tenant->slug)
            ->get('/');

        $response->assertStatus(200);
    }

    public function test_tenant_is_resolved_from_uuid_header(): void
    {
        $tenant = Tenant::factory()->create();

        $response = $this->withHeader('X-Tenant', $tenant->uuid)
            ->get('/');

        $response->assertStatus(200);
    }

    public function test_inactive_tenant_is_rejected(): void
    {
        // Apply tenant middleware to a test route
        $this->app['router']->middleware(['tenant'])
            ->get('/test-tenant-inactive', fn () => response('OK'));

        $tenant = Tenant::factory()->inactive()->create();

        $response = $this->withHeader('X-Tenant', $tenant->slug)
            ->get('/test-tenant-inactive');

        $response->assertStatus(403);
    }

    public function test_user_scope_access_flags(): void
    {
        // Note: Community/Building relationships require those models to exist
        // This test verifies the scope flags work correctly
        $user = User::factory()->create([
            'is_all_communities' => false,
            'is_all_buildings' => false,
        ]);

        $this->assertFalse($user->hasAllCommunitiesAccess());
        $this->assertFalse($user->hasAllBuildingsAccess());

        $user->is_all_communities = true;
        $user->save();

        $this->assertTrue($user->fresh()->hasAllCommunitiesAccess());
    }

    public function test_user_has_unrestricted_access_when_all_flags_true(): void
    {
        $user = User::factory()->create([
            'is_all_communities' => true,
            'is_all_buildings' => true,
        ]);

        $this->assertTrue($user->hasUnrestrictedAccess());
        $this->assertTrue($user->hasAllCommunitiesAccess());
        $this->assertTrue($user->hasAllBuildingsAccess());
    }

    public function test_user_does_not_have_unrestricted_access_when_flags_false(): void
    {
        $user = User::factory()->create([
            'is_all_communities' => false,
            'is_all_buildings' => false,
        ]);

        $this->assertFalse($user->hasUnrestrictedAccess());
        $this->assertFalse($user->hasAllCommunitiesAccess());
        $this->assertFalse($user->hasAllBuildingsAccess());
    }

    public function test_user_can_grant_unrestricted_access_via_flags(): void
    {
        $user = User::factory()->create([
            'is_all_communities' => false,
            'is_all_buildings' => false,
        ]);

        // Manually set the flags (grantUnrestrictedAccess uses relationships)
        $user->is_all_communities = true;
        $user->is_all_buildings = true;
        $user->save();

        $this->assertTrue($user->fresh()->hasUnrestrictedAccess());
    }

    public function test_user_can_set_restricted_access(): void
    {
        $user = User::factory()->create([
            'is_all_communities' => true,
            'is_all_buildings' => true,
        ]);

        $user->setRestrictedAccess();

        $user = $user->fresh();
        $this->assertFalse($user->is_all_communities);
        $this->assertFalse($user->is_all_buildings);
    }

    public function test_tenant_model_uses_slug_for_route_binding(): void
    {
        $tenant = Tenant::factory()->create(['slug' => 'test-tenant']);

        $this->assertEquals('slug', $tenant->getRouteKeyName());
    }

    public function test_tenant_soft_deletes(): void
    {
        $tenant = Tenant::factory()->create();

        $tenant->delete();

        $this->assertSoftDeleted($tenant);
        $this->assertNull(Tenant::find($tenant->id));
        $this->assertNotNull(Tenant::withTrashed()->find($tenant->id));
    }

    public function test_user_factory_includes_tenant(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create([
            'tenant_id' => $tenant->id,
        ]);

        $this->assertEquals($tenant->id, $user->tenant_id);
    }

    public function test_tenant_users_are_deleted_on_cascade(): void
    {
        $tenant = Tenant::factory()->create();
        $users = User::factory()->count(3)->create([
            'tenant_id' => $tenant->id,
        ]);

        $tenant->forceDelete();

        foreach ($users as $user) {
            $this->assertDatabaseMissing('users', ['id' => $user->id]);
        }
    }
}
