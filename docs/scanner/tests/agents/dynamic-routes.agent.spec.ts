import { test, expect } from '../fixtures/scanner.fixture';

// Using known test data IDs from API-EXPLORATION-SUMMARY.md
// Community ID: 1, Building IDs: 1-4, Unit IDs: 1-3
const DYNAMIC_ROUTES = [
  // Properties detail views
  { path: '/properties-list/units/unit/details/1', name: 'properties-units-details-1' },
  { path: '/properties-list/units/unit/details/2', name: 'properties-units-details-2' },
  { path: '/properties-list/communities/1/building/1', name: 'properties-communities-building-1' },
  
  // Transaction details
  { path: '/transactions/1', name: 'transactions-details-1' },
  
  // Settings with facility
  { path: '/settings/addNewFacility', name: 'settings-add-facility' },
  
  // Directory with facility
  { path: '/directory/addNewFacility', name: 'directory-add-facility' },
];

test.describe('Dynamic Routes Agent', () => {
  for (const route of DYNAMIC_ROUTES) {
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
