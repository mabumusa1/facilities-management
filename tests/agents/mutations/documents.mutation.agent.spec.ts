/**
 * Documents Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - File uploads (rf/files)
 * - Excel sheet imports
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

  test.describe('File Upload', () => {
    test('POST /rf/files - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/files', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/files (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/files - upload file metadata', async ({ api }) => {
      // Get a unit to attach file to
      const unitsData = await api.fetchReferenceData('rf/units?is_paginate=0');
      const units = (unitsData as any)?.data || [];

      const data: Record<string, unknown> = {
        name: `Test File ${Date.now()}`,
        type: 'document',
        description: 'Test file description',
      };

      if (units.length > 0) {
        data.fileable_type = 'unit';
        data.fileable_id = units[0].id;
      }

      const capture = await api.post('rf/files', data);
      captures.push(capture);

      console.log(`POST /rf/files => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('DELETE /rf/files/{id} - delete file', async ({ api }) => {
      const capture = await api.delete('rf/files/999');
      captures.push(capture);

      console.log(`DELETE /rf/files/999 => ${capture.response.status}`);
    });
  });

  test.describe('Excel Sheet Imports', () => {
    test('POST /rf/excel-sheets - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/excel-sheets', EMPTY_DATA);
      captures.push(capture);

      console.log(`POST /rf/excel-sheets (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/excel-sheets/land - import land data', async ({ api }) => {
      const capture = await api.post('rf/excel-sheets/land', {
        file: 'test.xlsx',
        type: 'land',
      });
      captures.push(capture);

      console.log(`POST /rf/excel-sheets/land => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/excel-sheets/leads - import leads data', async ({ api }) => {
      const capture = await api.post('rf/excel-sheets/leads', {
        file: 'test.xlsx',
        type: 'leads',
      });
      captures.push(capture);

      console.log(`POST /rf/excel-sheets/leads => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });
  });

  test.describe('Reference Data', () => {
    test('GET /rf/files - list files', async ({ api }) => {
      try {
        const data = await api.get('rf/files?is_paginate=0');
        const files = (data as any)?.data || [];
        console.log(`GET /rf/files => ${files.length} files found`);
      } catch (error) {
        console.log(`GET /rf/files => Failed`);
      }
    });
  });
});
