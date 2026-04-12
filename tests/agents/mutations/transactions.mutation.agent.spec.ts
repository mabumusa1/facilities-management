/**
 * Transactions Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Transaction recording (money in/out)
 * - Invoice management
 * - Payment processing
 * - Journal entries
 * - Chart of accounts
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import { EMPTY_DATA } from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

// Transaction types
const TRANSACTION_TYPES = ['money_in', 'money_out'];

test.describe('Transactions Mutation Agent', () => {
  // Run all tests in serial mode to ensure captures are collected properly
  test.describe.configure({ mode: 'serial' });
  test.afterAll(async () => {
    await writeMutationCaptures('transactions', captures);
    console.log(`\n=== Transactions Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Errors: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Transaction Recording', () => {
    test('POST /transactions - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('transactions', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /transactions (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /transactions - record money in transaction', async ({ api }) => {
      // Get categories for money in
      const categoriesData = await api.fetchReferenceData('transactions/categories?type=in');
      const categories = (categoriesData as any)?.data || [];

      // Get leases for transaction reference
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      const today = new Date().toISOString().split('T')[0];

      const data: Record<string, unknown> = {
        type: 'money_in',
        amount: 5000,
        date: today,
        description: `Test transaction ${Date.now()}`,
        payment_method: 'bank_transfer',
      };

      if (categories.length > 0) {
        data.category_id = categories[0].id;
      }

      if (leases.length > 0) {
        data.lease_id = leases[0].id;
      }

      const capture = await api.post('transactions', data);
      captures.push(capture);

      console.log(`POST /transactions (money_in) => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created transaction ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('POST /transactions - record money out transaction', async ({ api }) => {
      const categoriesData = await api.fetchReferenceData('transactions/categories?type=out');
      const categories = (categoriesData as any)?.data || [];

      const today = new Date().toISOString().split('T')[0];

      const data: Record<string, unknown> = {
        type: 'money_out',
        amount: 1500,
        date: today,
        description: `Test expense ${Date.now()}`,
        payment_method: 'cash',
      };

      if (categories.length > 0) {
        data.category_id = categories[0].id;
      }

      const capture = await api.post('transactions', data);
      captures.push(capture);

      console.log(`POST /transactions (money_out) => ${capture.response.status}`);
    });

    test('PUT /transactions/{id} - update transaction', async ({ api }) => {
      const transactionsData = await api.fetchReferenceData('transactions?filter_type=all&page=1');
      const transactions = (transactionsData as any)?.data || [];

      if (transactions.length > 0) {
        const transactionId = transactions[0].id;
        const capture = await api.put(`transactions/${transactionId}`, {
          description: `Updated transaction ${Date.now()}`,
        });
        captures.push(capture);

        console.log(`PUT /transactions/${transactionId} => ${capture.response.status}`);
      } else {
        console.log('Skipped: No transactions available for update');
      }
    });

    test('DELETE /transactions/{id} - delete transaction', async ({ api }) => {
      const transactionsData = await api.fetchReferenceData('transactions?filter_type=all&page=1');
      const transactions = (transactionsData as any)?.data || [];

      if (transactions.length > 0) {
        // Use a newer transaction to avoid deleting important data
        const transactionId = transactions[transactions.length - 1].id;
        const capture = await api.delete(`transactions/${transactionId}`);
        captures.push(capture);

        console.log(`DELETE /transactions/${transactionId} => ${capture.response.status}`);
      }
    });
  });

  test.describe('Invoice Management', () => {
    test('POST /invoices - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('invoices', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /invoices (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /invoices - create invoice', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const lease = leases[0];
        const today = new Date().toISOString().split('T')[0];
        const dueDate = new Date();
        dueDate.setDate(dueDate.getDate() + 30);

        const capture = await api.post('invoices', {
          lease_id: lease.id,
          amount: 5000,
          issue_date: today,
          due_date: dueDate.toISOString().split('T')[0],
          description: `Invoice for lease ${lease.id}`,
          type: 'rent',
        });
        captures.push(capture);

        console.log(`POST /invoices => ${capture.response.status}`);
        if (capture.success) {
          console.log(`  Created invoice ID: ${api.extractFirstId(capture.response.body)}`);
        }
      } else {
        console.log('Skipped: No leases available for invoice creation');
      }
    });

    test('POST /invoices/mark-paid - mark invoice as paid', async ({ api }) => {
      // This endpoint might require an invoice ID
      const capture = await api.post('invoices/mark-paid', {
        invoice_id: 1,
        payment_date: new Date().toISOString().split('T')[0],
        payment_method: 'bank_transfer',
      });
      captures.push(capture);

      console.log(`POST /invoices/mark-paid => ${capture.response.status}`);
    });

    test('PUT /invoice-settings - update invoice settings', async ({ api }) => {
      const settingsData = await api.fetchReferenceData('invoice-settings');

      const capture = await api.put('invoice-settings', {
        auto_generate: true,
        prefix: 'INV-',
        due_days: 30,
      });
      captures.push(capture);

      console.log(`PUT /invoice-settings => ${capture.response.status}`);
    });
  });

  test.describe('Journal Entries', () => {
    test('POST /transactions/journal-entries - validation errors', async ({ api }) => {
      const capture = await api.post('transactions/journal-entries', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /transactions/journal-entries (empty) => ${capture.response.status}`);
    });

    test('POST /transactions/journal-entries - create entry', async ({ api }) => {
      const today = new Date().toISOString().split('T')[0];

      const capture = await api.post('transactions/journal-entries', {
        date: today,
        description: `Journal entry ${Date.now()}`,
        entries: [
          { account_id: 1, debit: 1000, credit: 0 },
          { account_id: 2, debit: 0, credit: 1000 },
        ],
      });
      captures.push(capture);

      console.log(`POST /transactions/journal-entries => ${capture.response.status}`);
    });
  });

  test.describe('Chart of Accounts', () => {
    test('POST /transactions/chart-of-accounts - validation errors', async ({ api }) => {
      const capture = await api.post('transactions/chart-of-accounts', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /transactions/chart-of-accounts (empty) => ${capture.response.status}`);
    });

    test('POST /transactions/chart-of-accounts - create account', async ({ api }) => {
      const capture = await api.post('transactions/chart-of-accounts', {
        name: `Test Account ${Date.now()}`,
        code: `${Math.floor(Math.random() * 9000) + 1000}`,
        type: 'asset',
        parent_id: null,
      });
      captures.push(capture);

      console.log(`POST /transactions/chart-of-accounts => ${capture.response.status}`);
    });

    test('PUT /transactions/chart-of-accounts/{id} - update account', async ({ api }) => {
      const capture = await api.put('transactions/chart-of-accounts/1', {
        name: `Updated Account ${Date.now()}`,
      });
      captures.push(capture);

      console.log(`PUT /transactions/chart-of-accounts/1 => ${capture.response.status}`);
    });

    test('DELETE /transactions/chart-of-accounts/{id} - delete account', async ({ api }) => {
      const capture = await api.delete('transactions/chart-of-accounts/999');
      captures.push(capture);

      console.log(`DELETE /transactions/chart-of-accounts/999 => ${capture.response.status}`);
    });
  });

  test.describe('Transaction Categories', () => {
    test('POST /transactions/categories - create category', async ({ api }) => {
      const capture = await api.post('transactions/categories', {
        name: `Test Category ${Date.now()}`,
        type: 'in',
      });
      captures.push(capture);

      console.log(`POST /transactions/categories => ${capture.response.status}`);
    });

    test('PUT /transactions/categories/{id} - update category', async ({ api }) => {
      const capture = await api.put('transactions/categories/1', {
        name: `Updated Category ${Date.now()}`,
      });
      captures.push(capture);

      console.log(`PUT /transactions/categories/1 => ${capture.response.status}`);
    });
  });

  test.describe('Reference Data', () => {
    test('GET /transactions - list transactions', async ({ api }) => {
      try {
        const data = await api.get('transactions?filter_type=all&page=1');
        const transactions = (data as any)?.data || [];
        console.log(`GET /transactions => ${transactions.length} transactions found`);
      } catch (error) {
        console.log(`GET /transactions => Failed`);
      }
    });

    test('GET /transactions/categories - list categories', async ({ api }) => {
      try {
        const dataIn = await api.get('transactions/categories?type=in');
        const dataOut = await api.get('transactions/categories?type=out');
        const categoriesIn = (dataIn as any)?.data || [];
        const categoriesOut = (dataOut as any)?.data || [];
        console.log(`GET /transactions/categories => ${categoriesIn.length} in, ${categoriesOut.length} out`);
      } catch (error) {
        console.log(`GET /transactions/categories => Failed`);
      }
    });
  });
});
