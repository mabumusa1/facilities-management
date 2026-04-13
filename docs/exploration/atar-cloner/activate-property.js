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
  console.log('Checking for activation/publish requirements...\n');

  // Check activation endpoints
  console.log('=== Activation Endpoints ===');
  const activationEndpoints = [
    '/rf/communities/1/activate',
    '/rf/communities/1/publish',
    '/rf/communities/1/enable',
    '/rf/buildings/1/activate',
    '/rf/buildings/1/publish',
    '/rf/buildings/1/enable',
    '/rf/communities/1/status',
    '/rf/buildings/1/status',
  ];

  for (const ep of activationEndpoints) {
    // Try GET first
    const getRes = await request('GET', ep);
    console.log(`GET ${ep}: ${getRes.status}`);
    if (getRes.status === 200) {
      console.log(JSON.stringify(getRes.data, null, 2).substring(0, 300));
    }

    // Try POST
    const postRes = await request('POST', ep, {});
    console.log(`POST ${ep}: ${postRes.status}`);
    if (postRes.status === 200 || postRes.status === 201) {
      console.log(JSON.stringify(postRes.data, null, 2).substring(0, 300));
    }
  }

  // Check if the community needs more configuration
  console.log('\n=== Updating Community with more fields ===');
  const communityUpdate = await request('PUT', '/rf/communities/1', {
    name: 'Test Community 1',
    description: 'A test community for development',
    country_id: 1,
    currency_id: 1,
    city_id: 1,
    district_id: 1,
    is_active: true,
    status: 'active',
    completion_percent: 100,
    is_market_place: '1',
    community_marketplace_type: 'rent',
    is_buy: 1,
    allow_cash_sale: 1,
    allow_bank_financing: 1,
  });
  console.log('Community update:', communityUpdate.status);
  console.log(JSON.stringify(communityUpdate.data, null, 2).substring(0, 500));

  // Check if building needs more configuration
  console.log('\n=== Updating Building with more fields ===');
  const buildingUpdate = await request('PUT', '/rf/buildings/1', {
    name: 'Test Building 1',
    rf_community_id: 1,
    description: 'A test building',
    no_floors: 10,
    year_build: 2020,
    is_active: true,
    status: 'active',
  });
  console.log('Building update:', buildingUpdate.status);
  console.log(JSON.stringify(buildingUpdate.data, null, 2).substring(0, 500));

  // Try unit creation again
  console.log('\n=== Retry Unit Creation ===');
  const unitRes = await request('POST', '/rf/units', {
    name: 'Test Apartment Final',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
    owner_id: 3
  });
  console.log('Unit creation:', unitRes.status);
  console.log(JSON.stringify(unitRes.data, null, 2));

  // Maybe the issue is with auto-generated fields - try with unit_number
  console.log('\n=== Unit with explicit unit_number ===');
  const unitWithNumber = await request('POST', '/rf/units', {
    name: 'Apartment 101',
    unit_number: 'APT-101',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  });
  console.log('Unit with number:', unitWithNumber.status);
  console.log(JSON.stringify(unitWithNumber.data, null, 2));

  // Check if there's a "store" action instead of direct POST
  console.log('\n=== Alternative create methods ===');
  const storeRes = await request('POST', '/rf/units/store', {
    name: 'Test Store Unit',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  });
  console.log('POST /rf/units/store:', storeRes.status);
  console.log(JSON.stringify(storeRes.data, null, 2).substring(0, 300));

  // Save all
  fs.writeFileSync('./activate-property-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to activate-property-log.json`);
}

main().catch(console.error);
