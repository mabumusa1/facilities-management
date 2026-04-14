import { test, expect } from '../fixtures/scanner.fixture';

const PROPERTIES_FOCUS_ROUTES = [
  {
    path: '/properties-list/communities?un_search=&un_page=1&bld_search=&bld_page=1&com_search=&com_page=1&tab=communities',
    name: 'properties-communities-focus',
  },
  {
    path: '/properties-list/new/community',
    name: 'properties-communities-create-focus',
  },
];

test.describe('Properties Focus Agent', () => {
  for (const route of PROPERTIES_FOCUS_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
        waitTimeout: 45000,
        failOnNotFound: true,
      });

      expect(result.endpoints.length).toBeGreaterThan(0);
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
