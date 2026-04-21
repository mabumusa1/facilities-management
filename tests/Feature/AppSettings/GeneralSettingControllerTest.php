<?php

namespace Tests\Feature\AppSettings;

use App\Models\AccountMembership;
use App\Models\Setting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class GeneralSettingControllerTest extends TestCase
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

    public function test_guests_cannot_access_general_settings(): void
    {
        $response = $this->get(route('app-settings.general.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_general_settings(): void
    {
        $this->authenticateUser();

        $response = $this->get(route('app-settings.general.index'));

        $response->assertOk();
    }

    public function test_index_respects_locale_header_for_setting_name_values(): void
    {
        $this->authenticateUser();

        Setting::factory()->create([
            'name_ar' => 'إيجار',
            'name_en' => 'Rental',
            'type' => 'rental_contract_type',
        ]);

        $this->get(route('app-settings.general.index'), ['X-App-Locale' => 'en'])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('settingGroups.rental_contract_type.0.name', 'Rental')
            );

        $this->get(route('app-settings.general.index'), ['X-App-Locale' => 'ar'])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('settingGroups.rental_contract_type.0.name', 'إيجار')
            );
    }

    public function test_authenticated_user_can_create_setting(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('app-settings.general.store'), [
            'name_ar' => 'إيجار',
            'name_en' => 'Rental',
            'type' => 'rental_contract_type',
        ]);

        $response->assertRedirect(route('app-settings.general.index'));
        $this->assertDatabaseHas('rf_settings', [
            'name_en' => 'Rental',
            'type' => 'rental_contract_type',
        ]);
    }

    public function test_authenticated_user_can_create_setting_with_extended_type(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('app-settings.general.store'), [
            'name_ar' => 'على المتر',
            'name_en' => 'Per Square Meter',
            'type' => 'calculation_basis',
        ]);

        $response->assertRedirect(route('app-settings.general.index'));
        $this->assertDatabaseHas('rf_settings', [
            'name_en' => 'Per Square Meter',
            'type' => 'calculation_basis',
        ]);
    }

    public function test_authenticated_user_can_update_setting(): void
    {
        $this->authenticateUser();
        $setting = Setting::factory()->create();

        $response = $this->put(route('app-settings.general.update', $setting), [
            'name_ar' => 'محدث',
            'name_en' => 'Updated',
        ]);

        $response->assertRedirect(route('app-settings.general.index'));
        $this->assertDatabaseHas('rf_settings', [
            'id' => $setting->id,
            'name_en' => 'Updated',
        ]);
    }

    public function test_authenticated_user_can_delete_setting(): void
    {
        $this->authenticateUser();
        $setting = Setting::factory()->create();

        $response = $this->delete(route('app-settings.general.destroy', $setting));

        $response->assertRedirect(route('app-settings.general.index'));
        $this->assertDatabaseMissing('rf_settings', [
            'id' => $setting->id,
        ]);
    }

    public function test_create_setting_requires_valid_type(): void
    {
        $this->authenticateUser();

        $response = $this->post(route('app-settings.general.store'), [
            'name_ar' => 'إيجار',
            'name_en' => 'Rental',
            'type' => 'invalid_type',
        ]);

        $response->assertSessionHasErrors(['type']);
    }
}
