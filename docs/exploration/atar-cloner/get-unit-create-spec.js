const https = require('https');
const fs = require('fs');

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
      method,
      headers: {
        'Authorization': `Bearer ${CONFIG.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Tenant': CONFIG.tenant,
      }
    };

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        try { resolve(JSON.parse(data)); }
        catch { resolve(data); }
      });
    });
    req.on('error', (e) => resolve({ error: e.message }));
    req.end();
  });
}

async function main() {
  console.log('Getting full unit creation specification...\n');

  const createSpec = await request('GET', '/rf/units/create');
  console.log('=== /rf/units/create response ===\n');
  console.log(JSON.stringify(createSpec, null, 2));

  // Save to file
  fs.writeFileSync('./unit-create-spec.json', JSON.stringify(createSpec, null, 2));
  console.log('\nSaved to unit-create-spec.json');

  // Also check if there's a categories endpoint specific to properties
  console.log('\n\n=== Checking for property-specific categories ===\n');

  const categoryEndpoints = [
    '/rf/unit/categories',
    '/rf/unit-categories-types',
    '/rf/property/categories',
    '/rf/property-categories-types',
    '/property-categories',
    '/unit-categories',
  ];

  for (const ep of categoryEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}:`, res?.code || res?.status || 'error');
    if (res?.data) {
      console.log(JSON.stringify(res.data, null, 2).substring(0, 300));
    }
  }
}

main();
