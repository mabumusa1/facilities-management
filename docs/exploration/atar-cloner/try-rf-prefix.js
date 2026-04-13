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
  console.log('Testing different field name formats...\n');

  // Test 1: with rf_ prefix for category and type
  console.log('=== Test 1: rf_category_id and rf_type_id ===');
  const test1 = {
    name: 'Apartment 101',
    rf_community_id: 1,
    rf_building_id: 1,
    rf_category_id: 2,
    rf_type_id: 17,
    rf_status_id: 26
  };
  const res1 = await request('POST', '/rf/units', test1);
  console.log('Response:', res1.status, JSON.stringify(res1.data));

  // Test 2: with rf_unit_category_id
  console.log('\n=== Test 2: rf_unit_category_id ===');
  const test2 = {
    name: 'Apartment 102',
    rf_community_id: 1,
    rf_building_id: 1,
    rf_unit_category_id: 2,
    rf_unit_type_id: 17,
    rf_status_id: 26
  };
  const res2 = await request('POST', '/rf/units', test2);
  console.log('Response:', res2.status, JSON.stringify(res2.data));

  // Test 3: property_category_id
  console.log('\n=== Test 3: property_category_id ===');
  const test3 = {
    name: 'Apartment 103',
    rf_community_id: 1,
    rf_building_id: 1,
    property_category_id: 2,
    property_type_id: 17,
    rf_status_id: 26
  };
  const res3 = await request('POST', '/rf/units', test3);
  console.log('Response:', res3.status, JSON.stringify(res3.data));

  // Test 4: Check unit create spec again for exact field names
  console.log('\n=== Getting unit create spec for field names ===');
  const createSpec = await request('GET', '/rf/units/create');
  // Look for field names in the response
  console.log('Keys in data:', Object.keys(createSpec.data?.data || {}));

  // Test 5: Try completely different approach - check if there's a form data structure
  console.log('\n=== Test 5: Try with unit object wrapper ===');
  const test5 = {
    unit: {
      name: 'Apartment 104',
      rf_community_id: 1,
      rf_building_id: 1,
      category_id: 2,
      type_id: 17,
    }
  };
  const res5 = await request('POST', '/rf/units', test5);
  console.log('Response:', res5.status, JSON.stringify(res5.data));

  // Test 6: Try with data wrapper
  console.log('\n=== Test 6: Try with data wrapper ===');
  const test6 = {
    data: {
      name: 'Apartment 105',
      rf_community_id: 1,
      rf_building_id: 1,
      category_id: 2,
      type_id: 17,
    }
  };
  const res6 = await request('POST', '/rf/units', test6);
  console.log('Response:', res6.status, JSON.stringify(res6.data));

  // Test 7: Check what happens if we remove rf_ prefix from community and building
  console.log('\n=== Test 7: community_id without rf_ prefix ===');
  const test7 = {
    name: 'Apartment 106',
    community_id: 1,
    building_id: 1,
    category_id: 2,
    type_id: 17,
  };
  const res7 = await request('POST', '/rf/units', test7);
  console.log('Response:', res7.status, JSON.stringify(res7.data));

  // Test 8: Try PUT method to existing endpoint
  console.log('\n=== Test 8: PUT method ===');
  const test8 = {
    name: 'Apartment 107',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  };
  const res8 = await request('PUT', '/rf/units', test8);
  console.log('Response:', res8.status, JSON.stringify(res8.data));

  // Test 9: Check if there's an edit endpoint that reveals field structure
  console.log('\n=== Test 9: Check edit endpoint structure ===');
  // Try to get edit form for a non-existent unit to see validation
  const res9 = await request('GET', '/rf/units/1/edit');
  console.log('Response:', res9.status, JSON.stringify(res9.data).substring(0, 500));

  // Test 10: Check the full list endpoint for field hints
  console.log('\n=== Test 10: Units list with all params ===');
  const res10 = await request('GET', '/rf/units?with_relations=1&include=all');
  console.log('Response:', res10.status, JSON.stringify(res10.data));
}

main();
