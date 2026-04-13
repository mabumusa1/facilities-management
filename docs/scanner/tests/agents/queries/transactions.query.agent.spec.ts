/**
 * Transactions Query Agent
 *
 * Captures GET requests for:
 * - Transactions (list, detail)
 * - Invoices
 * - Reports
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeQueryCaptures } from '../../utils/query-output-writer';
import { QueryCapture } from '../../utils/query-types';

const captures: QueryCapture[] = [];

test.describe('Transactions Query Agent', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterAll(async () => {
    await writeQueryCaptures('transactions', captures);
    console.log(`\n=== Transactions Query Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
  });

  test.describe('Transactions', () => {
    let transactionId: string | undefined;

    test('GET /rf/transactions - list all transactions', async ({ api }) => {
      const capture = await api.getCapture('rf/transactions');
      captures.push(capture);

      // Capture for documentation
      const ids = api.extractIds(capture.response.body);
      transactionId = ids[0];
      console.log(`  Found ${ids.length} transactions`);
    });

    test('GET /rf/transactions - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/transactions', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);
    });

    test('GET /rf/transactions/{id} - transaction detail', async ({ api }) => {
      if (!transactionId) {
        const listData = await api.get('rf/transactions');
        transactionId = api.extractFirstId(listData);
      }

      if (transactionId) {
        const capture = await api.getCapture(`rf/transactions/${transactionId}`);
        captures.push(capture);
      }
    });
  });

  test.describe('Invoices', () => {
    let invoiceId: string | undefined;

    test('GET /rf/invoices - list all invoices', async ({ api }) => {
      const capture = await api.getCapture('rf/invoices');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      invoiceId = ids[0];
      console.log(`  Found ${ids.length} invoices`);
    });

    test('GET /rf/invoices - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/invoices', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);
    });

    test('GET /rf/invoices/{id} - invoice detail', async ({ api }) => {
      if (!invoiceId) {
        const listData = await api.get('rf/invoices');
        invoiceId = api.extractFirstId(listData);
      }

      if (invoiceId) {
        const capture = await api.getCapture(`rf/invoices/${invoiceId}`);
        captures.push(capture);
      }
    });
  });

  test.describe('Invoice Settings', () => {
    test('GET /invoice-settings - invoice settings', async ({ api }) => {
      const capture = await api.getCapture('invoice-settings');
      captures.push(capture);
    });
  });

  test.describe('Reports', () => {
    test('GET /reports/performance/units - unit performance', async ({ api }) => {
      const capture = await api.getCapture('reports/performance/units');
      captures.push(capture);
    });

    test('GET /reports/income - income report', async ({ api }) => {
      const capture = await api.getCapture('reports/income');
      captures.push(capture);
    });

    test('GET /reports/expenses - expenses report', async ({ api }) => {
      const capture = await api.getCapture('reports/expenses');
      captures.push(capture);
    });
  });
});
