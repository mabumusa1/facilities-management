import { test, expect } from '../fixtures/scanner.fixture';

const LEASING_DETAIL_ROUTES = [
  // Lease details with IDs
  { path: '/leasing/details/1', name: 'leasing-details-1' },
  { path: '/leasing/details/2', name: 'leasing-details-2' },
  
  // Sub-leases
  { path: '/leasing/sub-leases', name: 'leasing-sub-leases' },
  { path: '/leasing/sub-leases/1', name: 'leasing-sub-leases-details' },
  
  // Lease create/renew forms
  { path: '/leasing/leases/create', name: 'leasing-create' },
  { path: '/leasing/leases/renew/1', name: 'leasing-renew' },
  
  // More dashboard views
  { path: '/dashboard/move-out-tenants', name: 'dashboard-move-out-main' },
  { path: '/dashboard/payment', name: 'dashboard-payment-main' },
];

test.describe('Leasing Details Agent', () => {
  for (const route of LEASING_DETAIL_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
        waitTimeout: 45000,
      });
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
