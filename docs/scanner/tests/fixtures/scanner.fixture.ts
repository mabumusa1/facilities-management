import { test as base, Page } from '@playwright/test';
import * as fs from 'fs/promises';
import * as path from 'path';
import { createNetworkCapture } from '../utils/network-capture';
import { writeEndpoints, writeScreenshot, writeSnapshot } from '../utils/output-writer';
import { ScannerContext, ScanOptions, ScanResult, ATAR_CONFIG, NetworkCapture } from '../utils/types';

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

      return {
        pageName,
        pagePath: urlPath,
        endpoints,
        screenshot,
        snapshot,
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
