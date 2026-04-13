const https = require('https');
const fs = require('fs');
const path = require('path');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrls: {
    management: 'https://api.goatar.com/api-management',
    tenancy: 'https://api.goatar.com/tenancy/api',
    main: 'https://api.goatar.com'
  },
  outputDir: './api-probe-results',
  maxRetries: 3,
  retryDelay: 1000,
  requestDelay: 500, // delay between requests to avoid rate limiting
};

// Results storage
const results = {
  timestamp: new Date().toISOString(),
  config: { tenant: CONFIG.tenant, baseUrls: CONFIG.baseUrls },
  endpoints: [],
  schemas: {},
  validationErrors: {},
  summary: { total: 0, success: 0, failed: 0, validation: 0 }
};

// Ensure output directory exists
if (!fs.existsSync(CONFIG.outputDir)) {
  fs.mkdirSync(CONFIG.outputDir, { recursive: true });
}

// Logger
function log(msg, type = 'info') {
  const icons = {
    info: '\x1b[36m[INFO]\x1b[0m',
    success: '\x1b[32m[OK]\x1b[0m',
    error: '\x1b[31m[ERR]\x1b[0m',
    warn: '\x1b[33m[WARN]\x1b[0m',
    validation: '\x1b[35m[VAL]\x1b[0m'
  };
  console.log(`${icons[type] || icons.info} ${msg}`);
}

// Sleep helper
const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

// Make HTTP request with retries
async function makeRequest(method, url, body = null, retries = CONFIG.maxRetries) {
  return new Promise((resolve) => {
    const urlObj = new URL(url);

    const options = {
      hostname: urlObj.hostname,
      port: 443,
      path: urlObj.pathname + urlObj.search,
      method: method,
      headers: {
        'Authorization': `Bearer ${CONFIG.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Tenant': CONFIG.tenant,
        'User-Agent': 'AtarAPIProber/1.0'
      }
    };

    if (body) {
      options.headers['Content-Length'] = Buffer.byteLength(JSON.stringify(body));
    }

    const startTime = Date.now();

    const req = https.request(options, (res) => {
      let data = '';

      res.on('data', chunk => data += chunk);

      res.on('end', () => {
        const duration = Date.now() - startTime;
        let parsedBody = null;
        let parseError = null;

        try {
          parsedBody = JSON.parse(data);
        } catch (e) {
          parseError = e.message;
          parsedBody = data;
        }

        resolve({
          success: res.statusCode >= 200 && res.statusCode < 300,
          statusCode: res.statusCode,
          headers: res.headers,
          body: parsedBody,
          parseError,
          duration,
          retries: CONFIG.maxRetries - retries
        });
      });
    });

    req.on('error', async (err) => {
      if (retries > 0) {
        log(`Request failed, retrying... (${retries} left): ${err.message}`, 'warn');
        await sleep(CONFIG.retryDelay);
        resolve(await makeRequest(method, url, body, retries - 1));
      } else {
        resolve({
          success: false,
          statusCode: 0,
          error: err.message,
          body: null,
          duration: Date.now() - startTime,
          retries: CONFIG.maxRetries
        });
      }
    });

    req.setTimeout(30000, () => {
      req.destroy();
    });

    if (body) {
      req.write(JSON.stringify(body));
    }

    req.end();
  });
}

// Probe an endpoint
async function probeEndpoint(endpoint) {
  const { method, path: endpointPath, base = 'management', body = null, description = '' } = endpoint;
  const url = CONFIG.baseUrls[base] + endpointPath;

  log(`[${method}] ${endpointPath}`, 'info');

  const request = {
    method,
    url,
    path: endpointPath,
    base,
    body,
    description,
    headers: {
      'Authorization': 'Bearer ***',
      'Content-Type': 'application/json',
      'X-Tenant': CONFIG.tenant
    }
  };

  const response = await makeRequest(method, url, body);

  const result = {
    request,
    response: {
      statusCode: response.statusCode,
      success: response.success,
      duration: response.duration,
      retries: response.retries,
      headers: response.headers,
      body: response.body,
      parseError: response.parseError
    },
    timestamp: new Date().toISOString()
  };

  // Categorize result
  results.summary.total++;

  if (response.success) {
    results.summary.success++;
    log(`  -> ${response.statusCode} OK (${response.duration}ms)`, 'success');

    // Extract schema from successful response
    if (response.body && typeof response.body === 'object') {
      const schemaKey = `${method}_${endpointPath.replace(/[\/:-]/g, '_')}`;
      results.schemas[schemaKey] = extractSchema(response.body);
    }
  } else if (response.statusCode === 422 || response.statusCode === 400) {
    results.summary.validation++;
    log(`  -> ${response.statusCode} Validation Error`, 'validation');

    // Store validation errors - these are gold for Swagger!
    const valKey = `${method}_${endpointPath.replace(/[\/:-]/g, '_')}`;
    results.validationErrors[valKey] = response.body;
  } else {
    results.summary.failed++;
    log(`  -> ${response.statusCode || 'FAILED'} ${response.error || ''}`, 'error');
  }

  results.endpoints.push(result);

  // Save individual result
  const filename = `${method}_${endpointPath.replace(/[\/:-]/g, '_')}.json`;
  fs.writeFileSync(
    path.join(CONFIG.outputDir, filename),
    JSON.stringify(result, null, 2)
  );

  await sleep(CONFIG.requestDelay);
  return result;
}

// Extract schema from response object
function extractSchema(obj, depth = 0) {
  if (depth > 10) return { type: 'object', note: 'max depth reached' };

  if (obj === null) return { type: 'null' };
  if (Array.isArray(obj)) {
    if (obj.length === 0) return { type: 'array', items: {} };
    return {
      type: 'array',
      items: extractSchema(obj[0], depth + 1),
      sampleLength: obj.length
    };
  }

  const type = typeof obj;

  if (type === 'object') {
    const schema = { type: 'object', properties: {} };
    for (const [key, value] of Object.entries(obj)) {
      schema.properties[key] = extractSchema(value, depth + 1);
    }
    return schema;
  }

  if (type === 'string') {
    // Detect special string types
    if (/^\d{4}-\d{2}-\d{2}/.test(obj)) return { type: 'string', format: 'date-time', example: obj };
    if (/^[a-f0-9-]{36}$/i.test(obj)) return { type: 'string', format: 'uuid', example: obj };
    if (/^[\w-]+@[\w-]+\.\w+$/.test(obj)) return { type: 'string', format: 'email', example: obj };
    if (/^https?:\/\//.test(obj)) return { type: 'string', format: 'uri', example: obj };
    return { type: 'string', example: obj.substring(0, 100) };
  }

  if (type === 'number') {
    return { type: Number.isInteger(obj) ? 'integer' : 'number', example: obj };
  }

  if (type === 'boolean') {
    return { type: 'boolean', example: obj };
  }

  return { type };
}

// All endpoints to probe
const ENDPOINTS = [
  // GET - Dashboard & Stats
  { method: 'GET', path: '/dashboard/requires-attention', description: 'Dashboard attention items' },

  // GET - Admins
  { method: 'GET', path: '/rf/admins', description: 'List admins' },
  { method: 'GET', path: '/rf/admins/manager-roles', description: 'Manager roles' },

  // GET - Leases
  { method: 'GET', path: '/rf/leases', description: 'List leases' },
  { method: 'GET', path: '/rf/leases/statistics', description: 'Lease statistics' },
  { method: 'GET', path: '/rf/sub-leases', description: 'Sub-leases' },

  // GET - Leads & Contacts
  { method: 'GET', path: '/rf/leads', description: 'List leads' },
  { method: 'GET', path: '/rf/contacts/statistics', description: 'Contact statistics' },
  { method: 'GET', path: '/rf/tenants', description: 'List tenants' },

  // GET - Requests
  { method: 'GET', path: '/rf/requests/categories', description: 'Request categories' },
  { method: 'GET', path: '/rf/users/requests', description: 'User requests' },
  { method: 'GET', path: '/rf/users/requests/types', description: 'Request types' },
  { method: 'GET', path: '/request-category', description: 'Request category' },

  // GET - Transactions
  { method: 'GET', path: '/rf/transactions/', description: 'List transactions' },

  // GET - Properties
  { method: 'GET', path: '/rf/buildings', description: 'List buildings' },
  { method: 'GET', path: '/rf/facilities', description: 'List facilities' },
  { method: 'GET', path: '/rf/communities/off-plan-sale', description: 'Off-plan sales' },
  { method: 'GET', path: '/rf/communities/edaat/product-codes', description: 'Edaat product codes' },

  // GET - Marketplace
  { method: 'GET', path: '/marketplace/admin/settings/banks', description: 'Bank settings' },
  { method: 'GET', path: '/marketplace/admin/settings/sales', description: 'Sales settings' },
  { method: 'GET', path: '/marketplace/admin/settings/visits', description: 'Visit settings' },
  { method: 'GET', path: '/marketplace/admin/units', description: 'Marketplace units' },
  { method: 'GET', path: '/marketplace/admin/visits', description: 'Marketplace visits' },

  // GET - Visitor Access
  { method: 'GET', path: '/rf/users/visitor-access', description: 'Visitor access list' },

  // GET - Notifications
  { method: 'GET', path: '/notifications/unread-count', description: 'Unread notifications count' },

  // GET - Announcements
  { method: 'GET', path: '/rf/announcements', description: 'List announcements' },

  // GET - Common
  { method: 'GET', path: '/rf/common-lists', description: 'Common lists' },
  { method: 'GET', path: '/rf/statuses', description: 'Status types' },
  { method: 'GET', path: '/rf/modules', description: 'Modules' },
  { method: 'GET', path: '/countries', description: 'Countries list' },

  // GET - Integrations
  { method: 'GET', path: '/integrations/powerbi/types', description: 'Power BI types' },

  // Tenancy endpoints
  { method: 'GET', path: '/me', base: 'tenancy', description: 'Current user info' },
  { method: 'GET', path: '/cities/all', base: 'tenancy', description: 'All cities' },
  { method: 'GET', path: '/districts/all', base: 'tenancy', description: 'All districts' },

  // POST - Validation probes (empty body to get required fields)
  { method: 'POST', path: '/rf/admins/check-validate', body: {}, description: 'Admin validation' },
  { method: 'POST', path: '/rf/leases/create', body: {}, description: 'Create lease validation' },
  { method: 'POST', path: '/rf/leases/change-status/move-out', body: {}, description: 'Move out validation' },
  { method: 'POST', path: '/rf/leases/change-status/terminate', body: {}, description: 'Terminate validation' },
  { method: 'POST', path: '/rf/leases/renew/store', body: {}, description: 'Renew lease validation' },
  { method: 'POST', path: '/rf/requests/change-status/canceled', body: {}, description: 'Cancel request validation' },
  { method: 'POST', path: '/marketplace/admin/settings/banks/store', body: {}, description: 'Store bank settings validation' },
  { method: 'POST', path: '/marketplace/admin/settings/sales/store', body: {}, description: 'Store sales settings validation' },
  { method: 'POST', path: '/marketplace/admin/settings/visits/store', body: {}, description: 'Store visit settings validation' },
  { method: 'POST', path: '/notifications/mark-all-as-read', body: {}, description: 'Mark notifications read' },
];

// Main execution
async function main() {
  console.log('\n' + '='.repeat(60));
  console.log('  ATAR API PROBER');
  console.log('  Capturing real API responses for Swagger generation');
  console.log('='.repeat(60));
  console.log(`\nTenant: ${CONFIG.tenant}`);
  console.log(`Output: ${CONFIG.outputDir}`);
  console.log(`Endpoints: ${ENDPOINTS.length}`);
  console.log('\n' + '-'.repeat(60) + '\n');

  for (const endpoint of ENDPOINTS) {
    await probeEndpoint(endpoint);
  }

  // Save complete results
  fs.writeFileSync(
    path.join(CONFIG.outputDir, '_all_results.json'),
    JSON.stringify(results, null, 2)
  );

  // Save schemas
  fs.writeFileSync(
    path.join(CONFIG.outputDir, '_schemas.json'),
    JSON.stringify(results.schemas, null, 2)
  );

  // Save validation errors
  fs.writeFileSync(
    path.join(CONFIG.outputDir, '_validation_errors.json'),
    JSON.stringify(results.validationErrors, null, 2)
  );

  // Generate summary report
  const summary = `# API Probe Results

## Summary
- **Timestamp:** ${results.timestamp}
- **Tenant:** ${CONFIG.tenant}
- **Total Endpoints:** ${results.summary.total}
- **Successful:** ${results.summary.success}
- **Validation Errors:** ${results.summary.validation}
- **Failed:** ${results.summary.failed}

## Endpoints Probed

| Method | Path | Status | Duration |
|--------|------|--------|----------|
${results.endpoints.map(e =>
  `| ${e.request.method} | \`${e.request.path}\` | ${e.response.statusCode} | ${e.response.duration}ms |`
).join('\n')}

## Validation Errors (Required Fields)

${Object.entries(results.validationErrors).map(([key, val]) =>
  `### ${key}\n\`\`\`json\n${JSON.stringify(val, null, 2)}\n\`\`\``
).join('\n\n')}

## Extracted Schemas

See \`_schemas.json\` for detailed schema information extracted from successful responses.
`;

  fs.writeFileSync(
    path.join(CONFIG.outputDir, 'REPORT.md'),
    summary
  );

  console.log('\n' + '='.repeat(60));
  console.log('  PROBE COMPLETE!');
  console.log('='.repeat(60));
  console.log(`\n  Total: ${results.summary.total}`);
  console.log(`  Success: ${results.summary.success}`);
  console.log(`  Validation: ${results.summary.validation}`);
  console.log(`  Failed: ${results.summary.failed}`);
  console.log(`\n  Results: ${CONFIG.outputDir}/`);
  console.log('    - _all_results.json (complete data)');
  console.log('    - _schemas.json (extracted schemas)');
  console.log('    - _validation_errors.json (required fields)');
  console.log('    - REPORT.md (summary)');
  console.log('='.repeat(60) + '\n');
}

main().catch(err => {
  log(`Fatal error: ${err.message}`, 'error');
  console.error(err);
  process.exit(1);
});
