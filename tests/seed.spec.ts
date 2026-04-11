import { test, expect } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

const ATAR_CONFIG = {
  baseUrl: 'https://goatar.com',
};

test.describe('Atar Dashboard', () => {
  test('seed', async ({ page }) => {
    // Load localStorage values from localstorage.json
    const localStoragePath = path.join(__dirname, 'localstorage.json');
    const localStorageData = JSON.parse(fs.readFileSync(localStoragePath, 'utf-8'));

    // Inject localStorage BEFORE page loads using addInitScript
    await page.addInitScript((data) => {
      for (const [key, value] of Object.entries(data)) {
        localStorage.setItem(key, value as string);
      }
    }, localStorageData);

    // Now navigate to dashboard - localStorage will already be set
    await page.goto(`${ATAR_CONFIG.baseUrl}/dashboard`);
  });
});
