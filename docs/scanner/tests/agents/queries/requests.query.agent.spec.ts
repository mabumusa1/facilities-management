/**
 * Requests Query Agent
 *
 * Captures GET requests for:
 * - Service requests (list, detail)
 * - Categories, sub-categories, types
 * - Service settings
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeQueryCaptures } from '../../utils/query-output-writer';
import { QueryCapture } from '../../utils/query-types';

const captures: QueryCapture[] = [];

test.describe('Requests Query Agent', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterAll(async () => {
    await writeQueryCaptures('requests', captures);
    console.log(`\n=== Requests Query Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
  });

  test.describe('Service Requests', () => {
    let requestId: string | undefined;

    test('GET /rf/requests - list all requests', async ({ api }) => {
      const capture = await api.getCapture('rf/requests');
      captures.push(capture);

      // Capture for documentation
      const ids = api.extractIds(capture.response.body);
      requestId = ids[0];
      console.log(`  Found ${ids.length} requests`);
    });

    test('GET /rf/requests - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/requests', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);
    });

    test('GET /rf/requests/{id} - request detail', async ({ api }) => {
      if (!requestId) {
        const listData = await api.get('rf/requests');
        requestId = api.extractFirstId(listData);
      }

      if (requestId) {
        const capture = await api.getCapture(`rf/requests/${requestId}`);
        captures.push(capture);
      }
    });
  });

  test.describe('Categories', () => {
    let categoryId: string | undefined;

    test('GET /rf/requests/categories - list categories', async ({ api }) => {
      const capture = await api.getCapture('rf/requests/categories');
      captures.push(capture);

      // Capture for documentation
      const ids = api.extractIds(capture.response.body);
      categoryId = ids[0];
      console.log(`  Found ${ids.length} categories`);
    });

    test('GET /rf/requests/categories/{id} - category detail', async ({ api }) => {
      if (!categoryId) {
        const listData = await api.get('rf/requests/categories');
        categoryId = api.extractFirstId(listData);
      }

      if (categoryId) {
        const capture = await api.getCapture(`rf/requests/categories/${categoryId}`);
        captures.push(capture);
      }
    });
  });

  test.describe('Sub-Categories', () => {
    let subCategoryId: string | undefined;

    test('GET /rf/requests/sub-categories - list sub-categories', async ({ api }) => {
      const capture = await api.getCapture('rf/requests/sub-categories');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      subCategoryId = ids[0];
      console.log(`  Found ${ids.length} sub-categories`);
    });

    test('GET /rf/requests/sub-categories/{id} - sub-category detail', async ({ api }) => {
      if (!subCategoryId) {
        const listData = await api.get('rf/requests/sub-categories');
        subCategoryId = api.extractFirstId(listData);
      }

      if (subCategoryId) {
        const capture = await api.getCapture(`rf/requests/sub-categories/${subCategoryId}`);
        captures.push(capture);
      }
    });
  });

  test.describe('Types', () => {
    let typeId: string | undefined;

    test('GET /rf/requests/types - list request types', async ({ api }) => {
      const capture = await api.getCapture('rf/requests/types');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      typeId = ids[0];
      console.log(`  Found ${ids.length} request types`);
    });

    test('GET /rf/requests/types/{id} - type detail', async ({ api }) => {
      if (!typeId) {
        const listData = await api.get('rf/requests/types');
        typeId = api.extractFirstId(listData);
      }

      if (typeId) {
        const capture = await api.getCapture(`rf/requests/types/${typeId}`);
        captures.push(capture);
      }
    });
  });

  test.describe('Service Settings', () => {
    test('GET /rf/requests/service-settings - list service settings', async ({ api }) => {
      const capture = await api.getCapture('rf/requests/service-settings');
      captures.push(capture);
    });
  });
});
