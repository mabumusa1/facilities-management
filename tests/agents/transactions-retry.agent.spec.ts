import { test, expect } from '../fixtures/scanner.fixture';

const TRANSACTIONS_RETRY_ROUTES = [
  { path: '/transactions/money-out', name: 'transactions-money-out' },
  { path: '/transactions/overdues', name: 'transactions-overdues' },
];

test.describe('Transactions Retry Agent', () => {
  for (const route of TRANSACTIONS_RETRY_ROUTES) {
    test(`rescan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
        waitTimeout: 45000, // Extra time for data loading
      });
      // Don't fail on empty - just capture what we can
      console.log(`Rescanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }
});
