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
}

export interface NetworkCapture {
  requests: CapturedRequest[];
  startCapture: () => void;
  stopCapture: () => void;
  getApiRequests: () => CapturedRequest[];
  formatEndpoints: () => string[];
}

export interface ScanResult {
  pageName: string;
  pagePath: string;
  endpoints: string[];
  screenshot?: Buffer;
  snapshot?: string;
  timestamp: string;
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
}

export const ATAR_CONFIG: AtarConfig = {
  baseUrl: 'https://goatar.com',
  apiUrl: 'https://api.goatar.com/api-management/',
};

export const API_DOMAIN = 'api.goatar.com';
