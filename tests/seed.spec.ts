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

    // Go directly to dashboard
    await page.goto(`${ATAR_CONFIG.baseUrl}/dashboard`);

    // Set all localStorage values
    await page.evaluate((data) => {
      for (const [key, value] of Object.entries(data)) {
        localStorage.setItem(key, value as string);
      }
    }, localStorageData);

    // Reload to apply auth
    await page.reload();
  });
});
