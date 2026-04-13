import { test, expect } from '../fixtures/scanner.fixture';

// Retry routes that failed due to timeout
const RETRY_ROUTES = [
  // Failed routes
  { path: '/leasing/leases/renew/1', name: 'leasing-renew' },
  { path: '/dashboard/move-out-tenants', name: 'dashboard-move-out-main' },

  // Additional lease forms
  { path: '/leasing/leases/renew/2', name: 'leasing-renew-2' },

  // Marketplace settings
  { path: '/marketplace/admin/settings', name: 'marketplace-admin-settings' },

  // Dashboard move-out with different ID
  { path: '/dashboard/move-out-tenants/1', name: 'dashboard-move-out-details' },
];

test.describe('Retry Failed Agent', () => {
  for (const route of RETRY_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
        waitTimeout: 90000,
      });
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
