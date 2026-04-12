/**
 * Validation Schema Generator
 *
 * Generates JSON Schema and TypeScript interfaces from extracted validation rules.
 */

import { EndpointValidation, FieldValidation, ExtractedRule } from './validation-rules-extractor';

export interface JSONSchema {
  $schema: string;
  $id?: string;
  title: string;
  description?: string;
  type: 'object';
  properties: Record<string, JSONSchemaProperty>;
  required: string[];
  additionalProperties?: boolean;
}

export interface JSONSchemaProperty {
  type: string | string[];
  description?: string;
  format?: string;
  minLength?: number;
  maxLength?: number;
  minimum?: number;
  maximum?: number;
  pattern?: string;
  enum?: unknown[];
  items?: JSONSchemaProperty;
  examples?: unknown[];
}

/**
 * Map validation type to JSON Schema type
 */
function mapTypeToJsonSchema(type: string): string | string[] {
  switch (type) {
    case 'string':
    case 'email':
    case 'date':
    case 'phone':
    case 'url':
      return 'string';
    case 'number':
    case 'integer':
      return type;
    case 'boolean':
      return 'boolean';
    case 'array':
      return 'array';
    case 'object':
      return 'object';
    case 'file':
      return 'string'; // file paths or base64
    default:
      return 'string';
  }
}

/**
 * Get JSON Schema format from type
 */
function getJsonSchemaFormat(type: string): string | undefined {
  switch (type) {
    case 'email':
      return 'email';
    case 'date':
      return 'date';
    case 'url':
      return 'uri';
    default:
      return undefined;
  }
}

/**
 * Extract constraints from rules
 */
function extractConstraints(
  rules: ExtractedRule[]
): Partial<JSONSchemaProperty> {
  const constraints: Partial<JSONSchemaProperty> = {};

  for (const rule of rules) {
    if (rule.constraints) {
      if (rule.constraints.minLength !== undefined) {
        constraints.minLength = rule.constraints.minLength;
      }
      if (rule.constraints.maxLength !== undefined) {
        constraints.maxLength = rule.constraints.maxLength;
      }
      if (rule.constraints.min !== undefined) {
        constraints.minimum = rule.constraints.min;
      }
      if (rule.constraints.max !== undefined) {
        constraints.maximum = rule.constraints.max;
      }
      if (rule.constraints.pattern) {
        constraints.pattern = rule.constraints.pattern;
      }
      if (rule.constraints.enum) {
        constraints.enum = rule.constraints.enum;
      }
    }
  }

  return constraints;
}

/**
 * Build JSON Schema property from field validation
 */
function buildJsonSchemaProperty(field: FieldValidation): JSONSchemaProperty {
  const schemaType = mapTypeToJsonSchema(field.type);
  const format = getJsonSchemaFormat(field.type);
  const constraints = extractConstraints(field.rules);

  // Handle nullable types properly
  let typeValue: string | string[];
  if (field.required) {
    typeValue = schemaType;
  } else {
    // If schemaType is already an array, add 'null' to it; otherwise create new array
    typeValue = Array.isArray(schemaType)
      ? [...schemaType, 'null']
      : [schemaType, 'null'];
  }

  const property: JSONSchemaProperty = {
    type: typeValue,
    ...constraints,
  };

  if (format) {
    property.format = format;
  }

  // Add description from rule messages
  const descriptions = field.rules
    .map((r) => r.rawMessage)
    .filter((m) => m && !m.includes('مطلوب') && !m.includes('required'));

  if (descriptions.length > 0) {
    property.description = descriptions.join('; ');
  }

  // Add examples
  if (field.examples.valid.length > 0) {
    property.examples = field.examples.valid;
  }

  // Handle array items
  if (field.type === 'array') {
    property.items = { type: 'object' }; // Default, could be more specific
  }

  return property;
}

/**
 * Generate JSON Schema from endpoint validation
 */
export function generateJsonSchema(validation: EndpointValidation): JSONSchema {
  const properties: Record<string, JSONSchemaProperty> = {};

  for (const [fieldName, fieldValidation] of Object.entries(validation.fields)) {
    properties[fieldName] = buildJsonSchemaProperty(fieldValidation);
  }

  const title = `${validation.method} ${validation.endpoint}`;

  return {
    $schema: 'https://json-schema.org/draft/2020-12/schema',
    $id: `atar-api/${validation.method.toLowerCase()}-${validation.endpoint.replace(/\//g, '-')}.schema.json`,
    title,
    description: `Request body schema for ${title}`,
    type: 'object',
    properties,
    required: validation.requiredFields,
    additionalProperties: true,
  };
}

/**
 * Generate TypeScript interface from endpoint validation
 */
export function generateTypeScriptInterface(validation: EndpointValidation): string {
  const interfaceName = generateInterfaceName(validation.method, validation.endpoint);
  const lines: string[] = [];

  lines.push('/**');
  lines.push(` * Request body for ${validation.method} ${validation.endpoint}`);
  lines.push(' * Auto-generated from API validation errors');
  lines.push(' */');
  lines.push(`export interface ${interfaceName} {`);

  // Sort fields: required first, then optional
  const sortedFields = [
    ...validation.requiredFields,
    ...validation.optionalFields,
  ];

  for (const fieldName of sortedFields) {
    const field = validation.fields[fieldName];
    if (!field) continue;

    const tsType = mapTypeToTypeScript(field.type);
    const optional = !field.required ? '?' : '';
    const comment = field.rules
      .map((r) => r.rule)
      .filter((r) => r !== 'unknown')
      .join(', ');

    if (comment) {
      lines.push(`  /** Rules: ${comment} */`);
    }
    lines.push(`  ${fieldName}${optional}: ${tsType};`);
  }

  lines.push('}');
  lines.push('');

  return lines.join('\n');
}

/**
 * Generate interface name from endpoint
 */
function generateInterfaceName(method: string, endpoint: string): string {
  const parts = endpoint
    .split('/')
    .filter((p) => p && !p.startsWith('{'))
    .map((p) => p.replace(/-/g, '_'));

  const name = parts
    .map((p) => p.charAt(0).toUpperCase() + p.slice(1).toLowerCase())
    .join('');

  return `${method.charAt(0).toUpperCase() + method.slice(1).toLowerCase()}${name}Request`;
}

/**
 * Map validation type to TypeScript type
 */
function mapTypeToTypeScript(type: string): string {
  switch (type) {
    case 'string':
    case 'email':
    case 'date':
    case 'phone':
    case 'url':
      return 'string';
    case 'number':
    case 'integer':
      return 'number';
    case 'boolean':
      return 'boolean';
    case 'array':
      return 'unknown[]';
    case 'object':
      return 'Record<string, unknown>';
    case 'file':
      return 'string | File';
    default:
      return 'unknown';
  }
}

/**
 * Generate barrel export file for all types
 */
export function generateBarrelExport(
  validations: EndpointValidation[]
): string {
  const lines: string[] = [
    '/**',
    ' * Auto-generated TypeScript types for Atar API',
    ' * Generated from validation error captures',
    ' */',
    '',
  ];

  // Group by module
  const modules = new Map<string, EndpointValidation[]>();

  for (const validation of validations) {
    const module = validation.endpoint.split('/')[0] || 'default';
    if (!modules.has(module)) {
      modules.set(module, []);
    }
    modules.get(module)!.push(validation);
  }

  for (const [module, moduleValidations] of Array.from(modules.entries())) {
    lines.push(`// ${module} module`);
    for (const validation of moduleValidations) {
      const fileName = `${validation.method}-${validation.endpoint.replace(/\//g, '-').replace(/[{}]/g, '')}`;
      lines.push(`export * from './${module}/${fileName}.types';`);
    }
    lines.push('');
  }

  return lines.join('\n');
}

/**
 * Generate markdown documentation
 */
export function generateMarkdownDoc(
  validations: EndpointValidation[]
): string {
  const lines: string[] = [
    '# Atar API Validation Reference',
    '',
    '> Auto-generated from API validation error captures',
    '',
    `Generated: ${new Date().toISOString()}`,
    '',
    '---',
    '',
  ];

  // Group by module
  const modules = new Map<string, EndpointValidation[]>();

  for (const validation of validations) {
    const parts = validation.endpoint.split('/');
    const module = parts.includes('rf') ? parts[parts.indexOf('rf') + 1] || parts[0] : parts[0];
    if (!modules.has(module)) {
      modules.set(module, []);
    }
    modules.get(module)!.push(validation);
  }

  // Table of contents
  lines.push('## Table of Contents');
  lines.push('');
  for (const [module] of Array.from(modules.entries())) {
    lines.push(`- [${module}](#${module.toLowerCase()})`);
  }
  lines.push('');
  lines.push('---');
  lines.push('');

  // Module sections
  for (const [module, moduleValidations] of Array.from(modules.entries())) {
    lines.push(`## ${module}`);
    lines.push('');

    for (const validation of moduleValidations) {
      lines.push(`### ${validation.method} \`${validation.endpoint}\``);
      lines.push('');

      // Required fields
      if (validation.requiredFields.length > 0) {
        lines.push('**Required Fields:**');
        lines.push('');
        lines.push('| Field | Type | Rules |');
        lines.push('|-------|------|-------|');

        for (const fieldName of validation.requiredFields) {
          const field = validation.fields[fieldName];
          if (!field) continue;
          const rules = field.rules.map((r) => `\`${r.rule}\``).join(', ');
          lines.push(`| ${fieldName} | ${field.type} | ${rules} |`);
        }
        lines.push('');
      }

      // Optional fields
      if (validation.optionalFields.length > 0) {
        lines.push('**Optional Fields:**');
        lines.push('');
        lines.push('| Field | Type | Rules |');
        lines.push('|-------|------|-------|');

        for (const fieldName of validation.optionalFields) {
          const field = validation.fields[fieldName];
          if (!field) continue;
          const rules = field.rules.map((r) => `\`${r.rule}\``).join(', ');
          lines.push(`| ${fieldName} | ${field.type} | ${rules} |`);
        }
        lines.push('');
      }

      lines.push('---');
      lines.push('');
    }
  }

  return lines.join('\n');
}
