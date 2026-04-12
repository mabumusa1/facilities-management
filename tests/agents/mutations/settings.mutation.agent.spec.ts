/**
 * Settings Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - General settings
 * - Notification settings
 * - Company information
 * - Email/SMS templates
 * - Roles and permissions
 * - API keys
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

  test.describe('Notification Settings', () => {
    test('PUT /notifications - update notification settings', async ({ api }) => {
      const capture = await api.put('notifications', {
        email_notifications: true,
        sms_notifications: true,
        push_notifications: true,
      });
      captures.push(capture);

      console.log(`PUT /notifications => ${capture.response.status}`);
    });

    test('POST /notifications/settings - update notification preferences', async ({ api }) => {
      const capture = await api.post('notifications/settings', {
        lease_expiry: true,
        payment_reminder: true,
        maintenance_updates: true,
      });
      captures.push(capture);

      console.log(`POST /notifications/settings => ${capture.response.status}`);
    });

    test('PUT /notifications/mark-read - mark notifications as read', async ({ api }) => {
      const capture = await api.put('notifications/mark-read', {
        notification_ids: [1, 2, 3],
      });
      captures.push(capture);

      console.log(`PUT /notifications/mark-read => ${capture.response.status}`);
    });

    test('DELETE /notifications/{id} - delete notification', async ({ api }) => {
      const capture = await api.delete('notifications/1');
      captures.push(capture);

      console.log(`DELETE /notifications/1 => ${capture.response.status}`);
    });
  });

  test.describe('Company Settings', () => {
    test('POST /rf/settings/company - validation errors', async ({ api }) => {
      const capture = await api.post('rf/settings/company', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/settings/company (empty) => ${capture.response.status}`);
    });

    test('PUT /rf/settings/company - update company info', async ({ api }) => {
      const capture = await api.put('rf/settings/company', {
        name: 'Test Company',
        email: 'company@test.com',
        phone: '+966500000000',
        address: 'Test Address, Riyadh',
      });
      captures.push(capture);

      console.log(`PUT /rf/settings/company => ${capture.response.status}`);
    });

    test('POST /rf/settings/logo - upload company logo', async ({ api }) => {
      // This would normally need file upload
      const capture = await api.post('rf/settings/logo', {
        logo_url: 'https://example.com/logo.png',
      });
      captures.push(capture);

      console.log(`POST /rf/settings/logo => ${capture.response.status}`);
    });
  });

  test.describe('Roles and Permissions', () => {
    test('POST /rf/roles - validation errors', async ({ api }) => {
      const capture = await api.post('rf/roles', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/roles (empty) => ${capture.response.status}`);
    });

    test('POST /rf/roles - create role', async ({ api }) => {
      const capture = await api.post('rf/roles', {
        name: `Test Role ${Date.now()}`,
        permissions: ['view_dashboard', 'view_properties'],
      });
      captures.push(capture);

      console.log(`POST /rf/roles => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created role ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('PUT /rf/roles/{id} - update role', async ({ api }) => {
      // Get existing roles
      const rolesData = await api.fetchReferenceData('rf/roles?is_paginate=0');
      const roles = (rolesData as any)?.data || [];

      if (roles.length > 0) {
        const roleId = roles[roles.length - 1].id;
        const capture = await api.put(`rf/roles/${roleId}`, {
          name: `Updated Role ${Date.now()}`,
        });
        captures.push(capture);

        console.log(`PUT /rf/roles/${roleId} => ${capture.response.status}`);
      }
    });

    test('DELETE /rf/roles/{id} - delete role', async ({ api }) => {
      const rolesData = await api.fetchReferenceData('rf/roles?is_paginate=0');
      const roles = (rolesData as any)?.data || [];

      if (roles.length > 1) {
        const roleId = roles[roles.length - 1].id;
        const capture = await api.delete(`rf/roles/${roleId}`);
        captures.push(capture);

        console.log(`DELETE /rf/roles/${roleId} => ${capture.response.status}`);
      }
    });
  });

  test.describe('Email Templates', () => {
    test('POST /rf/settings/email-templates - validation errors', async ({ api }) => {
      const capture = await api.post('rf/settings/email-templates', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/settings/email-templates (empty) => ${capture.response.status}`);
    });

    test('POST /rf/settings/email-templates - create template', async ({ api }) => {
      const capture = await api.post('rf/settings/email-templates', {
        name: `Test Template ${Date.now()}`,
        subject: 'Test Subject',
        body: '<p>Test email body</p>',
        type: 'lease_reminder',
      });
      captures.push(capture);

      console.log(`POST /rf/settings/email-templates => ${capture.response.status}`);
    });

    test('PUT /rf/settings/email-templates/{id} - update template', async ({ api }) => {
      const capture = await api.put('rf/settings/email-templates/1', {
        subject: `Updated Subject ${Date.now()}`,
      });
      captures.push(capture);

      console.log(`PUT /rf/settings/email-templates/1 => ${capture.response.status}`);
    });
  });

  test.describe('SMS Templates', () => {
    test('POST /rf/settings/sms-templates - validation errors', async ({ api }) => {
      const capture = await api.post('rf/settings/sms-templates', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/settings/sms-templates (empty) => ${capture.response.status}`);
    });

    test('POST /rf/settings/sms-templates - create template', async ({ api }) => {
      const capture = await api.post('rf/settings/sms-templates', {
        name: `SMS Template ${Date.now()}`,
        body: 'Test SMS message',
        type: 'payment_reminder',
      });
      captures.push(capture);

      console.log(`POST /rf/settings/sms-templates => ${capture.response.status}`);
    });

    test('PUT /rf/settings/sms-templates/{id} - update template', async ({ api }) => {
      const capture = await api.put('rf/settings/sms-templates/1', {
        body: `Updated SMS ${Date.now()}`,
      });
      captures.push(capture);

      console.log(`PUT /rf/settings/sms-templates/1 => ${capture.response.status}`);
    });
  });

  test.describe('General Settings', () => {
    test('PUT /rf/settings - update general settings', async ({ api }) => {
      const capture = await api.put('rf/settings', {
        language: 'en',
        timezone: 'Asia/Riyadh',
        date_format: 'YYYY-MM-DD',
        currency: 'SAR',
      });
      captures.push(capture);

      console.log(`PUT /rf/settings => ${capture.response.status}`);
    });

    test('POST /rf/settings/preferences - update user preferences', async ({ api }) => {
      const capture = await api.post('rf/settings/preferences', {
        dashboard_layout: 'grid',
        items_per_page: 25,
      });
      captures.push(capture);

      console.log(`POST /rf/settings/preferences => ${capture.response.status}`);
    });
  });

  test.describe('Mobile Notifications', () => {
    test('POST /rf/settings/mobile-notifications - update mobile settings', async ({ api }) => {
      const capture = await api.post('rf/settings/mobile-notifications', {
        enabled: true,
        sound: true,
        vibrate: true,
      });
      captures.push(capture);

      console.log(`POST /rf/settings/mobile-notifications => ${capture.response.status}`);
    });
  });

  test.describe('Announcements', () => {
    test('POST /rf/announcements - create announcement', async ({ api }) => {
      const capture = await api.post('rf/announcements', {
        title: `Test Announcement ${Date.now()}`,
        content: 'This is a test announcement',
        type: 'general',
        start_date: new Date().toISOString().split('T')[0],
      });
      captures.push(capture);

      console.log(`POST /rf/announcements => ${capture.response.status}`);
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
      }
    });
  });

  test.describe('Reference Data', () => {
    test('GET /rf/roles - list roles', async ({ api }) => {
      try {
        const data = await api.get('rf/roles?is_paginate=0');
        const roles = (data as any)?.data || [];
        console.log(`GET /rf/roles => ${roles.length} roles found`);
      } catch (error) {
        console.log(`GET /rf/roles => Failed`);
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
