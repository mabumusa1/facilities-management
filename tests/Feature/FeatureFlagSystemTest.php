<?php

namespace Tests\Feature;

use App\Models\FeatureFlag;
use App\Models\Tenant;
use Database\Seeders\FeatureFlagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureFlagSystemTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // FeatureFlag Model Tests
    // ==========================================

    public function test_feature_flag_has_correct_fillable_attributes(): void
    {
        $flag = FeatureFlag::factory()->create([
            'key' => 'TEST_FEATURE',
            'name' => 'Test Feature',
            'name_ar' => 'ميزة الاختبار',
            'description' => 'A test feature flag',
            'category' => FeatureFlag::CATEGORY_CONTACTS,
            'default_value' => true,
            'is_active' => true,
        ]);

        $this->assertEquals('TEST_FEATURE', $flag->key);
        $this->assertEquals('Test Feature', $flag->name);
        $this->assertEquals('ميزة الاختبار', $flag->name_ar);
        $this->assertEquals('A test feature flag', $flag->description);
        $this->assertEquals('contacts', $flag->category);
        $this->assertTrue($flag->default_value);
        $this->assertTrue($flag->is_active);
    }

    public function test_feature_flag_casts_boolean_attributes(): void
    {
        $flag = FeatureFlag::factory()->create([
            'default_value' => 1,
            'is_active' => 0,
        ]);

        $this->assertIsBool($flag->default_value);
        $this->assertIsBool($flag->is_active);
        $this->assertTrue($flag->default_value);
        $this->assertFalse($flag->is_active);
    }

    public function test_feature_flag_has_category_constants(): void
    {
        $this->assertEquals('contacts', FeatureFlag::CATEGORY_CONTACTS);
        $this->assertEquals('properties', FeatureFlag::CATEGORY_PROPERTIES);
        $this->assertEquals('leasing', FeatureFlag::CATEGORY_LEASING);
        $this->assertEquals('transactions', FeatureFlag::CATEGORY_TRANSACTIONS);
        $this->assertEquals('requests', FeatureFlag::CATEGORY_REQUESTS);
        $this->assertEquals('communication', FeatureFlag::CATEGORY_COMMUNICATION);
        $this->assertEquals('reports', FeatureFlag::CATEGORY_REPORTS);
        $this->assertEquals('tools', FeatureFlag::CATEGORY_TOOLS);
        $this->assertEquals('integrations', FeatureFlag::CATEGORY_INTEGRATIONS);
        $this->assertEquals('marketplace', FeatureFlag::CATEGORY_MARKETPLACE);
    }

    public function test_feature_flag_categories_method_returns_all_categories(): void
    {
        $categories = FeatureFlag::categories();

        $this->assertIsArray($categories);
        $this->assertCount(10, $categories);
        $this->assertContains('contacts', $categories);
        $this->assertContains('properties', $categories);
        $this->assertContains('leasing', $categories);
        $this->assertContains('transactions', $categories);
        $this->assertContains('requests', $categories);
        $this->assertContains('communication', $categories);
        $this->assertContains('reports', $categories);
        $this->assertContains('tools', $categories);
        $this->assertContains('integrations', $categories);
        $this->assertContains('marketplace', $categories);
    }

    public function test_feature_flag_find_by_key_returns_feature(): void
    {
        $flag = FeatureFlag::factory()->create(['key' => 'ENABLE_TENANTS']);

        $found = FeatureFlag::findByKey('ENABLE_TENANTS');

        $this->assertNotNull($found);
        $this->assertEquals($flag->id, $found->id);
    }

    public function test_feature_flag_find_by_key_returns_null_for_nonexistent(): void
    {
        $found = FeatureFlag::findByKey('NONEXISTENT_KEY');

        $this->assertNull($found);
    }

    // ==========================================
    // FeatureFlag Scope Tests
    // ==========================================

    public function test_feature_flag_active_scope(): void
    {
        FeatureFlag::factory()->count(3)->create(['is_active' => true]);
        FeatureFlag::factory()->count(2)->create(['is_active' => false]);

        $activeFlags = FeatureFlag::active()->get();

        $this->assertCount(3, $activeFlags);
    }

    public function test_feature_flag_for_category_scope(): void
    {
        FeatureFlag::factory()->count(3)->forCategory('contacts')->create();
        FeatureFlag::factory()->count(2)->forCategory('leasing')->create();

        $contactsFlags = FeatureFlag::forCategory('contacts')->get();
        $leasingFlags = FeatureFlag::forCategory('leasing')->get();

        $this->assertCount(3, $contactsFlags);
        $this->assertCount(2, $leasingFlags);
    }

    public function test_feature_flag_enabled_by_default_scope(): void
    {
        FeatureFlag::factory()->count(3)->enabledByDefault()->create();
        FeatureFlag::factory()->count(2)->disabledByDefault()->create();

        $enabledByDefault = FeatureFlag::enabledByDefault()->get();

        $this->assertCount(3, $enabledByDefault);
    }

    // ==========================================
    // FeatureFlag Tenant Relationship Tests
    // ==========================================

    public function test_feature_flag_belongs_to_many_tenants(): void
    {
        $flag = FeatureFlag::factory()->create();
        $tenant = Tenant::factory()->create();

        $flag->tenants()->attach($tenant->id, ['is_enabled' => true]);

        $this->assertCount(1, $flag->tenants);
        $this->assertEquals($tenant->id, $flag->tenants->first()->id);
        $this->assertEquals(1, $flag->tenants->first()->pivot->is_enabled);
    }

    public function test_feature_flag_is_enabled_for_tenant_when_override_exists(): void
    {
        $flag = FeatureFlag::factory()->create([
            'default_value' => false,
            'is_active' => true,
        ]);
        $tenant = Tenant::factory()->create();

        // Override to enabled
        $flag->tenants()->attach($tenant->id, ['is_enabled' => true]);

        $this->assertTrue($flag->isEnabledForTenant($tenant));
    }

    public function test_feature_flag_is_disabled_for_tenant_when_override_exists(): void
    {
        $flag = FeatureFlag::factory()->create([
            'default_value' => true,
            'is_active' => true,
        ]);
        $tenant = Tenant::factory()->create();

        // Override to disabled
        $flag->tenants()->attach($tenant->id, ['is_enabled' => false]);

        $this->assertFalse($flag->isEnabledForTenant($tenant));
    }

    public function test_feature_flag_uses_default_value_when_no_override(): void
    {
        $enabledFlag = FeatureFlag::factory()->create([
            'default_value' => true,
            'is_active' => true,
        ]);
        $disabledFlag = FeatureFlag::factory()->create([
            'default_value' => false,
            'is_active' => true,
        ]);
        $tenant = Tenant::factory()->create();

        $this->assertTrue($enabledFlag->isEnabledForTenant($tenant));
        $this->assertFalse($disabledFlag->isEnabledForTenant($tenant));
    }

    public function test_feature_flag_returns_default_value_when_null_tenant(): void
    {
        $enabledFlag = FeatureFlag::factory()->create([
            'default_value' => true,
            'is_active' => true,
        ]);
        $disabledFlag = FeatureFlag::factory()->create([
            'default_value' => false,
            'is_active' => true,
        ]);

        $this->assertTrue($enabledFlag->isEnabledForTenant(null));
        $this->assertFalse($disabledFlag->isEnabledForTenant(null));
    }

    public function test_feature_flag_returns_false_when_inactive(): void
    {
        $flag = FeatureFlag::factory()->create([
            'default_value' => true,
            'is_active' => false,
        ]);
        $tenant = Tenant::factory()->create();

        $this->assertFalse($flag->isEnabledForTenant($tenant));
        $this->assertFalse($flag->isEnabledForTenant(null));
    }

    // ==========================================
    // Tenant Feature Flag Relationship Tests
    // ==========================================

    public function test_tenant_has_feature_flags_relationship(): void
    {
        $tenant = Tenant::factory()->create();
        $flag = FeatureFlag::factory()->create();

        $tenant->featureFlags()->attach($flag->id, ['is_enabled' => true]);

        $this->assertCount(1, $tenant->featureFlags);
        $this->assertEquals($flag->id, $tenant->featureFlags->first()->id);
    }

    public function test_tenant_has_feature_returns_true_when_enabled(): void
    {
        $tenant = Tenant::factory()->create();
        $flag = FeatureFlag::factory()->create([
            'key' => 'ENABLE_TENANTS',
            'default_value' => true,
            'is_active' => true,
        ]);

        $this->assertTrue($tenant->hasFeature('ENABLE_TENANTS'));
    }

    public function test_tenant_has_feature_returns_false_when_disabled(): void
    {
        $tenant = Tenant::factory()->create();
        $flag = FeatureFlag::factory()->create([
            'key' => 'ENABLE_FEATURE',
            'default_value' => false,
            'is_active' => true,
        ]);

        $this->assertFalse($tenant->hasFeature('ENABLE_FEATURE'));
    }

    public function test_tenant_has_feature_returns_false_for_nonexistent_key(): void
    {
        $tenant = Tenant::factory()->create();

        $this->assertFalse($tenant->hasFeature('NONEXISTENT_KEY'));
    }

    public function test_tenant_enable_feature_creates_override(): void
    {
        $tenant = Tenant::factory()->create();
        $flag = FeatureFlag::factory()->create([
            'key' => 'ENABLE_FEATURE',
            'default_value' => false,
            'is_active' => true,
        ]);

        $result = $tenant->enableFeature('ENABLE_FEATURE');

        $this->assertTrue($result);
        $this->assertTrue($tenant->hasFeature('ENABLE_FEATURE'));
    }

    public function test_tenant_enable_feature_returns_false_for_nonexistent_key(): void
    {
        $tenant = Tenant::factory()->create();

        $result = $tenant->enableFeature('NONEXISTENT_KEY');

        $this->assertFalse($result);
    }

    public function test_tenant_disable_feature_creates_override(): void
    {
        $tenant = Tenant::factory()->create();
        $flag = FeatureFlag::factory()->create([
            'key' => 'ENABLE_FEATURE',
            'default_value' => true,
            'is_active' => true,
        ]);

        $result = $tenant->disableFeature('ENABLE_FEATURE');

        $this->assertTrue($result);
        $this->assertFalse($tenant->hasFeature('ENABLE_FEATURE'));
    }

    public function test_tenant_disable_feature_returns_false_for_nonexistent_key(): void
    {
        $tenant = Tenant::factory()->create();

        $result = $tenant->disableFeature('NONEXISTENT_KEY');

        $this->assertFalse($result);
    }

    public function test_tenant_can_toggle_feature_multiple_times(): void
    {
        $tenant = Tenant::factory()->create();
        $flag = FeatureFlag::factory()->create([
            'key' => 'ENABLE_FEATURE',
            'default_value' => false,
            'is_active' => true,
        ]);

        // Initially disabled by default
        $this->assertFalse($tenant->hasFeature('ENABLE_FEATURE'));

        // Enable
        $tenant->enableFeature('ENABLE_FEATURE');
        $this->assertTrue($tenant->hasFeature('ENABLE_FEATURE'));

        // Disable
        $tenant->disableFeature('ENABLE_FEATURE');
        $this->assertFalse($tenant->hasFeature('ENABLE_FEATURE'));

        // Enable again
        $tenant->enableFeature('ENABLE_FEATURE');
        $this->assertTrue($tenant->hasFeature('ENABLE_FEATURE'));
    }

    // ==========================================
    // Multi-tenant Isolation Tests
    // ==========================================

    public function test_feature_flag_overrides_are_tenant_isolated(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        $flag = FeatureFlag::factory()->create([
            'key' => 'ENABLE_FEATURE',
            'default_value' => false,
            'is_active' => true,
        ]);

        // Enable for tenant1 only
        $tenant1->enableFeature('ENABLE_FEATURE');

        $this->assertTrue($tenant1->hasFeature('ENABLE_FEATURE'));
        $this->assertFalse($tenant2->hasFeature('ENABLE_FEATURE'));
    }

    public function test_different_tenants_can_have_different_feature_settings(): void
    {
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();
        $tenant3 = Tenant::factory()->create();

        $flag = FeatureFlag::factory()->create([
            'key' => 'ENABLE_FEATURE',
            'default_value' => true,
            'is_active' => true,
        ]);

        // Tenant1 uses default (enabled)
        // Tenant2 explicitly disables
        // Tenant3 explicitly enables
        $tenant2->disableFeature('ENABLE_FEATURE');
        $tenant3->enableFeature('ENABLE_FEATURE');

        $this->assertTrue($tenant1->hasFeature('ENABLE_FEATURE')); // default
        $this->assertFalse($tenant2->hasFeature('ENABLE_FEATURE')); // override
        $this->assertTrue($tenant3->hasFeature('ENABLE_FEATURE')); // override
    }

    // ==========================================
    // Factory State Tests
    // ==========================================

    public function test_factory_creates_valid_feature_flag(): void
    {
        $flag = FeatureFlag::factory()->create();

        $this->assertNotNull($flag->id);
        $this->assertNotEmpty($flag->key);
        $this->assertNotEmpty($flag->name);
        $this->assertContains($flag->category, FeatureFlag::categories());
        $this->assertIsBool($flag->default_value);
        $this->assertTrue($flag->is_active);
    }

    public function test_factory_for_category_state(): void
    {
        $flag = FeatureFlag::factory()->forCategory('leasing')->create();

        $this->assertEquals('leasing', $flag->category);
    }

    public function test_factory_inactive_state(): void
    {
        $flag = FeatureFlag::factory()->inactive()->create();

        $this->assertFalse($flag->is_active);
    }

    public function test_factory_enabled_by_default_state(): void
    {
        $flag = FeatureFlag::factory()->enabledByDefault()->create();

        $this->assertTrue($flag->default_value);
    }

    public function test_factory_disabled_by_default_state(): void
    {
        $flag = FeatureFlag::factory()->disabledByDefault()->create();

        $this->assertFalse($flag->default_value);
    }

    public function test_factory_with_key_state(): void
    {
        $flag = FeatureFlag::factory()->withKey('CUSTOM_KEY')->create();

        $this->assertEquals('CUSTOM_KEY', $flag->key);
    }

    // ==========================================
    // Seeder Tests
    // ==========================================

    public function test_seeder_creates_feature_flags(): void
    {
        $this->seed(FeatureFlagSeeder::class);

        $this->assertGreaterThan(40, FeatureFlag::count());
    }

    public function test_seeder_creates_flags_for_all_categories(): void
    {
        $this->seed(FeatureFlagSeeder::class);

        // Check contacts category
        $this->assertGreaterThan(0, FeatureFlag::forCategory('contacts')->count());

        // Check properties category
        $this->assertGreaterThan(0, FeatureFlag::forCategory('properties')->count());

        // Check leasing category
        $this->assertGreaterThan(0, FeatureFlag::forCategory('leasing')->count());

        // Check transactions category
        $this->assertGreaterThan(0, FeatureFlag::forCategory('transactions')->count());

        // Check requests category
        $this->assertGreaterThan(0, FeatureFlag::forCategory('requests')->count());

        // Check communication category
        $this->assertGreaterThan(0, FeatureFlag::forCategory('communication')->count());

        // Check reports category
        $this->assertGreaterThan(0, FeatureFlag::forCategory('reports')->count());

        // Check tools category
        $this->assertGreaterThan(0, FeatureFlag::forCategory('tools')->count());
    }

    public function test_seeder_creates_specific_feature_flags(): void
    {
        $this->seed(FeatureFlagSeeder::class);

        // Contacts
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_ADMIN'));
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_TENANTS'));
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_OWNERS'));

        // Properties
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_FACILITIES'));

        // Leasing
        $this->assertNotNull(FeatureFlag::findByKey('CREATE_LEASES'));
        $this->assertNotNull(FeatureFlag::findByKey('INTEGRATE_WITH_EJAR'));

        // Transactions
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_ONLINE_PAYMENT'));
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_E_INVOICE'));

        // Communication
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_PUSH_NOTIFICATION'));
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_WHATSAPP_BUSINESS'));

        // Reports
        $this->assertNotNull(FeatureFlag::findByKey('ENABLE_DASHBOARD'));
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed(FeatureFlagSeeder::class);
        $firstCount = FeatureFlag::count();

        $this->seed(FeatureFlagSeeder::class);
        $secondCount = FeatureFlag::count();

        $this->assertEquals($firstCount, $secondCount);
    }

    public function test_seeded_flags_have_arabic_names(): void
    {
        $this->seed(FeatureFlagSeeder::class);

        $flag = FeatureFlag::findByKey('ENABLE_ADMIN');

        $this->assertNotNull($flag->name_ar);
        $this->assertEquals('تفعيل إدارة المشرفين', $flag->name_ar);
    }

    public function test_seeded_flags_are_all_active(): void
    {
        $this->seed(FeatureFlagSeeder::class);

        $inactiveCount = FeatureFlag::where('is_active', false)->count();

        $this->assertEquals(0, $inactiveCount);
    }
}
