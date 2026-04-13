# Authentication

Atar uses Bearer token authentication for all API requests. This guide explains how to authenticate with the Atar API and reuse authentication state across your automation scripts.

## Introduction

The Atar API requires two key headers for authentication:
- **Authorization**: Bearer token for user authentication
- **X-Tenant**: Tenant identifier for multi-tenant isolation

> **Warning**: The authentication token and tenant identifier are sensitive credentials. Never commit them to version control or share them publicly.

## Setup

Create an `auth/` directory and add it to `.gitignore`:

```bash
mkdir -p auth
echo 'auth/' >> .gitignore
```

## Core Concepts

### Authentication Headers

Every API request must include these headers:

```javascript
const headers = {
  'Authorization': 'Bearer YOUR_API_TOKEN',
  'X-Tenant': 'your-tenant-id',
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'Accept-Language': 'en' // or 'ar' for Arabic responses
};
```

### Base URL

All API endpoints are relative to:

```
https://api.goatar.com/api-management
```

## Basic: Shared Configuration

The simplest approach is to create a shared configuration file that all scripts can import.

### Create `auth/config.js`

```javascript
// auth/config.js
module.exports = {
  token: process.env.ATAR_API_TOKEN || 'YOUR_API_TOKEN',
  tenant: process.env.ATAR_TENANT || 'your-tenant-id',
  baseUrl: 'https://api.goatar.com/api-management',

  getHeaders() {
    return {
      'Authorization': `Bearer ${this.token}`,
      'X-Tenant': this.tenant,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Accept-Language': 'en',
    };
  }
};
```

### Using the Configuration

```javascript
// scripts/create-tenant.js
const config = require('../auth/config');
const https = require('https');

async function createTenant(data) {
  const options = {
    hostname: 'api.goatar.com',
    path: '/api-management/rf/tenants',
    method: 'POST',
    headers: config.getHeaders(),
  };

  // ... make request
}
```

## Moderate: Environment-Based Authentication

For different environments (development, staging, production), use environment variables.

### Create `.env` file

```bash
# .env.development
ATAR_API_TOKEN=your-dev-token
ATAR_TENANT=dev-tenant-id
ATAR_BASE_URL=https://api.goatar.com/api-management

# .env.production
ATAR_API_TOKEN=your-prod-token
ATAR_TENANT=prod-tenant-id
ATAR_BASE_URL=https://api.goatar.com/api-management
```

### Create `auth/env-config.js`

```javascript
// auth/env-config.js
require('dotenv').config({
  path: `.env.${process.env.NODE_ENV || 'development'}`
});

module.exports = {
  token: process.env.ATAR_API_TOKEN,
  tenant: process.env.ATAR_TENANT,
  baseUrl: process.env.ATAR_BASE_URL,

  getHeaders() {
    if (!this.token || !this.tenant) {
      throw new Error('Missing ATAR_API_TOKEN or ATAR_TENANT environment variables');
    }
    return {
      'Authorization': `Bearer ${this.token}`,
      'X-Tenant': this.tenant,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Accept-Language': 'en',
    };
  }
};
```

## Advanced: API Client Class

For complex applications, create a reusable API client class.

### Create `auth/api-client.js`

```javascript
// auth/api-client.js
const https = require('https');

class AtarApiClient {
  constructor(options = {}) {
    this.token = options.token || process.env.ATAR_API_TOKEN;
    this.tenant = options.tenant || process.env.ATAR_TENANT;
    this.baseUrl = options.baseUrl || 'https://api.goatar.com/api-management';
    this.language = options.language || 'en';
  }

  getHeaders() {
    return {
      'Authorization': `Bearer ${this.token}`,
      'X-Tenant': this.tenant,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Accept-Language': this.language,
    };
  }

  async request(method, endpoint, body = null) {
    return new Promise((resolve, reject) => {
      const url = new URL(this.baseUrl + endpoint);
      const options = {
        hostname: url.hostname,
        port: 443,
        path: url.pathname + url.search,
        method,
        headers: this.getHeaders(),
      };

      const bodyStr = body ? JSON.stringify(body) : null;
      if (bodyStr) {
        options.headers['Content-Length'] = Buffer.byteLength(bodyStr);
      }

      const req = https.request(options, (res) => {
        let data = '';
        res.on('data', chunk => data += chunk);
        res.on('end', () => {
          try {
            resolve({ status: res.statusCode, data: JSON.parse(data) });
          } catch {
            resolve({ status: res.statusCode, data });
          }
        });
      });

      req.on('error', reject);
      if (bodyStr) req.write(bodyStr);
      req.end();
    });
  }

  // Convenience methods
  get(endpoint) { return this.request('GET', endpoint); }
  post(endpoint, body) { return this.request('POST', endpoint, body); }
  put(endpoint, body) { return this.request('PUT', endpoint, body); }
  delete(endpoint) { return this.request('DELETE', endpoint); }
}

module.exports = AtarApiClient;
```

### Using the API Client

```javascript
// scripts/example.js
const AtarApiClient = require('../auth/api-client');

const client = new AtarApiClient({
  token: 'your-api-token',
  tenant: 'your-tenant-id',
});

async function main() {
  // Get all tenants
  const tenants = await client.get('/rf/tenants');
  console.log('Tenants:', tenants.data);

  // Create a new tenant
  const newTenant = await client.post('/rf/tenants', {
    first_name: 'John',
    last_name: 'Doe',
    phone_number: '+966512345678',
    email: 'john@example.com',
    national_id: '1234567890',
  });
  console.log('Created tenant:', newTenant.data);
}

main().catch(console.error);
```

## Multiple Roles

For testing different user roles (admin, property manager, tenant), create separate client instances.

### Create `auth/roles.js`

```javascript
// auth/roles.js
const AtarApiClient = require('./api-client');

// Admin user with full access
const adminClient = new AtarApiClient({
  token: process.env.ATAR_ADMIN_TOKEN,
  tenant: process.env.ATAR_TENANT,
});

// Property manager with limited access
const managerClient = new AtarApiClient({
  token: process.env.ATAR_MANAGER_TOKEN,
  tenant: process.env.ATAR_TENANT,
});

// Tenant user with restricted access
const tenantClient = new AtarApiClient({
  token: process.env.ATAR_TENANT_TOKEN,
  tenant: process.env.ATAR_TENANT,
});

module.exports = { adminClient, managerClient, tenantClient };
```

### Using Multiple Roles

```javascript
// scripts/role-test.js
const { adminClient, tenantClient } = require('../auth/roles');

async function testRolePermissions() {
  // Admin can list all units
  const adminUnits = await adminClient.get('/rf/units');
  console.log('Admin sees', adminUnits.data.data.length, 'units');

  // Tenant might have restricted view
  const tenantUnits = await tenantClient.get('/rf/units');
  console.log('Tenant sees', tenantUnits.data.data?.length || 0, 'units');
}
```

## Browser Authentication

For browser-based automation (Playwright, Puppeteer), you may need to authenticate through the UI.

### Playwright Example

```javascript
// auth/browser-setup.js
const { chromium } = require('playwright');
const fs = require('fs');

const AUTH_FILE = 'auth/storage-state.json';

async function authenticate() {
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();

  // Navigate to login page
  await page.goto('https://goatar.com/login');

  // Fill login form
  await page.fill('input[name="email"]', process.env.ATAR_EMAIL);
  await page.fill('input[name="password"]', process.env.ATAR_PASSWORD);
  await page.click('button[type="submit"]');

  // Wait for authentication to complete
  await page.waitForURL('**/dashboard');

  // Save authentication state
  await context.storageState({ path: AUTH_FILE });
  console.log('Authentication state saved to', AUTH_FILE);

  await browser.close();
}

async function getAuthenticatedContext() {
  if (!fs.existsSync(AUTH_FILE)) {
    await authenticate();
  }

  const browser = await chromium.launch();
  const context = await browser.newContext({
    storageState: AUTH_FILE,
  });

  return { browser, context };
}

module.exports = { authenticate, getAuthenticatedContext };
```

## Bypassing Authentication

For public endpoints or testing unauthenticated access:

```javascript
const AtarApiClient = require('../auth/api-client');

// Create client without authentication
const publicClient = new AtarApiClient({
  token: '',
  tenant: '',
});

// Test public endpoints
const publicData = await publicClient.get('/public/endpoint');
```

## Token Refresh

If your token expires, implement a refresh mechanism:

```javascript
// auth/token-manager.js
const fs = require('fs');

const TOKEN_FILE = 'auth/token.json';

class TokenManager {
  constructor() {
    this.loadToken();
  }

  loadToken() {
    if (fs.existsSync(TOKEN_FILE)) {
      const data = JSON.parse(fs.readFileSync(TOKEN_FILE));
      this.token = data.token;
      this.expiresAt = new Date(data.expiresAt);
    }
  }

  saveToken(token, expiresIn = 3600) {
    const expiresAt = new Date(Date.now() + expiresIn * 1000);
    fs.writeFileSync(TOKEN_FILE, JSON.stringify({ token, expiresAt }));
    this.token = token;
    this.expiresAt = expiresAt;
  }

  isExpired() {
    return !this.expiresAt || new Date() > this.expiresAt;
  }

  async getValidToken() {
    if (this.isExpired()) {
      await this.refreshToken();
    }
    return this.token;
  }

  async refreshToken() {
    // Implement token refresh logic here
    // This depends on how the Atar API handles token refresh
    throw new Error('Token refresh not implemented');
  }
}

module.exports = new TokenManager();
```

## Security Best Practices

1. **Never hardcode tokens** - Always use environment variables
2. **Add auth files to .gitignore** - Prevent accidental commits
3. **Use short-lived tokens** - Rotate tokens regularly
4. **Limit token scope** - Use tokens with minimum required permissions
5. **Secure storage** - Encrypt tokens at rest in production
6. **Audit access** - Log all API calls for security monitoring

## Troubleshooting

### Common Authentication Errors

| Error Code | Message | Solution |
|------------|---------|----------|
| 401 | Unauthorized | Check if token is valid and not expired |
| 403 | Forbidden | Token doesn't have required permissions |
| 422 | Validation Error | Check request payload format |

### Debugging Tips

```javascript
// Enable request logging
const client = new AtarApiClient({
  token: 'your-token',
  tenant: 'your-tenant',
  debug: true, // Add debug option
});

// Log all requests
client.request = async function(method, endpoint, body) {
  console.log(`[${method}] ${endpoint}`);
  if (body) console.log('Body:', JSON.stringify(body, null, 2));

  const result = await originalRequest.call(this, method, endpoint, body);
  console.log('Response:', result.status, result.data);

  return result;
};
```
