import { test, expect } from '../fixtures/scanner.fixture';

const PROPERTIES_ROUTES = [
  { path: '/properties-list', name: 'properties-list' },
  { path: '/properties-list/communities', name: 'properties-communities' },
  { path: '/properties-list/buildings', name: 'properties-buildings' },
  { path: '/properties-list/units', name: 'properties-units' },
];

test.describe('Properties Agent', () => {
  for (const route of PROPERTIES_ROUTES) {
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

  // Dynamic route scanning - extract IDs from list and scan detail pages
  test('scan community detail pages', async ({ scanner, page }) => {
    // First scan the communities list to get IDs
    await scanner.scanPage('/properties-list/communities', 'properties-communities-list', {
      waitForNetworkIdle: true,
    });

    // Extract community IDs from the page or captured API responses
    const communityIds: string[] = [];

    // Listen for the communities API response
    const response = await page.request.get('https://api.goatar.com/api-management/rf/communities?is_paginate=0');
    if (response.ok()) {
      try {
        const data = await response.json();
        if (data?.data) {
          communityIds.push(...data.data.slice(0, 3).map((c: any) => c.id));
        }
      } catch (e) {
        console.log('Could not parse communities response');
      }
    }

    // Scan first 3 community detail pages
    for (const id of communityIds.slice(0, 3)) {
      await scanner.scanPage(
        `/properties-list/communities/${id}/buildings`,
        `properties-community-${id}-buildings`,
        { waitForNetworkIdle: true }
      );
    }
  });
});
