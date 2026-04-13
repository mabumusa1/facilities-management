/**
 * Contacts Query Agent
 *
 * Captures GET requests for:
 * - Owners (list, detail)
 * - Tenants (list, detail)
 * - Admins (list, detail)
 * - Professionals (list, detail)
 * - Contact statistics
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeQueryCaptures } from '../../utils/query-output-writer';
import { QueryCapture } from '../../utils/query-types';

const captures: QueryCapture[] = [];

test.describe('Contacts Query Agent', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterAll(async () => {
    await writeQueryCaptures('contacts', captures);
    console.log(`\n=== Contacts Query Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
  });

  test.describe('Owners', () => {
    let ownerId: string | undefined;

    test('GET /rf/owners - list all owners', async ({ api }) => {
      const capture = await api.getCapture('rf/owners');
      captures.push(capture);

      // Capture for documentation
      const ids = api.extractIds(capture.response.body);
      ownerId = ids[0];
      console.log(`  Found ${ids.length} owners`);
    });

    test('GET /rf/owners - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/owners', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);
    });

    test('GET /rf/owners - search by name', async ({ api }) => {
      const capture = await api.getCapture('rf/owners', {
        params: { search: 'test' }
      });
      captures.push(capture);
    });

    test('GET /rf/owners/{id} - owner detail', async ({ api }) => {
      if (!ownerId) {
        const listData = await api.get('rf/owners');
        ownerId = api.extractFirstId(listData);
      }

      if (ownerId) {
        const capture = await api.getCapture(`rf/owners/${ownerId}`);
        captures.push(capture);
        // Capture for documentation
      }
    });
  });

  test.describe('Tenants', () => {
    let tenantId: string | undefined;

    test('GET /rf/tenants - list all tenants', async ({ api }) => {
      const capture = await api.getCapture('rf/tenants');
      captures.push(capture);

      // Capture for documentation
      const ids = api.extractIds(capture.response.body);
      tenantId = ids[0];
      console.log(`  Found ${ids.length} tenants`);
    });

    test('GET /rf/tenants - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/tenants', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);
    });

    test('GET /rf/tenants/{id} - tenant detail', async ({ api }) => {
      if (!tenantId) {
        const listData = await api.get('rf/tenants');
        tenantId = api.extractFirstId(listData);
      }

      if (tenantId) {
        const capture = await api.getCapture(`rf/tenants/${tenantId}`);
        captures.push(capture);
        // Capture for documentation
      }
    });
  });

  test.describe('Admins', () => {
    let adminId: string | undefined;

    test('GET /rf/admins - list all admins', async ({ api }) => {
      const capture = await api.getCapture('rf/admins');
      captures.push(capture);

      // Capture for documentation
      const ids = api.extractIds(capture.response.body);
      adminId = ids[0];
      console.log(`  Found ${ids.length} admins`);
    });

    test('GET /rf/admins/{id} - admin detail', async ({ api }) => {
      if (!adminId) {
        const listData = await api.get('rf/admins');
        adminId = api.extractFirstId(listData);
      }

      if (adminId) {
        const capture = await api.getCapture(`rf/admins/${adminId}`);
        captures.push(capture);
      }
    });

    test('GET /rf/admins/manager-roles - available roles', async ({ api }) => {
      const capture = await api.getCapture('rf/admins/manager-roles');
      captures.push(capture);
    });
  });

  test.describe('Professionals', () => {
    let professionalId: string | undefined;

    test('GET /rf/professionals - list all professionals', async ({ api }) => {
      const capture = await api.getCapture('rf/professionals');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      professionalId = ids[0];
      console.log(`  Found ${ids.length} professionals`);
    });

    test('GET /rf/professionals/{id} - professional detail', async ({ api }) => {
      if (!professionalId) {
        const listData = await api.get('rf/professionals');
        professionalId = api.extractFirstId(listData);
      }

      if (professionalId) {
        const capture = await api.getCapture(`rf/professionals/${professionalId}`);
        captures.push(capture);
      }
    });
  });
});
