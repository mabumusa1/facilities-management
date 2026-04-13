import { test, expect } from '../fixtures/scanner.fixture';

const MARKETPLACE_ROUTES = [
  { path: '/marketplace', name: 'marketplace' },
  { path: '/marketplace/customers', name: 'marketplace-customers' },
  { path: '/marketplace/listing', name: 'marketplace-listing' },
];

test.describe('Marketplace Agent', () => {
  for (const route of MARKETPLACE_ROUTES) {
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

  // Scan listing detail pages
  test('scan listing detail pages', async ({ scanner, page }) => {
    await scanner.scanPage('/marketplace/listing', 'marketplace-listing-list', {
      waitForNetworkIdle: true,
    });

    // Try to extract listing IDs
    const response = await page.request.get('https://api.goatar.com/api-management/marketplace/listings?is_paginate=0');
    if (response.ok()) {
      try {
        const data = await response.json();
        if (data?.data) {
          const listingIds = data.data.slice(0, 3).map((l: any) => l.id);
          for (const id of listingIds) {
            await scanner.scanPage(
              `/marketplace/listing/${id}`,
              `marketplace-listing-${id}`,
              { waitForNetworkIdle: true }
            );
          }
        }
      } catch (e) {
        console.log('Could not parse listings response');
      }
    }
  });
});
