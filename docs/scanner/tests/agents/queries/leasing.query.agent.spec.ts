/**
 * Leasing Query Agent
 *
 * Captures GET requests for:
 * - Leases (list, detail, statistics)
 * - Sub-leases (list, detail)
 * - Lease-related lookups
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeQueryCaptures } from '../../utils/query-output-writer';
import { QueryCapture } from '../../utils/query-types';

const captures: QueryCapture[] = [];

test.describe('Leasing Query Agent', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterAll(async () => {
    await writeQueryCaptures('leasing', captures);
    console.log(`\n=== Leasing Query Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
  });

  test.describe('Leases', () => {
    let leaseId: string | undefined;

    test('GET /rf/leases - list all leases', async ({ api }) => {
      const capture = await api.getCapture('rf/leases');
      captures.push(capture);

      // Capture for documentation
      const ids = api.extractIds(capture.response.body);
      leaseId = ids[0];
      console.log(`  Found ${ids.length} leases`);
    });

    test('GET /rf/leases - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/leases', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);
    });

    test('GET /rf/leases - filter by status (active)', async ({ api }) => {
      // Status 31 = active contract
      const capture = await api.getCapture('rf/leases', {
        params: { status_id: 31 }
      });
      captures.push(capture);
    });

    test('GET /rf/leases - filter by status (expired)', async ({ api }) => {
      // Status 32 = expired contract
      const capture = await api.getCapture('rf/leases', {
        params: { status_id: 32 }
      });
      captures.push(capture);
    });

    test('GET /rf/leases/{id} - lease detail', async ({ api }) => {
      if (!leaseId) {
        const listData = await api.get('rf/leases');
        leaseId = api.extractFirstId(listData);
      }

      if (leaseId) {
        const capture = await api.getCapture(`rf/leases/${leaseId}`);
        captures.push(capture);
        // Capture for documentation
      }
    });

    test('GET /rf/leases/statistics - lease statistics', async ({ api }) => {
      const capture = await api.getCapture('rf/leases/statistics');
      captures.push(capture);
    });

    test('GET /rf/leases/expiring - expiring leases', async ({ api }) => {
      const capture = await api.getCapture('rf/leases/expiring');
      captures.push(capture);
    });
  });

  test.describe('Sub-leases', () => {
    let subLeaseId: string | undefined;

    test('GET /rf/sub-leases - list all sub-leases', async ({ api }) => {
      const capture = await api.getCapture('rf/sub-leases');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      subLeaseId = ids[0];
      console.log(`  Found ${ids.length} sub-leases`);
    });

    test('GET /rf/sub-leases/{id} - sub-lease detail', async ({ api }) => {
      if (!subLeaseId) {
        const listData = await api.get('rf/sub-leases');
        subLeaseId = api.extractFirstId(listData);
      }

      if (subLeaseId) {
        const capture = await api.getCapture(`rf/sub-leases/${subLeaseId}`);
        captures.push(capture);
      }
    });
  });

  test.describe('Lease Lookups', () => {
    test('GET /rf/rental-contract-types - contract types', async ({ api }) => {
      const capture = await api.getCapture('rf/rental-contract-types');
      captures.push(capture);
    });

    test('GET /rf/payment-schedules - payment schedules', async ({ api }) => {
      const capture = await api.getCapture('rf/payment-schedules');
      captures.push(capture);
    });
  });
});
