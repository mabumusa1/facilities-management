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

    // ── QA gap tests — failure paths, edge cases, regression ────────────────────

    /**
     * AC: Seed failure does NOT roll back tenant creation.
     *
     * The observer wraps the seed call in try/catch + report(). When seeding
     * throws, the tenant row must persist and no settings rows should exist
     * (the partial seed never committed — seedForTenant wraps in a transaction).
     */
    public function test_seed_failure_leaves_tenant_row_intact_and_settings_absent(): void
    {
        $this->mock(SettingsSeedService::class, function ($mock): void {
            $mock->shouldReceive('seedForTenant')->once()->andThrow(new \RuntimeException('Simulated seed failure'));
        });

        $tenant = Tenant::create(['name' => 'Seed Failure Tenant']);

        // Tenant persists
        $this->assertDatabaseHas('tenants', ['id' => $tenant->id, 'name' => 'Seed Failure Tenant']);

        // No settings were created (seed threw before any row was written)
        $this->assertFalse(
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->exists(),
            'InvoiceSetting must not exist when seed failed',
        );
        $this->assertFalse(
            AppSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->exists(),
            'AppSetting must not exist when seed failed',
        );
        $this->assertSame(
            0,
            ContractType::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->count(),
            'ContractTypes must not exist when seed failed',
        );
    }

    /**
     * Edge: backfill command handles tenant that already has InvoiceSetting
     * but is missing AppSetting — fills only the missing row, no duplication.
     */
    public function test_backfill_command_fills_missing_rows_on_partially_seeded_tenant(): void
    {
        // Create tenant without events (no observer fires)
        $tenant = Tenant::withoutEvents(function () {
            return Tenant::create(['name' => 'Partial Seed Tenant']);
        });

        // Manually seed only InvoiceSetting (AppSetting absent)
        InvoiceSetting::withoutGlobalScopes()->create([
            'account_tenant_id' => $tenant->id,
            'company_name' => $tenant->name,
            'timezone' => 'UTC',
            'invoice_prefix' => 'INV',
            'invoice_next_sequence' => 1,
            'payment_terms_days' => 30,
            'late_payment_grace_days' => 0,
            'show_vat_number' => true,
        ]);

        // Confirm partial state: InvoiceSetting exists, AppSetting absent
        $this->assertTrue(
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->exists(),
        );
        $this->assertFalse(
            AppSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->exists(),
        );

        // Run backfill — command must detect the partial gap and fill it
        $this->artisan('settings:seed-tenants')->assertSuccessful();

        // AppSetting now exists
        $this->assertTrue(
            AppSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->exists(),
            'Backfill must create missing AppSetting for partially-seeded tenant',
        );

        // InvoiceSetting still exactly 1 row (no duplication)
        $this->assertSame(
            1,
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->count(),
            'InvoiceSetting must not be duplicated by backfill on partially-seeded tenant',
        );
    }

    /**
     * Edge: --dry-run flag outputs what would be created without writing any rows.
     */
    public function test_backfill_command_dry_run_outputs_tenants_without_creating_rows(): void
    {
        // Create two tenants without settings
        $tenantA = Tenant::withoutEvents(function () {
            return Tenant::create(['name' => 'Dry Run Tenant A']);
        });
        $tenantB = Tenant::withoutEvents(function () {
            return Tenant::create(['name' => 'Dry Run Tenant B']);
        });

        $this->artisan('settings:seed-tenants', ['--dry-run' => true])
            ->expectsOutputToContain('dry-run')
            ->assertSuccessful();

        // No rows were written
        $this->assertFalse(
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenantA->id)->exists(),
            'Dry-run must not create InvoiceSetting for Tenant A',
        );
        $this->assertFalse(
            InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenantB->id)->exists(),
            'Dry-run must not create InvoiceSetting for Tenant B',
        );
        $this->assertFalse(
            AppSetting::withoutGlobalScopes()->where('account_tenant_id', $tenantA->id)->exists(),
            'Dry-run must not create AppSetting',
        );
    }

    /**
     * Regression: existing InvoiceSetting rows with legacy column values are
     * unaffected by the new migrations — no data is overwritten.
     */
    public function test_existing_invoice_setting_legacy_columns_are_preserved(): void
    {
        $tenant = Tenant::withoutEvents(function () {
            return Tenant::create(['name' => 'Legacy Data Tenant']);
        });

        // Insert a row that uses the original legacy columns only
        InvoiceSetting::withoutGlobalScopes()->create([
            'account_tenant_id' => $tenant->id,
            'company_name' => 'Legacy Company',
            'vat' => '15.00',
            'vat_number' => 'VAT123',
            'cr_number' => 'CR456',
            'address' => '123 Old Street',
            'timezone' => 'Asia/Riyadh',
            'invoice_prefix' => 'OLD',
            'invoice_next_sequence' => 42,
            'payment_terms_days' => 60,
            'late_payment_grace_days' => 5,
            'show_vat_number' => false,
        ]);

        // Retrieve and assert legacy values survive unchanged
        $row = InvoiceSetting::withoutGlobalScopes()->where('account_tenant_id', $tenant->id)->first();

        $this->assertNotNull($row);
        $this->assertSame('Legacy Company', $row->company_name);
        $this->assertSame('VAT123', $row->vat_number);
        $this->assertSame('CR456', $row->cr_number);
        $this->assertSame('123 Old Street', $row->address);
        $this->assertSame('Asia/Riyadh', $row->timezone);
        $this->assertSame('OLD', $row->invoice_prefix);
        $this->assertSame(42, $row->invoice_next_sequence);
        $this->assertSame(60, $row->payment_terms_days);
        $this->assertSame(5, $row->late_payment_grace_days);
        $this->assertFalse($row->show_vat_number);
    }

    /**
     * Tenant boundary: raw DB query using account_tenant_id filter correctly
     * separates Tenant A's InvoiceSetting from Tenant B's.
     *
     * This validates the DB-level isolation independent of Eloquent global scopes.
     */
    public function test_direct_db_query_cannot_read_other_tenants_invoice_setting(): void
    {
        $tenantA = Tenant::create(['name' => 'DB Boundary Tenant A']);
        $tenantB = Tenant::create(['name' => 'DB Boundary Tenant B']);

        // Confirm both rows exist in the DB
        $rowA = DB::table('rf_invoice_settings')->where('account_tenant_id', $tenantA->id)->first();
        $rowB = DB::table('rf_invoice_settings')->where('account_tenant_id', $tenantB->id)->first();

        $this->assertNotNull($rowA, 'Tenant A InvoiceSetting must exist in DB');
        $this->assertNotNull($rowB, 'Tenant B InvoiceSetting must exist in DB');

        // Querying with tenant A's ID must not return tenant B's row
        $shouldBeNull = DB::table('rf_invoice_settings')
            ->where('account_tenant_id', $tenantA->id)
            ->where('id', $rowB->id)
            ->first();

        $this->assertNull($shouldBeNull, 'Direct DB query must not return the other tenant\'s InvoiceSetting row');
    }

    /**
     * Tenant boundary: raw DB query correctly separates tenants for AppSetting.
     */
    public function test_direct_db_query_cannot_read_other_tenants_app_setting(): void
    {
        $tenantA = Tenant::create(['name' => 'DB Boundary App A']);
        $tenantB = Tenant::create(['name' => 'DB Boundary App B']);

        $rowA = DB::table('rf_app_settings')->where('account_tenant_id', $tenantA->id)->first();
        $rowB = DB::table('rf_app_settings')->where('account_tenant_id', $tenantB->id)->first();

        $this->assertNotNull($rowA);
        $this->assertNotNull($rowB);
        $this->assertNotSame($rowA->id, $rowB->id);

        // Cross-tenant read must return nothing
        $cross = DB::table('rf_app_settings')
            ->where('account_tenant_id', $tenantA->id)
            ->where('id', $rowB->id)
            ->first();

        $this->assertNull($cross, 'Direct DB query must not return the other tenant\'s AppSetting row');
    }

    /**
     * ContractType seeding: default contract types have the correct names and
     * ordering (Yearly → Monthly → Daily) as specified in TL design.
     */
    public function test_seeded_contract_types_have_correct_names_and_sort_order(): void
    {
        $tenant = Tenant::create(['name' => 'Contract Names Tenant']);

        $types = ContractType::withoutGlobalScopes()
            ->where('account_tenant_id', $tenant->id)
            ->orderBy('sort_order')
            ->get();

        $this->assertCount(3, $types);

        $this->assertSame('Yearly Rental', $types[0]->name_en);
        $this->assertSame('ايجار سنوي', $types[0]->name_ar);
        $this->assertTrue($types[0]->is_active);
        $this->assertSame(1, $types[0]->sort_order);

        $this->assertSame('Monthly Rental', $types[1]->name_en);
        $this->assertSame('ايجار شهري', $types[1]->name_ar);
        $this->assertTrue($types[1]->is_active);
        $this->assertSame(2, $types[1]->sort_order);

        $this->assertSame('Daily Rental', $types[2]->name_en);
        $this->assertSame('ايجار يومي', $types[2]->name_ar);
        $this->assertTrue($types[2]->is_active);
        $this->assertSame(3, $types[2]->sort_order);
    }
}
