import { test, expect } from '../fixtures/scanner.fixture';

const FINAL_ROUTES = [
  // Dashboard create/edit forms
  { path: '/dashboard/announcements/edit/1', name: 'dashboard-announcements-edit' },
  { path: '/dashboard/announcements/1', name: 'dashboard-announcements-details' },
  { path: '/dashboard/suggestions/1', name: 'dashboard-suggestions-details' },
  { path: '/dashboard/offers/1/view', name: 'dashboard-offers-view' },
  { path: '/dashboard/issues/1/view', name: 'dashboard-issues-view' },
  { path: '/dashboard/directory/1', name: 'dashboard-directory-details' },
  
  // Leasing detail views
  { path: '/leasing/leases/expiring-leases/1', name: 'leasing-expiring-lease-details' },
  
  // Requests details
  { path: '/requests/1', name: 'requests-details' },
  
  // Visitor details  
  { path: '/visitor-access/visitor-details/1', name: 'visitor-access-details' },
  
  // Settings forms preview
  { path: '/settings/forms/preview/1', name: 'settings-forms-preview' },
  
  // Settings facility details
  { path: '/settings/facility/1', name: 'settings-facility-details' },
  
  // Directory facility details
  { path: '/directory/facility/1', name: 'directory-facility-details' },
];

test.describe('Final Batch Agent', () => {
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
