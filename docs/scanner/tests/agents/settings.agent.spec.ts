import { test, expect } from '../fixtures/scanner.fixture';

const SETTINGS_ROUTES = [
  { path: '/settings', name: 'settings' },
  { path: '/settings/facilities', name: 'settings-facilities' },
  { path: '/settings/forms', name: 'settings-forms' },
];

const SETTINGS_TABS = [
  'invoice',
  'service-request',
  'visitor-request',
  'bank-details',
  'visits-details',
  'sales-details',
];

test.describe('Settings Agent', () => {
  for (const route of SETTINGS_ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name, {
        waitForNetworkIdle: true,
        takeScreenshot: true,
        takeSnapshot: true,
      });

      expect(result.endpoints.length).toBeGreaterThan(0);
      console.log(`Scanned ${route.name}: ${result.endpoints.length} endpoints captured`);
    });
  }

  // Scan settings with all tabs
  test('scan settings tabs', async ({ scanner, page }) => {
    // First navigate to settings
    await scanner.scanPage('/settings', 'settings-main', {
      waitForNetworkIdle: true,
    });

    // Click through each tab and capture
    for (const tab of SETTINGS_TABS) {
      try {
        // Try to find and click the tab
        const tabSelector = `[data-tab="${tab}"], [href*="${tab}"], button:has-text("${tab}")`;
        const tabElement = page.locator(tabSelector).first();

        if (await tabElement.isVisible({ timeout: 2000 })) {
          scanner.capture.startCapture();
          await tabElement.click();
          await page.waitForLoadState('networkidle');
          await page.waitForTimeout(1000);
          scanner.capture.stopCapture();

          const endpoints = scanner.capture.formatEndpoints();
          const { writeEndpoints } = await import('../utils/output-writer');
          await writeEndpoints(`settings-tab-${tab}`, endpoints);

          console.log(`Scanned settings tab ${tab}: ${endpoints.length} endpoints`);
        }
      } catch (e) {
        console.log(`Could not scan tab ${tab}`);
      }
    }
  });
});
