import { test, expect } from '../fixtures/scanner.fixture';

const SERVICE_SETTINGS_ROUTES = [
  // Home service settings sub-routes
  { path: '/settings/home-service-settings/1/category/1', name: 'settings-home-service-category' },
  { path: '/settings/home-service-settings/1/ServiceDetails/1', name: 'settings-home-service-details' },
  { path: '/settings/home-service-settings/1/AddNewSubcategory', name: 'settings-home-service-add-subcategory' },
  { path: '/settings/home-service-settings/1/selectCommunityBuilding', name: 'settings-home-service-select-community' },
  { path: '/settings/home-service-settings/1/newType', name: 'settings-home-service-new-type' },
  
  // Visitor access more
  { path: '/visitor-access/visitor-details/2', name: 'visitor-access-details-2' },
  
  // Dashboard more detail views
  { path: '/dashboard/issues/2/view', name: 'dashboard-issues-view-2' },
  { path: '/dashboard/offers/2/view', name: 'dashboard-offers-view-2' },
];

test.describe('Service Settings Agent', () => {
  for (const route of SERVICE_SETTINGS_ROUTES) {
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
