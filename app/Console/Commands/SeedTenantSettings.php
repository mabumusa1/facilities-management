<?php

namespace App\Console\Commands;

use App\Models\AppSetting;
use App\Models\InvoiceSetting;
use App\Models\ServiceSetting;
use App\Models\Tenant;
use App\Services\SettingsSeedService;
use Illuminate\Console\Command;

/**
 * Backfill settings rows for existing tenants that were created before the
 * TenantObserver was registered, or whose settings seed failed on creation.
 *
 * Usage: php artisan settings:seed-tenants
 *        php artisan settings:seed-tenants --dry-run   (log only, no writes)
 */
class SeedTenantSettings extends Command
{
    protected $signature = 'settings:seed-tenants
                            {--dry-run : Log what would be seeded without writing}';

    protected $description = 'Backfill settings rows (InvoiceSetting, AppSetting, ContractTypes) for all tenants that are missing them';

    public function handle(SettingsSeedService $seedService): int
    {
        $isDryRun = (bool) $this->option('dry-run');

        $seeded = 0;
        $skipped = 0;
        $failed = 0;

        // Only process tenants that are missing at least one settings record.
        // Use withoutGlobalScopes because BelongsToAccountTenant scopes by current tenant.
        // We pick tenants missing from ANY of the three settings tables.
        // "Fully covered" means present in ALL three; only those are excluded.
        $withInvoiceSettings = InvoiceSetting::withoutGlobalScopes()->pluck('account_tenant_id')->filter()->all();
        $withServiceSettings = ServiceSetting::withoutGlobalScopes()->pluck('account_tenant_id')->filter()->all();
        $withAppSettings = AppSetting::withoutGlobalScopes()->pluck('account_tenant_id')->filter()->all();
        $fullyCovered = array_intersect($withInvoiceSettings, $withServiceSettings, $withAppSettings);

        $tenantsMissingSettings = Tenant::when(
            ! empty($fullyCovered),
            fn ($q) => $q->whereNotIn('id', $fullyCovered),
        )->pluck('id', 'name');

        if ($tenantsMissingSettings->isEmpty()) {
            $this->info('Nothing to do — all tenants have settings.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d tenant(s) with missing settings.', $tenantsMissingSettings->count()));

        $tenantsMissingSettings->each(function (int $tenantId, string $tenantName) use (
            $seedService,
            $isDryRun,
            &$seeded,
            &$skipped,
            &$failed,
        ): void {
            if ($isDryRun) {
                $this->line(sprintf('  [dry-run] Would seed settings for tenant #%d "%s"', $tenantId, $tenantName));
                $seeded++;

                return;
            }

            $tenant = Tenant::find($tenantId);

            if ($tenant === null) {
                $this->warn(sprintf('  Tenant #%d not found — skipped.', $tenantId));
                $skipped++;

                return;
            }

            try {
                $seedService->seedForTenant($tenant);
                $this->line(sprintf('  Seeded settings for tenant #%d "%s"', $tenantId, $tenantName));
                $seeded++;
            } catch (\Throwable $e) {
                $this->error(sprintf('  Failed to seed tenant #%d "%s": %s', $tenantId, $tenantName, $e->getMessage()));
                report($e);
                $failed++;
            }
        });

        $this->newLine();
        $this->info(sprintf(
            'Done: %d seeded, %d skipped, %d failed.',
            $seeded,
            $skipped,
            $failed,
        ));

        if ($isDryRun) {
            $this->warn('Dry-run mode — no rows were written.');
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
