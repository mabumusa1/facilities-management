import { test, expect } from '../fixtures/scanner.fixture';

const SETTINGS_FORMS_ROUTES = [
  { path: '/settings/forms/create', name: 'settings-forms-create' },
  { path: '/settings/forms/select-community', name: 'settings-forms-select-community' },
  { path: '/settings/forms/select-building', name: 'settings-forms-select-building' },
];

test.describe('Settings Forms Agent', () => {
  for (const route of SETTINGS_FORMS_ROUTES) {
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
