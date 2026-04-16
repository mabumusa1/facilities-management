import { test, expect } from '../fixtures/scanner.fixture';

/**
 * Pages discovered during manual exploration on 2026-04-15.
 * These pages were found while exploring the system configuration workflow.
 *
 * KNOWN BLOCKERS (see docs/bugs/CONTRACT-TYPES-API-BUG.md):
 * - Quote/Lease creation pages return 404 because Contract Types API is misconfigured
 * - Service request creation requires a unit with active lease (depends on lease creation)
 * - Some service detail pages may have dynamic IDs that don't exist
 */
test.describe('Discovered Pages - Configuration Workflow', () => {

  // Leasing Contract Types Settings
  test('settings-leasing-contract-types', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/leasing/contract-types',
      'settings-leasing-contract-types',
      { waitForNetworkIdle: true }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('settings-leasing-contract-types-add', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/leasing/contract-types/AddNewSubcategory',
      'settings-leasing-contract-types-add',
      { waitForNetworkIdle: true, exploreRelationships: true }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Unit Services Settings - Category Details
  test('settings-unit-services-category', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/Unit%20Services/1',
      'settings-unit-services-category',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Unit Services Settings - Maintenance Details
  // Note: Service detail IDs are dynamic - may not exist in all tenants
  test('settings-unit-services-maintenance-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/Unit%20Services/1/ServiceDetails/1',
      'settings-unit-services-maintenance-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Home Cleaning Service Details
  // Note: Service detail IDs are dynamic - may not exist in all tenants
  test('settings-unit-services-home-cleaning-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/Unit%20Services/1/ServiceDetails/2',
      'settings-unit-services-home-cleaning-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Car Cleaning Service Details
  // Note: Service detail IDs are dynamic - may not exist in all tenants
  test('settings-unit-services-car-cleaning-details', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/Unit%20Services/1/ServiceDetails/3',
      'settings-unit-services-car-cleaning-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Service Request Pages - use waitForNetworkIdle: false due to polling/websocket activity
  test('requests-unit-services', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/requests?type=1',
      'requests-unit-services',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // BLOCKED: Requires unit with active lease (depends on lease creation which is blocked)
  // Skip exploreRelationships since page is blocked anyway
  test('requests-unit-services-create', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/requests/create?type=1',
      'requests-unit-services-create',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('requests-common-area', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/requests?type=2',
      'requests-common-area',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // BLOCKED: Requires unit with active lease (depends on lease creation which is blocked)
  // Skip exploreRelationships since page is blocked anyway
  test('requests-common-area-create', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/requests/create?type=2',
      'requests-common-area-create',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Properties List - Units Tab (waitForNetworkIdle: false due to background activity)
  test('properties-list-units', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/units',
      'properties-list-units',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Properties List - Buildings Tab
  test('properties-list-buildings', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/buildings',
      'properties-list-buildings',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Properties List - Communities Tab
  test('properties-list-communities', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/communities',
      'properties-list-communities',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Common Area Services Settings
  test('settings-common-area-services-category', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/Common%20Area%20Services/2',
      'settings-common-area-services-category',
      { waitForNetworkIdle: true }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // BLOCKED: Requires Contract Types which cannot be created due to API bug
  // See docs/bugs/CONTRACT-TYPES-API-BUG.md
  test('leasing-quotes-create', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/quotes/create',
      'leasing-quotes-create',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Application Details (existing application)
  test('leasing-application-details', async ({ scanner }) => {
    // Note: This might need an actual application ID
    const result = await scanner.scanPage(
      '/leasing/applications/1',
      'leasing-application-details',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
    // Don't fail if page not found - just capture what we can
  });

  // Lease Creation Form - BLOCKED by Contract Types bug
  test('leasing-leases-create', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/leases/create',
      'leasing-leases-create',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Service Request History - use waitForNetworkIdle: false due to polling
  test('requests-history-unit-services', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/requests/history?type=1',
      'requests-history-unit-services',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Settings Services (main page)
  test('settings-services-main', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/services',
      'settings-services-main',
      { waitForNetworkIdle: true, failOnNotFound: false }
    );
  });

  // Transaction Schedules / Payment Schedules
  test('settings-transaction-schedules', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/transaction-schedules',
      'settings-transaction-schedules',
      { waitForNetworkIdle: true, failOnNotFound: false }
    );
  });
});
