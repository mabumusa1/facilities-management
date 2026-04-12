import { Page, Response } from '@playwright/test';
import { CapturedRequest, NetworkCapture, API_DOMAIN } from './types';

export function createNetworkCapture(page: Page): NetworkCapture {
  const requests: CapturedRequest[] = [];
  let isCapturing = false;

  const handleResponse = async (response: Response) => {
    if (!isCapturing) return;
    const request = response.request();
    requests.push({
      method: request.method(),
      url: request.url(),
      status: response.status(),
      timestamp: Date.now(),
    });
  };

  return {
    requests,
    startCapture: () => {
      isCapturing = true;
      page.on('response', handleResponse);
    },
    stopCapture: () => {
      isCapturing = false;
      page.off('response', handleResponse);
    },
    getApiRequests: () => requests.filter(r => r.url.includes(API_DOMAIN)),
    formatEndpoints: () => requests.map(r => `[${r.method}] ${r.url} => [${r.status}]`),
  };
}

export function formatEndpointLog(method: string, url: string, status: number): string {
  return `[${method}] ${url} => [${status}]`;
}

export function filterApiRequests(requests: CapturedRequest[], domain: string): CapturedRequest[] {
  return requests.filter(r => r.url.includes(domain));
}
