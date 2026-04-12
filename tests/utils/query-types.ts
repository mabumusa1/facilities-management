/**
 * Types for GET endpoint response capture and schema extraction
 */

// HTTP methods for queries
export type QueryMethod = 'GET';

// Query request structure
export interface QueryRequest {
  method: QueryMethod;
  endpoint: string;
  params?: Record<string, string | number | boolean>;
}

// Query response structure
export interface QueryResponse {
  status: number;
  statusText: string;
  headers: Record<string, string>;
  body: unknown;
  timing: number;
}

// Full query capture
export interface QueryCapture {
  request: QueryRequest;
  response: QueryResponse;
  timestamp: string;
  success: boolean;
  schema?: ResponseSchema;
}

// Inferred field schema
export interface FieldSchema {
  type: 'string' | 'number' | 'boolean' | 'array' | 'object' | 'null' | 'mixed';
  nullable: boolean;
  example?: unknown;
  items?: FieldSchema;  // For arrays
  properties?: Record<string, FieldSchema>;  // For objects
  description?: string;
}

// Response schema structure
export interface ResponseSchema {
  type: 'object' | 'array';
  properties?: Record<string, FieldSchema>;
  items?: FieldSchema;
  meta?: {
    hasPagination: boolean;
    paginationFields?: string[];
  };
}

// Pagination metadata
export interface PaginationMeta {
  current_page?: number;
  last_page?: number;
  per_page?: number;
  total?: number;
  from?: number;
  to?: number;
  path?: string;
  first_page_url?: string;
  last_page_url?: string;
  next_page_url?: string | null;
  prev_page_url?: string | null;
  links?: Array<{ url: string | null; label: string; active: boolean }>;
}

// Standard list response structure
export interface ListResponse<T = unknown> {
  data: T[];
  meta?: PaginationMeta;
  links?: Record<string, string | null>;
}

// Standard detail response structure
export interface DetailResponse<T = unknown> {
  data: T;
  success?: boolean;
  message?: string;
}

// Module query configuration
export interface QueryEndpoint {
  path: string;
  name: string;
  description?: string;
  params?: Record<string, string | number | boolean>;
  requiresId?: boolean;
  idSource?: string;  // Endpoint to get IDs from
}

// Query module configuration
export interface QueryModule {
  name: string;
  description: string;
  endpoints: QueryEndpoint[];
}

// Endpoint summary for documentation
export interface EndpointSummary {
  endpoint: string;
  method: string;
  description?: string;
  responseType: 'list' | 'detail' | 'other';
  hasPagination: boolean;
  fieldCount: number;
  fields: string[];
  sampleResponse?: unknown;
}

// Module summary
export interface ModuleSummary {
  module: string;
  endpointCount: number;
  endpoints: EndpointSummary[];
  capturedAt: string;
}

// Query capture options
export interface QueryOptions {
  timeout?: number;
  headers?: Record<string, string>;
  params?: Record<string, string | number | boolean>;
  includeSchema?: boolean;
}
