<?php

declare(strict_types=1);

namespace App\Multitenancy;

use App\Models\Tenant;

/**
 * Singleton to hold the current tenant context throughout the request lifecycle.
 *
 * This class is bound as a singleton in the service container and provides
 * a central place to access the current tenant from anywhere in the application.
 */
class TenantContext
{
    /**
     * The current tenant.
     */
    protected ?Tenant $tenant = null;

    /**
     * Get the current tenant.
     */
    public function get(): ?Tenant
    {
        return $this->tenant;
    }

    /**
     * Set the current tenant.
     */
    public function set(?Tenant $tenant): static
    {
        $this->tenant = $tenant;

        return $this;
    }

    /**
     * Get the current tenant ID.
     */
    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    /**
     * Check if a tenant is currently set.
     */
    public function has(): bool
    {
        return $this->tenant !== null;
    }

    /**
     * Clear the current tenant.
     */
    public function clear(): static
    {
        $this->tenant = null;

        return $this;
    }

    /**
     * Check if the current tenant is active.
     */
    public function isActive(): bool
    {
        return $this->tenant?->isActive() ?? false;
    }

    /**
     * Execute a callback with a specific tenant context.
     */
    public function run(Tenant $tenant, callable $callback): mixed
    {
        $previousTenant = $this->tenant;
        $this->tenant = $tenant;

        try {
            return $callback();
        } finally {
            $this->tenant = $previousTenant;
        }
    }
}
