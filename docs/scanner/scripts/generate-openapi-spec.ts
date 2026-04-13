/**
 * OpenAPI Spec Generator
 *
 * Generates an OpenAPI 3.0 specification from captured API data:
 * - Query schemas (GET responses)
 * - Mutation validations (POST/PUT/DELETE request bodies)
 * - API endpoints from React bundle analysis
 */

import * as fs from 'fs/promises';
import * as path from 'path';

const QUERIES_DIR = path.join(process.cwd(), 'src', 'api', 'queries');
const VALIDATIONS_DIR = path.join(process.cwd(), 'src', 'api', 'validations');
const SIGNALS_FILE = path.join(process.cwd(), 'pretty-js.split', 'signals.json');
const OUTPUT_FILE = path.join(process.cwd(), 'src', 'api', 'openapi.yaml');
const OUTPUT_JSON = path.join(process.cwd(), 'src', 'api', 'openapi.json');

interface FieldSchema {
  type: string;
  nullable: boolean;
  example?: unknown;
  items?: FieldSchema;
  properties?: Record<string, FieldSchema>;
}

interface ResponseSchema {
  type: string;
  properties?: Record<string, FieldSchema>;
  items?: FieldSchema;
  meta?: {
    hasPagination: boolean;
  };
}

interface QueryCapture {
  endpoint: string;
  method: string;
  params?: Record<string, unknown>;
  schema: ResponseSchema;
  sample?: unknown;
}

interface ValidationRule {
  rule: string;
  rawMessage: string;
  constraints?: Record<string, unknown>;
}

interface FieldValidation {
  field: string;
  required: boolean;
  type: string;
  rules: ValidationRule[];
}

interface EndpointValidation {
  endpoint: string;
  method: string;
  fields: Record<string, FieldValidation>;
  requiredFields: string[];
  optionalFields: string[];
}

interface OpenAPISpec {
  openapi: string;
  info: {
    title: string;
    description: string;
    version: string;
    contact?: { name?: string; url?: string; email?: string };
  };
  servers: Array<{ url: string; description: string }>;
  tags: Array<{ name: string; description: string }>;
  paths: Record<string, Record<string, unknown>>;
  components: {
    schemas: Record<string, unknown>;
    securitySchemes: Record<string, unknown>;
  };
  security: Array<Record<string, string[]>>;
}

// Convert field schema to OpenAPI schema
function fieldToOpenAPI(field: FieldSchema): Record<string, unknown> {
  const schema: Record<string, unknown> = {};

  switch (field.type) {
    case 'string':
      schema.type = 'string';
      break;
    case 'number':
      schema.type = 'number';
      break;
    case 'boolean':
      schema.type = 'boolean';
      break;
    case 'array':
      schema.type = 'array';
      if (field.items) {
        schema.items = fieldToOpenAPI(field.items);
      } else {
        schema.items = { type: 'object' };
      }
      break;
    case 'object':
      schema.type = 'object';
      if (field.properties) {
        schema.properties = {};
        for (const [name, prop] of Object.entries(field.properties)) {
          (schema.properties as Record<string, unknown>)[name] = fieldToOpenAPI(prop);
        }
      }
      break;
    case 'null':
      schema.type = 'string';
      schema.nullable = true;
      break;
    default:
      schema.type = 'object';
  }

  if (field.nullable) {
    schema.nullable = true;
  }

  if (field.example !== undefined && field.example !== null) {
    schema.example = field.example;
  }

  return schema;
}

// Convert validation to OpenAPI request body schema
function validationToOpenAPI(validation: EndpointValidation): Record<string, unknown> {
  const properties: Record<string, unknown> = {};
  const required: string[] = [];

  for (const [fieldName, field] of Object.entries(validation.fields)) {
    // Handle nested fields like "tenant.national_id"
    const parts = fieldName.split('.');
    if (parts.length > 1) {
      // Skip nested fields for now - they're handled differently
      continue;
    }

    const prop: Record<string, unknown> = {
      type: field.type === 'string' ? 'string' :
            field.type === 'number' ? 'number' :
            field.type === 'boolean' ? 'boolean' : 'string'
    };

    // Add constraints from rules
    for (const rule of field.rules) {
      if (rule.rule === 'email') {
        prop.format = 'email';
      } else if (rule.rule === 'date') {
        prop.format = 'date';
      } else if (rule.rule === 'minLength' && rule.constraints?.min) {
        prop.minLength = rule.constraints.min;
      } else if (rule.rule === 'maxLength' && rule.constraints?.max) {
        prop.maxLength = rule.constraints.max;
      }
    }

    properties[fieldName] = prop;

    if (field.required) {
      required.push(fieldName);
    }
  }

  return {
    type: 'object',
    properties,
    required: required.length > 0 ? required : undefined
  };
}

// Normalize endpoint path for OpenAPI (convert rf/communities/15 to rf/communities/{id})
function normalizeEndpoint(endpoint: string): string {
  return endpoint
    .replace(/\/(\d+)(?=\/|$)/g, '/{id}')
    .replace(/\$\{[^}]+\}/g, '{id}');
}

// Get tag from endpoint
function getTag(endpoint: string): string {
  const parts = endpoint.split('/');
  if (parts[0] === 'rf') {
    return parts[1] || 'general';
  }
  if (parts[0] === 'marketplace') {
    return 'marketplace';
  }
  return parts[0] || 'general';
}

// Read all query schemas
async function readQuerySchemas(): Promise<QueryCapture[]> {
  const captures: QueryCapture[] = [];
  const modules = await fs.readdir(QUERIES_DIR);

  for (const module of modules) {
    const moduleDir = path.join(QUERIES_DIR, module);
    const stat = await fs.stat(moduleDir);
    if (!stat.isDirectory()) continue;

    const files = await fs.readdir(moduleDir);
    for (const file of files) {
      if (file.endsWith('.schema.json')) {
        try {
          const content = await fs.readFile(path.join(moduleDir, file), 'utf-8');
          const schema = JSON.parse(content) as QueryCapture;
          captures.push(schema);
        } catch (e) {
          console.warn(`Error reading ${file}:`, e);
        }
      }
    }
  }

  return captures;
}

// Read all validation schemas
async function readValidationSchemas(): Promise<EndpointValidation[]> {
  const validations: EndpointValidation[] = [];

  try {
    const modules = await fs.readdir(VALIDATIONS_DIR);

    for (const module of modules) {
      const moduleDir = path.join(VALIDATIONS_DIR, module);
      const stat = await fs.stat(moduleDir);
      if (!stat.isDirectory()) continue;

      const files = await fs.readdir(moduleDir);
      for (const file of files) {
        if (file.endsWith('.validation.json')) {
          try {
            const content = await fs.readFile(path.join(moduleDir, file), 'utf-8');
            const validation = JSON.parse(content) as EndpointValidation;
            validations.push(validation);
          } catch (e) {
            console.warn(`Error reading ${file}:`, e);
          }
        }
      }
    }
  } catch (e) {
    console.warn('Validations directory not found');
  }

  return validations;
}

// Read signals for additional endpoints
async function readSignals(): Promise<{ routes: string[]; endpoints: string[] }> {
  try {
    const content = await fs.readFile(SIGNALS_FILE, 'utf-8');
    const signals = JSON.parse(content);

    const routes = signals.result?.routes?.map((r: { key: string }) => r.key) || [];
    const endpoints = signals.result?.endpoints?.map((e: { key: string }) =>
      e.key.replace('/api-management/', '')
    ) || [];

    return { routes, endpoints };
  } catch (e) {
    console.warn('Signals file not found');
    return { routes: [], endpoints: [] };
  }
}

// Generate the OpenAPI spec
async function generateOpenAPISpec(): Promise<OpenAPISpec> {
  console.log('Reading captured data...');

  const querySchemas = await readQuerySchemas();
  const validationSchemas = await readValidationSchemas();
  const signals = await readSignals();

  console.log(`  Query schemas: ${querySchemas.length}`);
  console.log(`  Validation schemas: ${validationSchemas.length}`);
  console.log(`  Signal endpoints: ${signals.endpoints.length}`);

  // Initialize spec
  const spec: OpenAPISpec = {
    openapi: '3.0.3',
    info: {
      title: 'Atar Property Management API',
      description: `
# Atar Property Management Platform API

This OpenAPI specification was auto-generated from captured API responses and validation rules.

## Base URL
- Production: \`https://api.goatar.com/api-management\`

## Authentication
All endpoints require:
- \`Authorization: Bearer {token}\` header
- \`X-Tenant: {tenant_id}\` header

## Modules
- **Properties**: Communities, Buildings, Units, Facilities
- **Contacts**: Owners, Tenants, Admins, Professionals
- **Leasing**: Leases, Sub-Leases, Contracts
- **Transactions**: Payments, Invoices
- **Requests**: Service Requests, Visitor Access, Facility Bookings
- **Marketplace**: Listings, Visits, Sales
- **Settings**: Announcements, Configurations
`,
      version: '1.0.0',
      contact: {
        name: 'API Documentation',
        url: 'https://goatar.com'
      }
    },
    servers: [
      {
        url: 'https://api.goatar.com/api-management',
        description: 'Production API'
      }
    ],
    tags: [
      { name: 'communities', description: 'Community/Property management' },
      { name: 'buildings', description: 'Building management' },
      { name: 'units', description: 'Unit/Apartment management' },
      { name: 'facilities', description: 'Facility management' },
      { name: 'owners', description: 'Property owner contacts' },
      { name: 'tenants', description: 'Tenant contacts' },
      { name: 'admins', description: 'Admin user management' },
      { name: 'professionals', description: 'Service professional contacts' },
      { name: 'leases', description: 'Lease/Contract management' },
      { name: 'transactions', description: 'Financial transactions' },
      { name: 'requests', description: 'Service requests' },
      { name: 'marketplace', description: 'Marketplace listings and sales' },
      { name: 'announcements', description: 'Announcements and notifications' },
      { name: 'general', description: 'General/Common endpoints' }
    ],
    paths: {},
    components: {
      schemas: {
        PaginationMeta: {
          type: 'object',
          properties: {
            current_page: { type: 'integer', example: 1 },
            last_page: { type: 'integer', example: 10 },
            per_page: { type: 'integer', example: 10 },
            total: { type: 'integer', example: 100 }
          }
        },
        ErrorResponse: {
          type: 'object',
          properties: {
            message: { type: 'string' },
            errors: {
              type: 'object',
              additionalProperties: {
                type: 'array',
                items: { type: 'string' }
              }
            }
          }
        },
        City: {
          type: 'object',
          properties: {
            id: { type: 'integer' },
            name: { type: 'string' }
          }
        },
        District: {
          type: 'object',
          properties: {
            id: { type: 'integer' },
            name: { type: 'string' }
          }
        },
        Country: {
          type: 'object',
          properties: {
            id: { type: 'integer' },
            name: { type: 'string' },
            code: { type: 'string' }
          }
        },
        Currency: {
          type: 'object',
          properties: {
            id: { type: 'integer' },
            name: { type: 'string' },
            code: { type: 'string' }
          }
        },
        Status: {
          type: 'object',
          properties: {
            id: { type: 'integer' },
            name: { type: 'string' }
          }
        },
        Media: {
          type: 'object',
          properties: {
            id: { type: 'integer' },
            url: { type: 'string', format: 'uri' },
            name: { type: 'string' },
            notes: { type: 'string' }
          }
        }
      },
      securitySchemes: {
        bearerAuth: {
          type: 'http',
          scheme: 'bearer',
          bearerFormat: 'JWT',
          description: 'JWT access token'
        },
        tenantHeader: {
          type: 'apiKey',
          in: 'header',
          name: 'X-Tenant',
          description: 'Tenant identifier'
        }
      }
    },
    security: [
      { bearerAuth: [], tenantHeader: [] }
    ]
  };

  // Process query schemas into paths
  const processedEndpoints = new Set<string>();

  for (const query of querySchemas) {
    const normalizedPath = '/' + normalizeEndpoint(query.endpoint);
    const pathKey = normalizedPath;
    const method = query.method.toLowerCase();

    if (processedEndpoints.has(`${method}:${pathKey}`)) continue;
    processedEndpoints.add(`${method}:${pathKey}`);

    if (!spec.paths[pathKey]) {
      spec.paths[pathKey] = {};
    }

    const tag = getTag(query.endpoint);
    const operation: Record<string, unknown> = {
      tags: [tag],
      summary: `Get ${query.endpoint}`,
      operationId: `get_${query.endpoint.replace(/[\/\-{}]/g, '_')}`,
      parameters: []
    };

    // Add path parameters
    const pathParams = pathKey.match(/\{(\w+)\}/g);
    if (pathParams) {
      for (const param of pathParams) {
        const paramName = param.replace(/[{}]/g, '');
        (operation.parameters as unknown[]).push({
          name: paramName,
          in: 'path',
          required: true,
          schema: { type: 'integer' },
          description: `${tag} ID`
        });
      }
    }

    // Add pagination parameters for list endpoints
    if (query.schema?.meta?.hasPagination) {
      (operation.parameters as unknown[]).push(
        { name: 'page', in: 'query', schema: { type: 'integer', default: 1 } },
        { name: 'per_page', in: 'query', schema: { type: 'integer', default: 10 } }
      );
    }

    // Add query parameters if present
    if (query.params) {
      for (const [key, value] of Object.entries(query.params)) {
        if (key !== 'page' && key !== 'per_page') {
          (operation.parameters as unknown[]).push({
            name: key,
            in: 'query',
            schema: { type: typeof value === 'number' ? 'integer' : 'string' }
          });
        }
      }
    }

    // Generate response schema
    const responseSchema: Record<string, unknown> = {
      type: 'object',
      properties: {}
    };

    if (query.schema?.properties?.data) {
      const dataField = query.schema.properties.data;
      (responseSchema.properties as Record<string, unknown>).data = fieldToOpenAPI(dataField);
    }

    if (query.schema?.meta?.hasPagination) {
      (responseSchema.properties as Record<string, unknown>).meta = {
        $ref: '#/components/schemas/PaginationMeta'
      };
    }

    operation.responses = {
      '200': {
        description: 'Successful response',
        content: {
          'application/json': {
            schema: responseSchema
          }
        }
      },
      '401': {
        description: 'Unauthorized',
        content: {
          'application/json': {
            schema: { $ref: '#/components/schemas/ErrorResponse' }
          }
        }
      },
      '404': {
        description: 'Not found',
        content: {
          'application/json': {
            schema: { $ref: '#/components/schemas/ErrorResponse' }
          }
        }
      }
    };

    (spec.paths[pathKey] as Record<string, unknown>)[method] = operation;
  }

  // Process validation schemas into POST/PUT paths
  for (const validation of validationSchemas) {
    const normalizedPath = '/' + normalizeEndpoint(validation.endpoint);
    const pathKey = normalizedPath;
    const method = validation.method.toLowerCase();

    if (processedEndpoints.has(`${method}:${pathKey}`)) continue;
    processedEndpoints.add(`${method}:${pathKey}`);

    if (!spec.paths[pathKey]) {
      spec.paths[pathKey] = {};
    }

    const tag = getTag(validation.endpoint);
    const operation: Record<string, unknown> = {
      tags: [tag],
      summary: `${validation.method} ${validation.endpoint}`,
      operationId: `${method}_${validation.endpoint.replace(/[\/\-{}]/g, '_')}`,
      parameters: []
    };

    // Add path parameters
    const pathParams = pathKey.match(/\{(\w+)\}/g);
    if (pathParams) {
      for (const param of pathParams) {
        const paramName = param.replace(/[{}]/g, '');
        (operation.parameters as unknown[]).push({
          name: paramName,
          in: 'path',
          required: true,
          schema: { type: 'integer' },
          description: `${tag} ID`
        });
      }
    }

    // Add request body
    if (method === 'post' || method === 'put') {
      operation.requestBody = {
        required: true,
        content: {
          'application/json': {
            schema: validationToOpenAPI(validation)
          }
        }
      };
    }

    operation.responses = {
      '200': {
        description: 'Successful response',
        content: {
          'application/json': {
            schema: {
              type: 'object',
              properties: {
                data: { type: 'object' },
                message: { type: 'string' }
              }
            }
          }
        }
      },
      '422': {
        description: 'Validation error',
        content: {
          'application/json': {
            schema: { $ref: '#/components/schemas/ErrorResponse' }
          }
        }
      }
    };

    (spec.paths[pathKey] as Record<string, unknown>)[method] = operation;
  }

  return spec;
}

// Convert spec to YAML format
function toYAML(obj: unknown, indent: number = 0): string {
  const spaces = '  '.repeat(indent);

  if (obj === null || obj === undefined) {
    return 'null';
  }

  if (typeof obj === 'string') {
    if (obj.includes('\n') || obj.includes(':') || obj.includes('#')) {
      return `|\n${obj.split('\n').map(line => spaces + '  ' + line).join('\n')}`;
    }
    return `"${obj.replace(/"/g, '\\"')}"`;
  }

  if (typeof obj === 'number' || typeof obj === 'boolean') {
    return String(obj);
  }

  if (Array.isArray(obj)) {
    if (obj.length === 0) return '[]';
    return '\n' + obj.map(item => {
      const itemStr = toYAML(item, indent + 1);
      if (typeof item === 'object' && item !== null) {
        return `${spaces}- ${itemStr.trim().replace(/^\n/, '')}`;
      }
      return `${spaces}- ${itemStr}`;
    }).join('\n');
  }

  if (typeof obj === 'object') {
    const entries = Object.entries(obj as Record<string, unknown>);
    if (entries.length === 0) return '{}';
    return '\n' + entries.map(([key, value]) => {
      const valueStr = toYAML(value, indent + 1);
      if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
        return `${spaces}${key}:${valueStr}`;
      }
      return `${spaces}${key}: ${valueStr.trim()}`;
    }).join('\n');
  }

  return String(obj);
}

// Main function
async function main() {
  console.log('Generating OpenAPI Specification...\n');

  const spec = await generateOpenAPISpec();

  // Write JSON
  await fs.writeFile(OUTPUT_JSON, JSON.stringify(spec, null, 2));
  console.log(`\nGenerated: ${OUTPUT_JSON}`);

  // Write YAML
  const yaml = `# Atar Property Management API - OpenAPI 3.0 Specification
# Auto-generated from captured API data
# Generated: ${new Date().toISOString()}

${toYAML(spec).trim()}
`;
  await fs.writeFile(OUTPUT_FILE, yaml);
  console.log(`Generated: ${OUTPUT_FILE}`);

  // Summary
  const pathCount = Object.keys(spec.paths).length;
  let operationCount = 0;
  for (const path of Object.values(spec.paths)) {
    operationCount += Object.keys(path as object).length;
  }

  console.log(`\nSummary:`);
  console.log(`  Paths: ${pathCount}`);
  console.log(`  Operations: ${operationCount}`);
  console.log(`  Tags: ${spec.tags.length}`);
  console.log(`  Component Schemas: ${Object.keys(spec.components.schemas).length}`);
}

main().catch(console.error);
