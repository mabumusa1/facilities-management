/**
 * Transactions Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Transaction recording (money in/out)
 * - Transaction categories
 *
 * Note: invoice-settings, journal-entries, and chart-of-accounts endpoints
 * return 404 in this tenant - they may not be enabled features.
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import { EMPTY_DATA } from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

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
    // The correct endpoint is rf/transactions, not transactions
    test('POST /rf/transactions - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/transactions', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/transactions (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/transactions - record money in transaction', async ({ api }) => {
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

      const capture = await api.post('rf/transactions', data);
      captures.push(capture);

      console.log(`POST /rf/transactions (money_in) => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created transaction ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('POST /rf/transactions - record money out transaction', async ({ api }) => {
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

      const capture = await api.post('rf/transactions', data);
      captures.push(capture);

      console.log(`POST /rf/transactions (money_out) => ${capture.response.status}`);
    });

    test('PUT /rf/transactions/{id} - update transaction', async ({ api }) => {
      const transactionsData = await api.fetchReferenceData('transactions?filter_type=all&page=1');
      const transactions = (transactionsData as any)?.data || [];

      if (transactions.length > 0) {
        const transactionId = transactions[0].id;
        const capture = await api.put(`rf/transactions/${transactionId}`, {
          description: `Updated transaction ${Date.now()}`,
        });
        captures.push(capture);

        console.log(`PUT /rf/transactions/${transactionId} => ${capture.response.status}`);
      } else {
        // Try with a dummy ID to capture validation
        const capture = await api.put('rf/transactions/1', {
          description: `Updated transaction ${Date.now()}`,
        });
        captures.push(capture);
        console.log(`PUT /rf/transactions/1 => ${capture.response.status}`);
      }
    });

    test('DELETE /rf/transactions/{id} - delete transaction', async ({ api }) => {
      const transactionsData = await api.fetchReferenceData('transactions?filter_type=all&page=1');
      const transactions = (transactionsData as any)?.data || [];

      if (transactions.length > 0) {
        // Use a newer transaction to avoid deleting important data
        const transactionId = transactions[transactions.length - 1].id;
        const capture = await api.delete(`rf/transactions/${transactionId}`);
        captures.push(capture);

        console.log(`DELETE /rf/transactions/${transactionId} => ${capture.response.status}`);
      } else {
        // Try with a non-existent ID to capture error response
        const capture = await api.delete('rf/transactions/99999');
        captures.push(capture);
        console.log(`DELETE /rf/transactions/99999 => ${capture.response.status}`);
      }
    });
  });

  test.describe('Invoice Settings', () => {
    // invoice-settings endpoint exists but uses PUT method
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

    test('POST /invoice-settings - validation errors', async ({ api }) => {
      const capture = await api.post('invoice-settings', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /invoice-settings => ${capture.response.status}`);
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
