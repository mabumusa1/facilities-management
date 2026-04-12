import { test, expect } from '../fixtures/scanner.fixture';

const EXTRA_ROUTES = [
  // Dashboard issues assign
  { path: '/dashboard/issues/1/assign', name: 'dashboard-issues-assign' },
  
  // Properties building type views
  { path: '/properties-list/buildings/residential', name: 'properties-buildings-residential' },
  { path: '/properties-list/buildings/commercial', name: 'properties-buildings-commercial' },
  
  // Transactions tenant view
  { path: '/transactions/tenant/1', name: 'transactions-tenant-1' },
  
  // Directory type views
  { path: '/directory/community/1', name: 'directory-community-details' },
  { path: '/directory/building/1', name: 'directory-building-details' },
  
  // Service request with category
  { path: '/settings/service-request/home/1/1', name: 'settings-service-request-category' },
];

test.describe('Extra Routes Agent', () => {
  for (const route of EXTRA_ROUTES) {
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
