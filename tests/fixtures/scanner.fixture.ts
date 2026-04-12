import { test as base, Page } from '@playwright/test';
import * as fs from 'fs/promises';
import * as path from 'path';
import { createNetworkCapture } from '../utils/network-capture';
import { writeEndpoints, writeScreenshot, writeSnapshot } from '../utils/output-writer';
import { ScannerContext, ScanOptions, ScanResult, ATAR_CONFIG, NetworkCapture } from '../utils/types';

async function loadLocalStorage(): Promise<Record<string, string>> {
  const filePath = path.join(process.cwd(), 'tests', 'localstorage.json');
  const content = await fs.readFile(filePath, 'utf-8');
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
      } = options;

      capture.startCapture();

      const fullUrl = urlPath.startsWith('http') ? urlPath : `${ATAR_CONFIG.baseUrl}${urlPath}`;
      await page.goto(fullUrl, { waitUntil: waitForNetworkIdle ? 'networkidle' : 'load', timeout: waitTimeout });

      if (waitForSelector) {
        await page.waitForSelector(waitForSelector, { timeout: waitTimeout });
      }

      // Wait a bit for any delayed API calls
      await page.waitForTimeout(2000);

      capture.stopCapture();

      const endpoints = capture.formatEndpoints();
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
