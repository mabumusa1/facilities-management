import { test as base, Page } from '@playwright/test';
import * as fs from 'fs/promises';
import * as path from 'path';
import { createNetworkCapture } from '../utils/network-capture';
import { writeEndpoints, writeFormData, writeScreenshot, writeSnapshot, writeApiResponses, writeRelationships, writeDataAnalysis } from '../utils/output-writer';
import { extractDropdownOptions, extractFormData } from '../utils/form-extractor';
import { explorePageRelationships } from '../utils/relationship-explorer';
import { analyzePageData } from '../utils/data-analyzer';
import { PageFormData, PageRelationships, DataAnalysis, ScannerContext, ScanOptions, ScanResult, ATAR_CONFIG, NetworkCapture } from '../utils/types';

const NOT_FOUND_ENDPOINT_PATTERN = /\/assets\/js\/404[-\w]*\.js/i;
const NOT_FOUND_TEXT_SNIPPETS = [
  'لم يتم العثور على الصفحة',
  'المحتوى الذي تبحث عنه غير موجود',
  'page not found',
  'the page you are looking for',
];

async function assertNoNotFoundPage(
  page: Page,
  pageName: string,
  fullUrl: string,
  endpoints: string[],
  navigationStatus: number | null,
): Promise<void> {
  const pageTitle = (await page.title()).toLowerCase();
  const bodyText = (await page.locator('body').innerText().catch(() => ''))
    .replace(/\s+/g, ' ')
    .trim()
    .toLowerCase();

  const has404Number = /\b404\b/.test(pageTitle) || /\b404\b/.test(bodyText);
  const hasNotFoundText = NOT_FOUND_TEXT_SNIPPETS.some((snippet) => bodyText.includes(snippet));
  const loaded404Chunk = endpoints.some((endpoint) => NOT_FOUND_ENDPOINT_PATTERN.test(endpoint));
  const isHttpNotFound = navigationStatus !== null && navigationStatus >= 400;

  const isLikelyNotFoundPage = isHttpNotFound || (has404Number && hasNotFoundText) || (hasNotFoundText && loaded404Chunk);

  if (isLikelyNotFoundPage) {
    throw new Error(
      [
        `Capture blocked for ${pageName}.`,
        `URL: ${fullUrl}`,
        `Current page URL: ${page.url()}`,
        `Navigation status: ${navigationStatus ?? 'unknown'}`,
        'Detected a likely 404/not-found page. Refusing to persist screenshot/snapshot.',
      ].join(' '),
    );
  }
}

async function loadLocalStorage(): Promise<Record<string, string>> {
  const fileUrl = new URL('../localstorage.json', import.meta.url);
  const content = await fs.readFile(fileUrl, 'utf-8');
  return JSON.parse(content);
}

/**
 * Scroll to bottom of page to trigger lazy loading and ensure all content is rendered.
 * Returns to top after scrolling for consistent screenshots.
 */
async function scrollPageToBottom(
  page: Page,
  options: { delay?: number; stepSize?: number } = {}
): Promise<void> {
  const { delay = 200, stepSize = 500 } = options;

  let previousHeight = 0;
  let currentHeight = await page.evaluate(() => document.body.scrollHeight);
  let scrollAttempts = 0;
  const maxAttempts = 50; // Prevent infinite loops

  while (previousHeight !== currentHeight && scrollAttempts < maxAttempts) {
    previousHeight = currentHeight;
    await page.evaluate((step) => window.scrollBy(0, step), stepSize);
    await page.waitForTimeout(delay);
    currentHeight = await page.evaluate(() => document.body.scrollHeight);
    scrollAttempts++;
  }

  // Final scroll to absolute bottom
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await page.waitForTimeout(delay);

  // Scroll back to top for consistent screenshot
  await page.evaluate(() => window.scrollTo(0, 0));
  await page.waitForTimeout(100);
}

/**
 * Expand collapsible sections (accordions, details elements, etc.)
 * to reveal hidden content before capture.
 */
async function expandCollapsibleSections(page: Page, customSelectors?: string[]): Promise<void> {
  const defaultSelectors = [
    '[data-testid*="expand"]',
    '[aria-expanded="false"]',
    'button[class*="collapse"]',
    'details:not([open])',
    '[class*="accordion"]:not([class*="open"])',
    '[class*="expandable"]:not([class*="expanded"])',
  ];

  const allSelectors = [...defaultSelectors, ...(customSelectors || [])];

  for (const selector of allSelectors) {
    try {
      const elements = await page.locator(selector).all();
      for (const element of elements) {
        const isVisible = await element.isVisible({ timeout: 500 }).catch(() => false);
        if (isVisible) {
          await element.click().catch(() => {});
          await page.waitForTimeout(100);
        }
      }
    } catch {
      // Continue if selector fails
    }
  }
}

export const test = base.extend<{ scanner: ScannerContext }>({
  scanner: async ({ page }, use) => {
    const localStorageData = await loadLocalStorage();
    const capture = createNetworkCapture(page);

    // Inject localStorage before navigation
    await page.addInitScript((data) => {
      for (const [key, value] of Object.entries(data)) {
        localStorage.setItem(key, typeof value === 'string' ? value : JSON.stringify(value));
      }
    }, localStorageData);

    const scanPage = async (
      urlPath: string,
      pageName: string,
      options: ScanOptions = {}
    ): Promise<ScanResult> => {
      const {
        waitForSelector,
        waitForNetworkIdle = true,
        waitTimeout = 30000,
        takeScreenshot = true,
        takeSnapshot = true,
        failOnNotFound = true,
        // New options with defaults
        scrollToBottom = true,
        scrollDelay = 200,
        expandSections = true,
        expandSelectors,
        extractFormData: shouldExtractForms = true,
        exploreRelationships: shouldExploreRelationships = false,
        analyzeData: shouldAnalyzeData = true, // Default ON - analyze API responses for relationships
      } = options;

      capture.startCapture();

      const fullUrl = urlPath.startsWith('http') ? urlPath : `${ATAR_CONFIG.baseUrl}${urlPath}`;
      const navigationResponse = await page.goto(fullUrl, {
        waitUntil: waitForNetworkIdle ? 'networkidle' : 'load',
        timeout: waitTimeout,
      });

      if (waitForSelector) {
        await page.waitForSelector(waitForSelector, { timeout: waitTimeout });
      }

      // Scroll to bottom to trigger lazy loading
      if (scrollToBottom) {
        await scrollPageToBottom(page, { delay: scrollDelay });
      }

      // Expand collapsible sections to reveal hidden content
      if (expandSections) {
        await expandCollapsibleSections(page, expandSelectors);
        await page.waitForTimeout(500);
      }

      // Wait a bit for any delayed API calls
      await page.waitForTimeout(2000);

      capture.stopCapture();

      const endpoints = capture.formatEndpoints();

      if (failOnNotFound) {
        await assertNoNotFoundPage(
          page,
          pageName,
          fullUrl,
          endpoints,
          navigationResponse ? navigationResponse.status() : null,
        );
      }

      let screenshot: Buffer | undefined;
      let snapshot: string | undefined;

      if (takeScreenshot) {
        screenshot = await page.screenshot({ fullPage: true });
        await writeScreenshot(pageName, screenshot);
      }

      if (takeSnapshot) {
        try {
          // Try to get accessibility snapshot (may not be available in all Playwright versions)
          const accessibilitySnapshot = await page.accessibility?.snapshot?.();
          if (accessibilitySnapshot) {
            snapshot = JSON.stringify(accessibilitySnapshot, null, 2);
            await writeSnapshot(pageName, snapshot);
          }
        } catch (e) {
          console.log(`Could not capture accessibility snapshot for ${pageName}`);
        }
      }

      await writeEndpoints(pageName, endpoints);

      // Save API responses with request/response bodies
      const apiResponses = capture.getApiRequestsWithBodies();
      if (apiResponses.length > 0) {
        await writeApiResponses(pageName, apiResponses);
      }

      // Extract form data (field values, dropdown options, etc.)
      let formData: PageFormData | undefined;
      if (shouldExtractForms) {
        try {
          formData = await extractFormData(page);
          const dropdownOptions = await extractDropdownOptions(page);
          formData.dropdownOptions = dropdownOptions;
          await writeFormData(pageName, formData);
        } catch (e) {
          console.log(`Could not extract form data for ${pageName}: ${e}`);
        }
      }

      // Explore dropdown relationships and extract hardcoded values
      let relationships: PageRelationships | undefined;
      if (shouldExploreRelationships) {
        try {
          // Re-start capture for relationship exploration
          capture.startCapture();
          relationships = await explorePageRelationships(page, capture.requests);
          capture.stopCapture();
          await writeRelationships(pageName, relationships);
          console.log(`Explored relationships for ${pageName}: ${relationships.dropdownRelationships.length} dropdown relationships found`);
        } catch (e) {
          console.log(`Could not explore relationships for ${pageName}: ${e}`);
        }
      }

      // Analyze API responses for relationships, enums, and foreign keys
      let dataAnalysis: DataAnalysis | undefined;
      if (shouldAnalyzeData && apiResponses.length > 0) {
        try {
          dataAnalysis = await analyzePageData(page, apiResponses);
          await writeDataAnalysis(pageName, dataAnalysis);
        } catch (e) {
          console.log(`Could not analyze data for ${pageName}: ${e}`);
        }
      }

      return {
        pageName,
        pagePath: urlPath,
        endpoints,
        apiResponses,
        screenshot,
        snapshot,
        formData,
        relationships,
        dataAnalysis,
        timestamp: new Date().toISOString(),
      };
    };

    await use({
      page,
      config: ATAR_CONFIG,
      capture,
      scanPage,
    });
  },
});

export { expect } from '@playwright/test';
