/**
 * Query Output Writer
 *
 * Writes GET response captures to organized output files
 */

import * as fs from 'fs/promises';
import * as path from 'path';
import { QueryCapture, ModuleSummary, EndpointSummary, FieldSchema } from './query-types';

const OUTPUT_DIR = path.join(process.cwd(), 'src', 'api', 'queries');

// Ensure output directory exists
async function ensureDir(dir: string): Promise<void> {
  try {
    await fs.mkdir(dir, { recursive: true });
  } catch (error) {
    // Directory already exists
  }
}

// Extract field names from a schema
function extractFieldNames(schema: FieldSchema | undefined): string[] {
  if (!schema) return [];

  if (schema.type === 'object' && schema.properties) {
    return Object.keys(schema.properties);
  }

  if (schema.type === 'array' && schema.items?.type === 'object' && schema.items.properties) {
    return Object.keys(schema.items.properties);
  }

  return [];
}

// Determine response type
function getResponseType(capture: QueryCapture): 'list' | 'detail' | 'other' {
  if (!capture.schema) return 'other';

  if (capture.schema.type === 'array') return 'list';

  if (capture.schema.properties?.data) {
    const dataField = capture.schema.properties.data;
    if (dataField.type === 'array') return 'list';
    if (dataField.type === 'object') return 'detail';
  }

  return 'other';
}

// Create endpoint summary
function createEndpointSummary(capture: QueryCapture): EndpointSummary {
  const responseType = getResponseType(capture);
  const hasPagination = capture.schema?.meta?.hasPagination || false;

  // Get fields from data object
  let fields: string[] = [];
  if (capture.schema?.properties?.data) {
    fields = extractFieldNames(capture.schema.properties.data);
  } else if (capture.schema?.items) {
    fields = extractFieldNames(capture.schema.items);
  } else if (capture.schema?.properties) {
    fields = Object.keys(capture.schema.properties);
  }

  // Get a sample item for reference
  let sampleResponse: unknown;
  const body = capture.response.body as Record<string, unknown>;
  if (body?.data) {
    if (Array.isArray(body.data) && body.data.length > 0) {
      sampleResponse = body.data[0];
    } else if (typeof body.data === 'object') {
      sampleResponse = body.data;
    }
  }

  return {
    endpoint: capture.request.endpoint,
    method: 'GET',
    responseType,
    hasPagination,
    fieldCount: fields.length,
    fields,
    sampleResponse,
  };
}

// Write query captures to files
export async function writeQueryCaptures(moduleName: string, captures: QueryCapture[]): Promise<void> {
  const moduleDir = path.join(OUTPUT_DIR, moduleName);
  await ensureDir(moduleDir);

  // Filter to only successful captures
  const successfulCaptures = captures.filter(c => c.success);

  // Write raw captures
  await fs.writeFile(
    path.join(moduleDir, 'captures.json'),
    JSON.stringify(captures, null, 2)
  );

  // Create and write endpoint summaries
  const summaries: EndpointSummary[] = successfulCaptures.map(createEndpointSummary);

  const moduleSummary: ModuleSummary = {
    module: moduleName,
    endpointCount: summaries.length,
    endpoints: summaries,
    capturedAt: new Date().toISOString(),
  };

  await fs.writeFile(
    path.join(moduleDir, 'summary.json'),
    JSON.stringify(moduleSummary, null, 2)
  );

  // Write individual schemas for each endpoint
  for (const capture of successfulCaptures) {
    if (capture.schema) {
      const endpointName = capture.request.endpoint
        .replace(/\//g, '-')
        .replace(/^-/, '')
        .replace(/-$/, '');

      await fs.writeFile(
        path.join(moduleDir, `${endpointName}.schema.json`),
        JSON.stringify({
          endpoint: capture.request.endpoint,
          method: 'GET',
          params: capture.request.params,
          schema: capture.schema,
          sample: (capture.response.body as Record<string, unknown>)?.data,
        }, null, 2)
      );
    }
  }

  console.log(`\nWrote ${captures.length} captures to ${moduleDir}`);
  console.log(`  - captures.json`);
  console.log(`  - summary.json`);
  console.log(`  - ${successfulCaptures.length} individual schema files`);
}
