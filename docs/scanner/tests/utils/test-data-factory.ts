/**
 * Test Data Factory
 *
 * Generates valid test data based on extracted validation rules and entity relationships.
 * Supports all Atar entities with proper relationship handling.
 */

// ============================================================================
// RANDOM GENERATORS
// ============================================================================

export function randomString(length: number = 8): string {
  const chars = 'abcdefghijklmnopqrstuvwxyz';
  return Array.from({ length }, () => chars[Math.floor(Math.random() * chars.length)]).join('');
}

export function randomNumber(min: number = 1, max: number = 1000): number {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

export function randomPhone(): string {
  return `5${randomNumber(10000000, 99999999)}`;
}

export function randomEmail(prefix?: string): string {
  return `${prefix || randomString(8)}@example.com`;
}

export function randomNationalId(): string {
  return `${randomNumber(1000000000, 9999999999)}`;
}

export function randomDate(daysFromNow: number = 0): string {
  const date = new Date();
  date.setDate(date.getDate() + daysFromNow);
  return date.toISOString().split('T')[0];
}

export function randomArabicName(): string {
  const names = ['محمد', 'أحمد', 'فهد', 'خالد', 'عبدالله', 'سعود', 'عمر', 'ياسر'];
  return names[Math.floor(Math.random() * names.length)];
}

export function uniqueId(): string {
  return `${Date.now()}${randomNumber(100, 999)}`;
}

// ============================================================================
// REFERENCE DATA (Lookup Tables)
// ============================================================================

export const REFERENCE_DATA = {
  countries: [
    { id: 1, name: 'المملكة العربية السعودية', code: 'SA' }
  ],
  currencies: [
    { id: 1, name: 'ريال سعودي', code: 'SAR' }
  ],
  cities: [
    { id: 1, name: 'الرياض' },
    { id: 2, name: 'جدة' },
    { id: 3, name: 'الدمام' }
  ],
  districts: [
    { id: 1, name: 'الدرعية' },
    { id: 2, name: 'العليا' },
    { id: 3, name: 'النزهة' }
  ],
  unitCategories: [
    { id: 1, name: 'شقة' },
    { id: 2, name: 'فيلا' },
    { id: 3, name: 'محل تجاري' }
  ],
  unitTypes: [
    { id: 1, name: 'غرفة واحدة' },
    { id: 2, name: 'غرفتين' },
    { id: 3, name: 'ثلاث غرف' }
  ],
  unitStatuses: [
    { id: 1, name: 'شاغرة' },
    { id: 2, name: 'مشغولة' },
    { id: 3, name: 'تحت الصيانة' },
    { id: 4, name: 'محجوزة' }
  ],
  leaseStatuses: [
    { id: 30, name: 'مسودة' },
    { id: 34, name: 'نشط' },
    { id: 31, name: 'منتهي' },
    { id: 32, name: 'ملغي' },
    { id: 33, name: 'موقف' }
  ],
  managerRoles: [
    { id: 1, name: 'Admin' },
    { id: 2, name: 'Accounting Manager' },
    { id: 3, name: 'Service Manager' },
    { id: 4, name: 'Marketing Manager' },
    { id: 5, name: 'Sales & Leasing Manager' }
  ],
  paymentSchedules: [
    { id: 1, name: 'شهري' },
    { id: 2, name: 'ربع سنوي' },
    { id: 3, name: 'نصف سنوي' },
    { id: 4, name: 'سنوي' }
  ],
  rentalTypes: [
    { id: 'new', name: 'جديد' },
    { id: 'renewal', name: 'تجديد' }
  ],
  tenantTypes: [
    { id: 'individual', name: 'فرد' },
    { id: 'company', name: 'شركة' }
  ],
  requestCategories: [
    { id: 1, name: 'خدمات الوحدات' },
    { id: 2, name: 'طلبات المناطق المشتركة' },
    { id: 3, name: 'طلبات تصاريح الزوار' },
    { id: 5, name: 'حجوزات المرافق' }
  ],
  requestSubCategories: [
    { id: 1, name: 'صيانة', categoryId: 1 },
    { id: 2, name: 'تنظيف المنزل', categoryId: 1 },
    { id: 7, name: 'الأمن و السلامة', categoryId: 2 }
  ],
  transactionCategories: [
    { id: 1, name: 'الإيجارات' },
    { id: 19, name: 'استرجاع التأمين' }
  ]
};

// ============================================================================
// ENTITY INTERFACES
// ============================================================================

export interface CommunityData {
  name: string;
  country_id: number;
  currency_id: number;
  city_id: number;
  district_id: number;
  description?: string;
  sales_commission_rate?: string;
  rental_commission_rate?: string;
}

export interface BuildingData {
  name: string;
  community_id: number;
  city_id?: number;
  district_id?: number;
  no_floors?: string;
}

export interface UnitData {
  name: string;
  community_id: number;
  building_id: number;
  category_id: number;
  type_id: number;
  status_id?: number;
  city_id?: number;
  district_id?: number;
  area?: string;
  price?: string;
}

export interface OwnerData {
  first_name: string;
  last_name: string;
  phone_country_code: string;
  phone_number: string;
  email?: string;
  national_id?: string;
}

export interface TenantData {
  first_name: string;
  last_name: string;
  phone_country_code: string;
  phone_number: string;
  email?: string;
  national_id?: string;
}

export interface AdminData {
  first_name: string;
  last_name: string;
  phone_country_code: string;
  phone_number: string;
  role: number;
  email?: string;
  is_all_communities?: boolean;
  is_all_buildings?: boolean;
  communities?: number[];
  buildings?: number[];
}

export interface ProfessionalData {
  first_name: string;
  last_name: string;
  phone_country_code: string;
  phone_number: string;
  service_types?: number[];
}

export interface LeaseData {
  tenant_type: 'individual' | 'company';
  tenant: {
    first_name: string;
    last_name: string;
    phone_country_code: string;
    phone_number: string;
    national_id?: string;
    email?: string;
  };
  units: Array<{
    unit_id: number;
    rental_amount: string;
    amount_type: string;
  }>;
  rental_contract_type_id: number;
  rental_type: string;
  lease_unit_type: string;
  payment_schedule_id: number;
  start_date: string;
  end_date: string;
  handover_date: string;
  created_at: string;
  autoGenerateLeaseNumber: boolean;
  number_of_years?: number;
  number_of_months?: number;
  contract_number?: string;
}

export interface TransactionData {
  category_id: string;
  subcategory_id?: string;
  amount: number;
  due_on: string;
  assignee_id: number;
  assignee_type: 'tenant' | 'owner';
  lease_id?: number;
  details?: string;
}

export interface FacilityData {
  name: string;
  community_id: number;
  category_id?: number;
  description?: string;
  capacity?: number;
}

export interface RequestSubCategoryData {
  name_ar: string;
  name_en: string;
  category_id: number;
  icon_id?: number;
  status?: string;
}

export interface AnnouncementData {
  title: string;
  content: string;
  community_id?: number;
  building_id?: number;
  start_date?: string;
  end_date?: string;
}

// ============================================================================
// FACTORY FUNCTIONS
// ============================================================================

/**
 * Generate valid Community data
 */
export function createCommunityData(overrides?: Partial<CommunityData>): CommunityData {
  return {
    name: `Test Community ${uniqueId()}`,
    country_id: 1,
    currency_id: 1,
    city_id: 1,
    district_id: 1,
    ...overrides
  };
}

/**
 * Generate valid Building data
 */
export function createBuildingData(communityId: number, overrides?: Partial<BuildingData>): BuildingData {
  return {
    name: `Test Building ${uniqueId()}`,
    community_id: communityId,
    city_id: 1,
    district_id: 1,
    no_floors: '5',
    ...overrides
  };
}

/**
 * Generate valid Unit data
 */
export function createUnitData(
  communityId: number,
  buildingId: number,
  overrides?: Partial<UnitData>
): UnitData {
  return {
    name: `Unit ${uniqueId()}`,
    community_id: communityId,
    building_id: buildingId,
    category_id: 1,
    type_id: 1,
    status_id: 1, // Vacant
    city_id: 1,
    district_id: 1,
    area: '100',
    price: '50000',
    ...overrides
  };
}

/**
 * Generate valid Owner data
 */
export function createOwnerData(overrides?: Partial<OwnerData>): OwnerData {
  return {
    first_name: 'Test',
    last_name: `Owner${randomString(4)}`,
    phone_country_code: 'SA',
    phone_number: randomPhone(),
    email: randomEmail('owner'),
    ...overrides
  };
}

/**
 * Generate valid Tenant data
 */
export function createTenantData(overrides?: Partial<TenantData>): TenantData {
  return {
    first_name: 'Test',
    last_name: `Tenant${randomString(4)}`,
    phone_country_code: 'SA',
    phone_number: randomPhone(),
    email: randomEmail('tenant'),
    ...overrides
  };
}

/**
 * Generate valid Admin data
 */
export function createAdminData(role: number = 1, overrides?: Partial<AdminData>): AdminData {
  return {
    first_name: 'Test',
    last_name: `Admin${randomString(4)}`,
    phone_country_code: 'SA',
    phone_number: randomPhone(),
    role,
    is_all_communities: true,
    is_all_buildings: true,
    ...overrides
  };
}

/**
 * Generate valid Professional data
 */
export function createProfessionalData(overrides?: Partial<ProfessionalData>): ProfessionalData {
  return {
    first_name: 'Test',
    last_name: `Professional${randomString(4)}`,
    phone_country_code: 'SA',
    phone_number: randomPhone(),
    service_types: [1], // Home Service Requests
    ...overrides
  };
}

/**
 * Generate valid Lease data
 */
export function createLeaseData(
  unitId: number,
  overrides?: Partial<LeaseData>
): LeaseData {
  const startDate = randomDate(0);
  const endDate = randomDate(365);

  return {
    tenant_type: 'individual',
    tenant: {
      first_name: 'Test',
      last_name: `Tenant${randomString(4)}`,
      phone_country_code: 'SA',
      phone_number: randomPhone(),
      national_id: randomNationalId(),
      email: randomEmail('tenant')
    },
    units: [{
      unit_id: unitId,
      rental_amount: '50000',
      amount_type: 'yearly'
    }],
    rental_contract_type_id: 1,
    rental_type: 'new',
    lease_unit_type: 'residential',
    payment_schedule_id: 4, // Yearly
    start_date: startDate,
    end_date: endDate,
    handover_date: startDate,
    created_at: new Date().toISOString(),
    autoGenerateLeaseNumber: true,
    number_of_years: 1,
    ...overrides
  };
}

/**
 * Generate valid Transaction data
 */
export function createTransactionData(
  assigneeId: number,
  assigneeType: 'tenant' | 'owner' = 'tenant',
  overrides?: Partial<TransactionData>
): TransactionData {
  return {
    category_id: '1', // Rentals
    amount: 50000,
    due_on: randomDate(30),
    assignee_id: assigneeId,
    assignee_type: assigneeType,
    ...overrides
  };
}

/**
 * Generate valid Facility data
 */
export function createFacilityData(communityId: number, overrides?: Partial<FacilityData>): FacilityData {
  return {
    name: `Test Facility ${uniqueId()}`,
    community_id: communityId,
    description: 'Test facility for automation',
    capacity: 20,
    ...overrides
  };
}

/**
 * Generate valid Request Sub-Category data
 */
export function createRequestSubCategoryData(
  categoryId: number,
  overrides?: Partial<RequestSubCategoryData>
): RequestSubCategoryData {
  return {
    name_ar: `فئة فرعية ${uniqueId()}`,
    name_en: `Sub Category ${uniqueId()}`,
    category_id: categoryId,
    status: '1',
    ...overrides
  };
}

/**
 * Generate valid Announcement data
 */
export function createAnnouncementData(overrides?: Partial<AnnouncementData>): AnnouncementData {
  return {
    title: `Test Announcement ${uniqueId()}`,
    content: 'This is a test announcement created by automation.',
    start_date: randomDate(0),
    end_date: randomDate(30),
    ...overrides
  };
}

// ============================================================================
// BATCH GENERATORS
// ============================================================================

/**
 * Generate a complete property hierarchy
 */
export function createPropertyHierarchy(options?: {
  buildingsPerCommunity?: number;
  unitsPerBuilding?: number;
}): {
  community: CommunityData;
  buildings: BuildingData[];
  units: UnitData[];
} {
  const { buildingsPerCommunity = 2, unitsPerBuilding = 3 } = options || {};

  const community = createCommunityData();
  const buildings: BuildingData[] = [];
  const units: UnitData[] = [];

  // Note: In real usage, you'd create the community first to get its ID
  // For now, use placeholder IDs that would be replaced after creation
  for (let b = 0; b < buildingsPerCommunity; b++) {
    const building = createBuildingData(0); // Community ID would be set after creation
    buildings.push(building);

    for (let u = 0; u < unitsPerBuilding; u++) {
      const unit = createUnitData(0, 0); // IDs would be set after creation
      units.push(unit);
    }
  }

  return { community, buildings, units };
}

/**
 * Generate multiple contacts
 */
export function createContactsBatch(counts: {
  owners?: number;
  tenants?: number;
  admins?: number;
  professionals?: number;
}): {
  owners: OwnerData[];
  tenants: TenantData[];
  admins: AdminData[];
  professionals: ProfessionalData[];
} {
  const { owners = 0, tenants = 0, admins = 0, professionals = 0 } = counts;

  return {
    owners: Array.from({ length: owners }, () => createOwnerData()),
    tenants: Array.from({ length: tenants }, () => createTenantData()),
    admins: Array.from({ length: admins }, () => createAdminData()),
    professionals: Array.from({ length: professionals }, () => createProfessionalData())
  };
}

// ============================================================================
// VALIDATION HELPERS
// ============================================================================

/**
 * Create invalid data variants for negative testing
 */
export function createInvalidVariants<T extends object>(validData: T): Record<string, Partial<T>> {
  const variants: Record<string, Partial<T>> = {};

  // Empty required fields
  for (const key of Object.keys(validData)) {
    variants[`empty_${key}`] = { ...validData, [key]: '' } as Partial<T>;
    variants[`null_${key}`] = { ...validData, [key]: null } as Partial<T>;
  }

  return variants;
}

/**
 * Create boundary test cases
 */
export function createBoundaryTests(field: string, type: 'string' | 'number'): Record<string, unknown> {
  if (type === 'string') {
    return {
      [`${field}_empty`]: '',
      [`${field}_single_char`]: 'a',
      [`${field}_max_length`]: 'a'.repeat(255),
      [`${field}_over_max`]: 'a'.repeat(256),
      [`${field}_special_chars`]: '!@#$%^&*()',
      [`${field}_arabic`]: 'اختبار',
      [`${field}_numbers_only`]: '12345'
    };
  } else {
    return {
      [`${field}_zero`]: 0,
      [`${field}_negative`]: -1,
      [`${field}_max_int`]: 2147483647,
      [`${field}_float`]: 123.45,
      [`${field}_string_number`]: '123'
    };
  }
}

// ============================================================================
// EXPORT ALL
// ============================================================================

export const TestDataFactory = {
  // Random generators
  randomString,
  randomNumber,
  randomPhone,
  randomEmail,
  randomNationalId,
  randomDate,
  randomArabicName,
  uniqueId,

  // Reference data
  REFERENCE_DATA,

  // Entity factories
  createCommunityData,
  createBuildingData,
  createUnitData,
  createOwnerData,
  createTenantData,
  createAdminData,
  createProfessionalData,
  createLeaseData,
  createTransactionData,
  createFacilityData,
  createRequestSubCategoryData,
  createAnnouncementData,

  // Batch generators
  createPropertyHierarchy,
  createContactsBatch,

  // Validation helpers
  createInvalidVariants,
  createBoundaryTests
};

export default TestDataFactory;
