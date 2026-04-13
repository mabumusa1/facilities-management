const https = require('https');
const fs = require('fs');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://api.goatar.com/api-management',
};

const allRequests = [];

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

    const requestLog = { timestamp: new Date().toISOString(), method, endpoint, url, body, response: null };

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        let parsedData;
        try { parsedData = JSON.parse(data); }
        catch { parsedData = data; }
        requestLog.response = { status: res.statusCode, data: parsedData };
        allRequests.push(requestLog);
        resolve({ status: res.statusCode, data: parsedData });
      });
    });
    req.on('error', (e) => {
      requestLog.response = { error: e.message };
      allRequests.push(requestLog);
      resolve({ status: 0, error: e.message });
    });
    if (bodyStr) req.write(bodyStr);
    req.end();
  });
}

async function main() {
  console.log('Trying to create owner first, then unit...\n');

  // Check owner creation spec
  console.log('=== Owner Creation Spec ===');
  const ownerCreateSpec = await request('GET', '/rf/owners/create');
  console.log('GET /rf/owners/create:', ownerCreateSpec.status);
  console.log(JSON.stringify(ownerCreateSpec.data, null, 2));

  // Try creating an owner with minimal data
  console.log('\n=== Creating Owner ===');
  const ownerData = {
    name: 'Test Owner',
    email: 'testowner@example.com',
    phone_number: '+966500000001'
  };
  const ownerRes = await request('POST', '/rf/owners', ownerData);
  console.log('POST /rf/owners:', ownerRes.status);
  console.log(JSON.stringify(ownerRes.data, null, 2));

  // If owner creation has validation errors, try to understand what's needed
  if (ownerRes.data?.errors) {
    console.log('\nOwner validation errors detected. Trying with more fields...');
    const extendedOwner = {
      name: 'Test Owner',
      email: 'testowner@example.com',
      phone_number: '+966500000001',
      phone_country_code: 'SA',
      national_id: '1234567890',
      type: 'individual'
    };
    const extendedRes = await request('POST', '/rf/owners', extendedOwner);
    console.log('Extended owner creation:', extendedRes.status);
    console.log(JSON.stringify(extendedRes.data, null, 2));
  }

  // Check if there's a different API version or path
  console.log('\n=== Checking alternative API paths ===');
  const altPaths = [
    { method: 'GET', path: '/api/rf/units' },
    { method: 'GET', path: '/v1/rf/units' },
    { method: 'GET', path: '/v2/rf/units' },
    { method: 'POST', path: '/api/units' },
    { method: 'POST', path: '/units' },
  ];

  for (const { method, path } of altPaths) {
    const body = method === 'POST' ? {
      name: 'Alt Path Test',
      rf_community_id: 1,
      rf_building_id: 1,
      category_id: 2,
      type_id: 17
    } : null;
    const res = await request(method, path, body);
    console.log(`${method} ${path}: ${res.status}`);
    if (res.status !== 404) {
      console.log(JSON.stringify(res.data).substring(0, 200));
    }
  }

  // Maybe there's a rate limiting or throttle issue
  console.log('\n=== Checking rate limits ===');
  console.log('Waiting 5 seconds...');
  await new Promise(r => setTimeout(r, 5000));

  const afterWait = await request('POST', '/rf/units', {
    name: 'After Wait Test',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17
  });
  console.log('After wait:', afterWait.status);
  console.log(JSON.stringify(afterWait.data));

  // Check if there's an issue with the JSON structure
  console.log('\n=== Testing form-urlencoded (simulated) ===');
  // Some APIs expect form data even if we send JSON
  const formTest = await request('POST', '/rf/units', {
    'name': 'Form Test',
    'rf_community_id': '1',  // String instead of number
    'rf_building_id': '1',
    'category_id': '2',
    'type_id': '17'
  });
  console.log('String values:', formTest.status);
  console.log(JSON.stringify(formTest.data));

  // Save all
  fs.writeFileSync('./owner-creation-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to owner-creation-log.json`);
}

main().catch(console.error);
