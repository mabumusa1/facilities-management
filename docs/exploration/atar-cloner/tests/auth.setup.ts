import { test as setup, expect } from '@playwright/test';
import path from 'path';

const authFile = path.join(__dirname, '../playwright/.auth/user.json');

// Atar API credentials
const ATAR_CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://goatar.com',
  apiUrl: 'https://api.goatar.com/api-management',
  language: 'en',
};

setup('authenticate', async ({ page }) => {
  // Set English language preference in headers
  await page.setExtraHTTPHeaders({
    'Accept-Language': 'en',
  });

  // Navigate to the app
  await page.goto(ATAR_CONFIG.baseUrl);

  // Set authentication and language via localStorage
  await page.evaluate((config) => {
    localStorage.setItem('token', config.token);
    localStorage.setItem('tenant', config.tenant);
    localStorage.setItem('language', config.language);
    localStorage.setItem('i18nextLng', config.language);
    localStorage.setItem('locale', config.language);
  }, ATAR_CONFIG);

  // Set auth cookies
  await page.context().addCookies([
    {
      name: 'auth_token',
      value: ATAR_CONFIG.token,
      domain: 'goatar.com',
      path: '/',
    },
    {
      name: 'tenant',
      value: ATAR_CONFIG.tenant,
      domain: 'goatar.com',
      path: '/',
    },
    {
      name: 'language',
      value: ATAR_CONFIG.language,
      domain: 'goatar.com',
      path: '/',
    },
  ]);

  // Navigate to dashboard to verify authentication
  await page.goto(`${ATAR_CONFIG.baseUrl}/dashboard`);

  // Wait for the page to load authenticated content
  await expect(page.locator('text=Dashboard')).toBeVisible({ timeout: 10000 });

  // Save authentication state
  await page.context().storageState({ path: authFile });
});
