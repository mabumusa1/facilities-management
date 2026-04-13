const https = require('https');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://api.goatar.com/api-management',
};

async function request(method, endpoint) {
  return new Promise((resolve) => {
    const url = endpoint.startsWith('http') ? endpoint : CONFIG.baseUrl + endpoint;
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
        'Accept-Language': 'en'
      }
    };

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        try {
          resolve({ status: res.statusCode, data: JSON.parse(data) });
        } catch (e) {
          resolve({ status: res.statusCode, data: data });
        }
      });
    });
    req.on('error', (err) => resolve({ status: 0, error: err.message }));
    req.end();
  });
}

async function main() {
  console.log('Finding unit category and type IDs...\n');

  // Try various endpoints that might have categories/types
  const endpoints = [
    '/rf/categories',
    '/rf/unit-categories',
    '/rf/property-categories',
    '/rf/types',
    '/rf/unit-types',
    '/rf/property-types',
    '/rf/lookups',
    '/rf/lookups/categories',
    '/rf/lookups/types',
    '/rf/units/categories',
    '/rf/units/types',
    '/lookups/categories',
    '/lookups/types',
    '/rf/common-lists?type=categories',
    '/rf/common-lists?type=types',
    '/rf/common-lists?type=unit_categories',
    '/rf/common-lists?type=unit_types',
    '/rf/common-lists?type=property_categories',
    '/rf/common-lists?type=property_types',
    '/rf/currencies',
    '/currencies',
    '/rf/countries',
  ];

  for (const endpoint of endpoints) {
    const res = await request('GET', endpoint);
    console.log(`${endpoint}: ${res.status}`);
    if (res.status === 200 && res.data?.data) {
      console.log(`  Found data:`, JSON.stringify(res.data.data).substring(0, 200));
    }
  }
}

main();
