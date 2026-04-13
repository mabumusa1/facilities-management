const https = require('https');
const fs = require('fs');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://api.goatar.com/api-management',
};

// Store all requests and responses
const allRequests = [];

async function request(method, endpoint, body = null, extraHeaders = {}) {
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
        ...extraHeaders
      }
    };
    const bodyStr = body ? JSON.stringify(body) : null;
    if (bodyStr) options.headers['Content-Length'] = Buffer.byteLength(bodyStr);

    const requestLog = {
      timestamp: new Date().toISOString(),
      method,
      endpoint,
      url,
      body,
      response: null
    };

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        let parsedData;
        try { parsedData = JSON.parse(data); }
        catch { parsedData = data; }

        requestLog.response = {
          status: res.statusCode,
          headers: res.headers,
          data: parsedData
        };
        allRequests.push(requestLog);

        resolve({ status: res.statusCode, data: parsedData, headers: res.headers });
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

function saveRequests() {
  fs.writeFileSync('./unit-investigation-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to unit-investigation-log.json`);
}

async function main() {
  console.log('Investigating 400 error in depth...\n');
  console.log('All requests and responses will be saved to unit-investigation-log.json\n');

  // Check subscription/billing info
  console.log('=== Subscription Endpoints ===');
  const subEndpoints = [
    '/rf/billing',
    '/rf/billing/subscription',
    '/rf/subscription/current',
    '/billing',
    '/billing/subscription',
    '/rf/plan/current',
    '/rf/plans',
    '/plans',
  ];

  for (const ep of subEndpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: ${res.status}`);
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
    }
  }

  // Check tenant settings
  console.log('\n=== Tenant/Settings Endpoints ===');
  const settingsEndpoints = [
    '/rf/tenant',
    '/rf/tenants/current',
    '/rf/business',
    '/rf/business/info',
    '/rf/settings/business',
    '/rf/company',
    '/rf/organization',
  ];

  for (const ep of settingsEndpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: ${res.status}`);
      console.log(JSON.stringify(res.data, null, 2).substring(0, 800));
    }
  }

  // Check if the issue is with specific category/type combination by testing all
  console.log('\n=== Testing All Category/Type Combinations ===');
  const categories = [
    { id: 2, name: 'Residential', types: [17, 18, 19, 20, 21, 22, 24, 25] },
    { id: 3, name: 'Commercial', types: [26, 27, 28, 29, 30, 31, 135, 136, 137, 138, 139, 140] }
  ];

  let anySuccess = false;
  for (const cat of categories) {
    for (const typeId of cat.types.slice(0, 3)) { // Test first 3 of each
      const body = {
        name: `Test ${cat.name} ${typeId}`,
        rf_community_id: 1,
        rf_building_id: 1,
        category_id: cat.id,
        type_id: typeId
      };
      const res = await request('POST', '/rf/units', body);
      if (res.status !== 400) {
        console.log(`category=${cat.id}, type=${typeId}: ${res.status}`);
        console.log(JSON.stringify(res.data).substring(0, 200));
        anySuccess = true;
      }
    }
  }
  if (!anySuccess) {
    console.log('All combinations returned 400');
  }

  // Check if the community needs specific configuration
  console.log('\n=== Community Configuration ===');
  const community = await request('GET', '/rf/communities/1');
  const commData = community.data?.data;
  if (commData) {
    console.log('Critical fields:');
    console.log('  is_market_place:', commData.is_market_place);
    console.log('  is_buy:', commData.is_buy);
    console.log('  community_marketplace_type:', commData.community_marketplace_type);
    console.log('  is_off_plan_sale:', commData.is_off_plan_sale);
    console.log('  completion_percent:', commData.completion_percent);
    console.log('  listed_percentage:', commData.listed_percentage);
    console.log('  allow_cash_sale:', commData.allow_cash_sale);
    console.log('  allow_bank_financing:', commData.allow_bank_financing);
  }

  // Try updating community to enable features
  console.log('\n=== Trying to update community settings ===');
  const updateRes = await request('PUT', '/rf/communities/1', {
    name: 'Test Community 1',
    is_market_place: '1',
    allow_cash_sale: 1,
    allow_bank_financing: 1,
    is_buy: 1,
  });
  console.log('Update result:', updateRes.status);
  console.log(JSON.stringify(updateRes.data).substring(0, 300));

  // Try creating unit again
  console.log('\n=== Retry unit creation after community update ===');
  const unitRes = await request('POST', '/rf/units', {
    name: 'Apartment After Update',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17
  });
  console.log('Unit creation:', unitRes.status, JSON.stringify(unitRes.data));

  // Check debug headers
  console.log('\n=== Check Debug Response Headers ===');
  const debugRes = await request('POST', '/rf/units', {
    name: 'Debug Test',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17
  }, { 'X-Debug': '1', 'Accept-Language': 'en' });
  console.log('Headers:', JSON.stringify(debugRes.headers, null, 2));
  console.log('Body:', JSON.stringify(debugRes.data, null, 2));

  // Save all requests and responses
  saveRequests();
}

main().catch(console.error);
