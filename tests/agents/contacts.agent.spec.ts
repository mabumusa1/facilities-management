import { test, expect } from '../fixtures/scanner.fixture';

const CONTACTS_ROUTES = [
  { path: '/contacts', name: 'contacts' },
  { path: '/contacts/tenants', name: 'contacts-tenants' },
  { path: '/contacts/owners', name: 'contacts-owners' },
  { path: '/contacts/managers', name: 'contacts-managers' },
  { path: '/requests', name: 'requests' },
  { path: '/visitor-access', name: 'visitor-access' },
];

test.describe('Contacts Agent', () => {
  for (const route of CONTACTS_ROUTES) {
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

  // Scan tenant detail pages
  test('scan tenant detail pages', async ({ scanner, page }) => {
    await scanner.scanPage('/contacts/tenants', 'contacts-tenants-list', {
      waitForNetworkIdle: true,
    });

    // Try to extract tenant IDs
    const response = await page.request.get('https://api.goatar.com/api-management/rf/tenants?is_paginate=0');
    if (response.ok()) {
      try {
        const data = await response.json();
        if (data?.data) {
          const tenantIds = data.data.slice(0, 3).map((t: any) => t.id);
          for (const id of tenantIds) {
            await scanner.scanPage(
              `/contacts/tenants/${id}`,
              `contacts-tenant-${id}`,
              { waitForNetworkIdle: true }
            );
          }
        }
      } catch (e) {
        console.log('Could not parse tenants response');
      }
    }
  });

  // Scan owner detail pages
  test('scan owner detail pages', async ({ scanner, page }) => {
    await scanner.scanPage('/contacts/owners', 'contacts-owners-list', {
      waitForNetworkIdle: true,
    });

    const response = await page.request.get('https://api.goatar.com/api-management/rf/owners?is_paginate=0');
    if (response.ok()) {
      try {
        const data = await response.json();
        if (data?.data) {
          const ownerIds = data.data.slice(0, 3).map((o: any) => o.id);
          for (const id of ownerIds) {
            await scanner.scanPage(
              `/contacts/owners/${id}`,
              `contacts-owner-${id}`,
              { waitForNetworkIdle: true }
            );
          }
        }
      } catch (e) {
        console.log('Could not parse owners response');
      }
    }
  });
});
