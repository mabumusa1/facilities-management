<?php

namespace Tests\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\RequestCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RequestCategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_guests_cannot_access_request_categories(): void
    {
        $response = $this->get(route('app-settings.request-categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_request_categories(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('app-settings.request-categories.index'));

        $response->assertOk();
    }

    public function test_authenticated_user_can_create_request_category(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('app-settings.request-categories.store'), [
            'name_ar' => 'صيانة',
            'name_en' => 'Maintenance',
            'status' => true,
            'has_sub_categories' => false,
        ]);

        $response->assertRedirect(route('app-settings.request-categories.index'));
        $this->assertDatabaseHas('rf_request_categories', [
            'name_en' => 'Maintenance',
            'name_ar' => 'صيانة',
        ]);
    }

    public function test_authenticated_user_can_update_request_category(): void
    {
        $this->authenticateUser();
        $category = RequestCategory::factory()->create();

        $response = $this->put(route('app-settings.request-categories.update', $category), [
            'name_ar' => 'محدث',
            'name_en' => 'Updated',
            'status' => false,
            'has_sub_categories' => true,
        ]);

        $response->assertRedirect(route('app-settings.request-categories.index'));
        $this->assertDatabaseHas('rf_request_categories', [
            'id' => $category->id,
            'name_en' => 'Updated',
        ]);
    }

    public function test_authenticated_user_can_delete_request_category(): void
    {
        $this->authenticateUser();
        $category = RequestCategory::factory()->create();

        $response = $this->delete(route('app-settings.request-categories.destroy', $category));

        $response->assertRedirect(route('app-settings.request-categories.index'));
        $this->assertDatabaseMissing('rf_request_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_authenticated_user_can_add_subcategory(): void
    {
        $this->authenticateUser();
        $category = RequestCategory::factory()->create();

        $response = $this->post(route('app-settings.request-categories.subcategories.store', $category), [
            'name_ar' => 'فرعي',
            'name_en' => 'Sub Category',
            'status' => true,
            'is_all_day' => false,
        ]);

        $response->assertRedirect(route('app-settings.request-categories.index'));
        $this->assertDatabaseHas('rf_request_subcategories', [
            'category_id' => $category->id,
            'name_en' => 'Sub Category',
        ]);
    }

    public function test_create_request_category_requires_name(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('app-settings.request-categories.store'), [
            'name_ar' => 'صيانة',
            'status' => true,
        ]);

        $response->assertSessionHasErrors(['name_en']);
    }
}
