<?php

namespace Tests\Feature\Http\Settings;

use App\Models\AppSetting;
use App\Models\ContractType;
use App\Models\InvoiceSetting;
use App\Models\RequestCategory;
use App\Models\ServiceSetting;
use App\Models\Tenant;
use App\Services\SettingsSeedService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests for the Settings data model (story #234).
 *
 * Covers:
 *   - SettingsSeedService creates required settings rows per tenant (idempotent)
 *   - Tenant observer auto-seeds on Tenant::create()
 *   - Tenant isolation: InvoiceSetting and ServiceSetting are scoped per tenant
 *   - Backfill Artisan command seeds missing settings without duplication
 *   - DB schema: new columns on rf_invoice_settings and rf_service_settings exist
 */
class SettingsDataModelTest extends TestCase
{
    use LazilyRefreshDatabase;

    // ── Happy paths ─────────────────────────────────────────────────────────────

    public function test_seed_for_tenant_creates_invoice_setting_app_setting_and_contract_types(): void
    {
        $tenant = Tenant::create(['name' => 'Alpha Corp']);

        // Observer fires on created() — verify DB state directly
        $this->assertTrue(
            InvoiceSetting::withoutGlobalScopes()
                ->where('account_tenant_id', $tenant->id)
                ->exists(),
            'InvoiceSetting row should be created for new tenant',
        );

        $this->assertTrue(
            AppSetting::withoutGlobalScopes()
                ->where('account_tenant_id', $tenant->id)
                ->exists(),
            'AppSetting row should be created for new tenant',
        );

        $contractTypeCount = ContractType::withoutGlobalScopes()
            ->where('account_tenant_id', $tenant->id)
            ->count();

        $this->assertSame(3, $contractTypeCount, 'Three default ContractType rows should be seeded per tenant');
    }

    public function test_seed_for_tenant_is_idempotent(): void
    {
        $tenant = Tenant::create(['name' => 'Beta Corp']);

        $service = app(SettingsSeedService::class);

        // Call seed service a second time — should not throw or duplicate rows
        $service->seedForTenant($tenant);
        $service->seedForTenant($tenant);

        $this->assertSame(
            1,
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->count(),
            'InvoiceSetting must not be duplicated on re-seed',
        );

        $this->assertSame(
            1,
            AppSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->count(),
            'AppSetting must not be duplicated on re-seed',
        );

        $this->assertSame(
            3,
            ContractType::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->count(),
            'ContractType rows must not be duplicated on re-seed',
        );
    }

    public function test_invoice_setting_global_scope_prevents_cross_tenant_read(): void
    {
        $tenantA = Tenant::create(['name' => 'Tenant A']);
        $tenantB = Tenant::create(['name' => 'Tenant B']);

        // Make tenant A current
        $tenantA->makeCurrent();

        $setting = InvoiceSetting::first();
        $this->assertNotNull($setting, 'Tenant A should see its own InvoiceSetting');
        $this->assertSame($tenantA->id, $setting->account_tenant_id, 'InvoiceSetting should belong to Tenant A');

        // Switch to tenant B — must not see tenant A's setting
        Tenant::forgetCurrent();
        $tenantB->makeCurrent();

        $settingB = InvoiceSetting::first();
        $this->assertNotNull($settingB, 'Tenant B should see its own InvoiceSetting');
        $this->assertSame($tenantB->id, $settingB->account_tenant_id, 'InvoiceSetting should belong to Tenant B');
        $this->assertNotSame($setting->id, $settingB->id, 'Tenant A and B must not share the same InvoiceSetting row');

        Tenant::forgetCurrent();
    }

    public function test_service_setting_global_scope_prevents_cross_tenant_read(): void
    {
        $tenantA = Tenant::create(['name' => 'Service Tenant A']);
        $tenantB = Tenant::create(['name' => 'Service Tenant B']);

        // Create a ServiceSetting scoped to tenant A (category_id is NOT NULL)
        $category = RequestCategory::factory()->create();
        $tenantA->makeCurrent();
        ServiceSetting::withoutGlobalScopes()->create([
            'account_tenant_id' => $tenantA->id,
            'category_id' => $category->id,
            'visibilities' => ['tenant'],
            'permissions' => ['view'],
        ]);
        Tenant::forgetCurrent();

        // Tenant B must not see tenant A's service setting
        $tenantB->makeCurrent();
        $count = ServiceSetting::count();
        $this->assertSame(0, $count, 'Tenant B must not see Tenant A\'s ServiceSetting rows');

        Tenant::forgetCurrent();

        // Confirm tenant A sees its own row
        $tenantA->makeCurrent();
        $countA = ServiceSetting::count();
        $this->assertSame(1, $countA, 'Tenant A should see its own ServiceSetting row');

        Tenant::forgetCurrent();
    }

    public function test_contract_types_are_scoped_per_tenant(): void
    {
        $tenantA = Tenant::create(['name' => 'Contract Tenant A']);
        $tenantB = Tenant::create(['name' => 'Contract Tenant B']);

        $tenantA->makeCurrent();
        $typesForA = ContractType::active()->count();
        $this->assertSame(3, $typesForA, 'Tenant A should see 3 active contract types');
        Tenant::forgetCurrent();

        // Tenant B should see its own 3 contract types, not tenant A's
        $tenantB->makeCurrent();
        $typesForB = ContractType::active()->count();
        $this->assertSame(3, $typesForB, 'Tenant B should see 3 active contract types');
        Tenant::forgetCurrent();

        // Total rows in DB = 6 (3 per tenant)
        $total = ContractType::withoutGlobalScopes()->count();
        $this->assertSame(6, $total);
    }

    public function test_backfill_command_seeds_missing_settings_without_duplication(): void
    {
        // Create a tenant without triggering observer (simulate pre-observer tenant)
        $tenant = Tenant::withoutEvents(function () {
            return Tenant::create(['name' => 'Legacy Tenant']);
        });

        // Confirm no settings exist
        $this->assertFalse(
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->exists(),
        );

        // Run backfill command
        $this->artisan('settings:seed-tenants')
            ->assertSuccessful();

        // Settings should now exist
        $this->assertTrue(
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->exists(),
        );
        $this->assertTrue(
            AppSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->exists(),
        );
        $this->assertSame(
            3,
            ContractType::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->count(),
        );

        // Run again — must not duplicate
        $this->artisan('settings:seed-tenants')
            ->assertSuccessful();

        $this->assertSame(
            1,
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->count(),
        );
    }

    // ── Schema tests ─────────────────────────────────────────────────────────────

    public function test_invoice_settings_table_has_new_columns(): void
    {
        $tenant = Tenant::create(['name' => 'Schema Test Tenant']);

        $row = InvoiceSetting::withoutGlobalScopes()
            ->where('account_tenant_id', $tenant->id)
            ->first();

        $this->assertNotNull($row);
        $this->assertSame('INV', $row->invoice_prefix);
        $this->assertSame(1, $row->invoice_next_sequence);
        $this->assertSame(30, $row->payment_terms_days);
        $this->assertSame(0, $row->late_payment_grace_days);
        $this->assertTrue($row->show_vat_number);
        $this->assertSame('UTC', $row->timezone);
    }

    public function test_service_settings_table_has_account_tenant_id_column(): void
    {
        $columns = DB::getSchemaBuilder()->getColumnListing('rf_service_settings');

        $this->assertContains('account_tenant_id', $columns);
    }

    // ── Failure / edge cases ─────────────────────────────────────────────────────

    public function test_no_tenant_context_returns_null_for_invoice_setting_first(): void
    {
        Tenant::forgetCurrent();

        // With no tenant context, global scope adds no WHERE clause
        // so ::first() returns null if no rows exist
        $this->assertNull(InvoiceSetting::first());
    }

    public function test_observer_catches_seed_failure_and_tenant_still_persists(): void
    {
        // Force SettingsSeedService to throw by using a mock
        $this->mock(SettingsSeedService::class, function ($mock): void {
            $mock->shouldReceive('seedForTenant')->once()->andThrow(new \RuntimeException('DB error'));
        });

        // Tenant creation should not throw even though seed failed
        $tenant = Tenant::create(['name' => 'Fragile Tenant']);

        $this->assertDatabaseHas('tenants', ['name' => 'Fragile Tenant']);
        $this->assertNotNull($tenant->id);
    }
}
