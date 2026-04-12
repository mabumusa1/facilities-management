/**
 * Sample Data for Mutation Testing
 * Based on working request patterns from atar-cloner exploration
 *
 * CRITICAL LEARNINGS:
 * - Unit map object MUST have ALL 8 fields
 * - Lease rental_type MUST be "detailed" (not "yearly" or "annual")
 * - Phone numbers WITHOUT country prefix (e.g., "500000002" not "+966500000002")
 * - national_id must be unique across owners/tenants
 * - Unit status must be 26 (Available) or 23 (Sold) for leasing
 */

import {
  STATUS_IDS,
  RENTAL_CONTRACT_TYPES,
  PAYMENT_SCHEDULES,
  UNIT_CATEGORIES,
  UNIT_TYPES,
} from './mutation-types';

// Helper to generate unique IDs
const generateUniqueId = () => Date.now().toString() + Math.random().toString(36).substr(2, 5);
const generatePhoneNumber = () => `5${Math.floor(Math.random() * 100000000).toString().padStart(8, '0')}`;
const generateNationalId = () => Math.floor(Math.random() * 9000000000 + 1000000000).toString();
const formatDate = (date: Date) => date.toISOString().split('T')[0];

/**
 * COMPLETE MAP OBJECT - CRITICAL
 * All 8 fields are required or you get a 400 error
 */
export const COMPLETE_MAP_OBJECT = {
  latitude: 24.7103488,
  longitude: 46.6878464,
  place_id: 'ChIJhz3eOQADLz4R_cqAJEJr0Qw',
  districtName: 'RHSA7555, 3287',
  formattedAddress: 'RHSA7555, 3287 Wadi Al Junah, As Sulimaniyah, Riyadh 12245, Saudi Arabia',
  latitudeDelta: 0.02,
  longitudeDelta: 0.009244060475161988,
  mapsLink: 'https://www.google.com/maps/search/?api=1&query=24.7103488,46.6878464',
};

/**
 * Community sample data
 */
export function createCommunitySampleData(overrides: Partial<Record<string, unknown>> = {}) {
  return {
    name: `Test Community ${generateUniqueId()}`,
    country_id: 1, // Saudi Arabia
    currency_id: 1, // SAR
    city_id: 1,
    district_id: 1,
    ...overrides,
  };
}

/**
 * Building sample data
 */
export function createBuildingSampleData(
  communityId: number | string,
  overrides: Partial<Record<string, unknown>> = {}
) {
  return {
    name: `Test Building ${generateUniqueId()}`,
    rf_community_id: communityId,
    ...overrides,
  };
}

/**
 * Unit sample data - COMPLETE with all required fields
 * CRITICAL: map object must have ALL 8 fields
 */
export function createUnitSampleData(
  communityId: number | string,
  buildingId?: number | string,
  overrides: Partial<Record<string, unknown>> = {}
) {
  return {
    name: `Test Unit ${generateUniqueId()}`,
    category_id: UNIT_CATEGORIES.residential,
    type_id: UNIT_TYPES.residential.apartment,
    rf_community_id: communityId,
    rf_building_id: buildingId || null,
    rf_status_id: STATUS_IDS.unit.available,
    map: { ...COMPLETE_MAP_OBJECT },
    specifications: [],
    features: [],
    ...overrides,
  };
}

/**
 * Owner sample data
 */
export function createOwnerSampleData(overrides: Partial<Record<string, unknown>> = {}) {
  return {
    first_name: 'Test',
    last_name: `Owner${generateUniqueId().slice(-6)}`,
    phone_country_code: 'SA',
    phone_number: generatePhoneNumber(),
    email: `owner${generateUniqueId()}@example.com`,
    national_id: generateNationalId(),
    ...overrides,
  };
}

/**
 * Tenant sample data (individual)
 */
export function createTenantSampleData(overrides: Partial<Record<string, unknown>> = {}) {
  const uniqueId = generateUniqueId().slice(-6);
  return {
    first_name: 'Test',
    last_name: `Tenant${uniqueId}`,
    phone_country_code: 'SA',
    phone_number: generatePhoneNumber(),
    email: `tenant${uniqueId}@example.com`,
    national_id: generateNationalId(),
    type: 'individual',
    name: `Test Tenant${uniqueId}`,
    ...overrides,
  };
}

/**
 * Tenant sample data (company)
 */
export function createCompanyTenantSampleData(overrides: Partial<Record<string, unknown>> = {}) {
  const uniqueId = generateUniqueId().slice(-6);
  return {
    first_name: 'Test',
    last_name: `Company${uniqueId}`,
    phone_country_code: 'SA',
    phone_number: generatePhoneNumber(),
    email: `company${uniqueId}@example.com`,
    type: 'company',
    company_name: `Test Company ${uniqueId}`,
    commercial_registration_number: generateNationalId(),
    ...overrides,
  };
}

/**
 * Lease sample data - COMPLETE with all required fields
 * CRITICAL: rental_type MUST be "detailed"
 */
export function createLeaseSampleData(
  unitId: number | string,
  tenantId: number | string,
  tenantInfo: { name: string; phone_number: string },
  overrides: Partial<Record<string, unknown>> = {}
) {
  const today = new Date();
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() + 7); // Start a week from now
  const endDate = new Date(startDate);
  endDate.setFullYear(endDate.getFullYear() + 1); // One year lease

  return {
    created_at: formatDate(today),
    start_date: formatDate(startDate),
    end_date: formatDate(endDate),
    handover_date: formatDate(startDate),
    number_of_years: 1,
    number_of_months: 0,
    lease_unit_type: 2, // Residential
    tenant_type: 'individual',
    tenant_id: tenantId,
    tenant: {
      id: tenantId,
      name: tenantInfo.name,
      phone_number: tenantInfo.phone_number,
    },
    autoGenerateLeaseNumber: true,
    rental_type: 'detailed', // CRITICAL: Must be "detailed"
    rental_contract_type_id: RENTAL_CONTRACT_TYPES.yearly,
    payment_schedule_id: PAYMENT_SCHEDULES.yearly.annual,
    lease_escalations_type: 'fixed',
    rental_total_amount: 60000,
    rf_status_id: STATUS_IDS.lease.newContract,
    units: [
      {
        id: unitId,
        rental_annual_type: 'total',
        annual_rental_amount: 60000,
        amount_type: 'total', // CRITICAL: Required field
      },
    ],
    ...overrides,
  };
}

/**
 * Lease status change data (move-out/terminate)
 */
export function createLeaseStatusChangeSampleData(
  leaseId: number | string,
  overrides: Partial<Record<string, unknown>> = {}
) {
  return {
    rf_lease_id: leaseId,
    end_at: formatDate(new Date()),
    ...overrides,
  };
}

/**
 * Lease renewal sample data
 */
export function createLeaseRenewalSampleData(
  leaseId: number | string,
  unitId: number | string,
  overrides: Partial<Record<string, unknown>> = {}
) {
  const today = new Date();
  const startDate = new Date(today);
  startDate.setDate(startDate.getDate() + 7);
  const endDate = new Date(startDate);
  endDate.setFullYear(endDate.getFullYear() + 1);

  return {
    rf_lease_id: leaseId,
    rental_contract_type_id: RENTAL_CONTRACT_TYPES.yearly,
    created_at: formatDate(today),
    start_date: formatDate(startDate),
    end_date: formatDate(endDate),
    number_of_years: 1,
    number_of_months: 0,
    autoGenerateLeaseNumber: true,
    rental_type: 'detailed',
    payment_schedule_id: PAYMENT_SCHEDULES.yearly.annual,
    units: [
      {
        id: unitId,
        rental_annual_type: 'total',
        annual_rental_amount: 60000,
        amount_type: 'total',
      },
    ],
    ...overrides,
  };
}

/**
 * Marketplace bank settings sample data
 */
export function createBankSettingsSampleData(overrides: Partial<Record<string, unknown>> = {}) {
  return {
    beneficiary_name: 'Test Beneficiary',
    bank_name: 'Test Bank',
    account_number: '12345678901234567890', // Min 14 digits
    iban: 'SA0380000000608010167519',
    ...overrides,
  };
}

/**
 * Marketplace sales settings sample data
 */
export function createSalesSettingsSampleData(overrides: Partial<Record<string, unknown>> = {}) {
  return {
    deposit_time_limit_days: 7,
    cash_contract_signing_days: 14,
    bank_contract_signing_days: 30,
    ...overrides,
  };
}

/**
 * Marketplace visits settings sample data
 */
export function createVisitsSettingsSampleData(overrides: Partial<Record<string, unknown>> = {}) {
  return {
    is_all_day: false,
    days: [
      { day: 'sunday', enabled: true, start_time: '09:00', end_time: '17:00' },
      { day: 'monday', enabled: true, start_time: '09:00', end_time: '17:00' },
      { day: 'tuesday', enabled: true, start_time: '09:00', end_time: '17:00' },
      { day: 'wednesday', enabled: true, start_time: '09:00', end_time: '17:00' },
      { day: 'thursday', enabled: true, start_time: '09:00', end_time: '17:00' },
    ],
    ...overrides,
  };
}

/**
 * Request service settings sample data
 */
export function createServiceSettingsSampleData(
  categoryId: number | string,
  subCategoryId: number | string,
  overrides: Partial<Record<string, unknown>> = {}
) {
  return {
    category_id: categoryId,
    sub_category_id: subCategoryId,
    is_active: true,
    auto_assign: false,
    ...overrides,
  };
}

/**
 * Request status change sample data
 */
export function createRequestStatusChangeSampleData(
  requestId: number | string,
  overrides: Partial<Record<string, unknown>> = {}
) {
  return {
    request_id: requestId,
    ...overrides,
  };
}

/**
 * Empty/invalid data for validation error testing
 */
export const EMPTY_DATA = {};

export const INVALID_UNIT_DATA = {
  name: 'Test Unit',
  category_id: 2,
  type_id: 17,
  // Missing rf_community_id and map - should fail
};

export const PARTIAL_MAP_DATA = {
  name: 'Test Unit',
  category_id: 2,
  type_id: 17,
  rf_community_id: 1,
  map: {
    latitude: 24.7103488,
    longitude: 46.6878464,
    // Missing other fields - should fail with 400
  },
};

export const INVALID_LEASE_DATA = {
  rental_type: 'yearly', // WRONG: should be "detailed"
  rental_contract_type_id: 13,
  // Missing required fields
};

export const INVALID_BANK_SETTINGS = {
  beneficiary_name: 'Test',
  account_number: '123', // Too short - min 14 digits
};
