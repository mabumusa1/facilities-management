/**
 * Mutation Output Writer
 *
 * Writes mutation captures to files in OpenAPI-compatible format
 */

import * as fs from 'fs/promises';
import * as path from 'path';
import {
  MutationCapture,
  MutationOutput,
  OpenApiSchema,
  OpenApiOperation,
  FieldRequirements,
  FieldRequirement,
} from './mutation-types';
import { ATAR_CONFIG } from './types';

const MUTATIONS_DIR = path.join(process.cwd(), 'src', 'api', 'mutations');

/**
 * Ensure directory exists
 */
async function ensureDir(dirPath: string): Promise<void> {
  try {
    await fs.mkdir(dirPath, { recursive: true });
  } catch (error) {
    // Directory already exists
  }
}

/**
 * Infer JSON Schema from a value
 */
function inferSchema(value: unknown): OpenApiSchema {
  if (value === null) {
    return { type: 'null' };
  }

  if (Array.isArray(value)) {
    return {
      type: 'array',
      items: value.length > 0 ? inferSchema(value[0]) : { type: 'object' },
    };
  }

  if (typeof value === 'object') {
    const properties: Record<string, OpenApiSchema> = {};
    for (const [key, val] of Object.entries(value as Record<string, unknown>)) {
      properties[key] = inferSchema(val);
    }
    return {
      type: 'object',
      properties,
    };
  }

  if (typeof value === 'number') {
    return Number.isInteger(value) ? { type: 'integer' } : { type: 'number' };
  }

  if (typeof value === 'boolean') {
    return { type: 'boolean' };
  }

  return { type: 'string' };
}

/**
 * Generate operation ID from method and endpoint
 */
function generateOperationId(method: string, endpoint: string): string {
  const cleanEndpoint = endpoint
    .replace(/^\//, '')
    .replace(/\//g, '_')
    .replace(/[{}]/g, '')
    .replace(/-/g, '_');
  return `${method.toLowerCase()}_${cleanEndpoint}`;
}

/**
 * Generate OpenAPI specification from captures
 */
function generateOpenApiSpec(module: string, captures: MutationCapture[]): MutationOutput {
  const paths: Record<string, Record<string, OpenApiOperation>> = {};

  // Group captures by endpoint and method
  const endpointMap = new Map<string, MutationCapture[]>();
  for (const capture of captures) {
    const key = `${capture.request.method}:${capture.request.endpoint}`;
    if (!endpointMap.has(key)) {
      endpointMap.set(key, []);
    }
    endpointMap.get(key)!.push(capture);
  }

  // Process each endpoint
  for (const [key, endpointCaptures] of endpointMap) {
    const [method, endpoint] = key.split(':');
    const successCapture = endpointCaptures.find((c) => c.success);
    const errorCapture = endpointCaptures.find((c) => !c.success);

    if (!paths[endpoint]) {
      paths[endpoint] = {};
    }

    const operation: OpenApiOperation = {
      operationId: generateOperationId(method, endpoint),
      summary: `${method} ${endpoint}`,
      tags: [module],
      responses: {},
    };

    // Add request body if present
    const captureWithBody = endpointCaptures.find((c) => c.request.body);
    if (captureWithBody?.request.body) {
      operation.requestBody = {
        required: true,
        content: {
          'application/json': {
            schema: inferSchema(captureWithBody.request.body),
            examples: {
              default: {
                value: captureWithBody.request.body,
                summary: 'Example request body',
              },
            },
          },
        },
      };
    }

    // Add success response
    if (successCapture) {
      operation.responses[successCapture.response.status.toString()] = {
        description: successCapture.response.statusText || 'Success',
        content: {
          'application/json': {
            schema: inferSchema(successCapture.response.body),
            examples: {
              success: {
                value: successCapture.response.body,
                summary: 'Successful response',
              },
            },
          },
        },
      };
    }

    // Add error response
    if (errorCapture) {
      operation.responses[errorCapture.response.status.toString()] = {
        description: errorCapture.response.statusText || 'Error',
        content: {
          'application/json': {
            schema: inferSchema(errorCapture.response.body),
            examples: {
              error: {
                value: errorCapture.response.body,
                summary: 'Error response with validation errors',
              },
            },
          },
        },
      };
    }

    // Add security
    operation.security = [{ bearerAuth: [], tenantHeader: [] }];

    paths[endpoint][method.toLowerCase()] = operation;
  }

  return {
    openapi: '3.0.3',
    info: {
      title: `Atar API - ${module} Module`,
      version: '1.0.0',
      description: `API documentation for the ${module} module of the Atar property management platform.`,
    },
    servers: [
      {
        url: ATAR_CONFIG.apiUrl,
        description: 'Production API',
      },
    ],
    paths,
    components: {
      schemas: {},
      securitySchemes: {
        bearerAuth: {
          type: 'http',
          scheme: 'bearer',
        },
        tenantHeader: {
          type: 'apiKey',
          in: 'header',
          name: 'X-Tenant',
        },
      },
    },
    security: [{ bearerAuth: [], tenantHeader: [] }],
  };
}

/**
 * Analyze field requirements from validation errors
 */
function analyzeFieldRequirements(captures: MutationCapture[]): FieldRequirements {
  const requirements: FieldRequirements = {};

  for (const capture of captures) {
    if (capture.validationErrors && capture.validationErrors.length > 0) {
      for (const error of capture.validationErrors) {
        if (!requirements[error.field]) {
          requirements[error.field] = {
            required: false,
            rules: [],
            messages: [],
          };
        }

        const req = requirements[error.field];
        if (error.rule === 'required') {
          req.required = true;
        }
        if (error.rule && !req.rules.includes(error.rule)) {
          req.rules.push(error.rule);
        }
        if (!req.messages.includes(error.message)) {
          req.messages.push(error.message);
        }
      }
    }
  }

  return requirements;
}

/**
 * Generate a summary report
 */
function generateSummary(module: string, captures: MutationCapture[]): Record<string, unknown> {
  const successCount = captures.filter((c) => c.success).length;
  const errorCount = captures.filter((c) => !c.success).length;

  const endpointStats: Record<
    string,
    {
      method: string;
      success: number;
      error: number;
      avgTiming: number;
    }
  > = {};

  for (const capture of captures) {
    const key = `${capture.request.method} ${capture.request.endpoint}`;
    if (!endpointStats[key]) {
      endpointStats[key] = {
        method: capture.request.method,
        success: 0,
        error: 0,
        avgTiming: 0,
      };
    }

    if (capture.success) {
      endpointStats[key].success++;
    } else {
      endpointStats[key].error++;
    }
    endpointStats[key].avgTiming =
      (endpointStats[key].avgTiming + capture.response.timing) /
      (endpointStats[key].success + endpointStats[key].error);
  }

  return {
    module,
    generatedAt: new Date().toISOString(),
    totalCaptures: captures.length,
    successCount,
    errorCount,
    successRate: `${((successCount / captures.length) * 100).toFixed(1)}%`,
    endpoints: endpointStats,
  };
}

/**
 * Write mutation captures to output files
 */
export async function writeMutationCaptures(
  module: string,
  captures: MutationCapture[]
): Promise<void> {
  if (captures.length === 0) {
    console.log(`No captures to write for module: ${module}`);
    return;
  }

  const moduleDir = path.join(MUTATIONS_DIR, module);
  await ensureDir(moduleDir);

  // Write raw captures
  const capturesPath = path.join(moduleDir, 'captures.json');
  await fs.writeFile(capturesPath, JSON.stringify(captures, null, 2));
  console.log(`Written: ${capturesPath} (${captures.length} captures)`);

  // Generate and write OpenAPI spec
  const openApiSpec = generateOpenApiSpec(module, captures);
  const openApiPath = path.join(moduleDir, 'openapi.json');
  await fs.writeFile(openApiPath, JSON.stringify(openApiSpec, null, 2));
  console.log(`Written: ${openApiPath}`);

  // Generate and write field requirements
  const fieldRequirements = analyzeFieldRequirements(captures);
  const fieldReqPath = path.join(moduleDir, 'field-requirements.json');
  await fs.writeFile(fieldReqPath, JSON.stringify(fieldRequirements, null, 2));
  console.log(`Written: ${fieldReqPath}`);

  // Generate and write summary
  const summary = generateSummary(module, captures);
  const summaryPath = path.join(moduleDir, 'summary.json');
  await fs.writeFile(summaryPath, JSON.stringify(summary, null, 2));
  console.log(`Written: ${summaryPath}`);
}

/**
 * Append captures to existing file (for incremental updates)
 */
export async function appendMutationCaptures(
  module: string,
  newCaptures: MutationCapture[]
): Promise<void> {
  const moduleDir = path.join(MUTATIONS_DIR, module);
  await ensureDir(moduleDir);

  const capturesPath = path.join(moduleDir, 'captures.json');

  let existingCaptures: MutationCapture[] = [];
  try {
    const existing = await fs.readFile(capturesPath, 'utf-8');
    existingCaptures = JSON.parse(existing);
  } catch {
    // File doesn't exist yet
  }

  const allCaptures = [...existingCaptures, ...newCaptures];
  await writeMutationCaptures(module, allCaptures);
}

/**
 * Read existing captures for a module
 */
export async function readMutationCaptures(module: string): Promise<MutationCapture[]> {
  const capturesPath = path.join(MUTATIONS_DIR, module, 'captures.json');
  try {
    const content = await fs.readFile(capturesPath, 'utf-8');
    return JSON.parse(content);
  } catch {
    return [];
  }
}
