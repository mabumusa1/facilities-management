/**
 * Test Data Seeder
 *
 * Creates test entities via API in the correct dependency order.
 * Maintains entity registry for relationship tracking.
 */

import { APIRequestContext } from '@playwright/test';
import {
  TestDataFactory,
  CommunityData,
  BuildingData,
  UnitData,
  OwnerData,
  TenantData,
  AdminData,
  LeaseData
} from './test-data-factory';

// ============================================================================
// TYPES
// ============================================================================

export interface CreatedEntity {
  id: number;
  type: EntityType;
  data: unknown;
  createdAt: string;
}

export type EntityType =
  | 'community'
  | 'building'
  | 'unit'
  | 'facility'
  | 'owner'
  | 'tenant'
  | 'admin'
  | 'professional'
  | 'lease'
  | 'transaction';

export interface EntityRegistry {
  communities: CreatedEntity[];
  buildings: CreatedEntity[];
  units: CreatedEntity[];
  facilities: CreatedEntity[];
  owners: CreatedEntity[];
  tenants: CreatedEntity[];
  admins: CreatedEntity[];
  professionals: CreatedEntity[];
  leases: CreatedEntity[];
  transactions: CreatedEntity[];
}

export interface SeederConfig {
  apiBaseUrl: string;
  tenant: string;
  authToken: string;
}

// ============================================================================
// TEST DATA SEEDER CLASS
// ============================================================================

export class TestDataSeeder {
  private request: APIRequestContext;
  private config: SeederConfig;
  private registry: EntityRegistry;

  constructor(request: APIRequestContext, config: SeederConfig) {
    this.request = request;
    this.config = config;
    this.registry = {
      communities: [],
      buildings: [],
      units: [],
      facilities: [],
      owners: [],
      tenants: [],
      admins: [],
      professionals: [],
      leases: [],
      transactions: []
    };
  }

  // ==========================================================================
  // PRIVATE HELPERS
  // ==========================================================================

  private get headers(): Record<string, string> {
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': `Bearer ${this.config.authToken}`,
      'X-Tenant': this.config.tenant
    };
  }

  private async post<T>(endpoint: string, data: unknown): Promise<T> {
    const response = await this.request.post(
      `${this.config.apiBaseUrl}/${endpoint}`,
      {
        headers: this.headers,
        data
      }
    );

    if (!response.ok()) {
      const error = await response.text();
      throw new Error(`POST ${endpoint} failed: ${response.status()} - ${error}`);
    }

    const json = await response.json();
    return json.data as T;
  }

  private async put<T>(endpoint: string, data: unknown): Promise<T> {
    const response = await this.request.put(
      `${this.config.apiBaseUrl}/${endpoint}`,
      {
        headers: this.headers,
        data
      }
    );

    if (!response.ok()) {
      const error = await response.text();
      throw new Error(`PUT ${endpoint} failed: ${response.status()} - ${error}`);
    }

    const json = await response.json();
    return json.data as T;
  }

  private async delete(endpoint: string): Promise<void> {
    const response = await this.request.delete(
      `${this.config.apiBaseUrl}/${endpoint}`,
      { headers: this.headers }
    );

    if (!response.ok()) {
      const error = await response.text();
      throw new Error(`DELETE ${endpoint} failed: ${response.status()} - ${error}`);
    }
  }

  private registerEntity(type: EntityType, id: number, data: unknown): CreatedEntity {
    const entity: CreatedEntity = {
      id,
      type,
      data,
      createdAt: new Date().toISOString()
    };

    switch (type) {
      case 'community':
        this.registry.communities.push(entity);
        break;
      case 'building':
        this.registry.buildings.push(entity);
        break;
      case 'unit':
        this.registry.units.push(entity);
        break;
      case 'facility':
        this.registry.facilities.push(entity);
        break;
      case 'owner':
        this.registry.owners.push(entity);
        break;
      case 'tenant':
        this.registry.tenants.push(entity);
        break;
      case 'admin':
        this.registry.admins.push(entity);
        break;
      case 'professional':
        this.registry.professionals.push(entity);
        break;
      case 'lease':
        this.registry.leases.push(entity);
        break;
      case 'transaction':
        this.registry.transactions.push(entity);
        break;
    }

    return entity;
  }

  // ==========================================================================
  // ENTITY CREATION METHODS
  // ==========================================================================

  /**
   * Create a Community
   */
  async createCommunity(overrides?: Partial<CommunityData>): Promise<CreatedEntity> {
    const data = TestDataFactory.createCommunityData(overrides);
    const result = await this.post<{ id: number }>('rf/communities', data);
    return this.registerEntity('community', result.id, data);
  }

  /**
   * Create a Building (requires community)
   */
  async createBuilding(
    communityId?: number,
    overrides?: Partial<BuildingData>
  ): Promise<CreatedEntity> {
    // Use provided community or get first from registry or create new
    let community_id = communityId;
    if (!community_id) {
      if (this.registry.communities.length === 0) {
        const community = await this.createCommunity();
        community_id = community.id;
      } else {
        community_id = this.registry.communities[0].id;
      }
    }

    const data = TestDataFactory.createBuildingData(community_id, overrides);
    const result = await this.post<{ id: number }>('rf/buildings/store', data);
    return this.registerEntity('building', result.id, data);
  }

  /**
   * Create a Unit (requires community and building)
   */
  async createUnit(
    communityId?: number,
    buildingId?: number,
    overrides?: Partial<UnitData>
  ): Promise<CreatedEntity> {
    // Ensure building exists (which ensures community exists)
    let community_id = communityId;
    let building_id = buildingId;

    if (!building_id) {
      if (this.registry.buildings.length === 0) {
        const building = await this.createBuilding(community_id);
        building_id = building.id;
        community_id = (building.data as BuildingData).community_id;
      } else {
        const building = this.registry.buildings[0];
        building_id = building.id;
        community_id = (building.data as BuildingData).community_id;
      }
    }

    if (!community_id) {
      community_id = this.registry.communities[0]?.id || 1;
    }

    const data = TestDataFactory.createUnitData(community_id, building_id, overrides);
    const result = await this.post<{ id: number }>('rf/units', data);
    return this.registerEntity('unit', result.id, data);
  }

  /**
   * Create an Owner
   */
  async createOwner(overrides?: Partial<OwnerData>): Promise<CreatedEntity> {
    const data = TestDataFactory.createOwnerData(overrides);
    const result = await this.post<{ id: number }>('rf/owners', data);
    return this.registerEntity('owner', result.id, data);
  }

  /**
   * Create a Tenant
   */
  async createTenant(overrides?: Partial<TenantData>): Promise<CreatedEntity> {
    const data = TestDataFactory.createTenantData(overrides);
    const result = await this.post<{ id: number }>('rf/tenants', data);
    return this.registerEntity('tenant', result.id, data);
  }

  /**
   * Create an Admin
   */
  async createAdmin(
    role: number = 1,
    overrides?: Partial<AdminData>
  ): Promise<CreatedEntity> {
    const data = TestDataFactory.createAdminData(role, overrides);
    const result = await this.post<{ id: number }>('rf/admins', data);
    return this.registerEntity('admin', result.id, data);
  }

  /**
   * Create a Lease (requires unit)
   */
  async createLease(
    unitId?: number,
    overrides?: Partial<LeaseData>
  ): Promise<CreatedEntity> {
    // Ensure unit exists
    let unit_id = unitId;
    if (!unit_id) {
      if (this.registry.units.length === 0) {
        const unit = await this.createUnit();
        unit_id = unit.id;
      } else {
        unit_id = this.registry.units[0].id;
      }
    }

    const data = TestDataFactory.createLeaseData(unit_id, overrides);
    const result = await this.post<{ id: number }>('rf/leases/create', data);
    return this.registerEntity('lease', result.id, data);
  }

  // ==========================================================================
  // BULK OPERATIONS
  // ==========================================================================

  /**
   * Create a complete property hierarchy
   */
  async seedPropertyHierarchy(options?: {
    communities?: number;
    buildingsPerCommunity?: number;
    unitsPerBuilding?: number;
  }): Promise<{
    communities: CreatedEntity[];
    buildings: CreatedEntity[];
    units: CreatedEntity[];
  }> {
    const {
      communities = 1,
      buildingsPerCommunity = 2,
      unitsPerBuilding = 3
    } = options || {};

    const createdCommunities: CreatedEntity[] = [];
    const createdBuildings: CreatedEntity[] = [];
    const createdUnits: CreatedEntity[] = [];

    for (let c = 0; c < communities; c++) {
      const community = await this.createCommunity();
      createdCommunities.push(community);

      for (let b = 0; b < buildingsPerCommunity; b++) {
        const building = await this.createBuilding(community.id);
        createdBuildings.push(building);

        for (let u = 0; u < unitsPerBuilding; u++) {
          const unit = await this.createUnit(community.id, building.id);
          createdUnits.push(unit);
        }
      }
    }

    return {
      communities: createdCommunities,
      buildings: createdBuildings,
      units: createdUnits
    };
  }

  /**
   * Create contacts batch
   */
  async seedContacts(counts: {
    owners?: number;
    tenants?: number;
    admins?: number;
  }): Promise<{
    owners: CreatedEntity[];
    tenants: CreatedEntity[];
    admins: CreatedEntity[];
  }> {
    const { owners = 0, tenants = 0, admins = 0 } = counts;

    const createdOwners: CreatedEntity[] = [];
    const createdTenants: CreatedEntity[] = [];
    const createdAdmins: CreatedEntity[] = [];

    for (let i = 0; i < owners; i++) {
      createdOwners.push(await this.createOwner());
    }

    for (let i = 0; i < tenants; i++) {
      createdTenants.push(await this.createTenant());
    }

    for (let i = 0; i < admins; i++) {
      createdAdmins.push(await this.createAdmin());
    }

    return {
      owners: createdOwners,
      tenants: createdTenants,
      admins: createdAdmins
    };
  }

  /**
   * Seed complete test environment
   */
  async seedTestEnvironment(): Promise<EntityRegistry> {
    // Create property hierarchy
    await this.seedPropertyHierarchy({
      communities: 1,
      buildingsPerCommunity: 2,
      unitsPerBuilding: 2
    });

    // Create contacts
    await this.seedContacts({
      owners: 2,
      tenants: 3
    });

    // Create a lease for one of the units
    if (this.registry.units.length > 0) {
      await this.createLease(this.registry.units[0].id);
    }

    return this.registry;
  }

  // ==========================================================================
  // REGISTRY ACCESS
  // ==========================================================================

  /**
   * Get entity registry
   */
  getRegistry(): EntityRegistry {
    return this.registry;
  }

  /**
   * Get first entity of type
   */
  getFirst(type: EntityType): CreatedEntity | undefined {
    switch (type) {
      case 'community':
        return this.registry.communities[0];
      case 'building':
        return this.registry.buildings[0];
      case 'unit':
        return this.registry.units[0];
      case 'facility':
        return this.registry.facilities[0];
      case 'owner':
        return this.registry.owners[0];
      case 'tenant':
        return this.registry.tenants[0];
      case 'admin':
        return this.registry.admins[0];
      case 'professional':
        return this.registry.professionals[0];
      case 'lease':
        return this.registry.leases[0];
      case 'transaction':
        return this.registry.transactions[0];
    }
  }

  /**
   * Get all entities of type
   */
  getAll(type: EntityType): CreatedEntity[] {
    switch (type) {
      case 'community':
        return this.registry.communities;
      case 'building':
        return this.registry.buildings;
      case 'unit':
        return this.registry.units;
      case 'facility':
        return this.registry.facilities;
      case 'owner':
        return this.registry.owners;
      case 'tenant':
        return this.registry.tenants;
      case 'admin':
        return this.registry.admins;
      case 'professional':
        return this.registry.professionals;
      case 'lease':
        return this.registry.leases;
      case 'transaction':
        return this.registry.transactions;
    }
  }

  /**
   * Clear registry (for test isolation)
   */
  clearRegistry(): void {
    this.registry = {
      communities: [],
      buildings: [],
      units: [],
      facilities: [],
      owners: [],
      tenants: [],
      admins: [],
      professionals: [],
      leases: [],
      transactions: []
    };
  }

  // ==========================================================================
  // CLEANUP
  // ==========================================================================

  /**
   * Cleanup all created entities (in reverse dependency order)
   */
  async cleanup(): Promise<void> {
    // Delete in reverse dependency order
    const cleanupOrder: Array<{ type: EntityType; endpoint: string }> = [
      { type: 'transaction', endpoint: 'rf/transactions' },
      { type: 'lease', endpoint: 'rf/leases' },
      { type: 'professional', endpoint: 'rf/professionals' },
      { type: 'admin', endpoint: 'rf/admins' },
      { type: 'tenant', endpoint: 'rf/tenants' },
      { type: 'owner', endpoint: 'rf/owners' },
      { type: 'facility', endpoint: 'rf/facilities' },
      { type: 'unit', endpoint: 'rf/units' },
      { type: 'building', endpoint: 'rf/buildings' },
      { type: 'community', endpoint: 'rf/communities' }
    ];

    for (const { type, endpoint } of cleanupOrder) {
      const entities = this.getAll(type);
      for (const entity of entities.reverse()) {
        try {
          await this.delete(`${endpoint}/${entity.id}`);
        } catch (error) {
          // Log but don't fail cleanup
          console.warn(`Failed to cleanup ${type} ${entity.id}:`, error);
        }
      }
    }

    this.clearRegistry();
  }
}

// ============================================================================
// FACTORY FUNCTION
// ============================================================================

export function createTestDataSeeder(
  request: APIRequestContext,
  config: SeederConfig
): TestDataSeeder {
  return new TestDataSeeder(request, config);
}

export default TestDataSeeder;
