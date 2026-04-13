import { test, expect } from '../fixtures/scanner.fixture';

const DASHBOARD_EXTRA_ROUTES = [
  { path: '/dashboard/system-reports/Lease', name: 'dashboard-system-reports-lease' },
  { path: '/dashboard/system-reports/maintenance', name: 'dashboard-system-reports-maintenance' },
  { path: '/dashboard/directory/create', name: 'dashboard-directory-create' },
  { path: '/dashboard/announcements/create', name: 'dashboard-announcements-create' },
  { path: '/dashboard/offers/create', name: 'dashboard-offers-create' },
  { path: '/dashboard/issues/create', name: 'dashboard-issues-create' },
];

test.describe('Dashboard Full Agent', () => {
  for (const route of DASHBOARD_EXTRA_ROUTES) {
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
