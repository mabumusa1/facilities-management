const https = require('https');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://api.goatar.com/api-management',
};

async function request(method, endpoint, body = null) {
  return new Promise((resolve) => {
    const url = endpoint.startsWith('http') ? endpoint : CONFIG.baseUrl + endpoint;
    const urlObj = new URL(url);
    const options = {
      hostname: urlObj.hostname,
      port: 443,
      path: urlObj.pathname + urlObj.search,
      method,
      headers: {
        'Authorization': `Bearer ${CONFIG.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Tenant': CONFIG.tenant,
      }
    };
    const bodyStr = body ? JSON.stringify(body) : null;
    if (bodyStr) options.headers['Content-Length'] = Buffer.byteLength(bodyStr);

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        try { resolve({ status: res.statusCode, data: JSON.parse(data) }); }
        catch { resolve({ status: res.statusCode, data }); }
      });
    });
    req.on('error', (e) => resolve({ status: 0, error: e.message }));
    if (bodyStr) req.write(bodyStr);
    req.end();
  });
}

async function main() {
  console.log('Searching for unit/property categories and types...\n');

  // Try many different endpoint patterns
  const endpoints = [
    '/rf/unit-categories',
    '/rf/property-categories',
    '/units/categories',
    '/properties/categories',
    '/rf/units/categories',
    '/rf/properties/categories',
    '/rf/units/types',
    '/rf/properties/types',
    '/lookup/categories',
    '/lookup/types',
    '/lookup/unit-categories',
    '/lookup/property-types',
    '/rf/lookups/unit-categories',
    '/rf/lookups/property-types',
    '/settings/unit-categories',
    '/settings/property-types',
    '/rf/settings/categories',
    '/rf/settings/types',
    // Try getting the community to see if it has embedded category info
    '/rf/communities/1',
    '/rf/buildings/1',
  ];

  for (const ep of endpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200 && res.data) {
      console.log(`  Data: ${JSON.stringify(res.data).substring(0, 300)}`);
    }
  }

  // Try to understand unit creation better by using the form structure
  console.log('\n\nTrying to create unit with different field combinations...\n');

  // Maybe the API uses different field names
  const testBodies = [
    // Try without building (maybe building is separate)
    { name: 'Test Unit', rf_community_id: 1, category_id: 2, type_id: 2 },
    // Try with property_category_id instead
    { name: 'Test Unit', rf_community_id: 1, property_category_id: 1, property_type_id: 1 },
    // Try with unit_category_id
    { name: 'Test Unit', rf_community_id: 1, unit_category_id: 1, unit_type_id: 1 },
    // Try with unit_category and unit_type (strings)
    { name: 'Test Unit', rf_community_id: 1, unit_category: 'residential', unit_type: 'apartment' },
    // Try with category and type (strings)
    { name: 'Test Unit', rf_community_id: 1, category: 'residential', type: 'apartment' },
  ];

  for (const body of testBodies) {
    console.log(`Trying: ${JSON.stringify(body)}`);
    const res = await request('POST', '/rf/units', body);
    console.log(`  Status: ${res.status}`);
    if (res.data?.errors) {
      console.log(`  Errors: ${JSON.stringify(res.data.errors)}`);
    } else if (res.status === 200 || res.status === 201) {
      console.log(`  SUCCESS! Created unit.`);
      console.log(`  Response: ${JSON.stringify(res.data).substring(0, 200)}`);
    }
  }
}

main();
