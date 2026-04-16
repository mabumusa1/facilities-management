import { Page, Request, Response } from '@playwright/test';

export interface AtarConfig {
  baseUrl: string;
  apiUrl: string;
  tenant?: string;
  token?: string;
  language?: string;
}

export interface CapturedRequest {
  method: string;
  url: string;
  status: number;
  timestamp: number;
  contentType?: string;
  requestBody?: unknown;
  responseBody?: unknown;
}

export interface NetworkCapture {
  requests: CapturedRequest[];
  startCapture: () => void;
  stopCapture: () => void;
  getApiRequests: () => CapturedRequest[];
  getApiRequestsWithBodies: () => CapturedRequest[];
  formatEndpoints: () => string[];
}

export interface ScanResult {
  pageName: string;
  pagePath: string;
  endpoints: string[];
  apiResponses?: CapturedRequest[];
  screenshot?: Buffer;
  snapshot?: string;
  timestamp: string;
  formData?: PageFormData;
  relationships?: PageRelationships;
  dataAnalysis?: DataAnalysis;
}

export interface RouteDefinition {
  path: string;
  name: string;
  children?: RouteDefinition[];
  tabs?: string[];
}

export interface ScannerContext {
  page: Page;
  config: AtarConfig;
  capture: NetworkCapture;
  scanPage: (url: string, pageName: string, options?: ScanOptions) => Promise<ScanResult>;
}

export interface ScanOptions {
  waitForSelector?: string;
  waitForNetworkIdle?: boolean;
  waitTimeout?: number;
  takeScreenshot?: boolean;
  takeSnapshot?: boolean;
  clickTabs?: string[];
  failOnNotFound?: boolean;
  // New scrolling and form extraction options
  scrollToBottom?: boolean;
  scrollDelay?: number;
  expandSections?: boolean;
  expandSelectors?: string[];
  extractFormData?: boolean;
  // Relationship exploration options
  exploreRelationships?: boolean;
  // Data analysis - analyze API responses for relationships/enums (default: true)
  analyzeData?: boolean;
}

// Form field data types
export interface FormFieldData {
  type: 'text' | 'select' | 'checkbox' | 'radio' | 'textarea' | 'hidden' | 'number' | 'date' | 'other';
  name: string;
  id?: string;
  label?: string;
  value?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  options?: Array<{ value: string; label: string; selected?: boolean }>;
  checked?: boolean;
  attributes?: Record<string, string>;
}

export interface PageFormData {
  forms: Array<{
    id?: string;
    name?: string;
    action?: string;
    fields: FormFieldData[];
  }>;
  orphanFields: FormFieldData[];
  dropdownOptions?: Record<string, string[]>;
}

// Relationship exploration types
export interface DropdownRelationship {
  triggerDropdown: {
    selector: string;
    name: string;
    selectedValue: string;
    selectedLabel: string;
  };
  triggeredApiCalls: Array<{
    method: string;
    url: string;
    queryParams: Record<string, string>;
  }>;
  affectedDropdowns: Array<{
    selector: string;
    name: string;
    optionsBefore: string[];
    optionsAfter: string[];
  }>;
}

export interface HardcodedValue {
  source: string;
  type: 'enum' | 'constant' | 'array' | 'object';
  name: string;
  value: unknown;
  context?: string;
}

export interface PageRelationships {
  dropdownRelationships: DropdownRelationship[];
  hardcodedValues: HardcodedValue[];
  staticOptions: Record<string, string[]>;
}

// Data analysis types
export interface ApiRelationship {
  parentEndpoint: string;
  parentField: string;
  childEndpoint: string;
  relationship: string;
}

export interface ExtractedEnum {
  name: string;
  source: string;
  values: Array<{ key: string; value: string; label?: string }>;
}

export interface DataAnalysis {
  apiRelationships: ApiRelationship[];
  enums: ExtractedEnum[];
  foreignKeys: Array<{ field: string; referencedEntity: string; foundIn: string[] }>;
  translationKeys: Record<string, string>;
}

export const ATAR_CONFIG: AtarConfig = {
  baseUrl: 'https://goatar.com',
  apiUrl: 'https://api.goatar.com/api-management/',
};

export const API_DOMAIN = 'api.goatar.com';
