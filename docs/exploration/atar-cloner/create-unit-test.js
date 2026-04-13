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
  console.log('Testing unit creation with various field combinations...\n');

  // First, let's see if there's a /rf/unit-config or similar endpoint
  const configEndpoints = [
    '/rf/unit-config',
    '/rf/units-config',
    '/rf/config/units',
    '/rf/config',
    '/config',
    '/rf/settings',
    '/settings',
    '/rf/unit-settings',
    '/marketplace/settings',
    '/rf/categories/units',
    '/rf/types/units',
  ];

  console.log('Looking for configuration endpoints...');
  for (const ep of configEndpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: FOUND!`);
      console.log(JSON.stringify(res.data, null, 2));
    }
  }

  // Try the existing community detail to see structure
  console.log('\nGetting community detail for structure hints...');
  const community = await request('GET', '/rf/communities/1');
  console.log('Community structure:');
  console.log(JSON.stringify(community.data?.data, null, 2));

  // Try building detail
  console.log('\nGetting building detail for structure hints...');
  const building = await request('GET', '/rf/buildings/1');
  console.log('Building structure:');
  console.log(JSON.stringify(building.data?.data, null, 2));

  // Now try to create unit with the minimum fields
  console.log('\n\nTrying to create units with incremental field discovery...\n');

  // Test 1: Just required fields with numeric IDs 1-10
  for (let catId = 1; catId <= 5; catId++) {
    for (let typeId = 1; typeId <= 5; typeId++) {
      const body = {
        name: `Test ${catId}-${typeId}`,
        rf_community_id: 1,
        rf_building_id: 1,
        category_id: catId,
        type_id: typeId
      };
      const res = await request('POST', '/rf/units', body);

      // Check for specific error patterns
      if (res.status === 200 || res.status === 201) {
        console.log(`SUCCESS: category_id=${catId}, type_id=${typeId}`);
        console.log(JSON.stringify(res.data, null, 2));
        return; // Stop on first success
      } else if (res.data?.errors) {
        const errs = Object.keys(res.data.errors);
        // If category_id and type_id are not in errors, note them as potentially valid
        if (!errs.includes('category_id') && !errs.includes('type_id')) {
          console.log(`PARTIAL: cat=${catId}, type=${typeId} are valid. Missing: ${errs.join(', ')}`);

          // Try adding more fields
          const bodyExtended = {
            ...body,
            area: 100,
            bedrooms: 2,
            bathrooms: 1,
            yearly_rent: 50000,
            status_id: 26, // "متاحة" from statuses
          };
          const res2 = await request('POST', '/rf/units', bodyExtended);
          console.log(`  Extended attempt: ${res2.status}`);
          if (res2.status === 200 || res2.status === 201) {
            console.log('  SUCCESS with extended fields!');
            console.log(JSON.stringify(res2.data, null, 2));
            return;
          } else if (res2.data?.errors) {
            console.log(`  Still missing: ${Object.keys(res2.data.errors).join(', ')}`);
          } else {
            console.log(`  Error: ${JSON.stringify(res2.data)}`);
          }
        }
      } else if (res.status === 400) {
        // 400 might mean business logic error
        console.log(`400 for cat=${catId}, type=${typeId}: ${JSON.stringify(res.data)}`);
      }
    }
  }
}

main();
