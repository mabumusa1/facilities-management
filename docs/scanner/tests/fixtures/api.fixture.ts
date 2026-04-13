/**
 * API Testing Fixture for Mutation Capture
 *
 * Uses Playwright's APIRequestContext for direct API calls
 * Captures full request/response data for OpenAPI generation
 */

import { test as base, APIRequestContext, request } from '@playwright/test';
import * as fs from 'fs/promises';
import * as path from 'path';
import { ATAR_CONFIG } from '../utils/types';
import {
  MutationCapture,
  MutationRequest,
  MutationResponse,
  ValidationError,
  FieldRequirements,
  FieldRequirement,
  HttpMethod,
  RequestOptions,
} from '../utils/mutation-types';
import {
  QueryCapture,
  QueryOptions,
  ResponseSchema,
  FieldSchema,
} from '../utils/query-types';

// API Context interface
export interface ApiContext {
  request: APIRequestContext;
  config: typeof ATAR_CONFIG & { token: string; tenant: string };

  // Core mutation methods
  post: (endpoint: string, body?: object, options?: RequestOptions) => Promise<MutationCapture>;
  put: (endpoint: string, body?: object, options?: RequestOptions) => Promise<MutationCapture>;
  patch: (endpoint: string, body?: object, options?: RequestOptions) => Promise<MutationCapture>;
  delete: (endpoint: string, options?: RequestOptions) => Promise<MutationCapture>;

  // Reference data fetching
  get: (endpoint: string, options?: RequestOptions) => Promise<unknown>;
  fetchReferenceData: (endpoint: string) => Promise<unknown>;

  // GET with full capture (for response schema extraction)
  getCapture: (endpoint: string, options?: QueryOptions) => Promise<QueryCapture>;

  // ID extraction helpers
  extractIds: (data: unknown, idField?: string) => string[];
  extractFirstId: (data: unknown, idField?: string) => string | undefined;

  // Validation error analysis
  analyzeValidationErrors: (captures: MutationCapture[]) => FieldRequirements;

  // Batch operations
  captureAll: MutationCapture[];
  queryCaptures: QueryCapture[];
  clearCaptures: () => void;
  clearQueryCaptures: () => void;
}

// Load auth from localstorage.json
async function loadLocalStorage(): Promise<Record<string, string>> {
  const filePath = path.join(process.cwd(), 'tests', 'localstorage.json');
  const content = await fs.readFile(filePath, 'utf-8');
  return JSON.parse(content);
}

// Parse validation errors from API response
function parseValidationErrors(body: unknown): ValidationError[] {
  if (!body || typeof body !== 'object') return [];

  const errors: ValidationError[] = [];
  const responseBody = body as Record<string, unknown>;

  // Handle Laravel-style validation errors
  const errObj = responseBody.errors as Record<string, unknown> | undefined;
  if (errObj && typeof errObj === 'object') {
    for (const [field, messages] of Object.entries(errObj)) {
      if (Array.isArray(messages)) {
        for (const message of messages) {
          const messageStr = typeof message === 'string' ? message : JSON.stringify(message);
          errors.push({
            field,
            message: messageStr,
            rule: extractRule(message),
          });
        }
      } else if (typeof messages === 'string') {
        errors.push({
          field,
          message: messages,
          rule: extractRule(messages),
        });
      }
    }
  }

  return errors;
}

// Extract validation rule from message
function extractRule(message: unknown): string | undefined {
  // Handle case where message might not be a string
  if (typeof message !== 'string') {
    if (Array.isArray(message)) {
      return extractRule(message[0]);
    }
    return undefined;
  }
  const lowerMessage = message.toLowerCase();
  if (lowerMessage.includes('required') || lowerMessage.includes('مطلوب')) return 'required';
  if (lowerMessage.includes('min:') || lowerMessage.includes('minimum')) return 'min';
  if (lowerMessage.includes('max:') || lowerMessage.includes('maximum')) return 'max';
  if (lowerMessage.includes('email') || lowerMessage.includes('بريد')) return 'email';
  if (lowerMessage.includes('unique') || lowerMessage.includes('فريد')) return 'unique';
  if (lowerMessage.includes('numeric') || lowerMessage.includes('رقم')) return 'numeric';
  if (lowerMessage.includes('exists') || lowerMessage.includes('موجود')) return 'exists';
  if (lowerMessage.includes('date') || lowerMessage.includes('تاريخ')) return 'date';
  if (lowerMessage.includes('array') || lowerMessage.includes('مصفوفة')) return 'array';
  return undefined;
}

// Infer field schema from a value
function inferFieldSchema(value: unknown): FieldSchema {
  if (value === null) {
    return { type: 'null', nullable: true };
  }

  if (Array.isArray(value)) {
    const itemSchema = value.length > 0 ? inferFieldSchema(value[0]) : { type: 'mixed' as const, nullable: true };
    return {
      type: 'array',
      nullable: false,
      items: itemSchema,
      example: value.slice(0, 2), // Keep first 2 items as example
    };
  }

  if (typeof value === 'object') {
    const properties: Record<string, FieldSchema> = {};
    for (const [key, val] of Object.entries(value as Record<string, unknown>)) {
      properties[key] = inferFieldSchema(val);
    }
    return {
      type: 'object',
      nullable: false,
      properties,
    };
  }

  if (typeof value === 'string') {
    return { type: 'string', nullable: false, example: value.length > 100 ? value.slice(0, 100) + '...' : value };
  }

  if (typeof value === 'number') {
    return { type: 'number', nullable: false, example: value };
  }

  if (typeof value === 'boolean') {
    return { type: 'boolean', nullable: false, example: value };
  }

  return { type: 'mixed', nullable: true };
}

// Infer response schema from response body
function inferResponseSchema(body: unknown): ResponseSchema | undefined {
  if (!body || typeof body !== 'object') return undefined;

  const responseBody = body as Record<string, unknown>;

  // Check if it's a paginated list response
  if (responseBody.data && Array.isArray(responseBody.data)) {
    const itemSchema = responseBody.data.length > 0
      ? inferFieldSchema(responseBody.data[0])
      : { type: 'object' as const, nullable: true, properties: {} };

    const hasPagination = !!(responseBody.meta || responseBody.links || responseBody.current_page);
    const paginationFields: string[] = [];

    if (responseBody.meta && typeof responseBody.meta === 'object') {
      paginationFields.push(...Object.keys(responseBody.meta as object));
    }
    if (responseBody.current_page) paginationFields.push('current_page');
    if (responseBody.last_page) paginationFields.push('last_page');
    if (responseBody.per_page) paginationFields.push('per_page');
    if (responseBody.total) paginationFields.push('total');

    return {
      type: 'object',
      properties: {
        data: {
          type: 'array',
          nullable: false,
          items: itemSchema,
        },
        ...(responseBody.meta ? { meta: inferFieldSchema(responseBody.meta) } : {}),
        ...(responseBody.links ? { links: inferFieldSchema(responseBody.links) } : {}),
      },
      meta: {
        hasPagination,
        paginationFields: paginationFields.length > 0 ? paginationFields : undefined,
      },
    };
  }

  // Check if it's a single object response with data wrapper
  if (responseBody.data && typeof responseBody.data === 'object' && !Array.isArray(responseBody.data)) {
    return {
      type: 'object',
      properties: {
        data: inferFieldSchema(responseBody.data),
        ...(responseBody.success !== undefined ? { success: inferFieldSchema(responseBody.success) } : {}),
        ...(responseBody.message ? { message: inferFieldSchema(responseBody.message) } : {}),
      },
      meta: { hasPagination: false },
    };
  }

  // Direct array response
  if (Array.isArray(body)) {
    const itemSchema = body.length > 0
      ? inferFieldSchema(body[0])
      : { type: 'object' as const, nullable: true, properties: {} };

    return {
      type: 'array',
      items: itemSchema,
      meta: { hasPagination: false },
    };
  }

  // Direct object response
  return {
    type: 'object',
    properties: Object.fromEntries(
      Object.entries(responseBody).map(([key, value]) => [key, inferFieldSchema(value)])
    ),
    meta: { hasPagination: false },
  };
}

// Analyze validation errors across multiple captures
function analyzeValidationErrors(captures: MutationCapture[]): FieldRequirements {
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

// Create the test fixture
export const test = base.extend<{ api: ApiContext }>({
  api: async ({}, use) => {
    // Load auth from localstorage.json
    const localStorageData = await loadLocalStorage();
    const token = localStorageData.token;
    const tenant = localStorageData['X-Tenant'];

    // Create API context with base URL and default headers
    const apiContext = await request.newContext({
      baseURL: ATAR_CONFIG.apiUrl,
      extraHTTPHeaders: {
        Authorization: `Bearer ${token}`,
        'X-Tenant': tenant,
        'Content-Type': 'application/json',
        Accept: 'application/json',
      },
    });

    // Store all captures for batch processing
    const captureAll: MutationCapture[] = [];
    const queryCaptures: QueryCapture[] = [];

    // Execute mutation and capture full request/response
    const executeMutation = async (
      method: HttpMethod,
      endpoint: string,
      body?: object,
      options?: RequestOptions
    ): Promise<MutationCapture> => {
      // Strip leading slash to work with baseURL that has trailing slash
      const cleanEndpoint = endpoint.startsWith('/') ? endpoint.slice(1) : endpoint;
      const startTime = Date.now();

      let response;
      const requestOptions: Record<string, unknown> = {
        timeout: options?.timeout || 30000,
      };

      if (body) {
        requestOptions.data = body;
      }

      if (options?.headers) {
        requestOptions.headers = options.headers;
      }

      switch (method) {
        case 'POST':
          response = await apiContext.post(cleanEndpoint, requestOptions);
          break;
        case 'PUT':
          response = await apiContext.put(cleanEndpoint, requestOptions);
          break;
        case 'PATCH':
          response = await apiContext.patch(cleanEndpoint, requestOptions);
          break;
        case 'DELETE':
          response = await apiContext.delete(cleanEndpoint, requestOptions);
          break;
      }

      const timing = Date.now() - startTime;

      // Parse response body
      let responseBody: unknown;
      try {
        responseBody = await response.json();
      } catch {
        try {
          responseBody = await response.text();
        } catch {
          responseBody = null;
        }
      }

      // Build capture object
      const capture: MutationCapture = {
        request: {
          method,
          endpoint,
          body: body as Record<string, unknown>,
        },
        response: {
          status: response.status(),
          statusText: response.statusText(),
          headers: response.headers(),
          body: responseBody,
          timing,
        },
        timestamp: new Date().toISOString(),
        success: response.status() >= 200 && response.status() < 300,
      };

      // Parse validation errors for 4xx responses
      if (response.status() >= 400 && response.status() < 500) {
        capture.validationErrors = parseValidationErrors(responseBody);
      }

      // Store in batch captures
      captureAll.push(capture);

      // Log the capture
      console.log(
        `[${method}] ${endpoint} => ${response.status()} (${timing}ms)` +
          (capture.validationErrors?.length
            ? ` - ${capture.validationErrors.length} validation errors`
            : '')
      );

      return capture;
    };

    // GET method for fetching reference data
    const executeGet = async (endpoint: string, options?: RequestOptions): Promise<unknown> => {
      // Strip leading slash to work with baseURL that has trailing slash
      const cleanEndpoint = endpoint.startsWith('/') ? endpoint.slice(1) : endpoint;
      const response = await apiContext.get(cleanEndpoint, {
        timeout: options?.timeout || 30000,
        headers: options?.headers,
      });

      try {
        return await response.json();
      } catch {
        return await response.text();
      }
    };

    // GET with full capture for response schema extraction
    const executeGetCapture = async (endpoint: string, options?: QueryOptions): Promise<QueryCapture> => {
      const cleanEndpoint = endpoint.startsWith('/') ? endpoint.slice(1) : endpoint;
      const startTime = Date.now();

      // Build URL with query params if provided
      let url = cleanEndpoint;
      if (options?.params) {
        const params = new URLSearchParams();
        for (const [key, value] of Object.entries(options.params)) {
          params.append(key, String(value));
        }
        url = `${cleanEndpoint}?${params.toString()}`;
      }

      const response = await apiContext.get(url, {
        timeout: options?.timeout || 30000,
        headers: options?.headers,
      });

      const timing = Date.now() - startTime;

      // Parse response body
      let responseBody: unknown;
      try {
        responseBody = await response.json();
      } catch {
        try {
          responseBody = await response.text();
        } catch {
          responseBody = null;
        }
      }

      // Build capture object
      const capture: QueryCapture = {
        request: {
          method: 'GET',
          endpoint,
          params: options?.params,
        },
        response: {
          status: response.status(),
          statusText: response.statusText(),
          headers: response.headers(),
          body: responseBody,
          timing,
        },
        timestamp: new Date().toISOString(),
        success: response.status() >= 200 && response.status() < 300,
      };

      // Infer schema if requested and response was successful
      if ((options?.includeSchema !== false) && capture.success) {
        capture.schema = inferResponseSchema(responseBody);
      }

      // Store in query captures
      queryCaptures.push(capture);

      // Log the capture
      const dataInfo = capture.schema?.type === 'object' && capture.schema?.properties?.data
        ? ` [${Object.keys(capture.schema.properties.data.properties || {}).length} fields]`
        : '';
      console.log(
        `[GET] ${endpoint} => ${response.status()} (${timing}ms)${dataInfo}`
      );

      return capture;
    };

    // Extract IDs from response data
    const extractIds = (data: unknown, idField = 'id'): string[] => {
      if (!data) return [];

      // Handle array directly
      if (Array.isArray(data)) {
        return data
          .map((item) => item?.[idField]?.toString())
          .filter((id): id is string => id !== undefined);
      }

      // Handle response with data property
      const responseData = data as Record<string, unknown>;
      if (responseData.data && Array.isArray(responseData.data)) {
        return responseData.data
          .map((item: Record<string, unknown>) => item?.[idField]?.toString())
          .filter((id): id is string => id !== undefined);
      }

      // Handle single object with id
      if (responseData[idField]) {
        return [responseData[idField].toString()];
      }

      // Handle nested data.id
      if (responseData.data && typeof responseData.data === 'object') {
        const nestedData = responseData.data as Record<string, unknown>;
        if (nestedData[idField]) {
          return [nestedData[idField].toString()];
        }
      }

      return [];
    };

    const extractFirstId = (data: unknown, idField = 'id'): string | undefined => {
      const ids = extractIds(data, idField);
      return ids[0];
    };

    // Provide the API context
    await use({
      request: apiContext,
      config: { ...ATAR_CONFIG, token, tenant },

      post: (endpoint, body, options) => executeMutation('POST', endpoint, body, options),
      put: (endpoint, body, options) => executeMutation('PUT', endpoint, body, options),
      patch: (endpoint, body, options) => executeMutation('PATCH', endpoint, body, options),
      delete: (endpoint, options) => executeMutation('DELETE', endpoint, undefined, options),

      get: executeGet,
      fetchReferenceData: executeGet,
      getCapture: executeGetCapture,

      extractIds,
      extractFirstId,

      analyzeValidationErrors,

      captureAll,
      queryCaptures,
      clearCaptures: () => {
        captureAll.length = 0;
      },
      clearQueryCaptures: () => {
        queryCaptures.length = 0;
      },
    });

    // Cleanup
    await apiContext.dispose();
  },
});

export { expect } from '@playwright/test';
