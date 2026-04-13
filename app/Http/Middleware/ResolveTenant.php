<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Multitenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to resolve the current tenant from the request.
 *
 * Tenant identification priority:
 * 1. X-Tenant header (UUID or slug)
 * 2. Subdomain (tenant.example.com)
 * 3. Authenticated user's tenant
 *
 * This middleware should be applied to all tenant-scoped routes.
 */
class ResolveTenant
{
    /**
     * The header name for tenant identification.
     */
    public const TENANT_HEADER = 'X-Tenant';

    public function __construct(
        protected TenantContext $tenantContext
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolveTenant($request);

        if ($tenant) {
            if (! $tenant->isActive()) {
                abort(403, 'Tenant is not active.');
            }

            $this->tenantContext->set($tenant);
        }

        return $next($request);
    }

    /**
     * Resolve the tenant from the request.
     */
    protected function resolveTenant(Request $request): ?Tenant
    {
        // 1. Try X-Tenant header first
        if ($request->hasHeader(self::TENANT_HEADER)) {
            $identifier = $request->header(self::TENANT_HEADER);

            return $this->findTenant($identifier);
        }

        // 2. Try subdomain
        $host = $request->getHost();
        $subdomain = $this->extractSubdomain($host);

        if ($subdomain) {
            $tenant = Tenant::where('slug', $subdomain)
                ->orWhere('domain', $host)
                ->first();

            if ($tenant) {
                return $tenant;
            }
        }

        // 3. Try authenticated user's tenant
        if ($request->user() && $request->user()->tenant_id) {
            return Tenant::find($request->user()->tenant_id);
        }

        return null;
    }

    /**
     * Find a tenant by UUID or slug.
     */
    protected function findTenant(string $identifier): ?Tenant
    {
        // Check if it looks like a UUID
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $identifier)) {
            return Tenant::where('uuid', $identifier)->first();
        }

        // Otherwise treat as slug
        return Tenant::where('slug', $identifier)->first();
    }

    /**
     * Extract subdomain from host.
     */
    protected function extractSubdomain(string $host): ?string
    {
        // Remove port if present
        $host = explode(':', $host)[0];

        // Get the app domain from config
        $appDomain = config('app.domain', 'localhost');

        // If host ends with app domain, extract subdomain
        if ($host !== $appDomain && str_ends_with($host, '.'.$appDomain)) {
            return str_replace('.'.$appDomain, '', $host);
        }

        return null;
    }

    /**
     * Terminate the middleware.
     */
    public function terminate(Request $request, Response $response): void
    {
        // Clear tenant context after request
        $this->tenantContext->clear();
    }
}
