import { test as base, expect, Page } from '@playwright/test';
import * as fs from 'fs/promises';
import * as path from 'path';
import { createNetworkCapture } from '../utils/network-capture';
import { writeEndpoints, writeScreenshot, writeSnapshot } from '../utils/output-writer';
import { ATAR_CONFIG } from '../utils/types';

/**
 * Auth Pages Agent - Captures unauthenticated pages
 *
 * These pages require NO localStorage injection (unauthenticated session):
 * - /login - Login page
 * - /verify - Email verification
 * - /no-access - Permission denied
 * - /403 - Forbidden page
 */

const AUTH_ROUTES = [
  { path: '/login', name: 'auth-login' },
  { path: '/verify', name: 'auth-verify' },
  { path: '/no-access', name: 'auth-no-access' },
  { path: '/403', name: 'auth-403' },
];

// Custom fixture WITHOUT localStorage injection for unauthenticated pages
const test = base.extend<{ unauthPage: Page }>({
  unauthPage: async ({ page }, use) => {
    // Do NOT inject localStorage - we want unauthenticated state
    await use(page);
  },
});

test.describe('Auth Pages Agent (Unauthenticated)', () => {
  for (const route of AUTH_ROUTES) {
    test(`scan ${route.name}`, async ({ unauthPage }) => {
      const capture = createNetworkCapture(unauthPage);
      capture.startCapture();

      const fullUrl = `${ATAR_CONFIG.baseUrl}${route.path}`;

      try {
        await unauthPage.goto(fullUrl, {
          waitUntil: 'networkidle',
          timeout: 30000
        });

        // Wait for page to stabilize
        await unauthPage.waitForTimeout(2000);
      } catch (e) {
        console.log(`Navigation to ${route.name} may have redirected or timed out`);
      }

      capture.stopCapture();

      const endpoints = capture.formatEndpoints();

      // Take screenshot
      try {
        const screenshot = await unauthPage.screenshot({ fullPage: true });
        await writeScreenshot(route.name, screenshot);
      } catch (e) {
        console.log(`Could not capture screenshot for ${route.name}`);
      }

      // Write endpoints
      await writeEndpoints(route.name, endpoints);

      console.log(`Scanned ${route.name}: ${endpoints.length} endpoints captured`);
      console.log(`Final URL: ${unauthPage.url()}`);
    });
  }
});

export { expect };
