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
  console.log('Exploring modules and features...\n');

  // Get all modules/features
  console.log('=== All Modules ===');
  const modules = await request('GET', '/rf/modules');
  console.log(JSON.stringify(modules.data, null, 2));

  // Check if there's a way to enable modules
  console.log('\n=== Module Settings/Config ===');
  const endpoints = [
    '/rf/modules/all',
    '/rf/modules/available',
    '/rf/modules/settings',
    '/rf/module-settings',
    '/rf/settings/modules',
    '/rf/app-settings',
    '/rf/app/settings',
    '/settings/modules',
    '/rf/tenant-modules',
    '/rf/tenant/modules',
    '/rf/business/settings',
    '/rf/business/modules',
  ];

  for (const ep of endpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: ${res.status}`);
      console.log(JSON.stringify(res.data, null, 2).substring(0, 800));
    }
  }

  // Check if there's a unit limitation
  console.log('\n=== Checking Limits ===');
  const limitEndpoints = [
    '/rf/limits',
    '/rf/quotas',
    '/rf/plan',
    '/rf/subscription/limits',
    '/rf/subscription/features',
    '/rf/features/units',
  ];

  for (const ep of limitEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2));
    }
  }

  // Check if we need a different building ID
  console.log('\n=== Buildings Detail ===');
  for (let i = 1; i <= 4; i++) {
    const building = await request('GET', `/rf/buildings/${i}`);
    if (building.status === 200 && building.data?.data) {
      console.log(`Building ${i}: community_id=${building.data.data.community?.id}, name=${building.data.data.name}`);
    }
  }

  // Check statuses (maybe we need a unit_status)
  console.log('\n=== Statuses ===');
  const statuses = await request('GET', '/statuses');
  console.log(JSON.stringify(statuses.data, null, 2));

  // Try with status
  console.log('\n=== Creating Unit with status ===');
  const unitWithStatus = {
    name: 'Apartment 101',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
    status: 'available'
  };
  const res1 = await request('POST', '/rf/units', unitWithStatus);
  console.log('With status string:', res1.status, JSON.stringify(res1.data));

  const unitWithStatusId = {
    name: 'Apartment 102',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
    rf_status_id: 26
  };
  const res2 = await request('POST', '/rf/units', unitWithStatusId);
  console.log('With rf_status_id:', res2.status, JSON.stringify(res2.data));
}

main();
