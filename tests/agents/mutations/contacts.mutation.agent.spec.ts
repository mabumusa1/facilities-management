/**
 * Contacts Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Owners
 * - Tenants
 * - Managers
 *
 * CRITICAL: national_id must be unique across owners/tenants
 * Phone numbers should be stored WITHOUT country prefix
 */

import { test, expect } from '../../fixtures/api.fixture';
import { createDependencyResolver, DependencyResolver } from '../../utils/dependency-resolver';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import {
  createOwnerSampleData,
  createTenantSampleData,
  createCompanyTenantSampleData,
  EMPTY_DATA,
} from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

test.describe('Contacts Mutation Agent', () => {
  let resolver: DependencyResolver;

  test.beforeEach(async ({ api }) => {
    resolver = createDependencyResolver(api);
  });

  test.afterAll(async () => {
    await writeMutationCaptures('contacts', captures);
    console.log(`\n=== Contacts Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Errors: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Owners', () => {
    test('POST /rf/owners - create owner with valid data', async ({ api }) => {
      const data = createOwnerSampleData();
      const capture = await api.post('rf/owners', data);
      captures.push(capture);

      console.log(`POST /rf/owners => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created owner ID: ${api.extractFirstId(capture.response.body)}`);
      } else {
        console.log(`  Response: ${JSON.stringify(capture.response.body)}`);
      }
    });

    test('POST /rf/owners - validation errors (empty body)', async ({ api }) => {
      const capture = await api.post('rf/owners', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/owners (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        console.log(`  Validation errors: ${capture.validationErrors.length}`);
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/owners - validation errors (missing phone)', async ({ api }) => {
      const capture = await api.post('rf/owners', {
        first_name: 'Test',
        last_name: 'Owner',
        // Missing phone_country_code and phone_number
      });
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/owners (no phone) => ${capture.response.status}`);
    });

    test('POST /rf/owners - validation errors (duplicate national_id)', async ({ api }) => {
      // First, create an owner
      const data1 = createOwnerSampleData();
      const capture1 = await api.post('rf/owners', data1);
      captures.push(capture1);

      if (capture1.success) {
        // Try to create another with the same national_id
        const data2 = createOwnerSampleData({ national_id: data1.national_id });
        const capture2 = await api.post('rf/owners', data2);
        captures.push(capture2);

        console.log(`POST /rf/owners (duplicate national_id) => ${capture2.response.status}`);
        if (!capture2.success) {
          console.log(`  CRITICAL: national_id must be unique`);
        }
      }
    });

    test('PUT /rf/owners/{id} - update owner', async ({ api }) => {
      // Ensure we have an owner
      await resolver.resolve('owner');
      const ownerId = resolver.getResolvedId('owner');

      if (ownerId) {
        const capture = await api.put(`rf/owners/${ownerId}`, {
          first_name: 'Updated',
          last_name: `Owner${Date.now()}`,
        });
        captures.push(capture);
        console.log(`PUT /rf/owners/${ownerId} => ${capture.response.status}`);
      } else {
        console.log('Skipped: No owner available for update');
      }
    });
  });

  test.describe('Tenants', () => {
    test('POST /rf/tenants - create individual tenant with valid data', async ({ api }) => {
      const data = createTenantSampleData();
      const capture = await api.post('rf/tenants', data);
      captures.push(capture);

      console.log(`POST /rf/tenants (individual) => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created tenant ID: ${api.extractFirstId(capture.response.body)}`);
      } else {
        console.log(`  Response: ${JSON.stringify(capture.response.body)}`);
      }
    });

    test('POST /rf/tenants - create company tenant with valid data', async ({ api }) => {
      const data = createCompanyTenantSampleData();
      const capture = await api.post('rf/tenants', data);
      captures.push(capture);

      console.log(`POST /rf/tenants (company) => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created company tenant ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('POST /rf/tenants - validation errors (empty body)', async ({ api }) => {
      const capture = await api.post('rf/tenants', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/tenants (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        console.log(`  Validation errors: ${capture.validationErrors.length}`);
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/tenants - validation errors (missing name)', async ({ api }) => {
      const capture = await api.post('rf/tenants', {
        phone_country_code: 'SA',
        phone_number: '500000000',
        // Missing first_name, last_name
      });
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/tenants (no name) => ${capture.response.status}`);
    });

    test('PUT /rf/tenants/{id} - update tenant', async ({ api }) => {
      // Ensure we have a tenant
      await resolver.resolve('tenant');
      const tenantId = resolver.getResolvedId('tenant');

      if (tenantId) {
        const capture = await api.put(`rf/tenants/${tenantId}`, {
          first_name: 'Updated',
          last_name: `Tenant${Date.now()}`,
        });
        captures.push(capture);
        console.log(`PUT /rf/tenants/${tenantId} => ${capture.response.status}`);
      } else {
        console.log('Skipped: No tenant available for update');
      }
    });
  });

  test.describe('Managers', () => {
    test('POST /rf/admins - create manager with valid data', async ({ api }) => {
      const data = {
        first_name: 'Test',
        last_name: `Manager${Date.now()}`,
        phone_country_code: 'SA',
        phone_number: `5${Math.floor(Math.random() * 100000000)
          .toString()
          .padStart(8, '0')}`,
        email: `manager${Date.now()}@example.com`,
        role_id: 1, // Default role
      };
      const capture = await api.post('rf/admins', data);
      captures.push(capture);

      console.log(`POST /rf/admins => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created manager ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('POST /rf/admins - validation errors (empty body)', async ({ api }) => {
      const capture = await api.post('rf/admins', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/admins (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/admins/check-validate - validate admin data', async ({ api }) => {
      const capture = await api.post('rf/admins/check-validate', {
        first_name: 'Test',
        last_name: 'Admin',
        phone_country_code: 'SA',
        phone_number: '500000000',
      });
      captures.push(capture);

      console.log(`POST /rf/admins/check-validate => ${capture.response.status}`);
    });
  });

  test.describe('Professionals', () => {
    test('POST /rf/professionals - create service professional', async ({ api }) => {
      const data = {
        first_name: 'Test',
        last_name: `Professional${Date.now()}`,
        phone_country_code: 'SA',
        phone_number: `5${Math.floor(Math.random() * 100000000)
          .toString()
          .padStart(8, '0')}`,
        email: `professional${Date.now()}@example.com`,
        profession: 'plumber',
      };
      const capture = await api.post('rf/professionals', data);
      captures.push(capture);

      console.log(`POST /rf/professionals => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Created professional ID: ${api.extractFirstId(capture.response.body)}`);
      }
    });

    test('POST /rf/professionals - validation errors (empty body)', async ({ api }) => {
      const capture = await api.post('rf/professionals', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/professionals (empty) => ${capture.response.status}`);
    });
  });
});
