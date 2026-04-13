import { test, expect } from '../fixtures/scanner.fixture';

const MARKETPLACE_DETAIL_ROUTES = [
  // Marketplace units
  { path: '/marketplace/admin/units', name: 'marketplace-admin-units' },
  { path: '/marketplace/admin/units/1', name: 'marketplace-admin-units-details' },
  
  // Marketplace visits
  { path: '/marketplace/admin/visits', name: 'marketplace-admin-visits' },
  { path: '/marketplace/admin/visits/1', name: 'marketplace-admin-visits-details' },
  
  // Marketplace bookings
  { path: '/marketplace/admin/bookings', name: 'marketplace-admin-bookings' },
  
  // Marketplace communities
  { path: '/marketplace/admin/communities', name: 'marketplace-admin-communities' },
  { path: '/marketplace/admin/communities/list/1', name: 'marketplace-admin-communities-list' },
  
  // Marketplace favorites
  { path: '/marketplace/favorites', name: 'marketplace-favorites' },
];

test.describe('Marketplace Details Agent', () => {
  for (const route of MARKETPLACE_DETAIL_ROUTES) {
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
