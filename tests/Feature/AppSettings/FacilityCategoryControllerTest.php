<?php

namespace Tests\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\FacilityCategory;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityCategoryControllerTest extends TestCase
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

    public function test_guests_cannot_access_facility_categories(): void
    {
        $response = $this->get(route('app-settings.facility-categories.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_facility_categories(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('app-settings.facility-categories.index'));

        $response->assertOk();
    }

    public function test_authenticated_user_can_create_facility_category(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('app-settings.facility-categories.store'), [
            'name_ar' => 'مسبح',
            'name_en' => 'Swimming Pool',
            'status' => true,
        ]);

        $response->assertRedirect(route('app-settings.facility-categories.index'));
        $this->assertDatabaseHas('rf_facility_categories', [
            'name_en' => 'Swimming Pool',
            'name_ar' => 'مسبح',
        ]);
    }

    public function test_authenticated_user_can_update_facility_category(): void
    {
        $this->authenticateUser();
        $category = FacilityCategory::factory()->create();

        $response = $this->put(route('app-settings.facility-categories.update', $category), [
            'name_ar' => 'محدث',
            'name_en' => 'Updated',
            'status' => false,
        ]);

        $response->assertRedirect(route('app-settings.facility-categories.index'));
        $this->assertDatabaseHas('rf_facility_categories', [
            'id' => $category->id,
            'name_en' => 'Updated',
        ]);
    }

    public function test_authenticated_user_can_delete_facility_category(): void
    {
        $this->authenticateUser();
        $category = FacilityCategory::factory()->create();

        $response = $this->delete(route('app-settings.facility-categories.destroy', $category));

        $response->assertRedirect(route('app-settings.facility-categories.index'));
        $this->assertDatabaseMissing('rf_facility_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_create_facility_category_requires_name(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('app-settings.facility-categories.store'), [
            'name_ar' => 'مسبح',
            'status' => true,
        ]);

        $response->assertSessionHasErrors(['name_en']);
    }
}
