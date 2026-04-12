/**
 * Properties Query Agent
 *
 * Captures GET requests for:
 * - Communities (list, detail)
 * - Buildings (list, detail)
 * - Units (list, detail)
 * - Facilities (list, detail)
 */

import { test, expect } from '../../fixtures/api.fixture';
import { writeQueryCaptures } from '../../utils/query-output-writer';
import { QueryCapture } from '../../utils/query-types';

// Store captures for this module
const captures: QueryCapture[] = [];

test.describe('Properties Query Agent', () => {
  test.describe.configure({ mode: 'serial' });

  test.afterAll(async () => {
    await writeQueryCaptures('properties', captures);
    console.log(`\n=== Properties Query Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Failed: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Communities', () => {
    let communityId: string | undefined;

    test('GET /rf/communities - list all communities', async ({ api }) => {
      const capture = await api.getCapture('rf/communities');
      captures.push(capture);

      // Capture for documentation

      // Extract first community ID for detail request
      const ids = api.extractIds(capture.response.body);
      communityId = ids[0];
      console.log(`  Found ${ids.length} communities`);
    });

    test('GET /rf/communities - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/communities', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);

      // Capture for documentation
    });

    test('GET /rf/communities/{id} - community detail', async ({ api }) => {
      if (!communityId) {
        // Get a community ID first
        const listData = await api.get('rf/communities');
        communityId = api.extractFirstId(listData);
      }

      if (communityId) {
        const capture = await api.getCapture(`rf/communities/${communityId}`);
        captures.push(capture);
        // Capture for documentation
      } else {
        console.log('  No community found for detail request');
      }
    });
  });

  test.describe('Buildings', () => {
    let buildingId: string | undefined;

    test('GET /rf/buildings - list all buildings', async ({ api }) => {
      const capture = await api.getCapture('rf/buildings');
      captures.push(capture);

      // Capture for documentation

      const ids = api.extractIds(capture.response.body);
      buildingId = ids[0];
      console.log(`  Found ${ids.length} buildings`);
    });

    test('GET /rf/buildings - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/buildings', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);

      // Capture for documentation
    });

    test('GET /rf/buildings/{id} - building detail', async ({ api }) => {
      if (!buildingId) {
        const listData = await api.get('rf/buildings');
        buildingId = api.extractFirstId(listData);
      }

      if (buildingId) {
        const capture = await api.getCapture(`rf/buildings/${buildingId}`);
        captures.push(capture);
        // Capture for documentation
      } else {
        console.log('  No building found for detail request');
      }
    });
  });

  test.describe('Units', () => {
    let unitId: string | undefined;

    test('GET /rf/units - list all units', async ({ api }) => {
      const capture = await api.getCapture('rf/units');
      captures.push(capture);

      // Capture for documentation

      const ids = api.extractIds(capture.response.body);
      unitId = ids[0];
      console.log(`  Found ${ids.length} units`);
    });

    test('GET /rf/units - with pagination', async ({ api }) => {
      const capture = await api.getCapture('rf/units', {
        params: { page: 1, per_page: 10 }
      });
      captures.push(capture);

      // Capture for documentation
    });

    test('GET /rf/units - filter by status', async ({ api }) => {
      // Status 26 = available
      const capture = await api.getCapture('rf/units', {
        params: { status_id: 26 }
      });
      captures.push(capture);
    });

    test('GET /rf/units/{id} - unit detail', async ({ api }) => {
      if (!unitId) {
        const listData = await api.get('rf/units');
        unitId = api.extractFirstId(listData);
      }

      if (unitId) {
        const capture = await api.getCapture(`rf/units/${unitId}`);
        captures.push(capture);
        // Capture for documentation
      } else {
        console.log('  No unit found for detail request');
      }
    });
  });

  test.describe('Facilities', () => {
    let facilityId: string | undefined;

    test('GET /rf/facilities - list all facilities', async ({ api }) => {
      const capture = await api.getCapture('rf/facilities');
      captures.push(capture);

      const ids = api.extractIds(capture.response.body);
      facilityId = ids[0];
      console.log(`  Found ${ids.length} facilities`);
    });

    test('GET /rf/facilities/{id} - facility detail', async ({ api }) => {
      if (!facilityId) {
        const listData = await api.get('rf/facilities');
        facilityId = api.extractFirstId(listData);
      }

      if (facilityId) {
        const capture = await api.getCapture(`rf/facilities/${facilityId}`);
        captures.push(capture);
      } else {
        console.log('  No facility found for detail request');
      }
    });
  });
});
