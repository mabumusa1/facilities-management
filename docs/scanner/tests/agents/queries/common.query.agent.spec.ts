/**
 * Common Lookups Query Agent
 *
 * Captures GET requests for:
 * - User profile & auth
 * - Dashboard data
 * - Notifications
 * - Common lookup lists
 * - Countries, cities, districts
 * - Modules, statuses
 * - Announcements, leads
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeQueryCaptures } from '../../utils/query-output-writer';
import { QueryCapture } from '../../utils/query-types';

const captures: QueryCapture[] = [];

test.describe('Common Lookups Query Agent', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterAll(async () => {
    await writeQueryCaptures('common', captures);
    console.log(`\n=== Common Lookups Query Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
  });

  test.describe('User & Auth', () => {
    test('GET /me - current user profile', async ({ api }) => {
      const capture = await api.getCapture('me');
      captures.push(capture);
      // Don't assert - capture for documentation even if 404
    });
  });

  test.describe('Dashboard', () => {
    test('GET /dashboard/requires-attention - attention items', async ({ api }) => {
      const capture = await api.getCapture('dashboard/requires-attention');
      captures.push(capture);
    });

    test('GET /dashboard/statistics - dashboard stats', async ({ api }) => {
      const capture = await api.getCapture('dashboard/statistics');
      captures.push(capture);
    });
  });

  test.describe('Notifications', () => {
    test('GET /notifications - list notifications', async ({ api }) => {
      const capture = await api.getCapture('notifications');
      captures.push(capture);
    });

    test('GET /notifications/unread-count - unread count', async ({ api }) => {
      const capture = await api.getCapture('notifications/unread-count');
      captures.push(capture);
    });
  });

  test.describe('Modules & Statuses', () => {
    test('GET /rf/modules - available modules', async ({ api }) => {
      const capture = await api.getCapture('rf/modules');
      captures.push(capture);
    });

    test('GET /rf/statuses - available statuses', async ({ api }) => {
      const capture = await api.getCapture('rf/statuses');
      captures.push(capture);
    });
  });

  test.describe('Geography', () => {
    test('GET /countries - list countries', async ({ api }) => {
      const capture = await api.getCapture('countries');
      captures.push(capture);
    });

    test('GET /tenancy/api/cities/all - all cities', async ({ api }) => {
      const capture = await api.getCapture('tenancy/api/cities/all');
      captures.push(capture);
    });

    test('GET /tenancy/api/districts/all - all districts', async ({ api }) => {
      const capture = await api.getCapture('tenancy/api/districts/all');
      captures.push(capture);
    });
  });

  test.describe('Common Lists', () => {
    test('GET /rf/common-lists - common lookup lists', async ({ api }) => {
      const capture = await api.getCapture('rf/common-lists');
      captures.push(capture);
    });
  });

  test.describe('Announcements', () => {
    let announcementId: string | undefined;

    test('GET /rf/announcements - list announcements', async ({ api }) => {
      const capture = await api.getCapture('rf/announcements');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      announcementId = ids[0];
      console.log(`  Found ${ids.length} announcements`);
    });

    test('GET /rf/announcements/{id} - announcement detail', async ({ api }) => {
      if (!announcementId) {
        const listData = await api.get('rf/announcements');
        announcementId = api.extractFirstId(listData);
      }

      if (announcementId) {
        const capture = await api.getCapture(`rf/announcements/${announcementId}`);
        captures.push(capture);
      }
    });
  });

  test.describe('Leads', () => {
    test('GET /rf/leads - list leads', async ({ api }) => {
      const capture = await api.getCapture('rf/leads');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      console.log(`  Found ${ids.length} leads`);
    });
  });

  test.describe('Integrations', () => {
    test('GET /integrations/powerbi/types - Power BI types', async ({ api }) => {
      const capture = await api.getCapture('integrations/powerbi/types');
      captures.push(capture);
    });
  });

  test.describe('Excel Templates', () => {
    test('GET /rf/excel-sheets - excel sheets', async ({ api }) => {
      const capture = await api.getCapture('rf/excel-sheets');
      captures.push(capture);
    });
  });
});
