/**
 * Settings Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Announcements
 * - Notifications (mark as read)
 *
 * Note: Most rf/settings/* endpoints return 404 - they may not exist
 * in this tenant or require different URL patterns.
 * Only rf/announcements and notifications/mark-all-as-read are confirmed working.
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import { EMPTY_DATA } from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

test.describe('Settings Mutation Agent', () => {
  // Run all tests in serial mode to ensure captures are collected properly
  test.describe.configure({ mode: 'serial' });
  test.afterAll(async () => {
    await writeMutationCaptures('settings', captures);
    console.log(`\n=== Settings Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Errors: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Announcements', () => {
    test('POST /rf/announcements - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/announcements', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/announcements (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/announcements - create announcement with required fields', async ({ api }) => {
      const capture = await api.post('rf/announcements', {
        title: `Test Announcement ${Date.now()}`,
        content: 'This is a test announcement content',
        type: 'general',
        start_date: new Date().toISOString().split('T')[0],
      });
      captures.push(capture);

      console.log(`POST /rf/announcements => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created announcement ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('POST /rf/announcements - partial data for validation', async ({ api }) => {
      // Send only title to trigger other required field validations
      const capture = await api.post('rf/announcements', {
        title: 'Test Title Only',
      });
      captures.push(capture);

      console.log(`POST /rf/announcements (title only) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('PUT /rf/announcements/{id} - update announcement', async ({ api }) => {
      const announcementsData = await api.fetchReferenceData('rf/announcements?type=upcoming');
      const announcements = (announcementsData as any)?.data || [];

      if (announcements.length > 0) {
        const announcementId = announcements[0].id;
        const capture = await api.put(`rf/announcements/${announcementId}`, {
          title: `Updated Announcement ${Date.now()}`,
        });
        captures.push(capture);

        console.log(`PUT /rf/announcements/${announcementId} => ${capture.response.status}`);
      } else {
        // Try with a dummy ID to capture validation
        const capture = await api.put('rf/announcements/1', {
          title: `Updated Announcement ${Date.now()}`,
        });
        captures.push(capture);
        console.log(`PUT /rf/announcements/1 => ${capture.response.status}`);
      }
    });

    test('DELETE /rf/announcements/{id} - delete announcement', async ({ api }) => {
      const announcementsData = await api.fetchReferenceData('rf/announcements?type=upcoming');
      const announcements = (announcementsData as any)?.data || [];

      if (announcements.length > 0) {
        const announcementId = announcements[announcements.length - 1].id;
        const capture = await api.delete(`rf/announcements/${announcementId}`);
        captures.push(capture);

        console.log(`DELETE /rf/announcements/${announcementId} => ${capture.response.status}`);
      } else {
        const capture = await api.delete('rf/announcements/99999');
        captures.push(capture);
        console.log(`DELETE /rf/announcements/99999 => ${capture.response.status}`);
      }
    });
  });

  test.describe('Notifications', () => {
    test('POST /notifications/mark-all-as-read - mark all notifications as read', async ({ api }) => {
      const capture = await api.post('notifications/mark-all-as-read', {});
      captures.push(capture);

      console.log(`POST /notifications/mark-all-as-read => ${capture.response.status}`);
    });
  });

  test.describe('Facilities', () => {
    // rf/facilities endpoints are managed under properties but also appear in settings
    test('POST /rf/facilities - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/facilities', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/facilities (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/facilities - create facility', async ({ api }) => {
      // Get communities for facility creation
      const communitiesData = await api.fetchReferenceData('rf/communities?is_paginate=0');
      const communities = (communitiesData as any)?.data || [];

      const data: Record<string, unknown> = {
        name: `Test Facility ${Date.now()}`,
        name_ar: `مرفق اختباري ${Date.now()}`,
        type: 'amenity',
      };

      if (communities.length > 0) {
        data.rf_community_id = communities[0].id;
      }

      const capture = await api.post('rf/facilities', data);
      captures.push(capture);

      console.log(`POST /rf/facilities => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created facility ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });
  });

  test.describe('Request Service Settings', () => {
    // This endpoint is for configuring service request settings
    test('POST /rf/requests/service-settings/updateOrCreate - validation errors', async ({ api }) => {
      const capture = await api.post('rf/requests/service-settings/updateOrCreate', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/requests/service-settings/updateOrCreate (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/requests/service-settings/updateOrCreate - with data', async ({ api }) => {
      // Get request sub-categories
      const subCategoriesData = await api.fetchReferenceData('rf/requests/sub-categories');
      const subCategories = (subCategoriesData as any)?.data || [];

      if (subCategories.length > 0) {
        const capture = await api.post('rf/requests/service-settings/updateOrCreate', {
          rf_sub_category_id: subCategories[0].id,
          is_active: true,
          max_slots_per_day: 10,
          service_fee: 100,
        });
        captures.push(capture);

        console.log(`POST /rf/requests/service-settings/updateOrCreate => ${capture.response.status}`);
      }
    });
  });

  test.describe('Reference Data', () => {
    test('GET /rf/announcements - list announcements', async ({ api }) => {
      try {
        const data = await api.get('rf/announcements?type=upcoming');
        const announcements = (data as any)?.data || [];
        console.log(`GET /rf/announcements => ${announcements.length} announcements found`);
      } catch (error) {
        console.log(`GET /rf/announcements => Failed`);
      }
    });

    test('GET /rf/modules - list modules', async ({ api }) => {
      try {
        const data = await api.get('rf/modules');
        console.log(`GET /rf/modules => Success`);
      } catch (error) {
        console.log(`GET /rf/modules => Failed`);
      }
    });
  });
});
