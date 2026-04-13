<?php

declare(strict_types=1);

namespace App\Providers;

use App\Multitenancy\TenantContext;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for multi-tenant architecture components.
 */
class MultitenancyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register TenantContext as a singleton
        $this->app->singleton(TenantContext::class, function ($app) {
            return new TenantContext;
        });

        // Register configuration
        $this->mergeConfigFrom(__DIR__.'/../../config/multitenancy.php', 'multitenancy');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../../config/multitenancy.php' => config_path('multitenancy.php'),
        ], 'multitenancy-config');
    }
}
