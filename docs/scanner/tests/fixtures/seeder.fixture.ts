/**
 * Test Data Seeder Fixture
 *
 * Provides test data seeding capabilities integrated with Playwright.
 * Automatically handles cleanup after each test.
 */

import { test as base } from '@playwright/test';
import { TestDataSeeder, SeederConfig, EntityRegistry } from '../utils/test-data-seeder';
import { TestDataFactory } from '../utils/test-data-factory';

// ============================================================================
// CONFIGURATION
// ============================================================================

const DEFAULT_CONFIG: SeederConfig = {
  apiBaseUrl: 'https://api.goatar.com/api-management',
  tenant: 'testbusiness123',
  // Token will be loaded from localstorage.json or provided via env
  authToken: process.env.ATAR_AUTH_TOKEN || ''
};

// ============================================================================
// FIXTURE TYPES
// ============================================================================

export interface SeederFixtures {
  /**
   * Test data seeder instance - creates entities via API
   */
  seeder: TestDataSeeder;

  /**
   * Pure data factory - generates data without API calls
   */
  factory: typeof TestDataFactory;

  /**
   * Pre-seeded test environment (created once per test file)
   */
  testEnv: EntityRegistry;
}

// ============================================================================
// FIXTURE IMPLEMENTATION
// ============================================================================

/**
 * Extended test with seeder fixtures
 */
export const test = base.extend<SeederFixtures>({
  /**
   * Test data seeder - available per test
   * Automatically cleans up created entities after test
   */
  seeder: async ({ request }, use) => {
    // Load auth token from localstorage.json if not provided
    let config = { ...DEFAULT_CONFIG };

    if (!config.authToken) {
      try {
        const fs = await import('fs/promises');
        const path = await import('path');
        const localStoragePath = path.join(process.cwd(), 'tests', 'localstorage.json');
        const content = await fs.readFile(localStoragePath, 'utf-8');
        const localStorage = JSON.parse(content);

        // Find auth token
        const tokenEntry = localStorage.find((item: { name: string }) =>
          item.name === 'token' || item.name.includes('auth')
        );
        if (tokenEntry) {
          config.authToken = tokenEntry.value;
        }
      } catch {
        console.warn('Could not load auth token from localstorage.json');
      }
    }

    const seeder = new TestDataSeeder(request, config);

    // Run the test
    await use(seeder);

    // Cleanup after test
    try {
      await seeder.cleanup();
    } catch (error) {
      console.warn('Seeder cleanup failed:', error);
    }
  },

  /**
   * Pure data factory - generates data structures without API calls
   */
  factory: async ({}, use) => {
    await use(TestDataFactory);
  },

  /**
   * Pre-seeded test environment
   * Creates a standard set of entities once per test file
   */
  testEnv: async ({ seeder }, use) => {
    // Seed a standard test environment
    const registry = await seeder.seedTestEnvironment();
    await use(registry);
    // Cleanup is handled by the seeder fixture
  }
});

// ============================================================================
// RE-EXPORT EXPECT
// ============================================================================

export { expect } from '@playwright/test';

// ============================================================================
// HELPER HOOKS
// ============================================================================

/**
 * Create a minimal test environment
 */
export async function createMinimalEnv(seeder: TestDataSeeder): Promise<{
  community: number;
  building: number;
  unit: number;
  tenant: number;
}> {
  const community = await seeder.createCommunity();
  const building = await seeder.createBuilding(community.id);
  const unit = await seeder.createUnit(community.id, building.id);
  const tenant = await seeder.createTenant();

  return {
    community: community.id,
    building: building.id,
    unit: unit.id,
    tenant: tenant.id
  };
}

/**
 * Create entities for lease testing
 */
export async function createLeaseTestEnv(seeder: TestDataSeeder): Promise<{
  community: number;
  building: number;
  units: number[];
  tenant: number;
}> {
  const community = await seeder.createCommunity();
  const building = await seeder.createBuilding(community.id);

  const units: number[] = [];
  for (let i = 0; i < 3; i++) {
    const unit = await seeder.createUnit(community.id, building.id);
    units.push(unit.id);
  }

  const tenant = await seeder.createTenant();

  return {
    community: community.id,
    building: building.id,
    units,
    tenant: tenant.id
  };
}

// ============================================================================
// USAGE EXAMPLE
// ============================================================================

/*

// Example test using seeder fixtures:

import { test, expect, createMinimalEnv } from '../fixtures/seeder.fixture';

test.describe('Lease Management', () => {
  test('should create a lease', async ({ seeder, request }) => {
    // Create required entities
    const env = await createMinimalEnv(seeder);

    // Create lease using the seeded entities
    const lease = await seeder.createLease(env.unit);

    expect(lease.id).toBeDefined();
    expect(lease.type).toBe('lease');
  });

  test('should use pre-seeded environment', async ({ testEnv }) => {
    // testEnv already has communities, buildings, units, tenants, etc.
    expect(testEnv.communities.length).toBeGreaterThan(0);
    expect(testEnv.units.length).toBeGreaterThan(0);
    expect(testEnv.tenants.length).toBeGreaterThan(0);
  });

  test('should generate valid data without API calls', async ({ factory }) => {
    // Generate data for manual use
    const ownerData = factory.createOwnerData({
      first_name: 'Custom Name'
    });

    expect(ownerData.first_name).toBe('Custom Name');
    expect(ownerData.phone_number).toMatch(/^5\d{8}$/);
  });
});

*/

export default test;
