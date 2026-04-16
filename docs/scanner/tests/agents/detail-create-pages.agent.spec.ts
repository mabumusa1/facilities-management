import { test, expect } from '../fixtures/scanner.fixture';

// Increase test timeout for detail/create pages (forms can be slow)
test.setTimeout(90000);

/**
 * Detail and Create Pages Scanner
 * Discovered via browser exploration on 2026-04-16.
 *
 * These pages include property detail views and create/add forms.
 */
test.describe('Detail Pages - Properties', () => {

  // Community Details
  test('properties-community-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/community/details/1',
      'properties-community-details',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Building Details
  test('properties-building-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/building/details/1',
      'properties-building-details',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Unit Details
  test('properties-unit-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/unit/details/1',
      'properties-unit-details',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Unit Owner Assignment Page
  test('properties-unit-owner-assign', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/unit/details/1/owner',
      'properties-unit-owner-assign',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });
});

test.describe('Create Pages - Properties', () => {

  // Add New Community
  test('properties-community-create', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/new/community',
      'properties-community-create',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Add New Building
  test('properties-building-create', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/new/building',
      'properties-building-create',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Add New Unit
  test('properties-unit-create', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/new/unit',
      'properties-unit-create',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });
});

test.describe('Create Pages - Requests', () => {

  // Create Service Request (type=0 is general)
  test('requests-create-general', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/requests/create?type=0',
      'requests-create-general',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Request History (type=0 is general)
  test('requests-history-general', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/requests/history?type=0',
      'requests-history-general',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });
});

test.describe('Detail Pages - Leasing', () => {

  // Lease Details - may have dynamic IDs
  test('leasing-lease-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/leases/1',
      'leasing-lease-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Application Details - may have dynamic IDs
  test('leasing-application-details-page', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/applications/1',
      'leasing-application-details-page',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Quote Details - may have dynamic IDs
  test('leasing-quote-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/quotes/1',
      'leasing-quote-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Visit Details - may have dynamic IDs
  test('leasing-visit-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/visits/1',
      'leasing-visit-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });
});

test.describe('Detail Pages - Contacts', () => {

  // Tenant Details
  test('contacts-tenant-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/contacts/tenants/1',
      'contacts-tenant-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Owner Details
  test('contacts-owner-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/contacts/owners/1',
      'contacts-owner-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Manager Details
  test('contacts-manager-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/contacts/managers/1',
      'contacts-manager-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });
});

test.describe('Detail Pages - Requests', () => {

  // Service Request Details
  test('requests-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/requests/1',
      'requests-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });
});

