import { test as setup, expect } from '@playwright/test';

const authFile = 'tests/e2e/.auth/user.json';

setup('authenticate', async ({ page }) => {
    await page.goto('/login');

    await page.locator('input[name="email"]').fill('test@example.com');
    await page.locator('input[name="password"]').fill('password');
    await page.getByRole('button', { name: 'Log in' }).click();

    await page.waitForURL('**/dashboard**');

    await expect(page.locator('body')).not.toContainText('These credentials do not match');

    await page.context().storageState({ path: authFile });
});
