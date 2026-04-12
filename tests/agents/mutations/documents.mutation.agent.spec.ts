/**
 * Documents Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Document uploads
 * - Document management
 * - Directory entries
 * - File attachments
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import { EMPTY_DATA } from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

test.describe('Documents Mutation Agent', () => {
  // Run all tests in serial mode to ensure captures are collected properly
  test.describe.configure({ mode: 'serial' });
  test.afterAll(async () => {
    await writeMutationCaptures('documents', captures);
    console.log(`\n=== Documents Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Errors: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Document Management', () => {
    test('POST /rf/documents - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/documents', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/documents (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/documents - upload document metadata', async ({ api }) => {
      // Get a unit to attach document to
      const unitsData = await api.fetchReferenceData('rf/units?is_paginate=0');
      const units = (unitsData as any)?.data || [];

      const data: Record<string, unknown> = {
        name: `Test Document ${Date.now()}`,
        type: 'contract',
        description: 'Test document description',
      };

      if (units.length > 0) {
        data.documentable_type = 'unit';
        data.documentable_id = units[0].id;
      }

      const capture = await api.post('rf/documents', data);
      captures.push(capture);

      console.log(`POST /rf/documents => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created document ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('PUT /rf/documents/{id} - update document', async ({ api }) => {
      const capture = await api.put('rf/documents/1', {
        name: `Updated Document ${Date.now()}`,
        description: 'Updated description',
      });
      captures.push(capture);

      console.log(`PUT /rf/documents/1 => ${capture.response.status}`);
    });

    test('DELETE /rf/documents/{id} - delete document', async ({ api }) => {
      const capture = await api.delete('rf/documents/999');
      captures.push(capture);

      console.log(`DELETE /rf/documents/999 => ${capture.response.status}`);
    });
  });

  test.describe('Lease Documents', () => {
    test('POST /rf/leases/{id}/documents - attach document to lease', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const leaseId = leases[0].id;
        const capture = await api.post(`rf/leases/${leaseId}/documents`, {
          name: `Lease Document ${Date.now()}`,
          type: 'contract',
          description: 'Lease document',
        });
        captures.push(capture);

        console.log(`POST /rf/leases/${leaseId}/documents => ${capture.response.status}`);
      } else {
        console.log('Skipped: No leases available for document attachment');
      }
    });
  });

  test.describe('Unit Documents', () => {
    test('POST /rf/units/{id}/documents - attach document to unit', async ({ api }) => {
      const unitsData = await api.fetchReferenceData('rf/units?is_paginate=0');
      const units = (unitsData as any)?.data || [];

      if (units.length > 0) {
        const unitId = units[0].id;
        const capture = await api.post(`rf/units/${unitId}/documents`, {
          name: `Unit Document ${Date.now()}`,
          type: 'floor_plan',
          description: 'Unit document',
        });
        captures.push(capture);

        console.log(`POST /rf/units/${unitId}/documents => ${capture.response.status}`);
      } else {
        console.log('Skipped: No units available for document attachment');
      }
    });

    test('POST /rf/units/{id}/images - upload unit images', async ({ api }) => {
      const unitsData = await api.fetchReferenceData('rf/units?is_paginate=0');
      const units = (unitsData as any)?.data || [];

      if (units.length > 0) {
        const unitId = units[0].id;
        const capture = await api.post(`rf/units/${unitId}/images`, {
          image_url: 'https://example.com/unit-image.jpg',
          is_primary: false,
        });
        captures.push(capture);

        console.log(`POST /rf/units/${unitId}/images => ${capture.response.status}`);
      }
    });

    test('DELETE /rf/units/images/{id} - delete unit image', async ({ api }) => {
      const capture = await api.delete('rf/units/images/1');
      captures.push(capture);

      console.log(`DELETE /rf/units/images/1 => ${capture.response.status}`);
    });
  });

  test.describe('Directory Management', () => {
    test('POST /rf/directory - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/directory', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/directory (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/directory - create directory entry', async ({ api }) => {
      const capture = await api.post('rf/directory', {
        name: `Test Directory ${Date.now()}`,
        type: 'folder',
        parent_id: null,
      });
      captures.push(capture);

      console.log(`POST /rf/directory => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created directory ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('PUT /rf/directory/{id} - update directory entry', async ({ api }) => {
      const capture = await api.put('rf/directory/1', {
        name: `Updated Directory ${Date.now()}`,
      });
      captures.push(capture);

      console.log(`PUT /rf/directory/1 => ${capture.response.status}`);
    });

    test('DELETE /rf/directory/{id} - delete directory entry', async ({ api }) => {
      const capture = await api.delete('rf/directory/999');
      captures.push(capture);

      console.log(`DELETE /rf/directory/999 => ${capture.response.status}`);
    });
  });

  test.describe('Tenant Documents', () => {
    test('POST /rf/tenants/{id}/documents - attach document to tenant', async ({ api }) => {
      const tenantsData = await api.fetchReferenceData('rf/tenants?is_paginate=0');
      const tenants = (tenantsData as any)?.data || [];

      if (tenants.length > 0) {
        const tenantId = tenants[0].id;
        const capture = await api.post(`rf/tenants/${tenantId}/documents`, {
          name: `Tenant ID Document ${Date.now()}`,
          type: 'id_document',
          description: 'Tenant identification document',
        });
        captures.push(capture);

        console.log(`POST /rf/tenants/${tenantId}/documents => ${capture.response.status}`);
      } else {
        console.log('Skipped: No tenants available for document attachment');
      }
    });
  });

  test.describe('Owner Documents', () => {
    test('POST /rf/owners/{id}/documents - attach document to owner', async ({ api }) => {
      const ownersData = await api.fetchReferenceData('rf/owners?is_paginate=0');
      const owners = (ownersData as any)?.data || [];

      if (owners.length > 0) {
        const ownerId = owners[0].id;
        const capture = await api.post(`rf/owners/${ownerId}/documents`, {
          name: `Owner Document ${Date.now()}`,
          type: 'contract',
          description: 'Owner contract document',
        });
        captures.push(capture);

        console.log(`POST /rf/owners/${ownerId}/documents => ${capture.response.status}`);
      } else {
        console.log('Skipped: No owners available for document attachment');
      }
    });
  });

  test.describe('Reference Data', () => {
    test('GET /rf/documents - list documents', async ({ api }) => {
      try {
        const data = await api.get('rf/documents?is_paginate=0');
        const documents = (data as any)?.data || [];
        console.log(`GET /rf/documents => ${documents.length} documents found`);
      } catch (error) {
        console.log(`GET /rf/documents => Failed`);
      }
    });

    test('GET /rf/document-types - list document types', async ({ api }) => {
      try {
        const data = await api.get('rf/document-types');
        console.log(`GET /rf/document-types => Success`);
      } catch (error) {
        console.log(`GET /rf/document-types => Failed`);
      }
    });
  });
});
