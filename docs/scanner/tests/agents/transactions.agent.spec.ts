import { test, expect } from '../fixtures/scanner.fixture';

const TRANSACTIONS_ROUTES = [
  { path: '/transactions', name: 'transactions' },
  { path: '/transactions/money-in', name: 'transactions-money-in' },
  { path: '/transactions/money-out', name: 'transactions-money-out' },
  { path: '/transactions/overdues', name: 'transactions-overdues' },
  { path: '/transactions/journal-entries', name: 'transactions-journal-entries' },
  { path: '/transactions/chart-of-accounts', name: 'transactions-chart-of-accounts' },
];

test.describe('Transactions Agent', () => {
  for (const route of TRANSACTIONS_ROUTES) {
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

  // Scan transaction detail pages
  test('scan transaction detail pages', async ({ scanner, page }) => {
    await scanner.scanPage('/transactions', 'transactions-list', {
      waitForNetworkIdle: true,
    });

    // Try to extract transaction IDs
    const response = await page.request.get('https://api.goatar.com/api-management/transactions?is_paginate=0');
    if (response.ok()) {
      try {
        const data = await response.json();
        if (data?.data) {
          const transactionIds = data.data.slice(0, 3).map((t: any) => t.id);
          for (const id of transactionIds) {
            await scanner.scanPage(
              `/transactions/${id}`,
              `transaction-${id}`,
              { waitForNetworkIdle: true }
            );
          }
        }
      } catch (e) {
        console.log('Could not parse transactions response');
      }
    }
  });
});
