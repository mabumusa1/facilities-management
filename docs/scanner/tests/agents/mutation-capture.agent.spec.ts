import { test, expect } from '../fixtures/scanner.fixture';

/**
 * Mutation Capture Agent - Triggers POST/PUT/DELETE endpoints
 *
 * This agent navigates to pages and interacts with action buttons
 * to capture mutation API calls that aren't triggered by simple page loads.
 */

// Pages that have mutation actions we need to capture
const MUTATION_PAGES = [
  // Dashboard - requires-attention endpoint
  {
    path: '/dashboard',
    name: 'mutation-dashboard-alerts',
    description: 'Dashboard with requires-attention API'
  },
  // Complaints - assign/cancel/resolve actions
  {
    path: '/dashboard/issues',
    name: 'mutation-issues-list',
    description: 'Issues list page'
  },
  // Notifications - mark-as-read actions
  {
    path: '/notifications',
    name: 'mutation-notifications',
    description: 'Notifications with mark-as-read'
  },
  // Marketplace admin visits
  {
    path: '/marketplace',
    name: 'mutation-marketplace-main',
    description: 'Marketplace main page'
  },
  // Leasing with expiring leases
  {
    path: '/leasing/leases',
    name: 'mutation-leasing-leases',
    description: 'Leasing leases list'
  },
];

test.describe('Mutation Capture Agent', () => {
  for (const route of MUTATION_PAGES) {
    test(`capture mutations on ${route.name}`, async ({ scanner }) => {
      const { page } = scanner;

      // Start capture before any navigation
      scanner.capture.startCapture();

      // Navigate to the page
      const fullUrl = `${scanner.config.baseUrl}${route.path}`;
      await page.goto(fullUrl, { waitUntil: 'networkidle', timeout: 30000 });

      // Wait for initial load
      await page.waitForTimeout(3000);

      // Try to interact with common action elements
      try {
        // Look for notification bell and click it
        const notificationBell = page.locator('[aria-label*="notification"], [data-testid*="notification"], .notification-icon, button:has-text("Notifications")').first();
        if (await notificationBell.isVisible({ timeout: 2000 })) {
          await notificationBell.click();
          await page.waitForTimeout(1500);
          console.log('Clicked notification bell');
        }
      } catch (e) {
        console.log('No notification bell found');
      }

      try {
        // Look for "Mark all as read" button
        const markAllBtn = page.locator('button:has-text("Mark all"), button:has-text("Read all"), [aria-label*="mark all"]').first();
        if (await markAllBtn.isVisible({ timeout: 2000 })) {
          await markAllBtn.click();
          await page.waitForTimeout(1500);
          console.log('Clicked mark all as read');
        }
      } catch (e) {
        console.log('No mark all button found');
      }

      try {
        // Look for dropdown/action menus
        const actionMenus = page.locator('[aria-label*="action"], [data-testid*="action"], .action-menu, button:has-text("Actions")');
        const count = await actionMenus.count();
        if (count > 0) {
          await actionMenus.first().click();
          await page.waitForTimeout(1000);
          console.log(`Found ${count} action menus`);
        }
      } catch (e) {
        console.log('No action menus found');
      }

      try {
        // Look for tab navigation that might load different data
        const tabs = page.locator('[role="tab"], .MuiTab-root');
        const tabCount = await tabs.count();
        if (tabCount > 1) {
          // Click through first few tabs
          for (let i = 0; i < Math.min(tabCount, 3); i++) {
            await tabs.nth(i).click();
            await page.waitForTimeout(1500);
          }
          console.log(`Clicked through ${Math.min(tabCount, 3)} tabs`);
        }
      } catch (e) {
        console.log('No tabs found');
      }

      // Final wait for any delayed API calls
      await page.waitForTimeout(2000);

      scanner.capture.stopCapture();

      const endpoints = scanner.capture.formatEndpoints();

      // Take screenshot
      const screenshot = await page.screenshot({ fullPage: true });

      // Write results
      const { writeEndpoints, writeScreenshot } = await import('../utils/output-writer');
      await writeEndpoints(route.name, endpoints);
      await writeScreenshot(route.name, screenshot);

      // Log mutation endpoints found
      const mutations = endpoints.filter(ep =>
        ep.includes('[POST]') || ep.includes('[PUT]') || ep.includes('[DELETE]')
      );

      console.log(`\n=== ${route.name} ===`);
      console.log(`Total endpoints: ${endpoints.length}`);
      console.log(`Mutation endpoints: ${mutations.length}`);
      if (mutations.length > 0) {
        console.log('Mutations found:');
        mutations.forEach(m => console.log(`  ${m}`));
      }
    });
  }
});

test.describe('Dashboard Alerts Capture', () => {
  test('capture requires-attention endpoint', async ({ scanner }) => {
    const { page } = scanner;

    scanner.capture.startCapture();

    // Navigate to dashboard
    await page.goto(`${scanner.config.baseUrl}/dashboard`, {
      waitUntil: 'networkidle',
      timeout: 30000
    });

    // Wait for dashboard to fully load
    await page.waitForTimeout(3000);

    // Look for "Requires Attention" section or alerts
    try {
      const alertSection = page.locator('text=Requires Attention, text=Attention Required, text=Expiring').first();
      if (await alertSection.isVisible({ timeout: 3000 })) {
        await alertSection.click();
        await page.waitForTimeout(2000);
        console.log('Clicked on attention section');
      }
    } catch (e) {
      console.log('No attention section found');
    }

    // Try navigating to expiring leases
    try {
      const expiringLink = page.locator('a[href*="expiring"], button:has-text("Expiring"), text=Expiring Leases').first();
      if (await expiringLink.isVisible({ timeout: 2000 })) {
        await expiringLink.click();
        await page.waitForTimeout(2000);
        console.log('Clicked on expiring leases link');
      }
    } catch (e) {
      console.log('No expiring leases link found');
    }

    await page.waitForTimeout(2000);
    scanner.capture.stopCapture();

    const endpoints = scanner.capture.formatEndpoints();

    const { writeEndpoints, writeScreenshot } = await import('../utils/output-writer');
    await writeEndpoints('mutation-dashboard-attention', endpoints);
    await writeScreenshot('mutation-dashboard-attention', await page.screenshot({ fullPage: true }));

    // Check for attention endpoints
    const attentionEndpoints = endpoints.filter(ep =>
      ep.includes('attention') || ep.includes('expiring')
    );

    console.log(`\n=== Dashboard Attention ===`);
    console.log(`Total endpoints: ${endpoints.length}`);
    console.log(`Attention endpoints: ${attentionEndpoints.length}`);
    attentionEndpoints.forEach(ep => console.log(`  ${ep}`));
  });
});

test.describe('Expiring Leases Capture', () => {
  test('capture expiring leases endpoint', async ({ scanner }) => {
    const { page } = scanner;

    scanner.capture.startCapture();

    // Navigate directly to expiring leases page
    await page.goto(`${scanner.config.baseUrl}/leasing/leases/expiring-leases`, {
      waitUntil: 'networkidle',
      timeout: 30000
    });

    await page.waitForTimeout(3000);

    // Try clicking on different lease types/filters
    try {
      const filterBtns = page.locator('[role="tab"], .filter-btn, button:has-text("All"), button:has-text("Expiring")');
      const count = await filterBtns.count();
      for (let i = 0; i < Math.min(count, 4); i++) {
        await filterBtns.nth(i).click();
        await page.waitForTimeout(1500);
      }
    } catch (e) {
      console.log('No filter buttons found');
    }

    await page.waitForTimeout(2000);
    scanner.capture.stopCapture();

    const endpoints = scanner.capture.formatEndpoints();

    const { writeEndpoints, writeScreenshot } = await import('../utils/output-writer');
    await writeEndpoints('mutation-expiring-leases', endpoints);
    await writeScreenshot('mutation-expiring-leases', await page.screenshot({ fullPage: true }));

    console.log(`\n=== Expiring Leases ===`);
    console.log(`Total endpoints: ${endpoints.length}`);

    const leaseEndpoints = endpoints.filter(ep => ep.includes('lease') || ep.includes('attention'));
    console.log(`Lease-related endpoints: ${leaseEndpoints.length}`);
    leaseEndpoints.forEach(ep => console.log(`  ${ep}`));
  });
});
