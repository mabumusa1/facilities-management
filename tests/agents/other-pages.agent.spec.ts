import { test, expect } from '../fixtures/scanner.fixture';

const OTHER_ROUTES = [
  { path: '/edit-profile', name: 'edit-profile' },
  { path: '/notifications', name: 'notifications' },
  { path: '/maintenance', name: 'maintenance' },
  { path: '/directory', name: 'directory' },
  { path: '/requests/history', name: 'requests-history' },
  { path: '/requests/create', name: 'requests-create' },
  { path: '/visitor-access/history', name: 'visitor-access-history' },
];

test.describe('Other Pages Agent', () => {
  for (const route of OTHER_ROUTES) {
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
