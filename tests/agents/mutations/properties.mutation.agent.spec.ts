/**
 * Properties Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Communities
 * - Buildings
 * - Units
 *
 * CRITICAL: Unit creation requires complete map object with ALL 8 fields
 */

import { test, expect } from '../../fixtures/api.fixture';
import { createDependencyResolver, DependencyResolver } from '../../utils/dependency-resolver';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import {
  createCommunitySampleData,
  createBuildingSampleData,
  createUnitSampleData,
  EMPTY_DATA,
  PARTIAL_MAP_DATA,
} from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

test.describe('Properties Mutation Agent', () => {
  let resolver: DependencyResolver;

  test.beforeEach(async ({ api }) => {
    resolver = createDependencyResolver(api);
  });

  test.afterAll(async () => {
    await writeMutationCaptures('properties', captures);
    console.log(`\n=== Properties Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Errors: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Communities', () => {
    test('POST /rf/communities - create community with valid data', async ({ api }) => {
      const data = createCommunitySampleData();
      const capture = await api.post('rf/communities', data);
      captures.push(capture);

      console.log(`POST /rf/communities => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created community ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('POST /rf/communities - validation errors (empty body)', async ({ api }) => {
      const capture = await api.post('rf/communities', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/communities (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        console.log(`  Validation errors: ${capture.validationErrors.length}`);
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('PUT /rf/communities/{id} - update community', async ({ api }) => {
      // Ensure we have a community
      await resolver.resolve('community');
      const communityId = resolver.getResolvedId('community');

      if (communityId) {
        const capture = await api.put(`rf/communities/${communityId}`, {
          name: `Updated Community ${Date.now()}`,
        });
        captures.push(capture);
        console.log(`PUT /rf/communities/${communityId} => ${capture.response.status}`);
      } else {
        console.log('Skipped: No community available for update');
      }
    });
  });

  test.describe('Buildings', () => {
    test('POST /rf/buildings - create building with valid data', async ({ api }) => {
      // Ensure we have a community
      await resolver.resolve('community');
      const communityId = resolver.getResolvedId('community');

      if (communityId) {
        const data = createBuildingSampleData(communityId);
        const capture = await api.post('rf/buildings', data);
        captures.push(capture);

        console.log(`POST /rf/buildings => ${capture.response.status}`);
        if (capture.success) {
          console.log(`  Created building ID: ${api.extractFirstId(capture.response.body)}`);
        }
      }
    });

    test('POST /rf/buildings - validation errors (empty body)', async ({ api }) => {
      const capture = await api.post('rf/buildings', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/buildings (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        console.log(`  Validation errors: ${capture.validationErrors.length}`);
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/buildings - validation errors (missing community)', async ({ api }) => {
      const capture = await api.post('rf/buildings', {
        name: 'Test Building',
        // Missing rf_community_id
      });
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/buildings (no community) => ${capture.response.status}`);
    });

    test('PUT /rf/buildings/{id} - update building', async ({ api }) => {
      // Ensure we have a building
      await resolver.resolve('building');
      const buildingId = resolver.getResolvedId('building');

      if (buildingId) {
        const capture = await api.put(`rf/buildings/${buildingId}`, {
          name: `Updated Building ${Date.now()}`,
        });
        captures.push(capture);
        console.log(`PUT /rf/buildings/${buildingId} => ${capture.response.status}`);
      } else {
        console.log('Skipped: No building available for update');
      }
    });
  });

  test.describe('Units', () => {
    test('POST /rf/units - create unit with valid data (complete map)', async ({ api }) => {
      // Ensure we have community and building
      await resolver.resolve('building');
      const communityId = resolver.getResolvedId('community');
      const buildingId = resolver.getResolvedId('building');

      if (communityId) {
        const data = createUnitSampleData(communityId, buildingId);
        const capture = await api.post('rf/units', data);
        captures.push(capture);

        console.log(`POST /rf/units => ${capture.response.status}`);
        if (capture.success) {
          console.log(`  Created unit ID: ${api.extractFirstId(capture.response.body)}`);
        } else {
          console.log(`  Response: ${JSON.stringify(capture.response.body)}`);
        }
      }
    });

    test('POST /rf/units - validation errors (empty body)', async ({ api }) => {
      const capture = await api.post('rf/units', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/units (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        console.log(`  Validation errors: ${capture.validationErrors.length}`);
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/units - validation errors (partial map - CRITICAL TEST)', async ({ api }) => {
      // This tests the critical learning: map object must have ALL 8 fields
      const capture = await api.post('rf/units', PARTIAL_MAP_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/units (partial map) => ${capture.response.status}`);
      console.log(`  CRITICAL: Partial map object causes 400 error`);
    });

    test('POST /rf/units - validation errors (missing map)', async ({ api }) => {
      await resolver.resolve('community');
      const communityId = resolver.getResolvedId('community');

      const capture = await api.post('rf/units', {
        name: 'Test Unit No Map',
        category_id: 2,
        type_id: 17,
        rf_community_id: communityId,
        // Missing map object entirely
      });
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/units (no map) => ${capture.response.status}`);
    });

    test('PUT /rf/units/{id} - update unit', async ({ api }) => {
      // Ensure we have a unit
      await resolver.resolve('unit');
      const unitId = resolver.getResolvedId('unit');

      if (unitId) {
        const capture = await api.put(`rf/units/${unitId}`, {
          name: `Updated Unit ${Date.now()}`,
        });
        captures.push(capture);
        console.log(`PUT /rf/units/${unitId} => ${capture.response.status}`);
      } else {
        console.log('Skipped: No unit available for update');
      }
    });

    test('PUT /rf/units/{id} - update unit status', async ({ api }) => {
      await resolver.resolve('unit');
      const unitId = resolver.getResolvedId('unit');

      if (unitId) {
        // Try to update status to Available (26)
        const capture = await api.put(`rf/units/${unitId}`, {
          rf_status_id: 26,
        });
        captures.push(capture);
        console.log(`PUT /rf/units/${unitId} (status change) => ${capture.response.status}`);
      }
    });
  });

  test.describe('Bulk Operations', () => {
    test('GET /rf/units/create - get unit creation metadata', async ({ api }) => {
      // This endpoint provides specs, types, amenities for unit creation
      try {
        const data = await api.get('rf/units/create');
        console.log(`GET /rf/units/create => Success`);
        // Log available categories and types
        const response = data as Record<string, unknown>;
        if (response.data) {
          console.log(`  Categories available: ${JSON.stringify(response.data).slice(0, 200)}...`);
        }
      } catch (error) {
        console.log(`GET /rf/units/create => Failed (may be 500 - known issue)`);
      }
    });
  });
});
