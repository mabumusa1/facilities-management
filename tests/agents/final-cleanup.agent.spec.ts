import { test, expect } from '../fixtures/scanner.fixture';

const FINAL_ROUTES = [
  // User/Admin management
  { path: '/admins', name: 'admins-list' },
  { path: '/admins/1', name: 'admins-details' },
  
  // Tenants details
  { path: '/contacts/tenants/1', name: 'contacts-tenants-details' },
  { path: '/contacts/owners/1', name: 'contacts-owners-details' },
  { path: '/contacts/managers/1', name: 'contacts-managers-details' },
  
  // Family members
  { path: '/contacts/family-members/1', name: 'contacts-family-members' },
  
  // Unit marketplace
  { path: '/properties-list/units/1/marketplace', name: 'properties-unit-marketplace' },
  
  // Statistics
  { path: '/leasing/statistics', name: 'leasing-statistics' },
  { path: '/contacts/statistics', name: 'contacts-statistics' },
];

test.describe('Final Cleanup Agent', () => {
  for (const route of FINAL_ROUTES) {
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
