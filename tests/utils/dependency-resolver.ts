/**
 * Dependency Resolver for Mutation Testing
 *
 * Handles entity dependencies and ensures required data exists
 * before running mutation tests
 */

import { ApiContext } from '../fixtures/api.fixture';
import {
  createCommunitySampleData,
  createBuildingSampleData,
  createUnitSampleData,
  createOwnerSampleData,
  createTenantSampleData,
} from './sample-data';

// Entity types that can be resolved
export type EntityType = 'community' | 'building' | 'unit' | 'owner' | 'tenant' | 'lease';

// Resolved entity data
export interface ResolvedEntity {
  id: string;
  data: Record<string, unknown>;
  created: boolean; // true if we created it, false if it already existed
}

// Dependency graph definition
interface EntityDependency {
  entity: EntityType;
  dependsOn: EntityType[];
  getEndpoint: string;
  createEndpoint: string;
}

const DEPENDENCY_GRAPH: EntityDependency[] = [
  {
    entity: 'community',
    dependsOn: [],
    getEndpoint: 'rf/communities?is_paginate=0',
    createEndpoint: 'rf/communities',
  },
  {
    entity: 'building',
    dependsOn: ['community'],
    getEndpoint: 'rf/buildings?is_paginate=0',
    createEndpoint: 'rf/buildings',
  },
  {
    entity: 'unit',
    dependsOn: ['community', 'building'],
    getEndpoint: 'rf/units?is_paginate=0',
    createEndpoint: 'rf/units',
  },
  {
    entity: 'owner',
    dependsOn: [],
    getEndpoint: 'rf/owners?is_paginate=0',
    createEndpoint: 'rf/owners',
  },
  {
    entity: 'tenant',
    dependsOn: [],
    getEndpoint: 'rf/tenants?is_paginate=0',
    createEndpoint: 'rf/tenants',
  },
  {
    entity: 'lease',
    dependsOn: ['unit', 'tenant'],
    getEndpoint: 'rf/leases?is_paginate=0',
    createEndpoint: 'rf/leases/create',
  },
];

/**
 * Dependency Resolver class
 */
export class DependencyResolver {
  private api: ApiContext;
  private resolvedEntities: Map<EntityType, ResolvedEntity[]> = new Map();

  constructor(api: ApiContext) {
    this.api = api;
  }

  /**
   * Get the dependency definition for an entity
   */
  private getDependency(entity: EntityType): EntityDependency | undefined {
    return DEPENDENCY_GRAPH.find((d) => d.entity === entity);
  }

  /**
   * Fetch existing entities from the API
   */
  private async fetchExisting(entity: EntityType): Promise<ResolvedEntity[]> {
    const dep = this.getDependency(entity);
    if (!dep) return [];

    try {
      const data = await this.api.fetchReferenceData(dep.getEndpoint);
      const ids = this.api.extractIds(data);

      return ids.map((id) => ({
        id,
        data: {},
        created: false,
      }));
    } catch (error) {
      console.warn(`Failed to fetch existing ${entity}:`, error);
      return [];
    }
  }

  /**
   * Create a new entity
   */
  private async createEntity(entity: EntityType): Promise<ResolvedEntity | undefined> {
    const dep = this.getDependency(entity);
    if (!dep) return undefined;

    // Resolve dependencies first
    for (const parentEntity of dep.dependsOn) {
      await this.resolve(parentEntity);
    }

    // Generate sample data based on entity type
    let sampleData: Record<string, unknown>;

    switch (entity) {
      case 'community':
        sampleData = createCommunitySampleData();
        break;

      case 'building': {
        const communityId = this.getResolvedId('community');
        if (!communityId) {
          throw new Error('Cannot create building without community');
        }
        sampleData = createBuildingSampleData(communityId);
        break;
      }

      case 'unit': {
        const communityId = this.getResolvedId('community');
        const buildingId = this.getResolvedId('building');
        if (!communityId) {
          throw new Error('Cannot create unit without community');
        }
        sampleData = createUnitSampleData(communityId, buildingId);
        break;
      }

      case 'owner':
        sampleData = createOwnerSampleData();
        break;

      case 'tenant':
        sampleData = createTenantSampleData();
        break;

      default:
        throw new Error(`Unsupported entity type: ${entity}`);
    }

    // Create the entity
    const result = await this.api.post(dep.createEndpoint, sampleData);

    if (result.success) {
      const id = this.api.extractFirstId(result.response.body);
      if (id) {
        return {
          id,
          data: sampleData,
          created: true,
        };
      }
    }

    console.warn(`Failed to create ${entity}:`, result.response.body);
    return undefined;
  }

  /**
   * Resolve an entity - fetch existing or create new
   */
  async resolve(entity: EntityType, forceCreate = false): Promise<ResolvedEntity[]> {
    // Check cache first
    if (!forceCreate && this.resolvedEntities.has(entity)) {
      return this.resolvedEntities.get(entity)!;
    }

    // Resolve dependencies first
    const dep = this.getDependency(entity);
    if (dep) {
      for (const parentEntity of dep.dependsOn) {
        await this.resolve(parentEntity);
      }
    }

    // Try to fetch existing entities
    let entities = await this.fetchExisting(entity);

    // If no existing entities and we need one, create it
    if (entities.length === 0 || forceCreate) {
      const created = await this.createEntity(entity);
      if (created) {
        entities = forceCreate ? [created] : [...entities, created];
      }
    }

    // Cache the result
    this.resolvedEntities.set(entity, entities);

    return entities;
  }

  /**
   * Get the first resolved ID for an entity
   */
  getResolvedId(entity: EntityType, index = 0): string | undefined {
    const entities = this.resolvedEntities.get(entity);
    return entities?.[index]?.id;
  }

  /**
   * Get all resolved IDs for an entity
   */
  getResolvedIds(entity: EntityType): string[] {
    const entities = this.resolvedEntities.get(entity);
    return entities?.map((e) => e.id) || [];
  }

  /**
   * Get the full resolved entity data
   */
  getResolvedEntity(entity: EntityType, index = 0): ResolvedEntity | undefined {
    const entities = this.resolvedEntities.get(entity);
    return entities?.[index];
  }

  /**
   * Clear the cache for a specific entity or all entities
   */
  clearCache(entity?: EntityType): void {
    if (entity) {
      this.resolvedEntities.delete(entity);
    } else {
      this.resolvedEntities.clear();
    }
  }

  /**
   * Check if an entity type has been resolved
   */
  isResolved(entity: EntityType): boolean {
    return this.resolvedEntities.has(entity);
  }

  /**
   * Get a summary of all resolved entities
   */
  getSummary(): Record<EntityType, { count: number; ids: string[]; created: number }> {
    const summary: Record<string, { count: number; ids: string[]; created: number }> = {};

    for (const [entity, entities] of this.resolvedEntities) {
      summary[entity] = {
        count: entities.length,
        ids: entities.map((e) => e.id),
        created: entities.filter((e) => e.created).length,
      };
    }

    return summary as Record<EntityType, { count: number; ids: string[]; created: number }>;
  }
}

/**
 * Helper to create a resolver instance
 */
export function createDependencyResolver(api: ApiContext): DependencyResolver {
  return new DependencyResolver(api);
}
