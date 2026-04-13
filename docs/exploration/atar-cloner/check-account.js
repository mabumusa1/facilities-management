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
        'Accept-Language': 'en',
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
  console.log('Checking account info and permissions...\n');

  // Try various user/account endpoints
  const endpoints = [
    '/profile',
    '/user',
    '/user/profile',
    '/auth/user',
    '/rf/user',
    '/rf/profile',
    '/auth/me',
    '/account',
    '/rf/account',
    '/rf/dashboard',
    '/dashboard',
    '/rf/permissions',
    '/permissions',
    '/rf/roles',
    '/rf/modules',
    '/modules',
    '/rf/features',
    '/rf/subscription',
    '/rf/plan',
    '/rf/limits',
    '/limits',
  ];

  for (const ep of endpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: ${res.status}`);
      const dataStr = JSON.stringify(res.data);
      if (dataStr.length > 500) {
        console.log(`  ${dataStr.substring(0, 500)}...`);
      } else {
        console.log(`  ${dataStr}`);
      }
      console.log('');
    }
  }

  // Try with Accept-Language: en to get English error messages
  console.log('\n=== Creating Unit (English errors) ===');
  const unitData = {
    name: 'Apartment 101',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  };
  const res = await request('POST', '/rf/units', unitData);
  console.log('Response:', res.status);
  console.log('Data:', JSON.stringify(res.data, null, 2));

  // Check if units can be created through building
  console.log('\n=== POST /rf/buildings/1/units ===');
  const buildingUnitRes = await request('POST', '/rf/buildings/1/units', unitData);
  console.log('Response:', buildingUnitRes.status);
  console.log('Data:', JSON.stringify(buildingUnitRes.data, null, 2));

  // Check if units can be created through community
  console.log('\n=== POST /rf/communities/1/units ===');
  const communityUnitRes = await request('POST', '/rf/communities/1/units', unitData);
  console.log('Response:', communityUnitRes.status);
  console.log('Data:', JSON.stringify(communityUnitRes.data, null, 2));

  // Check /rf/unit (singular)
  console.log('\n=== POST /rf/unit ===');
  const unitSingularRes = await request('POST', '/rf/unit', unitData);
  console.log('Response:', unitSingularRes.status);
  console.log('Data:', JSON.stringify(unitSingularRes.data, null, 2));
}

main();
