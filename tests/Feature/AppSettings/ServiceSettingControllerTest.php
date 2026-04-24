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

    /**
     * Regression: cross-tenant write vulnerability fix (#328 review).
     *
     * Tenant B must NOT overwrite Tenant A's ServiceSetting row for the same
     * category. After both tenants post to the endpoint, the DB must contain
     * exactly two rows — one per tenant.
     */
    public function test_cross_tenant_write_creates_separate_rows_not_overwrite(): void
    {
        $category = RequestCategory::factory()->create();

        $payload = [
            'rf_category_id' => $category->id,
            'permissions' => [
                'manager_close_Request' => true,
                'not_require_professional_enter_request_code' => false,
                'not_require_professional_upload_request_photo' => false,
                'attachments_required' => true,
                'allow_professional_reschedule' => false,
            ],
        ];

        // — Tenant A creates its ServiceSetting —
        $userA = User::factory()->create();
        $tenantA = Tenant::create(['name' => 'Cross-Tenant A']);
        AccountMembership::create([
            'user_id' => $userA->id,
            'account_tenant_id' => $tenantA->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($userA);
        $this->withSession(['tenant_id' => $tenantA->id])
            ->post(route('app-settings.service-settings.update-or-create'), $payload)
            ->assertRedirect();

        Tenant::forgetCurrent();

        $this->assertDatabaseCount('rf_service_settings', 1);
        $rowA = ServiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenantA->id)->first();
        $this->assertNotNull($rowA);
        $this->assertTrue($rowA->permissions['attachments_required']);

        // — Tenant B posts the same category —
        $userB = User::factory()->create();
        $tenantB = Tenant::create(['name' => 'Cross-Tenant B']);
        AccountMembership::create([
            'user_id' => $userB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => 'account_admins',
        ]);

        $payloadB = array_merge($payload, [
            'permissions' => array_merge($payload['permissions'], ['attachments_required' => false]),
        ]);

        // Reset the EnsureValidTenantSession key so the tenant switch is accepted.
        $this->flushSession();

        $this->actingAs($userB);
        $this->withSession(['tenant_id' => $tenantB->id])
            ->post(route('app-settings.service-settings.update-or-create'), $payloadB)
            ->assertRedirect();

        Tenant::forgetCurrent();

        // Guard: two separate rows, NOT one overwritten row
        $this->assertDatabaseCount('rf_service_settings', 2);

        // Tenant A's row is untouched
        $rowA->refresh();
        $this->assertTrue($rowA->permissions['attachments_required'], 'Tenant A\'s row must not be overwritten by Tenant B');
        $this->assertSame($tenantA->id, $rowA->account_tenant_id);

        // Tenant B has its own row
        $rowB = ServiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenantB->id)->first();
        $this->assertNotNull($rowB);
        $this->assertFalse($rowB->permissions['attachments_required']);
        $this->assertSame($tenantB->id, $rowB->account_tenant_id);
    }
}
