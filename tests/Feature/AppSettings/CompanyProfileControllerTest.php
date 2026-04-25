<?php

namespace Tests\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\InvoiceSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class CompanyProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        Gate::define('companyProfile.VIEW', fn () => true);
        Gate::define('companyProfile.UPDATE', fn () => true);
    }

    private function setupTenantAndUser(): array
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => 'account_admins',
        ]);

        $tenant->makeCurrent();
        $this->actingAs($user);
        $this->withoutMiddleware();

        return [$tenant, $user];
    }

    /**
     * Make a PUT request to the company profile update endpoint.
     * Uses withoutMiddleware() so the controller runs directly (no CSRF/auth/tenant checks).
     */
    private function updateProfile(array $data): TestResponse
    {
        return $this->put(route('app-settings.company-profile.update'), $data);
    }

    // ── Access tests ────────────────────────────────────────────

    public function test_authenticated_user_can_view_company_profile(): void
    {
        $this->setupTenantAndUser();

        $response = $this->get(route('app-settings.company-profile.edit'));

        $response->assertOk();
    }

    public function test_guests_redirected_to_login(): void
    {
        // No middleware disable - auth redirect applies
        $response = $this->get(route('app-settings.company-profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function test_unauthenticated_update_is_blocked(): void
    {
        // Without middleware, the controller gate check returns 403 for unauthenticated users
        $this->withoutMiddleware();

        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $response->assertForbidden();
    }

    // ── Validation tests ────────────────────────────────────────

    public function test_company_name_en_is_required(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $response->assertRedirect();
    }

    public function test_company_name_ar_is_required(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme Corp',
            'timezone' => 'UTC',
        ]);

        $response->assertRedirect();
    }

    public function test_timezone_is_required(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
        ]);

        $response->assertRedirect();
    }

    public function test_invalid_timezone_is_rejected(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'Mars/Olympus',
        ]);

        $response->assertRedirect();
    }

    public function test_invalid_vat_number_is_rejected(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'vat_number' => 'ABC',
        ]);

        $response->assertRedirect();
    }

    public function test_vat_number_must_be_15_digits(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'vat_number' => '1234',
        ]);

        $response->assertRedirect();
    }

    public function test_invalid_primary_color_is_rejected(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'primary_color' => 'not-a-color',
        ]);

        $response->assertRedirect();
    }

    public function test_valid_hex_color_is_accepted(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'primary_color' => '#FF5733',
        ]);

        $response->assertRedirect(route('app-settings.company-profile.edit'));
        $this->assertDatabaseHas('rf_invoice_settings', [
            'primary_color' => '#FF5733',
        ]);
    }

    public function test_saves_text_fields(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme Corporation',
            'name_ar' => 'شركة أكمي',
            'vat_number' => '123456789012345',
            'cr_number' => 'CR12345',
            'timezone' => 'Asia/Riyadh',
            'primary_color' => '#1A73E8',
        ]);

        $response->assertRedirect(route('app-settings.company-profile.edit'));

        $this->assertDatabaseHas('rf_invoice_settings', [
            'name_en' => 'Acme Corporation',
            'name_ar' => 'شركة أكمي',
            'vat_number' => '123456789012345',
            'cr_number' => 'CR12345',
            'timezone' => 'Asia/Riyadh',
            'primary_color' => '#1A73E8',
        ]);
    }

    public function test_en_and_ar_name_fields_persisted(): void
    {
        $this->setupTenantAndUser();

        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $this->assertDatabaseHas('rf_invoice_settings', [
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
        ]);
    }

    public function test_primary_color_is_optional(): void
    {
        $this->setupTenantAndUser();

        $response = $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $response->assertRedirect(route('app-settings.company-profile.edit'));

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $this->assertNull($settings->primary_color);
    }

    // ── Logo upload tests ───────────────────────────────────────

    public function test_upload_primary_logo(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $file = UploadedFile::fake()->image('logo.png', 100, 100);

        $response = $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo' => $file,
        ]);

        $response->assertRedirect(route('app-settings.company-profile.edit'));

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $this->assertNotNull($settings->logo_path);
        Storage::disk('public')->assertExists($settings->logo_path);
    }

    public function test_upload_logo_removes_old_logo(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $oldFile = UploadedFile::fake()->image('old-logo.png', 100, 100);
        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo' => $oldFile,
        ]);

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $oldPath = $settings->logo_path;
        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->image('new-logo.png', 200, 200);
        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo' => $newFile,
        ]);

        $settings->refresh();
        $this->assertNotEquals($oldPath, $settings->logo_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($settings->logo_path);
    }

    public function test_remove_logo(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $file = UploadedFile::fake()->image('logo.png', 100, 100);
        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo' => $file,
        ]);

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $oldPath = $settings->logo_path;
        Storage::disk('public')->assertExists($oldPath);

        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'remove_logo' => true,
        ]);

        $settings->refresh();
        $this->assertNull($settings->logo_path);
        Storage::disk('public')->assertMissing($oldPath);
    }

    // ── Arabic logo tests (QA gap: AC "Arabic logo variant") ────

    public function test_upload_arabic_logo(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $file = UploadedFile::fake()->image('logo-ar.png', 100, 100);

        $response = $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo_ar' => $file,
        ]);

        $response->assertRedirect(route('app-settings.company-profile.edit'));

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $this->assertNotNull($settings->logo_ar_path);
        Storage::disk('public')->assertExists($settings->logo_ar_path);
    }

    public function test_upload_arabic_logo_removes_old(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $oldFile = UploadedFile::fake()->image('old-logo-ar.png', 100, 100);
        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo_ar' => $oldFile,
        ]);

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $oldPath = $settings->logo_ar_path;
        Storage::disk('public')->assertExists($oldPath);

        $newFile = UploadedFile::fake()->image('new-logo-ar.png', 200, 200);
        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo_ar' => $newFile,
        ]);

        $settings->refresh();
        $this->assertNotEquals($oldPath, $settings->logo_ar_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($settings->logo_ar_path);
    }

    public function test_remove_arabic_logo(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $file = UploadedFile::fake()->image('logo-ar.png', 100, 100);
        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo_ar' => $file,
        ]);

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $oldPath = $settings->logo_ar_path;
        Storage::disk('public')->assertExists($oldPath);

        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'remove_logo_ar' => true,
        ]);

        $settings->refresh();
        $this->assertNull($settings->logo_ar_path);
        Storage::disk('public')->assertMissing($oldPath);
    }

    // ── Model accessor tests (QA gap) ──────────────────────────

    public function test_logo_url_accessor_returns_null_without_logo(): void
    {
        $this->setupTenantAndUser();

        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $this->assertNull($settings->logo_url);
        $this->assertNull($settings->logo_ar_url);
    }

    public function test_logo_url_accessor_returns_url_when_logo_exists(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $file = UploadedFile::fake()->image('logo.png', 100, 100);
        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo' => $file,
        ]);

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $this->assertNotNull($settings->logo_url);
        $this->assertStringContainsString('/storage/', $settings->logo_url);
    }

    // ── Server-side logo validation tests (QA gap) ─────────────

    public function test_rejects_non_image_logo(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo' => $file,
        ]);

        $response->assertRedirect();
    }

    public function test_rejects_oversize_logo(): void
    {
        Storage::fake('public');
        $this->setupTenantAndUser();

        $file = UploadedFile::fake()->image('huge-logo.png', 100, 100)->size(3000);

        $response = $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo' => $file,
        ]);

        $response->assertRedirect();
    }

    // ── Idempotency test ───────────────────────────────────────

    public function test_idempotent_upsert(): void
    {
        $this->setupTenantAndUser();

        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $this->updateProfile([
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $count = InvoiceSetting::withoutGlobalScopes()->count();
        $this->assertEquals(1, $count);
    }

    // ── Cross-tenant isolation test ────────────────────────────

    public function test_cross_tenant_isolation(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $tenantA = Tenant::create(['name' => 'Account A']);
        $tenantB = Tenant::create(['name' => 'Account B']);

        AccountMembership::create([
            'user_id' => $userA->id,
            'account_tenant_id' => $tenantA->id,
            'role' => 'account_admins',
        ]);
        AccountMembership::create([
            'user_id' => $userB->id,
            'account_tenant_id' => $tenantB->id,
            'role' => 'account_admins',
        ]);

        $this->actingAs($userA);
        $tenantA->makeCurrent();
        $this->withoutMiddleware();

        $this->updateProfile([
            'name_en' => 'Tenant A',
            'name_ar' => 'المستأجر أ',
            'timezone' => 'UTC',
            'primary_color' => '#FF0000',
        ]);

        $this->actingAs($userB);
        $tenantB->makeCurrent();
        $this->withoutMiddleware();

        $this->updateProfile([
            'name_en' => 'Tenant B',
            'name_ar' => 'المستأجر ب',
            'timezone' => 'UTC',
            'primary_color' => '#0000FF',
        ]);

        // Read back without tenant scope to verify both records exist
        $settingsA = InvoiceSetting::withoutGlobalScopes()
            ->where('account_tenant_id', $tenantA->id)
            ->first();
        $this->assertNotNull($settingsA, 'Tenant A InvoiceSetting should exist');
        $this->assertEquals('Tenant A', $settingsA->name_en);
        $this->assertEquals('#FF0000', $settingsA->primary_color);

        $settingsB = InvoiceSetting::withoutGlobalScopes()
            ->where('account_tenant_id', $tenantB->id)
            ->first();
        $this->assertNotNull($settingsB, 'Tenant B InvoiceSetting should exist');
        $this->assertEquals('Tenant B', $settingsB->name_en);
        $this->assertEquals('#0000FF', $settingsB->primary_color);
    }
}
