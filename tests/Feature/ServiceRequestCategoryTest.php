<?php

namespace Tests\Feature;

use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceRequestCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_category(): void
    {
        $category = ServiceRequestCategory::factory()->create([
            'name' => 'Test Category',
            'active' => true,
        ]);

        $this->assertDatabaseHas('service_request_categories', [
            'name' => 'Test Category',
            'active' => true,
        ]);

        $this->assertTrue($category->isActive());
    }

    public function test_category_has_subcategories_relationship(): void
    {
        $category = ServiceRequestCategory::factory()->withSubcategories()->create();
        $subcategories = ServiceRequestSubcategory::factory()->count(3)->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(3, $category->subcategories);
        $this->assertEquals($subcategories->pluck('id')->sort()->values(), $category->subcategories->pluck('id')->sort()->values());
    }

    public function test_active_scope_returns_only_active_categories(): void
    {
        $initialCount = ServiceRequestCategory::active()->count();

        ServiceRequestCategory::factory()->create(['active' => true]);
        ServiceRequestCategory::factory()->create(['active' => true]);
        ServiceRequestCategory::factory()->inactive()->create();

        $activeCategories = ServiceRequestCategory::active()->get();

        $this->assertCount($initialCount + 2, $activeCategories);
        $this->assertTrue($activeCategories->every(fn ($category) => $category->active === true));
    }

    public function test_inactive_scope_returns_only_inactive_categories(): void
    {
        ServiceRequestCategory::factory()->create(['active' => true]);
        ServiceRequestCategory::factory()->inactive()->create();
        ServiceRequestCategory::factory()->inactive()->create();

        $inactiveCategories = ServiceRequestCategory::inactive()->get();

        $this->assertCount(2, $inactiveCategories);
        $this->assertTrue($inactiveCategories->every(fn ($category) => $category->active === false));
    }

    public function test_with_subcategories_scope(): void
    {
        $initialCount = ServiceRequestCategory::withSubcategories()->count();

        ServiceRequestCategory::factory()->withSubcategories()->create();
        ServiceRequestCategory::factory()->withSubcategories()->create();
        ServiceRequestCategory::factory()->create(['has_sub_categories' => false]);

        $categoriesWithSubcategories = ServiceRequestCategory::withSubcategories()->get();

        $this->assertCount($initialCount + 2, $categoriesWithSubcategories);
        $this->assertTrue($categoriesWithSubcategories->every(fn ($category) => $category->has_sub_categories === true));
    }

    public function test_without_subcategories_scope(): void
    {
        $initialCount = ServiceRequestCategory::withoutSubcategories()->count();

        ServiceRequestCategory::factory()->withSubcategories()->create();
        ServiceRequestCategory::factory()->create(['has_sub_categories' => false]);
        ServiceRequestCategory::factory()->create(['has_sub_categories' => false]);

        $categoriesWithoutSubcategories = ServiceRequestCategory::withoutSubcategories()->get();

        $this->assertCount($initialCount + 2, $categoriesWithoutSubcategories);
        $this->assertTrue($categoriesWithoutSubcategories->every(fn ($category) => $category->has_sub_categories === false));
    }

    public function test_has_subcategories_helper_method(): void
    {
        $categoryWithSub = ServiceRequestCategory::factory()->withSubcategories()->create();
        $categoryWithoutSub = ServiceRequestCategory::factory()->create(['has_sub_categories' => false]);

        $this->assertTrue($categoryWithSub->hasSubcategories());
        $this->assertFalse($categoryWithoutSub->hasSubcategories());
    }

    public function test_get_service_setting_returns_correct_value(): void
    {
        $category = ServiceRequestCategory::factory()->create([
            'service_settings' => [
                'permissions' => [
                    'manager_close_Request' => true,
                    'attachments_required' => false,
                ],
            ],
        ]);

        $this->assertTrue($category->getServiceSetting('permissions.manager_close_Request'));
        $this->assertFalse($category->getServiceSetting('permissions.attachments_required'));
    }

    public function test_get_service_setting_returns_default_for_missing_key(): void
    {
        $category = ServiceRequestCategory::factory()->create();

        $this->assertNull($category->getServiceSetting('non_existent_key'));
        $this->assertEquals('default_value', $category->getServiceSetting('non_existent_key', 'default_value'));
    }

    public function test_has_permission_checks_permission_correctly(): void
    {
        $category = ServiceRequestCategory::factory()->managerCanClose()->create();

        $this->assertTrue($category->hasPermission('manager_close_Request'));
        $this->assertFalse($category->hasPermission('attachments_required'));
    }

    public function test_has_visibility_setting_checks_visibility_correctly(): void
    {
        $category = ServiceRequestCategory::factory()->create([
            'service_settings' => [
                'visibilities' => [
                    'hide_resident_number' => true,
                    'hide_resident_name' => false,
                ],
            ],
        ]);

        $this->assertTrue($category->hasVisibilitySetting('hide_resident_number'));
        $this->assertFalse($category->hasVisibilitySetting('hide_resident_name'));
    }

    public function test_soft_deletes_work_correctly(): void
    {
        $initialCount = ServiceRequestCategory::count();
        $initialTrashedCount = ServiceRequestCategory::onlyTrashed()->count();

        $category = ServiceRequestCategory::factory()->create();
        $categoryId = $category->id;

        $category->delete();

        $this->assertSoftDeleted('service_request_categories', ['id' => $categoryId]);
        $this->assertCount($initialCount, ServiceRequestCategory::all());
        $this->assertCount($initialTrashedCount + 1, ServiceRequestCategory::onlyTrashed()->get());
    }

    public function test_active_subcategories_relationship(): void
    {
        $category = ServiceRequestCategory::factory()->withSubcategories()->create();
        ServiceRequestSubcategory::factory()->count(2)->create([
            'category_id' => $category->id,
            'active' => true,
        ]);
        ServiceRequestSubcategory::factory()->inactive()->create([
            'category_id' => $category->id,
        ]);

        $this->assertCount(2, $category->activeSubcategories);
        $this->assertTrue($category->activeSubcategories->every(fn ($sub) => $sub->active === true));
    }

    public function test_unit_services_factory_state(): void
    {
        $category = ServiceRequestCategory::factory()->unitServices()->create();

        $this->assertEquals('Unit Services', $category->name);
        $this->assertEquals('خدمات الوحدات', $category->name_ar);
        $this->assertTrue($category->has_sub_categories);
    }

    public function test_common_area_requests_factory_state(): void
    {
        $category = ServiceRequestCategory::factory()->commonAreaRequests()->create();

        $this->assertEquals('Common Area Requests', $category->name);
        $this->assertEquals('طلبات المناطق المشتركة', $category->name_ar);
        $this->assertTrue($category->has_sub_categories);
    }

    public function test_visitor_access_requests_factory_state(): void
    {
        $category = ServiceRequestCategory::factory()->visitorAccessRequests()->create();

        $this->assertEquals('Visitor Access Requests', $category->name);
        $this->assertEquals('طلبات تصاريح الزوار', $category->name_ar);
        $this->assertFalse($category->has_sub_categories);
    }
}
