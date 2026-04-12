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
  // Run all tests in serial mode to ensure captures are collected properly
  test.describe.configure({ mode: 'serial' });
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

    test('DELETE /rf/communities/{id} - delete community', async ({ api }) => {
      // Fetch communities to find one to delete (use last one to avoid critical data)
      const communitiesData = await api.fetchReferenceData('rf/communities?is_paginate=0');
      const communities = (communitiesData as any)?.data || [];

      if (communities.length > 1) {
        const communityId = communities[communities.length - 1].id;
        const capture = await api.delete(`rf/communities/${communityId}`);
        captures.push(capture);
        console.log(`DELETE /rf/communities/${communityId} => ${capture.response.status}`);
      } else {
        // Try with a non-existent ID to capture the error response pattern
        const capture = await api.delete('rf/communities/99999');
        captures.push(capture);
        console.log(`DELETE /rf/communities/99999 => ${capture.response.status}`);
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

    test('DELETE /rf/buildings/{id} - delete building', async ({ api }) => {
      // Fetch buildings to find one to delete
      const buildingsData = await api.fetchReferenceData('rf/buildings?is_paginate=0');
      const buildings = (buildingsData as any)?.data || [];

      if (buildings.length > 1) {
        const buildingId = buildings[buildings.length - 1].id;
        const capture = await api.delete(`rf/buildings/${buildingId}`);
        captures.push(capture);
        console.log(`DELETE /rf/buildings/${buildingId} => ${capture.response.status}`);
      } else {
        const capture = await api.delete('rf/buildings/99999');
        captures.push(capture);
        console.log(`DELETE /rf/buildings/99999 => ${capture.response.status}`);
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

    test('DELETE /rf/units/{id} - delete unit', async ({ api }) => {
      // Fetch units to find one to delete
      const unitsData = await api.fetchReferenceData('rf/units?is_paginate=0');
      const units = (unitsData as any)?.data || [];

      if (units.length > 1) {
        const unitId = units[units.length - 1].id;
        const capture = await api.delete(`rf/units/${unitId}`);
        captures.push(capture);
        console.log(`DELETE /rf/units/${unitId} => ${capture.response.status}`);
      } else {
        const capture = await api.delete('rf/units/99999');
        captures.push(capture);
        console.log(`DELETE /rf/units/99999 => ${capture.response.status}`);
      }
    });

    test('POST /rf/units/bulk-update - bulk update units', async ({ api }) => {
      const unitsData = await api.fetchReferenceData('rf/units?is_paginate=0');
      const units = (unitsData as any)?.data || [];

      if (units.length > 0) {
        const unitIds = units.slice(0, 2).map((u: any) => u.id);
        const capture = await api.post('rf/units/bulk-update', {
          ids: unitIds,
          rf_status_id: 26, // Set to Available
        });
        captures.push(capture);
        console.log(`POST /rf/units/bulk-update => ${capture.response.status}`);
      } else {
        const capture = await api.post('rf/units/bulk-update', {
          ids: [],
          rf_status_id: 26,
        });
        captures.push(capture);
        console.log(`POST /rf/units/bulk-update (empty) => ${capture.response.status}`);
      }
    });

    test('POST /rf/units/bulk-delete - bulk delete units', async ({ api }) => {
      // Test with non-existent IDs to capture error pattern
      const capture = await api.post('rf/units/bulk-delete', {
        ids: [99997, 99998, 99999],
      });
      captures.push(capture);
      console.log(`POST /rf/units/bulk-delete => ${capture.response.status}`);
    });
  });

  test.describe('Facilities', () => {
    test('POST /rf/facilities - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/facilities', EMPTY_DATA);
      captures.push(capture);
      console.log(`POST /rf/facilities (empty) => ${capture.response.status}`);
    });

    test('POST /rf/facilities - create facility', async ({ api }) => {
      await resolver.resolve('community');
      const communityId = resolver.getResolvedId('community');

      const capture = await api.post('rf/facilities', {
        name: `Test Facility ${Date.now()}`,
        type: 'gym',
        rf_community_id: communityId,
        description: 'Test facility description',
      });
      captures.push(capture);
      console.log(`POST /rf/facilities => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created facility ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('PUT /rf/facilities/{id} - update facility', async ({ api }) => {
      const capture = await api.put('rf/facilities/1', {
        name: `Updated Facility ${Date.now()}`,
      });
      captures.push(capture);
      console.log(`PUT /rf/facilities/1 => ${capture.response.status}`);
    });

    test('DELETE /rf/facilities/{id} - delete facility', async ({ api }) => {
      const capture = await api.delete('rf/facilities/999');
      captures.push(capture);
      console.log(`DELETE /rf/facilities/999 => ${capture.response.status}`);
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
