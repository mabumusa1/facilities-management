import { test, expect } from '../fixtures/scanner.fixture';

const DIRECTORY_ROUTES = [
  { path: '/directory/owner', name: 'directory-owner' },
  { path: '/directory/documents', name: 'directory-documents' },
  { path: '/directory/facilities', name: 'directory-facilities' },
];

test.describe('Directory Pages Agent', () => {
  for (const route of DIRECTORY_ROUTES) {
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
