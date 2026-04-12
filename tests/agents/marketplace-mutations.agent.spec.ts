import { test, expect } from '../fixtures/scanner.fixture';

/**
 * Marketplace Mutations Agent - Captures marketplace admin POST/PUT endpoints
 *
 * Target endpoints:
 * - /marketplace/admin/communities/list/${e}
 * - /marketplace/admin/communities/unlist/${e}
 * - /marketplace/admin/units/prices-visibility/${e}
 * - /marketplace/admin/visits/assign/owner-visit/${e}
 * - /marketplace/admin/visits/completed/${e}
 * - /marketplace/admin/visits/rejected/${e}
 * - /marketplace/admin/settings/sales/store
 * - /marketplace/admin/settings/banks/store
 */

const MARKETPLACE_ADMIN_PAGES = [
  // Admin communities
  { path: '/marketplace/listing', name: 'mp-admin-listing' },
  // Admin units
  { path: '/marketplace', name: 'mp-admin-main' },
  // Admin visits - try with sample ID
  { path: '/dashboard/visits', name: 'mp-admin-visits-dashboard' },
  { path: '/dashboard/bookings', name: 'mp-admin-bookings' },
  // Settings pages
  { path: '/settings/sales-details', name: 'mp-settings-sales' },
  { path: '/settings/bank-details', name: 'mp-settings-banks' },
  { path: '/settings/visits-details', name: 'mp-settings-visits' },
];

test.describe('Marketplace Admin Mutations', () => {
  for (const route of MARKETPLACE_ADMIN_PAGES) {
    test(`capture ${route.name} mutations`, async ({ scanner }) => {
      const { page } = scanner;

      scanner.capture.startCapture();

      await page.goto(`${scanner.config.baseUrl}${route.path}`, {
        waitUntil: 'networkidle',
        timeout: 30000
      });

      await page.waitForTimeout(2000);

      // Try to find and click action buttons
      try {
        // Look for list/unlist buttons
        const listBtns = page.locator('button:has-text("List"), button:has-text("Unlist"), button:has-text("Publish")');
        if (await listBtns.first().isVisible({ timeout: 2000 })) {
          console.log('Found list/unlist buttons');
        }
      } catch (e) {}

      try {
        // Look for save/store buttons (for settings pages)
        const saveBtns = page.locator('button:has-text("Save"), button:has-text("Update"), button[type="submit"]');
        const count = await saveBtns.count();
        if (count > 0) {
          console.log(`Found ${count} save buttons`);
        }
      } catch (e) {}

      try {
        // Look for price visibility toggles
        const toggles = page.locator('[role="switch"], input[type="checkbox"], .MuiSwitch-root');
        const count = await toggles.count();
        if (count > 0) {
          console.log(`Found ${count} toggles/switches`);
        }
      } catch (e) {}

      try {
        // Look for visit action buttons
        const visitBtns = page.locator('button:has-text("Assign"), button:has-text("Complete"), button:has-text("Reject"), button:has-text("Accept")');
        const count = await visitBtns.count();
        if (count > 0) {
          console.log(`Found ${count} visit action buttons`);
        }
      } catch (e) {}

      // Navigate through tabs if present
      try {
        const tabs = page.locator('[role="tab"]');
        const tabCount = await tabs.count();
        for (let i = 0; i < Math.min(tabCount, 5); i++) {
          await tabs.nth(i).click();
          await page.waitForTimeout(1500);
        }
      } catch (e) {}

      await page.waitForTimeout(2000);
      scanner.capture.stopCapture();

      const endpoints = scanner.capture.formatEndpoints();

      const { writeEndpoints, writeScreenshot } = await import('../utils/output-writer');
      await writeEndpoints(route.name, endpoints);
      await writeScreenshot(route.name, await page.screenshot({ fullPage: true }));

      // Log marketplace-specific endpoints
      const mpEndpoints = endpoints.filter(ep =>
        ep.includes('marketplace') || ep.includes('visits') || ep.includes('settings')
      );

      console.log(`\n=== ${route.name} ===`);
      console.log(`Total endpoints: ${endpoints.length}`);
      console.log(`Marketplace endpoints: ${mpEndpoints.length}`);
      mpEndpoints.forEach(ep => console.log(`  ${ep}`));
    });
  }
});

test.describe('Marketplace Communities Admin', () => {
  test('capture community admin endpoints', async ({ scanner }) => {
    const { page } = scanner;

    scanner.capture.startCapture();

    // Navigate to marketplace listing (communities)
    await page.goto(`${scanner.config.baseUrl}/marketplace/listing`, {
      waitUntil: 'networkidle',
      timeout: 30000
    });

    await page.waitForTimeout(2000);

    // Try to click on first community card/row to get details
    try {
      const communityCards = page.locator('.MuiCard-root, [data-testid*="community"], tr').first();
      if (await communityCards.isVisible({ timeout: 2000 })) {
        await communityCards.click();
        await page.waitForTimeout(2000);
        console.log('Clicked on community card');
      }
    } catch (e) {}

    // Look for pagination and load more data
    try {
      const pagination = page.locator('[aria-label="Go to next page"], button:has-text("Next"), .MuiPagination-root button');
      if (await pagination.first().isVisible({ timeout: 2000 })) {
        await pagination.first().click();
        await page.waitForTimeout(2000);
        console.log('Clicked pagination');
      }
    } catch (e) {}

    await page.waitForTimeout(2000);
    scanner.capture.stopCapture();

    const endpoints = scanner.capture.formatEndpoints();

    const { writeEndpoints, writeScreenshot } = await import('../utils/output-writer');
    await writeEndpoints('mp-communities-admin', endpoints);
    await writeScreenshot('mp-communities-admin', await page.screenshot({ fullPage: true }));

    console.log(`\n=== Marketplace Communities Admin ===`);
    console.log(`Total endpoints: ${endpoints.length}`);

    const communityEndpoints = endpoints.filter(ep => ep.includes('communit'));
    console.log(`Community endpoints: ${communityEndpoints.length}`);
    communityEndpoints.forEach(ep => console.log(`  ${ep}`));
  });
});

test.describe('Marketplace Units Admin', () => {
  test('capture units admin endpoints', async ({ scanner }) => {
    const { page } = scanner;

    scanner.capture.startCapture();

    // Navigate to properties units (which connects to marketplace)
    await page.goto(`${scanner.config.baseUrl}/properties-list/units`, {
      waitUntil: 'networkidle',
      timeout: 30000
    });

    await page.waitForTimeout(2000);

    // Try clicking on unit rows
    try {
      const unitRows = page.locator('tr, .unit-card, [data-testid*="unit"]').first();
      if (await unitRows.isVisible({ timeout: 2000 })) {
        await unitRows.click();
        await page.waitForTimeout(2000);
        console.log('Clicked on unit row');
      }
    } catch (e) {}

    // Look for marketplace listing toggle
    try {
      const mpToggle = page.locator('button:has-text("Marketplace"), [aria-label*="marketplace"]');
      if (await mpToggle.first().isVisible({ timeout: 2000 })) {
        await mpToggle.first().click();
        await page.waitForTimeout(2000);
        console.log('Clicked marketplace toggle');
      }
    } catch (e) {}

    await page.waitForTimeout(2000);
    scanner.capture.stopCapture();

    const endpoints = scanner.capture.formatEndpoints();

    const { writeEndpoints, writeScreenshot } = await import('../utils/output-writer');
    await writeEndpoints('mp-units-admin', endpoints);
    await writeScreenshot('mp-units-admin', await page.screenshot({ fullPage: true }));

    console.log(`\n=== Marketplace Units Admin ===`);
    console.log(`Total endpoints: ${endpoints.length}`);

    const unitEndpoints = endpoints.filter(ep => ep.includes('unit') || ep.includes('price'));
    console.log(`Unit endpoints: ${unitEndpoints.length}`);
    unitEndpoints.forEach(ep => console.log(`  ${ep}`));
  });
});
