import { test, expect } from '../fixtures/scanner.fixture';

const MORE_STATIC_ROUTES = [
  // Settings facilities
  { path: '/settings/facilities', name: 'settings-facilities-list' },
  { path: '/settings/addNewFacility', name: 'settings-add-new-facility' },
  
  // Dashboard additional
  { path: '/dashboard/system-reports', name: 'dashboard-system-reports-main' },
  
  // Leasing additional
  { path: '/leasing/apps', name: 'leasing-apps-main' },
  { path: '/leasing/quotes', name: 'leasing-quotes-main' },
  { path: '/leasing/visits', name: 'leasing-visits-main' },
  
  // Properties communities create
  { path: '/properties-list/communities/new/community', name: 'properties-communities-create' },
];

test.describe('More Static Pages Agent', () => {
  for (const route of MORE_STATIC_ROUTES) {
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
