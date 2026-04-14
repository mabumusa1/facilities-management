import { defineConfig, devices } from '@playwright/test';

/**
 * Read environment variables from file.
 * https://github.com/motdotla/dotenv
 */
// import dotenv from 'dotenv';
// import path from 'path';
// dotenv.config({ path: path.resolve(__dirname, '.env') });

/**
 * See https://playwright.dev/docs/test-configuration.
 */
export default defineConfig({
  testDir: './tests',
  /* Run tests in files in parallel */
  fullyParallel: true,
  /* Fail the build on CI if you accidentally left test.only in the source code. */
  forbidOnly: !!process.env.CI,
  /* Retry on CI only */
  retries: process.env.CI ? 2 : 0,
  /* Opt out of parallel tests on CI. */
  workers: process.env.CI ? 1 : undefined,
  /* Reporter to use. See https://playwright.dev/docs/test-reporters */
  reporter: 'html',
  /* Shared settings for all the projects below. See https://playwright.dev/docs/api/class-testoptions. */
  use: {
    /* Base URL to use in actions like `await page.goto('')`. */
    // baseURL: 'http://localhost:3000',

    /* Collect trace for all tests. See https://playwright.dev/docs/trace-viewer */
    trace: 'on',
    /* Record video of all tests */
    video: 'on',
    /* Capture screenshots on failure */
    screenshot: 'on',
  },

  /* Configure projects for scanning agents */
  projects: [
    // Default browser for general tests
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },

    // Scanning agents - run with: npx playwright test --project=dashboard-agent
    {
      name: 'dashboard-agent',
      testMatch: /dashboard\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'properties-agent',
      testMatch: /properties\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'properties-focus-agent',
      testMatch: /properties-focus\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'leasing-agent',
      testMatch: /leasing\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'settings-agent',
      testMatch: /settings\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'transactions-agent',
      testMatch: /transactions\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'marketplace-agent',
      testMatch: /marketplace\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'contacts-agent',
      testMatch: /contacts\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'auth-agent',
      testMatch: /auth-pages\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'legal-agent',
      testMatch: /legal-pages\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'mutation-agent',
      testMatch: /mutation-capture\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'marketplace-mutations-agent',
      testMatch: /marketplace-mutations\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },

    // API Mutation Agents - Direct API testing
    {
      name: 'properties-mutation-agent',
      testMatch: /properties\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'contacts-mutation-agent',
      testMatch: /contacts\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'leasing-mutation-agent',
      testMatch: /leasing\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'marketplace-mutation-agent',
      testMatch: /marketplace\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'requests-mutation-agent',
      testMatch: /requests\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'transactions-mutation-agent',
      testMatch: /transactions\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'settings-mutation-agent',
      testMatch: /settings\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'documents-mutation-agent',
      testMatch: /documents\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    // Run all mutation agents together
    {
      name: 'all-mutation-agents',
      testMatch: /\.mutation\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },

    // API Query Agents - GET response capture
    {
      name: 'properties-query-agent',
      testMatch: /properties\.query\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'contacts-query-agent',
      testMatch: /contacts\.query\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'leasing-query-agent',
      testMatch: /leasing\.query\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'transactions-query-agent',
      testMatch: /transactions\.query\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'marketplace-query-agent',
      testMatch: /marketplace\.query\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'requests-query-agent',
      testMatch: /requests\.query\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'common-query-agent',
      testMatch: /common\.query\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
    // Run all query agents together
    {
      name: 'all-query-agents',
      testMatch: /\.query\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },

    // Run all agents together
    {
      name: 'all-agents',
      testMatch: /\.agent\.spec\.ts/,
      use: { ...devices['Desktop Chrome'] },
    },
  ],

  /* Run your local dev server before starting the tests */
  // webServer: {
  //   command: 'npm run start',
  //   url: 'http://localhost:3000',
  //   reuseExistingServer: !process.env.CI,
  // },
});
