<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\ContractType;
use App\Models\InvoiceSetting;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

/**
 * Provisions the standard settings rows for a given tenant.
 *
 * All upserts are wrapped in a DB transaction and are idempotent — calling
 * seedForTenant() multiple times for the same tenant produces no duplicate rows.
 */
class SettingsSeedService
{
    /**
     * Default contract types seeded for every new tenant.
     * Mirrors the rf_settings rows of type='rental_contract_type' so that a
     * future Leasing migration (story #226) can cut the FK over to rf_contract_types.
     *
     * @var array<int, array{name_en: string, name_ar: string, sort_order: int}>
     */
    private const DEFAULT_CONTRACT_TYPES = [
        ['name_en' => 'Yearly Rental', 'name_ar' => 'ايجار سنوي', 'sort_order' => 1],
        ['name_en' => 'Monthly Rental', 'name_ar' => 'ايجار شهري', 'sort_order' => 2],
        ['name_en' => 'Daily Rental', 'name_ar' => 'ايجار يومي', 'sort_order' => 3],
    ];

    /**
     * Seed (or ensure) all required settings rows for the given tenant.
     *
     * Wrapped in a transaction so partial failures do not leave half-seeded tenants.
     * This method is idempotent and safe to call multiple times.
     */
    public function seedForTenant(Tenant $tenant): void
    {
        DB::transaction(function () use ($tenant): void {
            $this->seedInvoiceSetting($tenant);
            $this->seedAppSetting($tenant);
            $this->seedContractTypes($tenant);
        });
    }

    private function seedInvoiceSetting(Tenant $tenant): void
    {
        InvoiceSetting::withoutGlobalScopes()->firstOrCreate(
            ['account_tenant_id' => $tenant->id],
            [
                'company_name' => $tenant->name,
                'name_en' => $tenant->name,
                'timezone' => 'UTC',
                'invoice_prefix' => 'INV',
                'invoice_next_sequence' => 1,
                'payment_terms_days' => 30,
                'late_payment_grace_days' => 0,
                'show_vat_number' => true,
            ],
        );
    }

    private function seedAppSetting(Tenant $tenant): void
    {
        AppSetting::withoutGlobalScopes()->firstOrCreate(
            ['account_tenant_id' => $tenant->id],
            [],
        );
    }

    private function seedContractTypes(Tenant $tenant): void
    {
        foreach (self::DEFAULT_CONTRACT_TYPES as $index => $contractType) {
            ContractType::withoutGlobalScopes()->firstOrCreate(
                [
                    'account_tenant_id' => $tenant->id,
                    'name_en' => $contractType['name_en'],
                ],
                [
                    'name_ar' => $contractType['name_ar'],
                    'is_active' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
