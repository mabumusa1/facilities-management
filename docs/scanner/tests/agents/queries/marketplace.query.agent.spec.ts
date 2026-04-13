/**
 * Marketplace Query Agent
 *
 * Captures GET requests for:
 * - Marketplace settings (banks, sales, visits)
 * - Marketplace units
 * - Marketplace visits
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeQueryCaptures } from '../../utils/query-output-writer';
import { QueryCapture } from '../../utils/query-types';

const captures: QueryCapture[] = [];

test.describe('Marketplace Query Agent', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterAll(async () => {
    await writeQueryCaptures('marketplace', captures);
    console.log(`\n=== Marketplace Query Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
  });

  test.describe('Settings', () => {
    test('GET /marketplace/admin/settings/banks - bank settings', async ({ api }) => {
      const capture = await api.getCapture('marketplace/admin/settings/banks');
      captures.push(capture);
    });

    test('GET /marketplace/admin/settings/sales - sales settings', async ({ api }) => {
      const capture = await api.getCapture('marketplace/admin/settings/sales');
      captures.push(capture);
    });

    test('GET /marketplace/admin/settings/visits - visit settings', async ({ api }) => {
      const capture = await api.getCapture('marketplace/admin/settings/visits');
      captures.push(capture);
    });
  });

  test.describe('Units', () => {
    test('GET /marketplace/admin/units - marketplace units list', async ({ api }) => {
      const capture = await api.getCapture('marketplace/admin/units');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      console.log(`  Found ${ids.length} marketplace units`);
    });

    test('GET /marketplace/admin/units - with pagination', async ({ api }) => {
      const capture = await api.getCapture('marketplace/admin/units', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);
    });
  });

  test.describe('Visits', () => {
    test('GET /marketplace/admin/visits - marketplace visits', async ({ api }) => {
      const capture = await api.getCapture('marketplace/admin/visits');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      console.log(`  Found ${ids.length} visits`);
    });

    test('GET /marketplace/admin/visits - with pagination', async ({ api }) => {
      const capture = await api.getCapture('marketplace/admin/visits', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);
    });
  });

  test.describe('Communities', () => {
    test('GET /marketplace/admin/communities/list - marketplace communities', async ({ api }) => {
      const capture = await api.getCapture('marketplace/admin/communities/list');
      captures.push(capture);
    });
  });
});
