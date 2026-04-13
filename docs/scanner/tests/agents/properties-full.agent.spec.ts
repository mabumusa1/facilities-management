import { test, expect } from '../fixtures/scanner.fixture';

const PROPERTIES_EXTRA_ROUTES = [
  { path: '/properties-list/communities/bulk-upload', name: 'properties-communities-bulk-upload' },
  { path: '/properties-list/buildings/bulk-upload', name: 'properties-buildings-bulk-upload' },
  { path: '/properties-list/units/new-unit', name: 'properties-units-new' },
  { path: '/properties-list/units/edit-unit', name: 'properties-units-edit' },
  { path: '/properties-list/units/marketplace-listing', name: 'properties-units-marketplace' },
];

test.describe('Properties Full Agent', () => {
  for (const route of PROPERTIES_EXTRA_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
      });
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
