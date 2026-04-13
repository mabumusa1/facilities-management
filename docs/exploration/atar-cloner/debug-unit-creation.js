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
  console.log('Debugging unit creation...\n');

  // 1. Check the building structure in detail
  console.log('=== Building Details ===');
  const building = await request('GET', '/rf/buildings/1');
  console.log('Building 1:', JSON.stringify(building.data, null, 2));

  // 2. Check subscription/limits
  console.log('\n=== Checking Subscription ===');
  const subscription = await request('GET', '/subscription');
  console.log('Subscription:', JSON.stringify(subscription.data, null, 2));

  // 3. Check tenant info
  console.log('\n=== Checking Tenant ===');
  const tenant = await request('GET', '/tenant');
  console.log('Tenant:', JSON.stringify(tenant.data, null, 2));

  // 4. Try creating unit with full specifications array
  console.log('\n=== Creating Unit with Specifications ===');
  const unitWithSpecs = {
    name: 'Apartment 101',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,  // Residential
    type_id: 17,     // Apartment
    specifications: [
      { id: 4, value: 100 },  // Net_Unit_Area
      { id: 5, value: 2 },    // Bedrooms
      { id: 6, value: 2 },    // Bathrooms
    ]
  };
  console.log('Request:', JSON.stringify(unitWithSpecs, null, 2));
  const res1 = await request('POST', '/rf/units', unitWithSpecs);
  console.log('Response:', res1.status);
  console.log('Data:', JSON.stringify(res1.data, null, 2));

  // 5. Try with different field structure (maybe specs are inline)
  console.log('\n=== Creating Unit with Inline Specs ===');
  const unitInlineSpecs = {
    name: 'Apartment 102',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,  // Residential
    type_id: 17,     // Apartment
    Net_Unit_Area: 100,
    Bedrooms: 2,
    Bathrooms: 2,
  };
  console.log('Request:', JSON.stringify(unitInlineSpecs, null, 2));
  const res2 = await request('POST', '/rf/units', unitInlineSpecs);
  console.log('Response:', res2.status);
  console.log('Data:', JSON.stringify(res2.data, null, 2));

  // 6. Try with unit_number
  console.log('\n=== Creating Unit with unit_number ===');
  const unitWithNumber = {
    name: 'Apartment 103',
    unit_number: '103',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  };
  console.log('Request:', JSON.stringify(unitWithNumber, null, 2));
  const res3 = await request('POST', '/rf/units', unitWithNumber);
  console.log('Response:', res3.status);
  console.log('Data:', JSON.stringify(res3.data, null, 2));

  // 7. Maybe the building needs to match the community - check building 1 community_id
  console.log('\n=== All Buildings with Community Info ===');
  const allBuildings = await request('GET', '/rf/buildings');
  console.log('Buildings:');
  allBuildings.data?.data?.forEach(b => {
    console.log(`  ID ${b.id}: ${b.name}, community_id=${b.rf_community_id}`);
  });

  // 8. Try OPTIONS request to see allowed methods/fields
  console.log('\n=== OPTIONS /rf/units ===');
  const optionsRes = await request('OPTIONS', '/rf/units');
  console.log('OPTIONS:', optionsRes.status, JSON.stringify(optionsRes.data));

  // 9. Check if there's a specific create endpoint
  console.log('\n=== POST to /rf/units/store ===');
  const storeRes = await request('POST', '/rf/units/store', unitWithNumber);
  console.log('Store response:', storeRes.status, JSON.stringify(storeRes.data));

  // 10. Check user permissions/roles
  console.log('\n=== User/Permissions Info ===');
  const meRes = await request('GET', '/me');
  console.log('Me:', JSON.stringify(meRes.data, null, 2));
}

main();
