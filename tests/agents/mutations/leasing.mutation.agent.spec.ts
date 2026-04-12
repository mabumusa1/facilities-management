/**
 * Leasing Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Lease creation
 * - Lease status changes (move-out, terminate)
 * - Lease renewal
 *
 * CRITICAL LEARNINGS:
 * - rental_type MUST be "detailed" (NOT "yearly", "annual", or numeric)
 * - Unit status must be 26 (Available) or 23 (Sold)
 * - units[].amount_type is required
 * - Payment schedules depend on rental_contract_type_id:
 *   - 13 (Yearly): 4, 5, 6, 7
 *   - 14 (Monthly): 16, 17
 *   - 15 (Daily): 18
 */

import { test, expect } from '../../fixtures/api.fixture';
import { createDependencyResolver, DependencyResolver } from '../../utils/dependency-resolver';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import {
  createLeaseSampleData,
  createLeaseStatusChangeSampleData,
  createLeaseRenewalSampleData,
  EMPTY_DATA,
  INVALID_LEASE_DATA,
} from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

test.describe('Leasing Mutation Agent', () => {
  // Run all tests in serial mode to ensure captures are collected properly
  test.describe.configure({ mode: 'serial' });
  let resolver: DependencyResolver;

  test.beforeEach(async ({ api }) => {
    resolver = createDependencyResolver(api);
  });

  test.afterAll(async () => {
    await writeMutationCaptures('leasing', captures);
    console.log(`\n=== Leasing Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Errors: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Lease Creation', () => {
    test('POST /rf/leases - create lease with valid data', async ({ api }) => {
      // Ensure we have unit and tenant
      await resolver.resolve('unit');
      await resolver.resolve('tenant');

      const unitId = resolver.getResolvedId('unit');
      const tenantId = resolver.getResolvedId('tenant');

      if (unitId && tenantId) {
        // Get tenant info for the lease
        const tenantsData = await api.fetchReferenceData('rf/tenants?is_paginate=0');
        const tenants = (tenantsData as any)?.data || [];
        const tenant = tenants.find((t: any) => t.id.toString() === tenantId);

        const tenantInfo = {
          name: tenant?.name || `Tenant ${tenantId}`,
          phone_number: tenant?.phone_number || '+966500000000',
        };

        const data = createLeaseSampleData(unitId, tenantId, tenantInfo);
        const capture = await api.post('rf/leases', data);
        captures.push(capture);

        console.log(`POST /rf/leases => ${capture.response.status}`);
        if (capture.success) {
          console.log(`  Created lease ID: ${api.extractFirstId(capture.response.body)}`);
        } else {
          console.log(`  Response: ${JSON.stringify(capture.response.body).slice(0, 500)}`);
          if (capture.validationErrors?.length) {
            capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.message}`));
          }
        }
      } else {
        console.log('Skipped: Missing unit or tenant');
      }
    });

    test('POST /rf/leases - validation errors (empty body)', async ({ api }) => {
      const capture = await api.post('rf/leases', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/leases (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        console.log(`  Validation errors: ${capture.validationErrors.length}`);
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/leases - validation errors (wrong rental_type)', async ({ api }) => {
      // This tests the critical learning: rental_type must be "detailed"
      const capture = await api.post('rf/leases', INVALID_LEASE_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/leases (wrong rental_type) => ${capture.response.status}`);
      console.log(`  CRITICAL: rental_type must be "detailed", not "yearly"`);
    });

    test('POST /rf/leases - validation errors (missing amount_type)', async ({ api }) => {
      await resolver.resolve('unit');
      await resolver.resolve('tenant');

      const unitId = resolver.getResolvedId('unit');
      const tenantId = resolver.getResolvedId('tenant');

      if (unitId && tenantId) {
        const today = new Date();
        const startDate = new Date(today);
        startDate.setDate(startDate.getDate() + 7);
        const endDate = new Date(startDate);
        endDate.setFullYear(endDate.getFullYear() + 1);

        const capture = await api.post('rf/leases', {
          created_at: today.toISOString().split('T')[0],
          start_date: startDate.toISOString().split('T')[0],
          end_date: endDate.toISOString().split('T')[0],
          number_of_years: 1,
          number_of_months: 0,
          lease_unit_type: 2,
          tenant_type: 'individual',
          tenant_id: tenantId,
          autoGenerateLeaseNumber: true,
          rental_type: 'detailed',
          rental_contract_type_id: 13,
          payment_schedule_id: 7,
          rental_total_amount: 60000,
          rf_status_id: 30,
          units: [
            {
              id: unitId,
              annual_rental_amount: 60000,
              // Missing amount_type - should cause error
            },
          ],
        });
        captures.push(capture);

        console.log(`POST /rf/leases (no amount_type) => ${capture.response.status}`);
        if (!capture.success) {
          console.log(`  CRITICAL: units[].amount_type is required`);
        }
      }
    });
  });

  test.describe('Lease Updates', () => {
    test('PUT /rf/leases/{id} - update lease details', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const leaseId = leases[0].id;
        const capture = await api.put(`rf/leases/${leaseId}`, {
          notes: `Updated lease notes ${Date.now()}`,
        });
        captures.push(capture);
        console.log(`PUT /rf/leases/${leaseId} => ${capture.response.status}`);
      } else {
        console.log('Skipped: No lease available for update');
      }
    });

    test('DELETE /rf/leases/{id} - delete lease', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 1) {
        const leaseId = leases[leases.length - 1].id;
        const capture = await api.delete(`rf/leases/${leaseId}`);
        captures.push(capture);
        console.log(`DELETE /rf/leases/${leaseId} => ${capture.response.status}`);
      } else {
        const capture = await api.delete('rf/leases/99999');
        captures.push(capture);
        console.log(`DELETE /rf/leases/99999 => ${capture.response.status}`);
      }
    });

    test('POST /rf/leases/{id}/addendum - create lease addendum', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const leaseId = leases[0].id;
        const capture = await api.post(`rf/leases/${leaseId}/addendum`, {
          type: 'modification',
          description: `Lease addendum ${Date.now()}`,
          effective_date: new Date().toISOString().split('T')[0],
        });
        captures.push(capture);
        console.log(`POST /rf/leases/${leaseId}/addendum => ${capture.response.status}`);
      }
    });
  });

  test.describe('Lease Status Changes', () => {
    test('POST /rf/leases/change-status/move-out - validation errors', async ({ api }) => {
      // Test with empty body first to capture required fields
      const capture = await api.post('rf/leases/change-status/move-out', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/leases/change-status/move-out (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/leases/change-status/move-out - with lease ID', async ({ api }) => {
      // Get an existing lease
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const leaseId = leases[0].id;
        const data = createLeaseStatusChangeSampleData(leaseId);
        const capture = await api.post('rf/leases/change-status/move-out', data);
        captures.push(capture);

        console.log(`POST /rf/leases/change-status/move-out => ${capture.response.status}`);
      } else {
        console.log('Skipped: No lease available for move-out');
      }
    });

    test('POST /rf/leases/change-status/terminate - validation errors', async ({ api }) => {
      const capture = await api.post('rf/leases/change-status/terminate', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/leases/change-status/terminate (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/leases/change-status/terminate - with lease ID', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const leaseId = leases[0].id;
        const data = createLeaseStatusChangeSampleData(leaseId);
        const capture = await api.post('rf/leases/change-status/terminate', data);
        captures.push(capture);

        console.log(`POST /rf/leases/change-status/terminate => ${capture.response.status}`);
      }
    });

    test('POST /rf/leases/change-status/suspend - suspend lease', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const leaseId = leases[0].id;
        const capture = await api.post('rf/leases/change-status/suspend', {
          lease_id: leaseId,
          reason: 'Test suspension',
          date: new Date().toISOString().split('T')[0],
        });
        captures.push(capture);
        console.log(`POST /rf/leases/change-status/suspend => ${capture.response.status}`);
      }
    });

    test('POST /rf/leases/change-status/reactivate - reactivate lease', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const leaseId = leases[0].id;
        const capture = await api.post('rf/leases/change-status/reactivate', {
          lease_id: leaseId,
          date: new Date().toISOString().split('T')[0],
        });
        captures.push(capture);
        console.log(`POST /rf/leases/change-status/reactivate => ${capture.response.status}`);
      }
    });
  });

  test.describe('Sub-Leases', () => {
    test('POST /rf/sub-leases - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('rf/sub-leases', EMPTY_DATA);
      captures.push(capture);
      console.log(`POST /rf/sub-leases (empty) => ${capture.response.status}`);
    });

    test('POST /rf/sub-leases - create sub-lease', async ({ api }) => {
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const leaseId = leases[0].id;
        const startDate = new Date();
        const endDate = new Date();
        endDate.setMonth(endDate.getMonth() + 6);

        const capture = await api.post('rf/sub-leases', {
          parent_lease_id: leaseId,
          sub_tenant_name: `Sub Tenant ${Date.now()}`,
          sub_tenant_phone: '+966500000001',
          start_date: startDate.toISOString().split('T')[0],
          end_date: endDate.toISOString().split('T')[0],
          rental_amount: 30000,
        });
        captures.push(capture);
        console.log(`POST /rf/sub-leases => ${capture.response.status}`);
      }
    });

    test('DELETE /rf/sub-leases/{id} - delete sub-lease', async ({ api }) => {
      const capture = await api.delete('rf/sub-leases/999');
      captures.push(capture);
      console.log(`DELETE /rf/sub-leases/999 => ${capture.response.status}`);
    });
  });

  test.describe('Lease Renewal', () => {
    test('POST /rf/leases/renew/store - validation errors', async ({ api }) => {
      const capture = await api.post('rf/leases/renew/store', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(`POST /rf/leases/renew/store (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        console.log(`  Validation errors: ${capture.validationErrors.length}`);
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /rf/leases/renew/store - with lease and unit IDs', async ({ api }) => {
      // Get an existing lease
      const leasesData = await api.fetchReferenceData('rf/leases?is_paginate=0');
      const leases = (leasesData as any)?.data || [];

      if (leases.length > 0) {
        const lease = leases[0];
        const leaseId = lease.id;
        const unitId = lease.units?.[0]?.id || lease.rf_unit_id;

        if (unitId) {
          const data = createLeaseRenewalSampleData(leaseId, unitId);
          const capture = await api.post('rf/leases/renew/store', data);
          captures.push(capture);

          console.log(`POST /rf/leases/renew/store => ${capture.response.status}`);
          if (capture.success) {
            console.log(`  Renewed lease ID: ${api.extractFirstId(capture.response.body)}`);
          }
        }
      } else {
        console.log('Skipped: No lease available for renewal');
      }
    });
  });

  test.describe('Reference Data', () => {
    test('GET /rf/leases - list leases', async ({ api }) => {
      try {
        const data = await api.get('rf/leases?is_paginate=0');
        const leases = (data as any)?.data || [];
        console.log(`GET /rf/leases => ${leases.length} leases found`);
      } catch (error) {
        console.log(`GET /rf/leases => Failed`);
      }
    });

    test('GET /rf/payment-schedules - list payment schedules', async ({ api }) => {
      try {
        const data = await api.get('rf/common-lists');
        console.log(`GET /rf/common-lists => Success (contains payment schedules)`);
      } catch (error) {
        console.log(`GET /rf/common-lists => Failed`);
      }
    });
  });
});
