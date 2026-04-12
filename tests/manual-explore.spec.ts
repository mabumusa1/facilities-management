import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

const ATAR_CONFIG = {
  baseUrl: 'https://goatar.com',
};

test.describe('Atar Manual Exploration', () => {
  test('explore with tracing', async ({ page, context }) => {
    // Load localStorage data for authentication
    const localStoragePath = path.join(__dirname, 'localstorage.json');
    const localStorageData = JSON.parse(fs.readFileSync(localStoragePath, 'utf-8'));

    // Inject localStorage before page loads
    await page.addInitScript((data) => {
      for (const [key, value] of Object.entries(data)) {
        localStorage.setItem(key, value as string);
      }
    }, localStorageData);

    // Tracing is already enabled in playwright.config.ts

    // Navigate to dashboard
    await page.goto(`${ATAR_CONFIG.baseUrl}/dashboard`);

    // Wait for page to load
    await page.waitForLoadState('networkidle');

    // Switch to English if needed
    const englishBtn = page.getByRole('button', { name: 'english' });
    if (await englishBtn.isVisible()) {
      await englishBtn.click();
      await page.waitForLoadState('networkidle');
    }

    // Pause here - user can manually interact with the browser
    // All interactions will be recorded in the trace
    console.log('Browser paused for manual exploration. Press resume in Playwright Inspector when done.');
    await page.pause();

    // Trace is automatically saved by playwright.config.ts
  });
});
