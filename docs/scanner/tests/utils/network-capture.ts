import { Page, Response } from '@playwright/test';
import { CapturedRequest, NetworkCapture, API_DOMAIN } from './types';

// Also capture translation files and other important static assets
const CAPTURE_PATTERNS = [
  API_DOMAIN,
  '/locales/',
  '/translation.json',
  '/i18n/',
];

function shouldCaptureBody(url: string): boolean {
  return CAPTURE_PATTERNS.some(pattern => url.includes(pattern));
}

export function createNetworkCapture(page: Page): NetworkCapture {
  const requests: CapturedRequest[] = [];
  let isCapturing = false;

  const handleResponse = async (response: Response) => {
    if (!isCapturing) return;

    const request = response.request();
    const url = request.url();
    const method = request.method();
    const status = response.status();
    const contentType = response.headers()['content-type'] || '';

    const capturedRequest: CapturedRequest = {
      method,
      url,
      status,
      timestamp: Date.now(),
      contentType,
    };

    // Capture request/response bodies for API calls and translation files
    if (shouldCaptureBody(url)) {
      try {
        // Capture request body for POST/PUT/PATCH
        if (['POST', 'PUT', 'PATCH'].includes(method)) {
          const postData = request.postData();
          if (postData) {
            try {
              capturedRequest.requestBody = JSON.parse(postData);
            } catch {
              capturedRequest.requestBody = postData;
            }
          }
        }

        // Capture response body for JSON responses
        if (contentType.includes('application/json')) {
          try {
            const body = await response.json();
            capturedRequest.responseBody = body;
          } catch {
            // Response body might not be available (e.g., streaming)
            try {
              const text = await response.text();
              if (text) {
                capturedRequest.responseBody = text;
              }
            } catch {
              // Ignore if we can't get the body
            }
          }
        }
      } catch (e) {
        // Silently ignore errors when capturing bodies
        console.log(`Could not capture body for ${url}: ${e}`);
      }
    }

    requests.push(capturedRequest);
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
    getApiRequests: () => requests.filter((r) => r.url.includes(API_DOMAIN)),
    getApiRequestsWithBodies: () =>
      requests.filter((r) => shouldCaptureBody(r.url) && (r.responseBody !== undefined || r.requestBody !== undefined)),
    formatEndpoints: () => requests.map((r) => `[${r.method}] ${r.url} => [${r.status}]`),
  };
}

export function formatEndpointLog(method: string, url: string, status: number): string {
  return `[${method}] ${url} => [${status}]`;
}

export function filterApiRequests(requests: CapturedRequest[], domain: string): CapturedRequest[] {
  return requests.filter((r) => r.url.includes(domain));
}
