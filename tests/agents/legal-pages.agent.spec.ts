import { test as base, expect, Page } from '@playwright/test';
import { createNetworkCapture } from '../utils/network-capture';
import { writeEndpoints, writeScreenshot } from '../utils/output-writer';
import { ATAR_CONFIG } from '../utils/types';

/**
 * Legal Pages Agent - Captures static legal/policy pages
 *
 * These are typically static pages that may or may not require auth:
 * - /privacy_policy - Privacy policy page
 * - /terms_and_conditions - Terms and conditions page
 */

const LEGAL_ROUTES = [
  { path: '/privacy_policy', name: 'legal-privacy-policy' },
  { path: '/terms_and_conditions', name: 'legal-terms-and-conditions' },
];

// Custom fixture - try both authenticated and unauthenticated
const test = base.extend<{ legalPage: Page }>({
  legalPage: async ({ page }, use) => {
    // These are static pages - try without auth first
    await use(page);
  },
});

test.describe('Legal Pages Agent', () => {
  for (const route of LEGAL_ROUTES) {
    test(`scan ${route.name}`, async ({ legalPage }) => {
      const capture = createNetworkCapture(legalPage);
      capture.startCapture();

      const fullUrl = `${ATAR_CONFIG.baseUrl}${route.path}`;

      try {
        await legalPage.goto(fullUrl, {
          waitUntil: 'networkidle',
          timeout: 30000
        });

        // Wait for page content to load
        await legalPage.waitForTimeout(2000);
      } catch (e) {
        console.log(`Navigation to ${route.name} may have issues: ${e}`);
      }

      capture.stopCapture();

      const endpoints = capture.formatEndpoints();

      // Take screenshot
      try {
        const screenshot = await legalPage.screenshot({ fullPage: true });
        await writeScreenshot(route.name, screenshot);
        console.log(`Screenshot captured for ${route.name}`);
      } catch (e) {
        console.log(`Could not capture screenshot for ${route.name}`);
      }

      // Write endpoints (may be empty for static pages)
      await writeEndpoints(route.name, endpoints);

      console.log(`Scanned ${route.name}: ${endpoints.length} endpoints captured`);
      console.log(`Final URL: ${legalPage.url()}`);

      // Log page title for verification
      const title = await legalPage.title();
      console.log(`Page title: ${title}`);
    });
  }
});

export { expect };
