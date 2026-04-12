import { test, expect } from '../fixtures/scanner.fixture';

const REMAINING_ROUTES = [
  { path: '/transactions/record-transaction', name: 'transactions-record-transaction' },
  { path: '/marketplace/customers/upload-leads/errors', name: 'marketplace-upload-leads-errors' },
  { path: '/dashboard/issues/create', name: 'dashboard-issues-create' },
  { path: '/dashboard/directory/update', name: 'dashboard-directory-update' },
];

test.describe('Remaining Static Pages Agent', () => {
  for (const route of REMAINING_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
        waitTimeout: 60000,
      });
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
