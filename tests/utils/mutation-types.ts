/**
 * Mutation Types for API Testing Agents
 * Defines types for capturing POST, PUT, PATCH, DELETE requests
 */

// Re-export validation types from the validation extractor
export type {
  ExtractedRule,
  FieldValidation,
  EndpointValidation,
} from './validation-rules-extractor';

// Re-export JSON Schema types from the validation schema generator
export type {
  JSONSchema,
  JSONSchemaProperty,
} from './validation-schema-generator';

// HTTP Methods for mutations
export type HttpMethod = 'POST' | 'PUT' | 'PATCH' | 'DELETE';

// Mutation Request structure
export interface MutationRequest {
  method: HttpMethod;
  endpoint: string;
  body?: Record<string, unknown>;
  headers?: Record<string, string>;
}

// Mutation Response structure
export interface MutationResponse {
  status: number;
  statusText: string;
  headers: Record<string, string>;
  body: unknown;
  timing: number;
}

// Validation error from API
export interface ValidationError {
  field: string;
  message: string;
  rule?: string; // 'required', 'min', 'max', 'unique', 'email', etc.
}

// Complete mutation capture
export interface MutationCapture {
  request: MutationRequest;
  response: MutationResponse;
  timestamp: string;
  validationErrors?: ValidationError[];
  success: boolean;
}

// Field requirements discovered from validation errors
export interface FieldRequirement {
  required: boolean;
  rules: string[];
  messages: string[];
  type?: string;
}

export interface FieldRequirements {
  [fieldName: string]: FieldRequirement;
}

// OpenAPI types for output generation
export interface OpenApiSchema {
  type: string;
  properties?: Record<string, OpenApiSchema>;
  items?: OpenApiSchema;
  required?: string[];
  enum?: unknown[];
  format?: string;
  minimum?: number;
  maximum?: number;
  minLength?: number;
  maxLength?: number;
}

export interface OpenApiRequestBody {
  required: boolean;
  content: {
    'application/json': {
      schema: OpenApiSchema;
      examples?: Record<string, { value: unknown; summary?: string }>;
    };
  };
}

export interface OpenApiResponse {
  description: string;
  content?: {
    'application/json': {
      schema: OpenApiSchema;
      examples?: Record<string, { value: unknown; summary?: string }>;
    };
  };
}

export interface OpenApiOperation {
  operationId: string;
  summary: string;
  description?: string;
  tags: string[];
  requestBody?: OpenApiRequestBody;
  responses: Record<string, OpenApiResponse>;
  security?: Array<Record<string, string[]>>;
}

export interface OpenApiPath {
  [method: string]: OpenApiOperation;
}

export interface MutationOutput {
  openapi: '3.0.3';
  info: {
    title: string;
    version: string;
    description?: string;
  };
  servers: Array<{ url: string; description?: string }>;
  paths: Record<string, OpenApiPath>;
  components: {
    schemas: Record<string, OpenApiSchema>;
    securitySchemes: {
      bearerAuth: {
        type: 'http';
        scheme: 'bearer';
      };
      tenantHeader: {
        type: 'apiKey';
        in: 'header';
        name: 'X-Tenant';
      };
    };
  };
  security: Array<Record<string, string[]>>;
}

// Entity dependency definition
export interface EntityDependency {
  entity: string;
  dependsOn: string[];
  getEndpoint: string;
  createEndpoint: string;
  updateEndpoint?: string;
  requiredFields: string[];
}

// API Context configuration
export interface ApiContextConfig {
  baseUrl: string;
  token: string;
  tenant: string;
}

// Request options for API calls
export interface RequestOptions {
  headers?: Record<string, string>;
  timeout?: number;
  retries?: number;
}

// Status ID references
export interface StatusIds {
  unit: {
    sold: 23;
    soldAndRented: 24;
    rented: 25;
    available: 26;
  };
  lease: {
    newContract: 30;
    activeContract: 31;
    expiredContract: 32;
    canceledContract: 33;
    closedContract: 34;
  };
}

export const STATUS_IDS: StatusIds = {
  unit: {
    sold: 23,
    soldAndRented: 24,
    rented: 25,
    available: 26,
  },
  lease: {
    newContract: 30,
    activeContract: 31,
    expiredContract: 32,
    canceledContract: 33,
    closedContract: 34,
  },
};

// Rental type references
export interface RentalTypes {
  yearly: 13;
  monthly: 14;
  daily: 15;
}

export const RENTAL_CONTRACT_TYPES: RentalTypes = {
  yearly: 13,
  monthly: 14,
  daily: 15,
};

// Payment schedule references
export interface PaymentSchedules {
  yearly: {
    monthly: 4;
    quarterly: 5;
    semiAnnual: 6;
    annual: 7;
  };
  monthly: {
    monthlyPayment: 16;
    upfrontPayment: 17;
  };
  daily: {
    upfrontPayment: 18;
  };
}

export const PAYMENT_SCHEDULES: PaymentSchedules = {
  yearly: {
    monthly: 4,
    quarterly: 5,
    semiAnnual: 6,
    annual: 7,
  },
  monthly: {
    monthlyPayment: 16,
    upfrontPayment: 17,
  },
  daily: {
    upfrontPayment: 18,
  },
};

// Unit categories
export interface UnitCategories {
  residential: 2;
  commercial: 3;
}

export const UNIT_CATEGORIES: UnitCategories = {
  residential: 2,
  commercial: 3,
};

// Unit types by category
export interface UnitTypes {
  residential: {
    apartment: 17;
    penthouse: 18;
    duplexApartment: 19;
    duplexVilla: 20;
    floor: 21;
    villa: 22;
    townhouse: 24;
    land: 25;
  };
  commercial: {
    retailStore: 26;
    fnb: 27;
    warehouse: 28;
    storage: 29;
    office: 30;
    land: 31;
    showroom: 135;
    kiosk: 136;
    executiveOffice: 137;
    sharedOffice: 138;
    building: 139;
    tower: 140;
  };
}

export const UNIT_TYPES: UnitTypes = {
  residential: {
    apartment: 17,
    penthouse: 18,
    duplexApartment: 19,
    duplexVilla: 20,
    floor: 21,
    villa: 22,
    townhouse: 24,
    land: 25,
  },
  commercial: {
    retailStore: 26,
    fnb: 27,
    warehouse: 28,
    storage: 29,
    office: 30,
    land: 31,
    showroom: 135,
    kiosk: 136,
    executiveOffice: 137,
    sharedOffice: 138,
    building: 139,
    tower: 140,
  },
};
