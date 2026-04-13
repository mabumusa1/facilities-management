import { test, expect } from '../fixtures/scanner.fixture';

const LEASING_ROUTES = [
  { path: '/leasing', name: 'leasing' },
  { path: '/leasing/apps', name: 'leasing-apps' },
  { path: '/leasing/leases', name: 'leasing-leases' },
  { path: '/leasing/quotes', name: 'leasing-quotes' },
  { path: '/leasing/visits', name: 'leasing-visits' },
];

test.describe('Leasing Agent', () => {
  for (const route of LEASING_ROUTES) {
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

  // Scan lease detail pages
  test('scan lease detail pages', async ({ scanner, page }) => {
    await scanner.scanPage('/leasing/leases', 'leasing-leases-list', {
      waitForNetworkIdle: true,
    });

    // Try to extract lease IDs from API
    const response = await page.request.get('https://api.goatar.com/api-management/rf/leases?is_paginate=0');
    if (response.ok()) {
      try {
        const data = await response.json();
        if (data?.data) {
          const leaseIds = data.data.slice(0, 3).map((l: any) => l.id);
          for (const id of leaseIds) {
            await scanner.scanPage(
              `/leasing/leases/${id}`,
              `leasing-lease-${id}`,
              { waitForNetworkIdle: true }
            );
          }
        }
      } catch (e) {
        console.log('Could not parse leases response');
      }
    }
  });
});
