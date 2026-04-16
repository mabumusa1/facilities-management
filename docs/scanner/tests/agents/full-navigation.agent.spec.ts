import { test, expect } from '../fixtures/scanner.fixture';

// Increase test timeout for full navigation tests (network can be slow)
test.setTimeout(90000);

/**
 * Full Application Navigation Scanner
 * Discovered via manual browser exploration on 2026-04-16.
 *
 * This covers all main navigation areas and settings pages.
 */
test.describe('Full Navigation - Dashboard & Main Areas', () => {

  test('dashboard', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/dashboard',
      'dashboard',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Properties Section - uses /properties-list/ path
  test('properties-communities-list', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/communities',
      'properties-communities-list',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('properties-buildings-list', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/buildings',
      'properties-buildings-list',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('properties-units-list', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/properties-list/units',
      'properties-units-list',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });
});

test.describe('Full Navigation - Leasing Section', () => {

  test('leasing-visits', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/visits',
      'leasing-visits',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('leasing-applications', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/applications',
      'leasing-applications',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('leasing-quotes', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/quotes',
      'leasing-quotes',
      { waitForNetworkIdle: false, waitTimeout: 90000, failOnNotFound: false }
    );
  });

  test('leasing-leases', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/leasing/leases',
      'leasing-leases',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });
});

test.describe('Full Navigation - Visitor & Facilities', () => {

  test('visitor-access', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/visitor-access',
      'visitor-access',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('visitor-access-history', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/visitor-access/history',
      'visitor-access-history',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('facilities-booking', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/facilities-booking',
      'facilities-booking',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });
});

test.describe('Full Navigation - Accounting', () => {

  test('transactions-all', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/transactions',
      'transactions-all',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Money In tab (activeTab=2)
  test('transactions-money-in', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/transactions?filter[activeTab]=2',
      'transactions-money-in',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  // Money Out tab (activeTab=3)
  test('transactions-money-out', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/transactions?filter%5BactiveTab%5D=3',
      'transactions-money-out',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });
});

test.describe('Full Navigation - Communication', () => {

  test('communication-offers', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/offers',
      'communication-offers',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('communication-directory', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/directory',
      'communication-directory',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('communication-suggestions', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/suggestions',
      'communication-suggestions',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });
});

test.describe('Full Navigation - Contacts', () => {

  test('contacts-tenants', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/contacts/tenants',
      'contacts-tenants',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('contacts-owners', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/contacts/owners',
      'contacts-owners',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('contacts-managers', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/contacts/managers',
      'contacts-managers',
      { waitForNetworkIdle: false, waitTimeout: 60000 }
    );
    expect(result.endpoints.length).toBeGreaterThan(0);
  });

  test('contacts-professionals', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/contacts/professionals',
      'contacts-professionals',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });
});

test.describe('Full Navigation - Reporting', () => {

  test('reporting-system', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/reporting/system',
      'reporting-system',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('reporting-powerbi', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/reporting/powerbi',
      'reporting-powerbi',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });
});

test.describe('Full Navigation - Settings Pages', () => {

  test('settings-main', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings',
      'settings-main',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('settings-lease', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/leasing',
      'settings-lease',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('settings-visitor', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/visitor',
      'settings-visitor',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('settings-offers', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/offers',
      'settings-offers',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('settings-facilities', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/facilities',
      'settings-facilities',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('settings-company-profile', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/company-profile',
      'settings-company-profile',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('settings-directory', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/directory',
      'settings-directory',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  // Service Flow Settings
  test('settings-home-service-flow', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/home-service-settings/1',
      'settings-home-service-flow',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });

  test('settings-neighbourhood-service-flow', async ({ scanner }) => {
    const result = await scanner.scanPage(
      '/settings/neighbourhood-service-settings/2',
      'settings-neighbourhood-service-flow',
      { waitForNetworkIdle: false, waitTimeout: 60000, failOnNotFound: false }
    );
  });
});
