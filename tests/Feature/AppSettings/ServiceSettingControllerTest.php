<?php

namespace Tests\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\RequestCategory;
use App\Models\ServiceSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceSettingControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $tenantId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function authenticateUser(): User
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);
        $this->tenantId = $tenant->id;
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_guests_cannot_update_service_settings(): void
    {
        $response = $this->post(route('app-settings.service-settings.update-or-create'), [
            'rf_category_id' => 1,
            'permissions' => ['attachments_required' => true],
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_service_settings(): void
    {
        $this->authenticateUser();
        $category = RequestCategory::factory()->create();

        $payload = [
            'rf_category_id' => $category->id,
            'permissions' => [
                'manager_close_Request' => true,
                'not_require_professional_enter_request_code' => true,
                'not_require_professional_upload_request_photo' => false,
                'attachments_required' => true,
                'allow_professional_reschedule' => true,
            ],
        ];

        $response = $this->post(route('app-settings.service-settings.update-or-create'), $payload);

        $response->assertRedirect(route('app-settings.request-categories.edit', $category));
        $this->assertDatabaseHas('rf_service_settings', ['category_id' => $category->id]);

        $setting = ServiceSetting::where('category_id', $category->id)->first();
        $this->assertNotNull($setting);
        $this->assertTrue($setting->permissions['attachments_required']);
        $this->assertTrue($setting->permissions['allow_professional_reschedule']);
    }

    public function test_authenticated_user_updates_existing_service_setting_without_duplicates(): void
    {
        $this->authenticateUser();
        $category = RequestCategory::factory()->create();

        $setting = ServiceSetting::factory()->create([
            'category_id' => $category->id,
            'account_tenant_id' => $this->tenantId,
            'permissions' => ['attachments_required' => false],
        ]);

        $response = $this->post(route('app-settings.service-settings.update-or-create'), [
            'rf_category_id' => $category->id,
            'permissions' => [
                'manager_close_Request' => false,
                'not_require_professional_enter_request_code' => false,
                'not_require_professional_upload_request_photo' => true,
                'attachments_required' => true,
                'allow_professional_reschedule' => false,
            ],
        ]);

        $response->assertRedirect(route('app-settings.request-categories.edit', $category));
        $this->assertDatabaseCount('rf_service_settings', 1);

        $setting->refresh();

        $this->assertTrue($setting->permissions['attachments_required']);
        $this->assertTrue($setting->permissions['not_require_professional_upload_request_photo']);
    }

    public function test_update_service_settings_requires_category_and_permissions(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('app-settings.service-settings.update-or-create'), []);

        $response->assertSessionHasErrors(['rf_category_id', 'permissions']);
    }
}
