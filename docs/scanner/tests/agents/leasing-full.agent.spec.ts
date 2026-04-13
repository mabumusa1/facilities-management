import { test, expect } from '../fixtures/scanner.fixture';

const LEASING_EXTRA_ROUTES = [
  { path: '/leasing/leases/overdues', name: 'leasing-leases-overdues' },
  { path: '/leasing/leases/expiring-leases', name: 'leasing-expiring-leases' },
];

test.describe('Leasing Full Agent', () => {
  for (const route of LEASING_EXTRA_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
      });
      expect(result.endpoints.length).toBeGreaterThan(0);
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
