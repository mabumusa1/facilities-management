import { Page } from '@playwright/test';
import { CapturedRequest, API_DOMAIN } from './types';

export interface ApiRelationship {
  parentEndpoint: string;
  parentField: string; // e.g., "community_id"
  childEndpoint: string;
  relationship: string; // e.g., "community_id from communities used in buildings query"
}

export interface ExtractedEnum {
  name: string;
  source: string; // 'translation' | 'api' | 'js'
  values: Array<{ key: string; value: string; label?: string }>;
}

export interface DataAnalysis {
  apiRelationships: ApiRelationship[];
  enums: ExtractedEnum[];
  foreignKeys: Array<{ field: string; referencedEntity: string; foundIn: string[] }>;
  translationKeys: Record<string, string>;
}

/**
 * Analyze API responses to find relationships between entities
 */
export function analyzeApiRelationships(apiRequests: CapturedRequest[]): ApiRelationship[] {
  const relationships: ApiRelationship[] = [];
  const endpoints = apiRequests.filter(r => r.url.includes(API_DOMAIN));

  // Common relationship patterns
  const relationshipPatterns = [
    { parent: 'communities', child: 'buildings', foreignKey: 'community_id' },
    { parent: 'buildings', child: 'units', foreignKey: 'building_id' },
    { parent: 'communities', child: 'units', foreignKey: 'community_id' },
    { parent: 'units', child: 'leases', foreignKey: 'unit_id' },
    { parent: 'units', child: 'tenants', foreignKey: 'unit_id' },
    { parent: 'owners', child: 'units', foreignKey: 'owner_id' },
    { parent: 'categories', child: 'subcategories', foreignKey: 'category_id' },
    { parent: 'categories', child: 'services', foreignKey: 'category_id' },
  ];

  // Find actual relationships in captured requests
  for (const pattern of relationshipPatterns) {
    const parentEndpoint = endpoints.find(e => e.url.includes(`/${pattern.parent}`));
    const childEndpoint = endpoints.find(e =>
      e.url.includes(`/${pattern.child}`) &&
      (e.url.includes(pattern.foreignKey) ||
       (e.responseBody && JSON.stringify(e.responseBody).includes(pattern.foreignKey)))
    );

    if (parentEndpoint && childEndpoint) {
      relationships.push({
        parentEndpoint: parentEndpoint.url,
        parentField: 'id',
        childEndpoint: childEndpoint.url,
        relationship: `${pattern.foreignKey} from ${pattern.parent} filters ${pattern.child}`,
      });
    }
  }

  // Look for query parameter relationships
  for (const req of endpoints) {
    const url = new URL(req.url);
    const params = Object.fromEntries(url.searchParams.entries());

    for (const [key, value] of Object.entries(params)) {
      if (key.endsWith('_id') && key !== 'tenant_id' && value) {
        const entityName = key.replace('_id', '');
        const parentEndpoint = endpoints.find(e => e.url.includes(`/${entityName}`));

        if (parentEndpoint && !relationships.find(r => r.childEndpoint === req.url && r.parentField === key)) {
          relationships.push({
            parentEndpoint: parentEndpoint?.url || `/${entityName}s`,
            parentField: key,
            childEndpoint: req.url,
            relationship: `${key} parameter filters this endpoint`,
          });
        }
      }
    }
  }

  return relationships;
}

/**
 * Extract enum-like values from API responses
 */
export function extractEnumsFromResponses(apiRequests: CapturedRequest[]): ExtractedEnum[] {
  const enums: ExtractedEnum[] = [];
  const seenEnums = new Set<string>();

  for (const req of apiRequests) {
    if (!req.responseBody || typeof req.responseBody !== 'object') continue;

    const response = req.responseBody as any;
    const data = response.data || response;

    // Look for status/type fields in response items
    if (Array.isArray(data)) {
      const statusValues = new Map<string, Set<string>>();

      for (const item of data) {
        if (typeof item !== 'object') continue;

        // Check common enum fields
        const enumFields = ['status', 'type', 'state', 'category', 'role', 'gender', 'priority'];
        for (const field of enumFields) {
          if (item[field] !== undefined && item[field] !== null) {
            if (!statusValues.has(field)) {
              statusValues.set(field, new Set());
            }
            statusValues.get(field)!.add(String(item[field]));
          }
        }
      }

      // Convert to enums
      for (const [field, values] of statusValues.entries()) {
        const enumKey = `${extractEntityName(req.url)}_${field}`;
        if (seenEnums.has(enumKey) || values.size < 2 || values.size > 20) continue;
        seenEnums.add(enumKey);

        enums.push({
          name: enumKey,
          source: 'api',
          values: Array.from(values).map(v => ({ key: v, value: v })),
        });
      }
    }

    // Look for dropdown-like arrays (id + name pattern)
    if (Array.isArray(data) && data.length > 0 && data[0]?.id && (data[0]?.name || data[0]?.title)) {
      const entityName = extractEntityName(req.url);
      if (!seenEnums.has(entityName)) {
        seenEnums.add(entityName);
        enums.push({
          name: entityName,
          source: 'api',
          values: data.slice(0, 50).map((item: any) => ({
            key: String(item.id),
            value: String(item.id),
            label: item.name || item.title || item.label,
          })),
        });
      }
    }
  }

  return enums;
}

/**
 * Extract entity name from URL
 */
function extractEntityName(url: string): string {
  try {
    const urlObj = new URL(url);
    const parts = urlObj.pathname.split('/').filter(Boolean);
    // Find the entity name (usually after api-management or rf)
    for (let i = parts.length - 1; i >= 0; i--) {
      const part = parts[i];
      if (!part.match(/^\d+$/) && !['api-management', 'rf', 'api'].includes(part)) {
        return part.replace(/-/g, '_');
      }
    }
    return 'unknown';
  } catch {
    return 'unknown';
  }
}

/**
 * Extract translations/labels from page
 */
export async function extractTranslations(page: Page): Promise<Record<string, string>> {
  const translations: Record<string, string> = {};

  try {
    // Get translations from i18n if available
    const i18nData = await page.evaluate(() => {
      // Check common i18n storage locations
      const sources = [
        (window as any).i18n?.store?.data,
        (window as any).__i18n__,
        (window as any).translations,
        localStorage.getItem('i18nextLng') ? JSON.parse(localStorage.getItem('translations') || '{}') : null,
      ];

      for (const source of sources) {
        if (source && typeof source === 'object') {
          return source;
        }
      }
      return null;
    });

    if (i18nData) {
      flattenObject(i18nData, '', translations);
    }

    // Also try to get translations from network requests
    // (already captured in API responses as translation.json)
  } catch (e) {
    console.log(`Could not extract translations: ${e}`);
  }

  return translations;
}

/**
 * Flatten nested object to dot notation
 */
function flattenObject(obj: any, prefix: string, result: Record<string, string>): void {
  for (const [key, value] of Object.entries(obj)) {
    const newKey = prefix ? `${prefix}.${key}` : key;
    if (typeof value === 'string') {
      result[newKey] = value;
    } else if (typeof value === 'object' && value !== null) {
      flattenObject(value, newKey, result);
    }
  }
}

/**
 * Find foreign key patterns in API responses
 */
export function findForeignKeys(apiRequests: CapturedRequest[]): Array<{ field: string; referencedEntity: string; foundIn: string[] }> {
  const foreignKeys = new Map<string, { referencedEntity: string; foundIn: Set<string> }>();

  for (const req of apiRequests) {
    if (!req.responseBody || typeof req.responseBody !== 'object') continue;

    const response = req.responseBody as any;
    const data = response.data || response;
    const items = Array.isArray(data) ? data : (data?.list || [data]);

    for (const item of items) {
      if (typeof item !== 'object') continue;

      for (const [key, value] of Object.entries(item)) {
        // Look for _id fields that reference other entities
        if (key.endsWith('_id') && typeof value === 'number') {
          const entityName = key.replace('_id', '');
          if (!foreignKeys.has(key)) {
            foreignKeys.set(key, { referencedEntity: entityName, foundIn: new Set() });
          }
          foreignKeys.get(key)!.foundIn.add(extractEntityName(req.url));
        }
      }
    }
  }

  return Array.from(foreignKeys.entries()).map(([field, data]) => ({
    field,
    referencedEntity: data.referencedEntity,
    foundIn: Array.from(data.foundIn),
  }));
}

/**
 * Main function to analyze all captured data
 */
export async function analyzePageData(
  page: Page,
  apiRequests: CapturedRequest[]
): Promise<DataAnalysis> {
  console.log('Analyzing captured data...');

  const apiRelationships = analyzeApiRelationships(apiRequests);
  const enums = extractEnumsFromResponses(apiRequests);
  const foreignKeys = findForeignKeys(apiRequests);
  const translationKeys = await extractTranslations(page);

  console.log(`Found ${apiRelationships.length} API relationships`);
  console.log(`Found ${enums.length} enum-like structures`);
  console.log(`Found ${foreignKeys.length} foreign key patterns`);
  console.log(`Found ${Object.keys(translationKeys).length} translation keys`);

  return {
    apiRelationships,
    enums,
    foreignKeys,
    translationKeys,
  };
}
