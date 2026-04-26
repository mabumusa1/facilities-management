<?php

namespace Tests\Feature\ServiceRequests;

use App\Enums\RolesEnum;
use App\Models\AccountMembership;
use App\Models\Community;
use App\Models\ServiceCategory;
use App\Models\ServiceSubcategory;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ServiceCategoryConfigTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RbacSeeder::class);

        $this->adminUser = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'SR Category Test Account']);

        AccountMembership::create([
            'user_id' => $this->adminUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->adminUser->assignRole(RolesEnum::ACCOUNT_ADMINS->value);
        $this->tenant->makeCurrent();
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get(route('services.categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_service_categories_index(): void
    {
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'name_en' => 'Plumbing',
            'name_ar' => 'سباكة',
            'response_sla_hours' => 4,
            'resolution_sla_hours' => 24,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('services.categories.index'));

        $response->assertOk();
        $response->assertInertia(
            fn ($page) => $page
                ->component('services/categories/Index')
                ->has('categories', 1)
                ->where('categories.0.name_en', 'Plumbing')
                ->where('categories.0.response_sla_hours', 4)
                ->where('categories.0.resolution_sla_hours', 24)
        );
    }

    public function test_unprivileged_user_cannot_view_categories(): void
    {
        $unprivilegedUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'tenants',
        ]);

        $response = $this
            ->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->get(route('services.categories.index'));

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Store category
    // -------------------------------------------------------------------------

    public function test_admin_can_create_service_category(): void
    {
        $community = Community::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('services.categories.store'), [
                'name_en' => 'Electrical',
                'name_ar' => 'كهرباء',
                'icon' => '💡',
                'response_sla_hours' => 2,
                'resolution_sla_hours' => 12,
                'require_completion_photo' => false,
                'status' => 'active',
                'community_ids' => [$community->id],
            ]);

        $response->assertRedirect(route('services.categories.index'));

        $this->assertDatabaseHas('service_categories', [
            'name_en' => 'Electrical',
            'name_ar' => 'كهرباء',
            'icon' => '💡',
            'response_sla_hours' => 2,
            'resolution_sla_hours' => 12,
            'status' => 'active',
            'account_tenant_id' => $this->tenant->id,
        ]);

        $category = ServiceCategory::where('name_en', 'Electrical')->first();
        $this->assertNotNull($category);
        $this->assertDatabaseHas('service_category_communities', [
            'service_category_id' => $category->id,
            'community_id' => $community->id,
        ]);
    }

    public function test_create_requires_name_en(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('services.categories.store'), [
                'name_en' => '',
                'name_ar' => 'اختبار',
                'icon' => '🔧',
                'response_sla_hours' => 4,
                'resolution_sla_hours' => 24,
                'status' => 'active',
                'community_ids' => [],
            ]);

        $response->assertSessionHasErrors('name_en');
    }

    public function test_create_requires_at_least_one_community(): void
    {
        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('services.categories.store'), [
                'name_en' => 'Test',
                'name_ar' => 'اختبار',
                'icon' => '🔧',
                'response_sla_hours' => 4,
                'resolution_sla_hours' => 24,
                'status' => 'active',
                'community_ids' => [],
            ]);

        $response->assertSessionHasErrors('community_ids');
    }

    public function test_unprivileged_user_cannot_create_category(): void
    {
        $unprivilegedUser = User::factory()->create();
        AccountMembership::create([
            'user_id' => $unprivilegedUser->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'tenants',
        ]);

        $community = Community::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this
            ->actingAs($unprivilegedUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('services.categories.store'), [
                'name_en' => 'Unauthorized',
                'name_ar' => 'غير مصرح',
                'icon' => '🔧',
                'response_sla_hours' => 4,
                'resolution_sla_hours' => 24,
                'status' => 'active',
                'community_ids' => [$community->id],
            ]);

        $response->assertForbidden();
    }

    // -------------------------------------------------------------------------
    // Update category
    // -------------------------------------------------------------------------

    public function test_admin_can_update_service_category(): void
    {
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'name_en' => 'Old Name',
        ]);

        $community = Community::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->put(route('services.categories.update', $category), [
                'name_en' => 'Updated Name',
                'name_ar' => 'اسم محدث',
                'icon' => '🔧',
                'response_sla_hours' => 8,
                'resolution_sla_hours' => 48,
                'require_completion_photo' => true,
                'status' => 'active',
                'community_ids' => [$community->id],
            ]);

        $response->assertRedirect(route('services.categories.index'));

        $this->assertDatabaseHas('service_categories', [
            'id' => $category->id,
            'name_en' => 'Updated Name',
            'response_sla_hours' => 8,
            'require_completion_photo' => true,
        ]);
    }

    // -------------------------------------------------------------------------
    // Toggle status
    // -------------------------------------------------------------------------

    public function test_admin_can_toggle_category_status(): void
    {
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'status' => 'active',
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('services.categories.toggle-status', $category));

        $response->assertRedirect(route('services.categories.index'));

        $this->assertDatabaseHas('service_categories', [
            'id' => $category->id,
            'status' => 'inactive',
        ]);
    }

    // -------------------------------------------------------------------------
    // Subcategory
    // -------------------------------------------------------------------------

    public function test_admin_can_create_subcategory_with_inherited_sla(): void
    {
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'response_sla_hours' => 4,
            'resolution_sla_hours' => 24,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('services.categories.subcategories.store', $category), [
                'name_en' => 'Water Leak',
                'name_ar' => 'تسرب مياه',
                'response_sla_hours' => null,
                'resolution_sla_hours' => null,
                'status' => 'active',
            ]);

        $response->assertRedirect(route('services.categories.index'));

        $this->assertDatabaseHas('service_subcategories', [
            'service_category_id' => $category->id,
            'name_en' => 'Water Leak',
            'response_sla_hours' => null,
            'resolution_sla_hours' => null,
        ]);

        // Verify SLA inheritance
        $subcategory = ServiceSubcategory::where('name_en', 'Water Leak')->firstOrFail();
        $subcategory->load('serviceCategory');
        $this->assertSame(4, $subcategory->resolvedResponseSlaHours());
        $this->assertSame(24, $subcategory->resolvedResolutionSlaHours());
    }

    public function test_admin_can_create_subcategory_with_custom_sla(): void
    {
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
            'response_sla_hours' => 4,
            'resolution_sla_hours' => 24,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->post(route('services.categories.subcategories.store', $category), [
                'name_en' => 'Pipe Burst',
                'name_ar' => 'انفجار أنبوب',
                'response_sla_hours' => 1,
                'resolution_sla_hours' => 6,
                'status' => 'active',
            ]);

        $response->assertRedirect(route('services.categories.index'));

        $this->assertDatabaseHas('service_subcategories', [
            'service_category_id' => $category->id,
            'name_en' => 'Pipe Burst',
            'response_sla_hours' => 1,
            'resolution_sla_hours' => 6,
        ]);
    }

    public function test_admin_can_delete_subcategory(): void
    {
        $category = ServiceCategory::factory()->create([
            'account_tenant_id' => $this->tenant->id,
        ]);

        $subcategory = ServiceSubcategory::factory()->create([
            'service_category_id' => $category->id,
        ]);

        $response = $this
            ->actingAs($this->adminUser)
            ->withSession(['tenant_id' => $this->tenant->id])
            ->delete(route('services.categories.subcategories.destroy', [$category, $subcategory]));

        $response->assertRedirect(route('services.categories.index'));

        $this->assertDatabaseMissing('service_subcategories', ['id' => $subcategory->id]);
    }
}
