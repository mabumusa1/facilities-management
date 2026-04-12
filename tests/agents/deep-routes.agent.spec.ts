import { test, expect } from '../fixtures/scanner.fixture';

const DEEP_ROUTES = [
  // Properties community type details
  { path: '/properties-list/communities/community/details/1', name: 'properties-community-type-details' },
  { path: '/properties-list/communities/building/details/1', name: 'properties-building-type-details' },
  
  // Home service settings
  { path: '/settings/home-service-settings/1', name: 'settings-home-service' },
  
  // Neighbourhood service settings
  { path: '/settings/neighbourhood-service-settings/1', name: 'settings-neighbourhood-service' },
  
  // More unit views
  { path: '/properties-list/units/unit/details/3', name: 'properties-units-details-3' },
  
  // Buildings with IDs
  { path: '/properties-list/buildings/1', name: 'properties-building-details-1' },
  
  // More transactions
  { path: '/transactions/2', name: 'transactions-details-2' },
  { path: '/transactions/3', name: 'transactions-details-3' },
  
  // More requests
  { path: '/requests/2', name: 'requests-details-2' },
];

test.describe('Deep Routes Agent', () => {
  for (const route of DEEP_ROUTES) {
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
