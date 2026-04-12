# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **website reverse engineering project** that uses Playwright to systematically capture pages and API requests from the Atar property management platform (goatar.com). The goal is to reconstruct the API surface, business rules, and UI structure for documentation or cloning purposes.

**Target application:** `index-BqM3yZMa.js` - A React-based property management SaaS with ~49 page components covering properties, leasing, transactions, contacts, and settings.

## Commands

```bash
# Run all scanning agents
npx playwright test --project=all-agents

# Run a specific module's scanning agent
npx playwright test --project=dashboard-agent
npx playwright test --project=properties-agent
npx playwright test --project=leasing-agent
npx playwright test --project=contacts-agent
npx playwright test --project=transactions-agent
npx playwright test --project=settings-agent
npx playwright test --project=marketplace-agent

# Run manual exploration (interactive mode with page.pause())
npx playwright test tests/manual-explore.spec.ts

# Run a single test file
npx playwright test tests/agents/dashboard.agent.spec.ts

# View test report
npx playwright show-report
```

## Architecture

### Directory Structure

```
src/
├── pages/{page-name}/           # Captured data per page
│   ├── api/endpoints.json       # Network requests: [METHOD] URL => [STATUS]
│   ├── screenshot.png           # Full-page screenshot
│   └── snapshot.yml             # Accessibility tree (optional)
├── api/                         # Consolidated API documentation
│   ├── endpoints-from-browser.json
│   ├── endpoints-from-logs.json
│   └── endpoints-from-react.json
└── routes.json                  # 280+ route definitions

tests/
├── fixtures/scanner.fixture.ts  # Custom Playwright fixture with scanPage()
├── utils/
│   ├── types.ts                 # ScanResult, NetworkCapture, AtarConfig
│   ├── network-capture.ts       # Response interception and formatting
│   └── output-writer.ts         # File output to src/pages/
├── agents/*.agent.spec.ts       # Module-specific scanning tests
├── manual-explore.spec.ts       # Interactive exploration with pause()
└── localstorage.json            # Authentication/session data

atar-cloner/                     # Previous exploration work (reference)
```

### Scanning Flow

1. **Authentication**: `tests/localstorage.json` contains pre-captured auth tokens and session data that gets injected via `addInitScript` before navigation
2. **Network Capture**: All HTTP responses are intercepted and formatted as `[METHOD] URL => [STATUS]`
3. **Page Scan**: Navigate to URL, wait for network idle, capture endpoints + screenshot + accessibility snapshot
4. **Output**: Results written to `src/pages/{pageName}/api/endpoints.json`

### Scanner Fixture API

```typescript
import { test, expect } from '../fixtures/scanner.fixture';

test('scan page', async ({ scanner }) => {
  const result = await scanner.scanPage('/dashboard', 'dashboard', {
    waitForNetworkIdle: true,  // default: true
    takeScreenshot: true,       // default: true
    takeSnapshot: true,         // default: true
    waitTimeout: 30000,         // default: 30000ms
    waitForSelector: '.content' // optional: wait for specific element
  });

  expect(result.endpoints.length).toBeGreaterThan(0);
});
```

### Agent Pattern

Each agent test file scans a module's routes:
```typescript
const ROUTES = [
  { path: '/dashboard', name: 'dashboard' },
  { path: '/dashboard/reports', name: 'dashboard-reports' },
];

test.describe('Module Agent', () => {
  for (const route of ROUTES) {
    test(`scan ${route.name}`, async ({ scanner }) => {
      const result = await scanner.scanPage(route.path, route.name);
      expect(result.endpoints.length).toBeGreaterThan(0);
    });
  }
});
```

## API Configuration

- **Base URL**: `https://goatar.com`
- **API URL**: `https://api.goatar.com/api-management`
- **Auth**: Bearer token + X-Tenant header
- **Tenant**: `testbusiness123`

## Key Files

- `playwright.config.ts` - Projects for each scanning agent
- `tests/fixtures/scanner.fixture.ts` - Core scanning logic
- `tests/localstorage.json` - Auth tokens and user session
- `src/routes.json` - Complete route mapping with dynamic params
- `atar-cloner/API-EXPLORATION-SUMMARY.md` - Documented API endpoints with request/response schemas
