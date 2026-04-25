<?php

namespace Tests\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\InvoiceSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyProfileControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function authenticateUser(string $role = 'account_admins'): User
    {
        $user = User::factory()->create();
        $tenant = Tenant::create(['name' => 'Test Account']);
        AccountMembership::create([
            'user_id' => $user->id,
            'account_tenant_id' => $tenant->id,
            'role' => $role,
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_guests_cannot_access_company_profile(): void
    {
        $response = $this->get(route('app-settings.company-profile.edit'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_company_profile(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('app-settings.company-profile.edit'));

        $response->assertOk();
    }

    public function test_unauthenticated_cannot_update_company_profile(): void
    {
        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_company_name_en_is_required(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $response->assertSessionHasErrors(['name_en']);
    }

    public function test_company_name_ar_is_required(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme Corp',
            'timezone' => 'UTC',
        ]);

        $response->assertSessionHasErrors(['name_ar']);
    }

    public function test_timezone_is_required(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
        ]);

        $response->assertSessionHasErrors(['timezone']);
    }

    public function test_invalid_timezone_is_rejected(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'Mars/Olympus',
        ]);

        $response->assertSessionHasErrors(['timezone']);
    }

    public function test_invalid_vat_number_is_rejected(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'vat_number' => 'ABC',
        ]);

        $response->assertSessionHasErrors(['vat_number']);
    }

    public function test_vat_number_must_be_15_digits(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'vat_number' => '1234',
        ]);

        $response->assertSessionHasErrors(['vat_number']);
    }

    public function test_invalid_primary_color_is_rejected(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme Corp',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'primary_color' => 'not-a-color',
        ]);

        $response->assertSessionHasErrors(['primary_color']);
    }

    public function test_valid_hex_color_is_accepted(): void
    {
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
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
        $this->authenticateUser();

        $response = $this->put(route('app-settings.company-profile.update'), [
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
        $this->authenticateUser();

        $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $this->assertDatabaseHas('rf_invoice_settings', [
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
        ]);
    }

    public function test_upload_primary_logo(): void
    {
        Storage::fake('public');
        $this->authenticateUser();

        $file = UploadedFile::fake()->image('logo.png', 100, 100);

        $response = $this->put(route('app-settings.company-profile.update'), [
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
        $this->authenticateUser();

        $oldFile = UploadedFile::fake()->image('old-logo.png', 100, 100);
        $this->put(route('app-settings.company-profile.update'), [
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
        $this->put(route('app-settings.company-profile.update'), [
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
        $this->authenticateUser();

        $file = UploadedFile::fake()->image('logo.png', 100, 100);
        $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'logo' => $file,
        ]);

        $settings = InvoiceSetting::first();
        $this->assertNotNull($settings);
        $oldPath = $settings->logo_path;
        Storage::disk('public')->assertExists($oldPath);

        $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
            'remove_logo' => true,
        ]);

        $settings->refresh();
        $this->assertNull($settings->logo_path);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_idempotent_upsert(): void
    {
        $this->authenticateUser();

        $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Acme',
            'name_ar' => 'أكمي',
            'timezone' => 'UTC',
        ]);

        $count = InvoiceSetting::withoutGlobalScopes()->count();
        $this->assertEquals(1, $count);
    }

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
        $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Tenant A',
            'name_ar' => 'المستأجر أ',
            'timezone' => 'UTC',
            'primary_color' => '#FF0000',
        ]);

        $this->actingAs($userB);
        $this->put(route('app-settings.company-profile.update'), [
            'name_en' => 'Tenant B',
            'name_ar' => 'المستأجر ب',
            'timezone' => 'UTC',
            'primary_color' => '#0000FF',
        ]);

        $this->actingAs($userA);
        $settingsA = InvoiceSetting::with('accountTenant')->first();
        $this->assertNotNull($settingsA);
        $this->assertEquals('Tenant A', $settingsA->name_en);
        $this->assertEquals('#FF0000', $settingsA->primary_color);

        $this->actingAs($userB);
        $settingsB = InvoiceSetting::with('accountTenant')->first();
        $this->assertNotNull($settingsB);
        $this->assertEquals('Tenant B', $settingsB->name_en);
        $this->assertEquals('#0000FF', $settingsB->primary_color);
    }
}
