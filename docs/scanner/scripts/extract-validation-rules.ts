#!/usr/bin/env npx ts-node
/**
 * Extract Validation Rules Script
 *
 * Processes captured API responses to extract validation rules
 * and generates JSON Schema + TypeScript interfaces.
 *
 * Usage: npx ts-node scripts/extract-validation-rules.ts
 */

import * as fs from 'fs/promises';
import * as path from 'path';
import {
  extractRulesFromErrors,
  buildEndpointValidation,
  EndpointValidation,
} from '../tests/utils/validation-rules-extractor';
import {
  generateJsonSchema,
  generateTypeScriptInterface,
  generateBarrelExport,
  generateMarkdownDoc,
} from '../tests/utils/validation-schema-generator';

const MUTATIONS_DIR = path.join(process.cwd(), 'src', 'api', 'mutations');
const OUTPUT_DIR = path.join(process.cwd(), 'src', 'api', 'validations');

interface CapturedRequest {
  method: string;
  endpoint: string;
  body?: unknown;
}

interface CapturedResponse {
  status: number;
  statusText: string;
  body?: {
    message?: string;
    errors?: Record<string, string[]>;
  };
}

interface MutationCapture {
  request: CapturedRequest;
  response: CapturedResponse;
  success: boolean;
  validationErrors?: Array<{
    field: string;
    message: string;
    rule?: string;
  }>;
}

/**
 * Read captures from a module directory
 */
async function readModuleCaptures(moduleDir: string): Promise<MutationCapture[]> {
  const capturesPath = path.join(moduleDir, 'captures.json');
  try {
    const content = await fs.readFile(capturesPath, 'utf-8');
    return JSON.parse(content);
  } catch {
    return [];
  }
}

/**
 * Filter for validation errors only (422 status)
 */
function filterValidationErrors(captures: MutationCapture[]): MutationCapture[] {
  return captures.filter((c) => {
    // Must be 422 or 400 with errors object
    const status = c.response.status;
    if (status !== 422 && status !== 400) return false;

    // Must have errors object
    const errors = c.response.body?.errors;
    if (!errors || typeof errors !== 'object') return false;

    // Filter out exception traces
    if (errors['\x00*\x00message']) return false;
    if (errors['\x00Exception\x00string']) return false;

    return true;
  });
}

/**
 * Group captures by endpoint and method
 */
function groupByEndpoint(
  captures: MutationCapture[]
): Map<string, MutationCapture[]> {
  const groups = new Map<string, MutationCapture[]>();

  for (const capture of captures) {
    // Normalize endpoint (remove IDs from path)
    const normalizedEndpoint = normalizeEndpoint(capture.request.endpoint);
    const key = `${capture.request.method}:${normalizedEndpoint}`;

    if (!groups.has(key)) {
      groups.set(key, []);
    }
    groups.get(key)!.push(capture);
  }

  return groups;
}

/**
 * Normalize endpoint by replacing numeric IDs with {id}
 */
function normalizeEndpoint(endpoint: string): string {
  return endpoint
    .replace(/\/\d+(?=\/|$)/g, '/{id}')
    .replace(/\/[a-f0-9-]{36}(?=\/|$)/gi, '/{uuid}'); // UUID pattern
}

/**
 * Extract validation from captures group
 */
function extractValidation(
  method: string,
  endpoint: string,
  captures: MutationCapture[]
): EndpointValidation {
  const errorCaptures: Array<{ errors: Record<string, string[]> }> = [];

  for (const capture of captures) {
    if (capture.response.body?.errors) {
      errorCaptures.push({ errors: capture.response.body.errors });
    }
  }

  return buildEndpointValidation(endpoint, method, errorCaptures);
}

/**
 * Ensure directory exists
 */
async function ensureDir(dirPath: string): Promise<void> {
  try {
    await fs.mkdir(dirPath, { recursive: true });
  } catch {
    // Directory exists
  }
}

/**
 * Write validation outputs for an endpoint
 */
async function writeValidationOutputs(
  moduleDir: string,
  validation: EndpointValidation
): Promise<void> {
  const fileName = `${validation.method}-${validation.endpoint.replace(/\//g, '-').replace(/[{}]/g, '')}`;

  // Write JSON Schema
  const schema = generateJsonSchema(validation);
  const schemaPath = path.join(moduleDir, `${fileName}.schema.json`);
  await fs.writeFile(schemaPath, JSON.stringify(schema, null, 2));
  console.log(`  Written: ${schemaPath}`);

  // Write TypeScript interface
  const tsInterface = generateTypeScriptInterface(validation);
  const tsPath = path.join(moduleDir, `${fileName}.types.ts`);
  await fs.writeFile(tsPath, tsInterface);
  console.log(`  Written: ${tsPath}`);

  // Write raw validation data
  const validationPath = path.join(moduleDir, `${fileName}.validation.json`);
  await fs.writeFile(validationPath, JSON.stringify(validation, null, 2));
  console.log(`  Written: ${validationPath}`);
}

/**
 * Process a single module
 */
async function processModule(
  moduleName: string
): Promise<EndpointValidation[]> {
  const moduleInputDir = path.join(MUTATIONS_DIR, moduleName);
  const moduleOutputDir = path.join(OUTPUT_DIR, moduleName);

  console.log(`\n=== Processing ${moduleName} ===`);

  // Read captures
  const captures = await readModuleCaptures(moduleInputDir);
  console.log(`  Found ${captures.length} total captures`);

  // Filter for validation errors
  const validationCaptures = filterValidationErrors(captures);
  console.log(`  Found ${validationCaptures.length} validation error captures`);

  if (validationCaptures.length === 0) {
    console.log(`  No validation errors to process`);
    return [];
  }

  // Group by endpoint
  const groups = groupByEndpoint(validationCaptures);
  console.log(`  Found ${groups.size} unique endpoints`);

  // Ensure output directory
  await ensureDir(moduleOutputDir);

  // Process each endpoint
  const validations: EndpointValidation[] = [];

  for (const [key, endpointCaptures] of groups) {
    const [method, endpoint] = key.split(':');
    console.log(`  Processing ${method} ${endpoint}...`);

    const validation = extractValidation(method, endpoint, endpointCaptures);
    validations.push(validation);

    // Write outputs
    await writeValidationOutputs(moduleOutputDir, validation);
  }

  return validations;
}

/**
 * Main function
 */
async function main(): Promise<void> {
  console.log('========================================');
  console.log('  Validation Rules Extraction Script');
  console.log('========================================');
  console.log(`Input: ${MUTATIONS_DIR}`);
  console.log(`Output: ${OUTPUT_DIR}`);

  // Get all module directories
  const modules = await fs.readdir(MUTATIONS_DIR);
  console.log(`\nFound ${modules.length} modules: ${modules.join(', ')}`);

  // Ensure output directory
  await ensureDir(OUTPUT_DIR);

  // Process all modules
  const allValidations: EndpointValidation[] = [];

  for (const module of modules) {
    const moduleInputPath = path.join(MUTATIONS_DIR, module);
    const stat = await fs.stat(moduleInputPath);

    if (stat.isDirectory()) {
      const validations = await processModule(module);
      allValidations.push(...validations);
    }
  }

  console.log('\n========================================');
  console.log('  Generating Summary Files');
  console.log('========================================');

  // Generate barrel export
  const barrelContent = generateBarrelExport(allValidations);
  const barrelPath = path.join(OUTPUT_DIR, 'index.ts');
  await fs.writeFile(barrelPath, barrelContent);
  console.log(`Written: ${barrelPath}`);

  // Generate markdown documentation
  const markdownContent = generateMarkdownDoc(allValidations);
  const markdownPath = path.join(OUTPUT_DIR, 'VALIDATION-REFERENCE.md');
  await fs.writeFile(markdownPath, markdownContent);
  console.log(`Written: ${markdownPath}`);

  // Summary
  console.log('\n========================================');
  console.log('  Summary');
  console.log('========================================');
  console.log(`Total endpoints processed: ${allValidations.length}`);

  // Count fields and rules
  let totalFields = 0;
  let totalRules = 0;
  const ruleCounts: Record<string, number> = {};

  for (const validation of allValidations) {
    for (const field of Object.values(validation.fields)) {
      totalFields++;
      for (const rule of field.rules) {
        totalRules++;
        ruleCounts[rule.rule] = (ruleCounts[rule.rule] || 0) + 1;
      }
    }
  }

  console.log(`Total fields extracted: ${totalFields}`);
  console.log(`Total rules extracted: ${totalRules}`);
  console.log('\nRule distribution:');

  const sortedRules = Object.entries(ruleCounts).sort((a, b) => b[1] - a[1]);
  for (const [rule, count] of sortedRules) {
    console.log(`  ${rule}: ${count}`);
  }

  console.log('\n✓ Extraction complete!');
}

// Run
main().catch((error) => {
  console.error('Error:', error);
  process.exit(1);
});
