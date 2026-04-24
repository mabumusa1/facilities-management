import { test, expect } from '@playwright/test';

test.describe('E2E Smoke', () => {
    test('authenticated user can access dashboard', async ({ page }) => {
        const response = await page.goto('/dashboard');

        expect(response).not.toBeNull();
        expect(response!.status()).toBeLessThan(400);

        await expect(page).toHaveURL(/\/dashboard/);
    });
});
