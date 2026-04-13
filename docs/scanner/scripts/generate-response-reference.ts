/**
 * Generate Response Schema Reference
 *
 * Processes all query captures and generates a unified reference document
 * for GET endpoint response schemas.
 */

import * as fs from 'fs/promises';
import * as path from 'path';

const QUERIES_DIR = path.join(process.cwd(), 'src', 'api', 'queries');
const OUTPUT_FILE = path.join(QUERIES_DIR, 'RESPONSE-REFERENCE.md');

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
    paginationFields?: string[];
  };
}

interface EndpointSummary {
  endpoint: string;
  method: string;
  responseType: string;
  hasPagination: boolean;
  fieldCount: number;
  fields: string[];
  sampleResponse?: unknown;
}

interface ModuleSummary {
  module: string;
  endpointCount: number;
  endpoints: EndpointSummary[];
  capturedAt: string;
}

interface SchemaFile {
  endpoint: string;
  method: string;
  params?: Record<string, unknown>;
  schema: ResponseSchema;
  sample?: unknown;
}

// Get all schema files for a module
async function getModuleSchemas(moduleDir: string): Promise<SchemaFile[]> {
  const files = await fs.readdir(moduleDir);
  const schemas: SchemaFile[] = [];

  for (const file of files) {
    if (file.endsWith('.schema.json')) {
      const content = await fs.readFile(path.join(moduleDir, file), 'utf-8');
      try {
        const schema = JSON.parse(content) as SchemaFile;
        schemas.push(schema);
      } catch (e) {
        console.error(`Error parsing ${file}:`, e);
      }
    }
  }

  return schemas;
}

// Format field type for markdown
function formatFieldType(field: FieldSchema): string {
  if (field.type === 'array' && field.items) {
    return `array<${formatFieldType(field.items)}>`;
  }
  if (field.type === 'object' && field.properties) {
    return 'object';
  }
  return field.nullable ? `${field.type}?` : field.type;
}

// Extract fields from schema
function extractFields(schema: ResponseSchema): Record<string, string> {
  const fields: Record<string, string> = {};

  // Get data field if present
  if (schema.properties?.data) {
    const dataField = schema.properties.data;

    if (dataField.type === 'array' && dataField.items?.properties) {
      // List response - get item fields
      for (const [name, field] of Object.entries(dataField.items.properties)) {
        fields[name] = formatFieldType(field);
      }
    } else if (dataField.type === 'object' && dataField.properties) {
      // Detail response - get fields directly
      for (const [name, field] of Object.entries(dataField.properties)) {
        fields[name] = formatFieldType(field);
      }
    }
  } else if (schema.type === 'array' && schema.items?.properties) {
    // Direct array response
    for (const [name, field] of Object.entries(schema.items.properties)) {
      fields[name] = formatFieldType(field);
    }
  } else if (schema.properties) {
    // Direct object response
    for (const [name, field] of Object.entries(schema.properties)) {
      fields[name] = formatFieldType(field);
    }
  }

  return fields;
}

// Generate markdown for a module
function generateModuleMarkdown(module: string, summary: ModuleSummary, schemas: SchemaFile[]): string {
  const lines: string[] = [];

  lines.push(`## ${module}`);
  lines.push('');

  // Group endpoints by base path
  const schemasByEndpoint = new Map<string, SchemaFile>();
  for (const schema of schemas) {
    schemasByEndpoint.set(schema.endpoint, schema);
  }

  for (const endpoint of summary.endpoints) {
    const schema = schemasByEndpoint.get(endpoint.endpoint);

    lines.push(`### GET \`${endpoint.endpoint}\``);
    lines.push('');

    // Response type info
    const typeInfo: string[] = [];
    typeInfo.push(`**Type:** ${endpoint.responseType}`);
    if (endpoint.hasPagination) {
      typeInfo.push('**Paginated:** Yes');
    }
    lines.push(typeInfo.join(' | '));
    lines.push('');

    // Fields table
    if (schema && schema.schema) {
      const fields = extractFields(schema.schema);
      const fieldNames = Object.keys(fields);

      if (fieldNames.length > 0) {
        lines.push('**Response Fields:**');
        lines.push('');
        lines.push('| Field | Type |');
        lines.push('|-------|------|');

        // Show first 20 fields
        const displayFields = fieldNames.slice(0, 20);
        for (const name of displayFields) {
          lines.push(`| ${name} | \`${fields[name]}\` |`);
        }

        if (fieldNames.length > 20) {
          lines.push(`| ... | *${fieldNames.length - 20} more fields* |`);
        }
        lines.push('');
      }
    } else if (endpoint.fields.length > 0) {
      lines.push('**Fields:** ' + endpoint.fields.slice(0, 10).join(', '));
      if (endpoint.fields.length > 10) {
        lines.push(` *(+${endpoint.fields.length - 10} more)*`);
      }
      lines.push('');
    }

    lines.push('---');
    lines.push('');
  }

  return lines.join('\n');
}

// Main function
async function main() {
  console.log('Generating Response Schema Reference...\n');

  const modules = await fs.readdir(QUERIES_DIR);
  const allModules: { name: string; summary: ModuleSummary; schemas: SchemaFile[] }[] = [];

  let totalEndpoints = 0;
  let totalFields = 0;

  for (const module of modules) {
    const moduleDir = path.join(QUERIES_DIR, module);
    const stat = await fs.stat(moduleDir);

    if (!stat.isDirectory()) continue;

    const summaryPath = path.join(moduleDir, 'summary.json');
    try {
      const content = await fs.readFile(summaryPath, 'utf-8');
      const summary = JSON.parse(content) as ModuleSummary;
      const schemas = await getModuleSchemas(moduleDir);

      allModules.push({ name: module, summary, schemas });
      totalEndpoints += summary.endpointCount;

      for (const endpoint of summary.endpoints) {
        totalFields += endpoint.fieldCount;
      }

      console.log(`  ${module}: ${summary.endpointCount} endpoints`);
    } catch (e) {
      console.log(`  ${module}: No summary found`);
    }
  }

  // Generate markdown
  const lines: string[] = [];

  lines.push('# Atar API Response Reference');
  lines.push('');
  lines.push('> Auto-generated from API query captures');
  lines.push('');
  lines.push(`Generated: ${new Date().toISOString()}`);
  lines.push('');
  lines.push('---');
  lines.push('');

  // Summary
  lines.push('## Summary');
  lines.push('');
  lines.push(`- **Total Endpoints:** ${totalEndpoints}`);
  lines.push(`- **Total Fields Documented:** ${totalFields}`);
  lines.push(`- **Modules:** ${allModules.length}`);
  lines.push('');

  // Table of contents
  lines.push('## Table of Contents');
  lines.push('');
  for (const { name, summary } of allModules) {
    lines.push(`- [${name}](#${name}) (${summary.endpointCount} endpoints)`);
  }
  lines.push('');
  lines.push('---');
  lines.push('');

  // Module sections
  for (const { name, summary, schemas } of allModules) {
    lines.push(generateModuleMarkdown(name, summary, schemas));
  }

  // Write output
  await fs.writeFile(OUTPUT_FILE, lines.join('\n'));

  console.log(`\nGenerated: ${OUTPUT_FILE}`);
  console.log(`  Total endpoints: ${totalEndpoints}`);
  console.log(`  Total fields: ${totalFields}`);
}

main().catch(console.error);
