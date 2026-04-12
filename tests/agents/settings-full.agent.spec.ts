import { test, expect } from '../fixtures/scanner.fixture';

const SETTINGS_ROUTES = [
  { path: '/settings/invoice', name: 'settings-invoice' },
  { path: '/settings/service-request', name: 'settings-service-request' },
  { path: '/settings/visitor-request', name: 'settings-visitor-request' },
  { path: '/settings/bank-details', name: 'settings-bank-details' },
  { path: '/settings/visits-details', name: 'settings-visits-details' },
  { path: '/settings/sales-details', name: 'settings-sales-details' },
];

test.describe('Settings Full Agent', () => {
  for (const route of SETTINGS_ROUTES) {
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
