<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Services\SettingsSeedService;

/**
 * Observes the Tenant model to auto-provision settings on creation.
 *
 * Registered in AppServiceProvider::boot().
 */
class TenantObserver
{
    public function __construct(private readonly SettingsSeedService $seedService) {}

    /**
     * Auto-seed all required settings rows when a new tenant is created.
     *
     * A seed failure must NOT roll back tenant creation — tenants are the
     * root entity and must persist even if settings seeding fails.
     * The backfill command `php artisan settings:seed-tenants` provides a
     * recovery path for any tenants whose settings are absent.
     */
    public function created(Tenant $tenant): void
    {
        try {
            $this->seedService->seedForTenant($tenant);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
