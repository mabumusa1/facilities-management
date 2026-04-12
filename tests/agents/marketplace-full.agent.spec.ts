import { test, expect } from '../fixtures/scanner.fixture';

const MARKETPLACE_EXTRA_ROUTES = [
  { path: '/marketplace/customers/upload-leads', name: 'marketplace-upload-leads' },
  { path: '/marketplace/listing/off-plan-sale-form', name: 'marketplace-off-plan-form' },
];

test.describe('Marketplace Full Agent', () => {
  for (const route of MARKETPLACE_EXTRA_ROUTES) {
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
