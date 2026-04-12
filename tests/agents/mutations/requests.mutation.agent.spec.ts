/**
 * Requests Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Service settings
 * - Request status changes (pending, in-progress, completed, canceled)
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import { createServiceSettingsSampleData, EMPTY_DATA } from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

// Request status types
const REQUEST_STATUSES = ['pending', 'in-progress', 'completed', 'canceled', 'approved', 'rejected'];

test.describe('Requests Mutation Agent', () => {
  test.afterAll(async () => {
    await writeMutationCaptures('requests', captures);
    console.log(`\n=== Requests Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Errors: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Service Settings', () => {
    test('POST /rf/requests/service-settings/updateOrCreate - with valid data', async ({ api }) => {
      // First get available categories
      const categoriesData = await api.fetchReferenceData('rf/requests/categories?is_paginate=0');
      const categories = (categoriesData as any)?.data || [];

      if (categories.length > 0) {
        const category = categories[0];
        const subCategory =
          category.sub_categories?.[0] || category.children?.[0] || { id: category.id };

        const data = createServiceSettingsSampleData(category.id, subCategory.id);
        const capture = await api.post('rf/requests/service-settings/updateOrCreate', data);
        captures.push(capture);

        console.log(
          `POST /rf/requests/service-settings/updateOrCreate => ${capture.response.status}`
        );
      } else {
        // Try with placeholder IDs
        const data = createServiceSettingsSampleData(1, 1);
        const capture = await api.post('rf/requests/service-settings/updateOrCreate', data);
        captures.push(capture);

        console.log(
          `POST /rf/requests/service-settings/updateOrCreate => ${capture.response.status}`
        );
      }
    });

    test('POST /rf/requests/service-settings/updateOrCreate - validation errors', async ({
      api,
    }) => {
      const capture = await api.post('rf/requests/service-settings/updateOrCreate', EMPTY_DATA);
      captures.push(capture);

      console.log(
        `POST /rf/requests/service-settings/updateOrCreate (empty) => ${capture.response.status}`
      );
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });
  });

  test.describe('Request Status Changes', () => {
    // Test each status change endpoint
    for (const status of REQUEST_STATUSES) {
      test(`POST /rf/requests/change-status/${status} - validation errors`, async ({ api }) => {
        const capture = await api.post(`rf/requests/change-status/${status}`, EMPTY_DATA);
        captures.push(capture);

        console.log(`POST /rf/requests/change-status/${status} (empty) => ${capture.response.status}`);
        if (capture.validationErrors?.length) {
          capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
        }
      });
    }

    test('POST /rf/requests/change-status/canceled - with request ID', async ({ api }) => {
      // Get existing requests
      const requestsData = await api.fetchReferenceData('rf/requests?is_paginate=0');
      const requests = (requestsData as any)?.data || [];

      if (requests.length > 0) {
        const requestId = requests[0].id;
        const capture = await api.post('rf/requests/change-status/canceled', {
          request_id: requestId,
        });
        captures.push(capture);

        console.log(`POST /rf/requests/change-status/canceled => ${capture.response.status}`);
      } else {
        console.log('Skipped: No requests available for status change');
      }
    });

    test('POST /rf/requests/change-status/completed - with request ID', async ({ api }) => {
      const requestsData = await api.fetchReferenceData('rf/requests?is_paginate=0');
      const requests = (requestsData as any)?.data || [];

      if (requests.length > 0) {
        const requestId = requests[0].id;
        const capture = await api.post('rf/requests/change-status/completed', {
          request_id: requestId,
        });
        captures.push(capture);

        console.log(`POST /rf/requests/change-status/completed => ${capture.response.status}`);
      }
    });
  });

  test.describe('Request Creation', () => {
    test('POST /rf/requests - create new request', async ({ api }) => {
      // Get categories first
      const categoriesData = await api.fetchReferenceData('rf/requests/categories?is_paginate=0');
      const categories = (categoriesData as any)?.data || [];

      // Get units
      const unitsData = await api.fetchReferenceData('rf/units?is_paginate=0');
      const units = (unitsData as any)?.data || [];

      if (categories.length > 0 && units.length > 0) {
        const capture = await api.post('rf/requests', {
          category_id: categories[0].id,
          rf_unit_id: units[0].id,
          description: `Test request ${Date.now()}`,
          priority: 'normal',
        });
        captures.push(capture);

        console.log(`POST /rf/requests => ${capture.response.status}`);
        if (capture.success) {
          console.log(`  Created request ID: ${api.extractFirstId(capture.response.body)}`);
        }
      } else {
        console.log('Skipped: Missing categories or units for request creation');
      }
    });

    test('POST /rf/requests - validation errors', async ({ api }) => {
      const capture = await api.post('rf/requests', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/requests (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });
  });

  test.describe('Reference Data', () => {
    test('GET /rf/requests/categories - list categories', async ({ api }) => {
      try {
        const data = await api.get('rf/requests/categories?is_paginate=0');
        const categories = (data as any)?.data || [];
        console.log(`GET /rf/requests/categories => ${categories.length} categories found`);
      } catch (error) {
        console.log(`GET /rf/requests/categories => Failed`);
      }
    });

    test('GET /rf/requests - list requests', async ({ api }) => {
      try {
        const data = await api.get('rf/requests?is_paginate=0');
        const requests = (data as any)?.data || [];
        console.log(`GET /rf/requests => ${requests.length} requests found`);
      } catch (error) {
        console.log(`GET /rf/requests => Failed`);
      }
    });
  });
});
