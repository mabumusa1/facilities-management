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
  console.log('Checking subscription and features...\n');

  // Get full plans info
  console.log('=== Available Plans ===');
  const plans = await request('GET', '/plans');
  console.log(JSON.stringify(plans.data, null, 2));

  // Check current user subscription
  console.log('\n=== User/Subscription Info ===');
  const userEndpoints = [
    '/rf/user',
    '/rf/user/subscription',
    '/rf/user/plan',
    '/rf/current-plan',
    '/rf/active-plan',
    '/rf/subscription/status',
    '/rf/account/subscription',
  ];

  for (const ep of userEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2));
    }
  }

  // Check feature endpoints
  console.log('\n=== Features ===');
  const featureEndpoints = [
    '/rf/features',
    '/rf/features/units',
    '/rf/features/properties',
    '/features',
    '/features/enabled',
    '/rf/enabled-features',
  ];

  for (const ep of featureEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2));
    }
  }

  // Check if there's a unit import feature
  console.log('\n=== Unit Import/Export ===');
  const importEndpoints = [
    '/rf/units/import',
    '/rf/units/export',
    '/rf/units/bulk',
    '/rf/units/batch',
    '/rf/import/units',
  ];

  for (const ep of importEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
    }
  }

  // Try alternative endpoints
  console.log('\n=== Alternative Unit Endpoints ===');
  const altEndpoints = [
    '/rf/properties',
    '/rf/property',
    '/rf/property/create',
    '/rf/properties/create',
    '/rf/real-estate/units',
    '/rf/rentals/units',
  ];

  for (const ep of altEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2).substring(0, 300));
    }
  }

  // Check if unit creation needs to go through building
  console.log('\n=== Check Building Units Endpoint ===');
  const buildingUnits = await request('GET', '/rf/buildings/1/units');
  console.log('GET /rf/buildings/1/units:', buildingUnits.status);
  console.log(JSON.stringify(buildingUnits.data, null, 2).substring(0, 500));

  // Check if there's a dashboard that shows creation capabilities
  console.log('\n=== Dashboard/Stats ===');
  const dashEndpoints = [
    '/rf/dashboard',
    '/rf/dashboard/stats',
    '/rf/stats',
    '/rf/overview',
    '/rf/home',
  ];

  for (const ep of dashEndpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: ${res.status}`);
      console.log(JSON.stringify(res.data, null, 2).substring(0, 800));
    }
  }

  // Save all
  fs.writeFileSync('./subscription-investigation-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to subscription-investigation-log.json`);
}

main().catch(console.error);
