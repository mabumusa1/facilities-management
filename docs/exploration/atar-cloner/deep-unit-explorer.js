const https = require('https');
const fs = require('fs');

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
        try { resolve({ status: res.statusCode, data: JSON.parse(data), headers: res.headers }); }
        catch { resolve({ status: res.statusCode, data, headers: res.headers }); }
      });
    });
    req.on('error', (e) => resolve({ status: 0, error: e.message }));
    if (bodyStr) req.write(bodyStr);
    req.end();
  });
}

async function main() {
  console.log('Deep exploration of unit creation requirements...\n');

  // 1. Check if there are tenant-specific categories/types that need to be created
  console.log('=== Checking tenant configuration ===\n');

  const tenantEndpoints = [
    '/rf/tenant-settings',
    '/rf/tenant-config',
    '/tenant/settings',
    '/tenant/config',
    '/rf/subscription',
    '/subscription',
    '/rf/features',
    '/features',
    '/rf/modules/settings',
  ];

  for (const ep of tenantEndpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: ${res.status}`);
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
      console.log('...\n');
    }
  }

  // 2. Check /rf/units endpoint with GET to see structure
  console.log('=== Checking unit list endpoint for structure hints ===\n');
  const unitsList = await request('GET', '/rf/units?with_meta=1');
  console.log('Units list response:');
  console.log(JSON.stringify(unitsList.data, null, 2));

  // 3. Check for unit creation form/schema endpoint
  console.log('\n=== Looking for form schema endpoints ===\n');
  const formEndpoints = [
    '/rf/units/create',
    '/rf/units/form',
    '/rf/units/schema',
    '/rf/units/new',
    '/rf/form/units',
    '/rf/schema/units',
  ];

  for (const ep of formEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
    }
  }

  // 4. Try to understand the 400 error better
  console.log('\n=== Analyzing 400 error ===\n');
  const testUnit = {
    name: 'Test Unit',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 2
  };

  const res400 = await request('POST', '/rf/units', testUnit);
  console.log('400 Response details:');
  console.log('Status:', res400.status);
  console.log('Headers:', JSON.stringify(res400.headers, null, 2));
  console.log('Body:', JSON.stringify(res400.data, null, 2));

  // 5. Try PUT instead of POST (maybe it's an update-only endpoint)
  console.log('\n=== Trying PUT method ===\n');
  const resPut = await request('PUT', '/rf/units', testUnit);
  console.log('PUT Response:', resPut.status, JSON.stringify(resPut.data));

  // 6. Try multipart form data pattern (sometimes APIs expect different content type)
  console.log('\n=== Checking if there\'s a store endpoint ===\n');
  const storeEndpoints = [
    '/rf/units/store',
    '/units/store',
    '/rf/store/units',
    '/rf/units/add',
  ];

  for (const ep of storeEndpoints) {
    const res = await request('POST', ep, testUnit);
    console.log(`${ep}: ${res.status}`);
    if (res.data?.errors) {
      console.log('Errors:', JSON.stringify(res.data.errors));
    } else if (res.status === 200 || res.status === 201) {
      console.log('SUCCESS!');
      console.log(JSON.stringify(res.data, null, 2));
    }
  }

  // 7. Check the exact error message content
  console.log('\n=== Checking for Arabic error message ===\n');
  const res400ar = await request('POST', '/rf/units', testUnit);
  if (res400ar.data?.message) {
    console.log('Message:', res400ar.data.message);
  }
  if (res400ar.data?.code) {
    console.log('Code:', res400ar.data.code);
  }
  console.log('Full response:', JSON.stringify(res400ar.data, null, 2));

  // 8. Maybe units need to be created through the building?
  console.log('\n=== Trying to create unit via building endpoint ===\n');
  const buildingUnitRes = await request('POST', '/rf/buildings/1/units', testUnit);
  console.log(`POST /rf/buildings/1/units: ${buildingUnitRes.status}`);
  console.log(JSON.stringify(buildingUnitRes.data, null, 2));
}

main();
