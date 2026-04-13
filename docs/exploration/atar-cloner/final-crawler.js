const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const CONFIG = {
  baseUrl: 'https://goatar.com',
  businessName: 'testbusiness123',
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  outputDir: './output-final',
  screenshotDir: './output-final/screenshots',
};

const visitedPaths = new Set();
const sitemap = [];
let screenshotCount = 0;

function log(msg, type = 'info') {
  const icons = { info: '📘', success: '✅', error: '❌', nav: '🔗', shot: '📸', click: '👆' };
  console.log(`[${new Date().toISOString()}] ${icons[type] || '📘'} ${msg}`);
}

function sanitize(str) {
  return str.replace(/[^a-zA-Z0-9\u0600-\u06FF]/g, '_').substring(0, 50) || 'page';
}

function setup() {
  [CONFIG.outputDir, CONFIG.screenshotDir].forEach(dir => {
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
  });
}

async function screenshot(page, name) {
  await page.waitForTimeout(1000);
  const filename = `${String(++screenshotCount).padStart(3, '0')}_${sanitize(name)}.png`;
  await page.screenshot({ path: path.join(CONFIG.screenshotDir, filename), fullPage: true });
  log(`Screenshot: ${filename}`, 'shot');
  return filename;
}

async function recordPage(page, name, type = 'page', parent = null) {
  const state = await page.evaluate(() => ({
    url: location.href,
    path: location.pathname,
    title: document.title,
  }));

  const pathKey = state.path.split('?')[0];

  if (visitedPaths.has(pathKey)) {
    return false;
  }

  visitedPaths.add(pathKey);
  const ssName = await screenshot(page, name);

  sitemap.push({
    ...state,
    screenshot: ssName,
    name,
    type,
    parent,
  });

  log(`Recorded: "${name}" -> ${state.path}`, 'success');
  return true;
}

// Click by text content
async function clickByText(page, text) {
  const elements = await page.$$(`text="${text}"`);
  if (elements.length > 0) {
    await elements[0].click();
    return true;
  }
  return false;
}

async function main() {
  setup();
  log('Starting Final Atar Crawler...');

  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1920, height: 1080 } });
  const page = await context.newPage();

  // Auth
  log('Setting up authentication...');
  await page.goto(CONFIG.baseUrl);
  await page.evaluate((c) => {
    localStorage.setItem('authToken', c.token);
    localStorage.setItem('access_token', c.token);
    localStorage.setItem('tenantDomain', c.businessName);
    localStorage.setItem('tenant', c.businessName);
  }, CONFIG);
  await context.addCookies([
    { name: 'authToken', value: CONFIG.token, domain: 'goatar.com', path: '/' },
    { name: 'access_token', value: CONFIG.token, domain: 'goatar.com', path: '/' },
    { name: 'tenantDomain', value: CONFIG.businessName, domain: 'goatar.com', path: '/' },
  ]);

  // Go to dashboard
  log('Navigating to dashboard...', 'nav');
  await page.goto(`${CONFIG.baseUrl}/dashboard`, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(2000);

  // Switch to English
  log('Switching to English...', 'click');
  try {
    await page.click('text="English"');
    await page.waitForTimeout(1500);
  } catch (e) {
    log('Already in English or switch failed', 'info');
  }

  // Record dashboard
  await recordPage(page, 'Dashboard', 'main');

  // Define navigation paths to explore
  const navPaths = [
    // Main nav items
    { click: 'Requests', sub: ['Visitor Access', 'Facility Bookings'] },
    { click: 'Communication', sub: ['Announcements', 'Messages', 'Notifications'] },
    { click: 'Users', sub: ['Tenants', 'Owners', 'Staff', 'Managers', 'Professionals'] },
    { click: 'Support', sub: ['Tickets', 'FAQ', 'Help'] },
    // Arabic fallback
    { click: 'الطلبات', sub: ['طلبات دخول الزوار', 'حجوزات المرافق'] },
    { click: 'التواصل', sub: ['الإعلانات', 'الرسائل', 'الإشعارات'] },
    { click: 'المستخدمين', sub: ['المستأجرين', 'الملاك', 'الموظفين', 'المديرين', 'المهنيين'] },
    { click: 'الدعم الفني', sub: ['التذاكر', 'الأسئلة الشائعة', 'المساعدة'] },
  ];

  // Click each nav item
  for (const nav of navPaths) {
    log(`\nTrying: "${nav.click}"`, 'click');

    try {
      // Try to click the parent
      const clicked = await clickByText(page, nav.click);

      if (clicked) {
        await page.waitForTimeout(1000);

        // Try to record the page
        await recordPage(page, nav.click, 'page');

        // Try sub-items
        for (const sub of nav.sub) {
          log(`  Trying sub: "${sub}"`, 'click');

          try {
            const subClicked = await clickByText(page, sub);
            if (subClicked) {
              await page.waitForTimeout(1000);
              await recordPage(page, sub, 'subpage', nav.click);
            }
          } catch (e) {
            // Sub item not found
          }

          // Return to dashboard
          await page.goto(`${CONFIG.baseUrl}/dashboard`, { waitUntil: 'domcontentloaded', timeout: 15000 });
          await page.waitForTimeout(500);

          // Re-click parent to keep dropdown open
          await clickByText(page, nav.click);
          await page.waitForTimeout(500);
        }
      }

      // Return to dashboard
      await page.goto(`${CONFIG.baseUrl}/dashboard`, { waitUntil: 'domcontentloaded', timeout: 15000 });
      await page.waitForTimeout(500);

    } catch (e) {
      log(`Error: ${e.message}`, 'error');
    }
  }

  // Direct URL exploration for common routes
  log('\nExploring direct URLs...', 'nav');

  const directUrls = [
    '/visitor-access',
    '/dashboard/bookings',
    '/announcements',
    '/messages',
    '/notifications',
    '/contacts',
    '/contacts/tenants',
    '/contacts/owners',
    '/contacts/managers',
    '/contacts/professionals',
    '/support',
    '/support/tickets',
    '/settings',
    '/settings/profile',
    '/settings/account',
    '/leasing',
    '/leasing/visits',
    '/leasing/applications',
    '/leasing/quotes',
    '/leasing/leases',
    '/properties',
    '/accounting',
    '/accounting/invoices',
    '/accounting/payments',
    '/reporting',
  ];

  for (const url of directUrls) {
    const fullUrl = CONFIG.baseUrl + url;
    log(`Trying: ${url}`, 'nav');

    try {
      await page.goto(fullUrl, { waitUntil: 'domcontentloaded', timeout: 10000 });
      await page.waitForTimeout(1500);

      // Check if it's a valid page (not 404, not redirected to login)
      const currentUrl = page.url();
      const title = await page.title();

      if (!title.includes('404') && !currentUrl.includes('/login') && !currentUrl.includes('/signup')) {
        await recordPage(page, url.replace('/', '').replace(/\//g, '_') || 'root', 'direct');
      }

    } catch (e) {
      // URL didn't work
    }
  }

  await browser.close();

  // Generate outputs
  const output = {
    generatedAt: new Date().toISOString(),
    baseUrl: CONFIG.baseUrl,
    businessName: CONFIG.businessName,
    totalPages: sitemap.length,
    totalScreenshots: screenshotCount,
    routes: Array.from(visitedPaths).sort(),
    pages: sitemap,
  };

  fs.writeFileSync(path.join(CONFIG.outputDir, 'sitemap.json'), JSON.stringify(output, null, 2));

  // Markdown sitemap
  let md = `# Atar Application Sitemap\n\n`;
  md += `**Generated:** ${new Date().toISOString()}\n\n`;
  md += `**Business:** ${CONFIG.businessName}\n\n`;
  md += `## Summary\n\n`;
  md += `- **Total Pages:** ${sitemap.length}\n`;
  md += `- **Screenshots:** ${screenshotCount}\n`;
  md += `- **Unique Routes:** ${visitedPaths.size}\n\n`;

  md += `## Routes\n\n`;
  Array.from(visitedPaths).sort().forEach(r => { md += `- \`${r}\`\n`; });

  md += `\n## Screenshots\n\n`;
  md += `| # | Page | Path | Screenshot |\n`;
  md += `|---|------|------|------------|\n`;
  sitemap.forEach((p, i) => {
    md += `| ${i + 1} | ${p.name} | \`${p.path}\` | ![](screenshots/${p.screenshot}) |\n`;
  });

  md += `\n## Page Details\n\n`;
  sitemap.forEach(p => {
    md += `### ${p.name}\n\n`;
    md += `- **URL:** ${p.url}\n`;
    md += `- **Path:** \`${p.path}\`\n`;
    md += `- **Title:** ${p.title}\n`;
    md += `- **Type:** ${p.type}\n`;
    if (p.parent) md += `- **Parent:** ${p.parent}\n`;
    md += `- **Screenshot:** \`${p.screenshot}\`\n\n`;
    md += `![${p.name}](screenshots/${p.screenshot})\n\n`;
    md += `---\n\n`;
  });

  fs.writeFileSync(path.join(CONFIG.outputDir, 'SITEMAP.md'), md);

  log('\n' + '='.repeat(60));
  log('CRAWL COMPLETE!', 'success');
  log(`Pages Captured: ${sitemap.length}`);
  log(`Screenshots: ${screenshotCount}`);
  log(`Unique Routes: ${visitedPaths.size}`);
  log(`Output Directory: ${CONFIG.outputDir}`);
  log('='.repeat(60));

  // List all captured pages
  log('\nCaptured Pages:');
  sitemap.forEach(p => log(`  - ${p.name}: ${p.path}`));
}

main().catch(e => {
  log(`Fatal: ${e.message}`, 'error');
  console.error(e);
  process.exit(1);
});
