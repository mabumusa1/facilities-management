<?php

namespace Tests\Feature\Http\Settings;

use App\Models\AccountMembership;
use App\Models\Tenant;
use App\Models\User;
use DB;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class RegionalSettingsApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->tenant = Tenant::create(['name' => 'Regional Test']);
        $this->tenant->makeCurrent();

        AccountMembership::create([
            'user_id' => $this->user->id,
            'account_tenant_id' => $this->tenant->id,
            'role' => 'account_admins',
        ]);

        $this->ensureAccountAdminsRoleExists();
        $this->user->assignRole('accountAdmins');

        $this->actingAs($this->user);
        $this->withSession(['tenant_id' => $this->tenant->id]);
    }

    protected function tearDown(): void
    {
        Tenant::forgetCurrent();
        parent::tearDown();
    }

    private function ensureAccountAdminsRoleExists(): void
    {
        if (! DB::table('roles')->where('name', 'accountAdmins')->where('guard_name', 'web')->exists()) {
            DB::table('roles')->insert([
                'name' => 'accountAdmins',
                'guard_name' => 'web',
                'name_en' => 'Account Admins',
                'name_ar' => 'مدراء الحسابات',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function test_update_regional_settings_persists_values(): void
    {
        $response = $this->putJson('/rf/regional-settings', [
            'default_locale' => 'ar',
            'date_format' => 'd/m/Y',
            'working_days' => ['sun', 'mon', 'tue', 'wed', 'thu'],
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('rf_system_settings', [
            'key' => 'default_locale',
            'account_tenant_id' => $this->tenant->id,
        ]);
        $this->assertDatabaseHas('rf_system_settings', [
            'key' => 'date_format',
            'account_tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_update_rejects_invalid_locale(): void
    {
        $response = $this->putJson('/rf/regional-settings', [
            'default_locale' => 'fr',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['default_locale']);
    }

    public function test_update_rejects_invalid_date_format(): void
    {
        $response = $this->putJson('/rf/regional-settings', [
            'date_format' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['date_format']);
    }

    public function test_update_rejects_invalid_working_day(): void
    {
        $response = $this->putJson('/rf/regional-settings', [
            'working_days' => ['monday'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['working_days.0']);
    }

    public function test_update_accepts_empty_payload(): void
    {
        $response = $this->putJson('/rf/regional-settings', []);

        $response->assertStatus(200);
    }
}
