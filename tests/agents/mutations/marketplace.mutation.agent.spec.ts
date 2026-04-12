/**
 * Marketplace Mutation Agent
 *
 * Captures POST, PUT, PATCH, DELETE requests for:
 * - Bank settings
 * - Sales settings
 * - Visit settings
 * - Community listing/unlisting
 * - Unit price visibility
 *
 * CRITICAL: Bank account_number must be at least 14 digits
 */

import { test, expect } from '../../fixtures/api.fixture';
import { createDependencyResolver, DependencyResolver } from '../../utils/dependency-resolver';
import { writeMutationCaptures } from '../../utils/mutation-output-writer';
import { MutationCapture } from '../../utils/mutation-types';
import {
  createBankSettingsSampleData,
  createSalesSettingsSampleData,
  createVisitsSettingsSampleData,
  EMPTY_DATA,
  INVALID_BANK_SETTINGS,
} from '../../utils/sample-data';

// Store captures for this module
const captures: MutationCapture[] = [];

test.describe('Marketplace Mutation Agent', () => {
  let resolver: DependencyResolver;

  test.beforeEach(async ({ api }) => {
    resolver = createDependencyResolver(api);
  });

  test.afterAll(async () => {
    await writeMutationCaptures('marketplace', captures);
    console.log(`\n=== Marketplace Agent Complete ===`);
    console.log(`Total captures: ${captures.length}`);
    console.log(`Successful: ${captures.filter((c) => c.success).length}`);
    console.log(`Errors: ${captures.filter((c) => !c.success).length}`);
  });

  test.describe('Bank Settings', () => {
    test('POST /marketplace/admin/settings/banks/store - with valid data', async ({ api }) => {
      const data = createBankSettingsSampleData();
      const capture = await api.post('marketplace/admin/settings/banks/store', data);
      captures.push(capture);

      console.log(`POST /marketplace/admin/settings/banks/store => ${capture.response.status}`);
      if (capture.success) {
        console.log(`  Bank settings stored successfully`);
      }
    });

    test('POST /marketplace/admin/settings/banks/store - validation errors (empty)', async ({
      api,
    }) => {
      const capture = await api.post('marketplace/admin/settings/banks/store', EMPTY_DATA);
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(
        `POST /marketplace/admin/settings/banks/store (empty) => ${capture.response.status}`
      );
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /marketplace/admin/settings/banks/store - validation errors (short account)', async ({
      api,
    }) => {
      // Account number must be at least 14 digits
      const capture = await api.post(
        'marketplace/admin/settings/banks/store',
        INVALID_BANK_SETTINGS
      );
      captures.push(capture);

      expect(capture.response.status).toBeGreaterThanOrEqual(400);
      console.log(
        `POST /marketplace/admin/settings/banks/store (short account) => ${capture.response.status}`
      );
      console.log(`  CRITICAL: account_number must be at least 14 digits`);
    });

    test('PUT /marketplace/admin/settings/banks/{id} - update bank setting', async ({ api }) => {
      const capture = await api.put('marketplace/admin/settings/banks/1', {
        beneficiary_name: `Updated Beneficiary ${Date.now()}`,
      });
      captures.push(capture);
      console.log(`PUT /marketplace/admin/settings/banks/1 => ${capture.response.status}`);
    });

    test('DELETE /marketplace/admin/settings/banks/{id} - delete bank setting', async ({ api }) => {
      const capture = await api.delete('marketplace/admin/settings/banks/999');
      captures.push(capture);
      console.log(`DELETE /marketplace/admin/settings/banks/999 => ${capture.response.status}`);
    });
  });

  test.describe('Sales Settings', () => {
    test('POST /marketplace/admin/settings/sales/store - with valid data', async ({ api }) => {
      const data = createSalesSettingsSampleData();
      const capture = await api.post('marketplace/admin/settings/sales/store', data);
      captures.push(capture);

      console.log(`POST /marketplace/admin/settings/sales/store => ${capture.response.status}`);
    });

    test('POST /marketplace/admin/settings/sales/store - validation errors', async ({ api }) => {
      const capture = await api.post('marketplace/admin/settings/sales/store', EMPTY_DATA);
      captures.push(capture);

      console.log(
        `POST /marketplace/admin/settings/sales/store (empty) => ${capture.response.status}`
      );
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });
  });

  test.describe('Visit Settings', () => {
    test('POST /marketplace/admin/settings/visits/store - with valid data', async ({ api }) => {
      const data = createVisitsSettingsSampleData();
      const capture = await api.post('marketplace/admin/settings/visits/store', data);
      captures.push(capture);

      console.log(`POST /marketplace/admin/settings/visits/store => ${capture.response.status}`);
    });

    test('POST /marketplace/admin/settings/visits/store - validation errors', async ({ api }) => {
      const capture = await api.post('marketplace/admin/settings/visits/store', EMPTY_DATA);
      captures.push(capture);

      console.log(
        `POST /marketplace/admin/settings/visits/store (empty) => ${capture.response.status}`
      );
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });
  });

  test.describe('Community Listing', () => {
    test('POST /marketplace/admin/communities/list/{id} - list community', async ({ api }) => {
      // Ensure we have a community
      await resolver.resolve('community');
      const communityId = resolver.getResolvedId('community');

      if (communityId) {
        const capture = await api.post(`marketplace/admin/communities/list/${communityId}`, {});
        captures.push(capture);

        console.log(
          `POST /marketplace/admin/communities/list/${communityId} => ${capture.response.status}`
        );
      } else {
        console.log('Skipped: No community available for listing');
      }
    });

    test('POST /marketplace/admin/communities/unlist/{id} - unlist community', async ({ api }) => {
      await resolver.resolve('community');
      const communityId = resolver.getResolvedId('community');

      if (communityId) {
        const capture = await api.post(`marketplace/admin/communities/unlist/${communityId}`, {});
        captures.push(capture);

        console.log(
          `POST /marketplace/admin/communities/unlist/${communityId} => ${capture.response.status}`
        );
      }
    });
  });

  test.describe('Unit Price Visibility', () => {
    test('POST /marketplace/admin/units/prices-visibility/{id} - show prices', async ({ api }) => {
      await resolver.resolve('unit');
      const unitId = resolver.getResolvedId('unit');

      if (unitId) {
        const capture = await api.post(`marketplace/admin/units/prices-visibility/${unitId}`, {
          show_price: true,
        });
        captures.push(capture);

        console.log(
          `POST /marketplace/admin/units/prices-visibility/${unitId} => ${capture.response.status}`
        );
      }
    });

    test('POST /marketplace/admin/units/prices-visibility/{id} - hide prices', async ({ api }) => {
      await resolver.resolve('unit');
      const unitId = resolver.getResolvedId('unit');

      if (unitId) {
        const capture = await api.post(`marketplace/admin/units/prices-visibility/${unitId}`, {
          show_price: false,
        });
        captures.push(capture);

        console.log(`POST /marketplace/admin/units/prices-visibility/${unitId} (hide) => ${capture.response.status}`);
      }
    });
  });

  test.describe('Visits Management', () => {
    test('POST /marketplace/admin/visits/assign/owner-visit/{id} - assign visit', async ({
      api,
    }) => {
      // Get existing visits
      const visitsData = await api.fetchReferenceData('marketplace/admin/visits?is_paginate=0');
      const visits = (visitsData as any)?.data || [];

      if (visits.length > 0) {
        const visitId = visits[0].id;
        const capture = await api.post(`marketplace/admin/visits/assign/owner-visit/${visitId}`, {
          assigned_to: 1, // Assign to first admin
        });
        captures.push(capture);

        console.log(
          `POST /marketplace/admin/visits/assign/owner-visit/${visitId} => ${capture.response.status}`
        );
      } else {
        console.log('Skipped: No visits available for assignment');
      }
    });

    test('POST /marketplace/admin/visits/completed/{id} - mark visit completed', async ({
      api,
    }) => {
      const visitsData = await api.fetchReferenceData('marketplace/admin/visits?is_paginate=0');
      const visits = (visitsData as any)?.data || [];

      if (visits.length > 0) {
        const visitId = visits[0].id;
        const capture = await api.post(`marketplace/admin/visits/completed/${visitId}`, {});
        captures.push(capture);

        console.log(
          `POST /marketplace/admin/visits/completed/${visitId} => ${capture.response.status}`
        );
      }
    });

    test('POST /marketplace/admin/visits/rejected/{id} - reject visit', async ({ api }) => {
      const visitsData = await api.fetchReferenceData('marketplace/admin/visits?is_paginate=0');
      const visits = (visitsData as any)?.data || [];

      if (visits.length > 0) {
        const visitId = visits[0].id;
        const capture = await api.post(`marketplace/admin/visits/rejected/${visitId}`, {
          reason: 'Test rejection',
        });
        captures.push(capture);

        console.log(
          `POST /marketplace/admin/visits/rejected/${visitId} => ${capture.response.status}`
        );
      }
    });

    test('POST /marketplace/admin/visits/cancel/{id} - cancel visit', async ({ api }) => {
      const visitsData = await api.fetchReferenceData('marketplace/admin/visits?is_paginate=0');
      const visits = (visitsData as any)?.data || [];

      if (visits.length > 0) {
        const visitId = visits[visits.length - 1].id;
        const capture = await api.post(`marketplace/admin/visits/cancel/${visitId}`, {
          reason: 'Test cancellation',
        });
        captures.push(capture);
        console.log(`POST /marketplace/admin/visits/cancel/${visitId} => ${capture.response.status}`);
      } else {
        const capture = await api.post('marketplace/admin/visits/cancel/999', {
          reason: 'Test cancellation',
        });
        captures.push(capture);
        console.log(`POST /marketplace/admin/visits/cancel/999 => ${capture.response.status}`);
      }
    });
  });

  test.describe('Offers Management', () => {
    test('POST /marketplace/admin/offers - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('marketplace/admin/offers', EMPTY_DATA);
      captures.push(capture);
      console.log(`POST /marketplace/admin/offers (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /marketplace/admin/offers - create offer', async ({ api }) => {
      await resolver.resolve('unit');
      const unitId = resolver.getResolvedId('unit');

      const capture = await api.post('marketplace/admin/offers', {
        unit_id: unitId,
        title: `Test Offer ${Date.now()}`,
        description: 'Test offer description',
        discount_type: 'percentage',
        discount_value: 10,
        start_date: new Date().toISOString().split('T')[0],
        end_date: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
      });
      captures.push(capture);
      console.log(`POST /marketplace/admin/offers => ${capture.response.status}`);
    });

    test('PUT /marketplace/admin/offers/{id} - update offer', async ({ api }) => {
      const capture = await api.put('marketplace/admin/offers/1', {
        title: `Updated Offer ${Date.now()}`,
        discount_value: 15,
      });
      captures.push(capture);
      console.log(`PUT /marketplace/admin/offers/1 => ${capture.response.status}`);
    });

    test('DELETE /marketplace/admin/offers/{id} - delete offer', async ({ api }) => {
      const capture = await api.delete('marketplace/admin/offers/999');
      captures.push(capture);
      console.log(`DELETE /marketplace/admin/offers/999 => ${capture.response.status}`);
    });
  });

  test.describe('Listings Management', () => {
    test('POST /marketplace/admin/listings - validation errors (empty)', async ({ api }) => {
      const capture = await api.post('marketplace/admin/listings', EMPTY_DATA);
      captures.push(capture);
      console.log(`POST /marketplace/admin/listings (empty) => ${capture.response.status}`);
      if (capture.validationErrors?.length) {
        capture.validationErrors.forEach((e) => console.log(`    - ${e.field}: ${e.rule}`));
      }
    });

    test('POST /marketplace/admin/listings - create listing', async ({ api }) => {
      await resolver.resolve('unit');
      const unitId = resolver.getResolvedId('unit');

      const capture = await api.post('marketplace/admin/listings', {
        unit_id: unitId,
        title: `Test Listing ${Date.now()}`,
        description: 'Test listing description',
        listing_type: 'rent',
        price: 50000,
        is_featured: false,
      });
      captures.push(capture);
      console.log(`POST /marketplace/admin/listings => ${capture.response.status}`);
    });

    test('PUT /marketplace/admin/listings/{id} - update listing', async ({ api }) => {
      const capture = await api.put('marketplace/admin/listings/1', {
        title: `Updated Listing ${Date.now()}`,
        price: 55000,
      });
      captures.push(capture);
      console.log(`PUT /marketplace/admin/listings/1 => ${capture.response.status}`);
    });

    test('DELETE /marketplace/admin/listings/{id} - delete listing', async ({ api }) => {
      const capture = await api.delete('marketplace/admin/listings/999');
      captures.push(capture);
      console.log(`DELETE /marketplace/admin/listings/999 => ${capture.response.status}`);
    });
  });

  test.describe('Reference Data', () => {
    test('GET /marketplace/admin/visits - list visits', async ({ api }) => {
      try {
        const data = await api.get('marketplace/admin/visits?is_paginate=0');
        const visits = (data as any)?.data || [];
        console.log(`GET /marketplace/admin/visits => ${visits.length} visits found`);
      } catch (error) {
        console.log(`GET /marketplace/admin/visits => Failed`);
      }
    });

    test('GET /marketplace/admin/offers - list offers', async ({ api }) => {
      try {
        const data = await api.get('marketplace/admin/offers?is_paginate=0');
        const offers = (data as any)?.data || [];
        console.log(`GET /marketplace/admin/offers => ${offers.length} offers found`);
      } catch (error) {
        console.log(`GET /marketplace/admin/offers => Failed`);
      }
    });
  });
});
