import { test, expect } from '../fixtures/scanner.fixture';

const DASHBOARD_ROUTES = [
  { path: '/dashboard', name: 'dashboard' },
  { path: '/dashboard/directory', name: 'dashboard-directory' },
  { path: '/dashboard/announcements', name: 'dashboard-announcements' },
  { path: '/dashboard/suggestions', name: 'dashboard-suggestions' },
  { path: '/dashboard/offers', name: 'dashboard-offers' },
  { path: '/dashboard/issues', name: 'dashboard-issues' },
  { path: '/dashboard/visits', name: 'dashboard-visits' },
  { path: '/dashboard/bookings', name: 'dashboard-bookings' },
  { path: '/dashboard/booking-contracts', name: 'dashboard-booking-contracts' },
  { path: '/dashboard/payment', name: 'dashboard-payment' },
  { path: '/dashboard/move-out-tenants', name: 'dashboard-move-out-tenants' },
  { path: '/dashboard/power-bi-reports', name: 'dashboard-power-bi-reports' },
  { path: '/dashboard/reports', name: 'dashboard-reports' },
  { path: '/dashboard/system-reports', name: 'dashboard-system-reports' },
];

test.describe('Dashboard Agent', () => {
  for (const route of DASHBOARD_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
      });

      expect(result.endpoints.length).toBeGreaterThan(0);
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
