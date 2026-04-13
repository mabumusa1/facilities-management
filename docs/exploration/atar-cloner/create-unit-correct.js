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
    const url = CONFIG.baseUrl + endpoint;
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

    const requestLog = { timestamp: new Date().toISOString(), method, endpoint, body, response: null };

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
  console.log('Testing unit creation with correct format based on show API response...\n');

  // Based on the show response, the create should use similar field names
  // Key insight: map field needs full structure

  const unitPayload = {
    name: 'Test Unit API v2',
    category_id: 2,  // Residential
    type_id: 17,     // Apartment (17 for apartment, 18 for penthouse)
    rf_community_id: 1,
    rf_building_id: 1,  // Test Building 1
    rf_status_id: 26,   // Vacant
    map: {
      latitude: 24.7103488,
      longitude: 46.6878464,
      place_id: 'ChIJhz3eOQADLz4R_cqAJEJr0Qw',
      districtName: '3287 ,RHSA7555',
      formattedAddress: 'RHSA7555, 3287 Wadi Al Junah, As Sulimaniyah, 7555, Riyadh 12245, Saudi Arabia',
      latitudeDelta: 0.02,
      longitudeDelta: 0.009244060475161988,
      mapsLink: 'https://www.google.com/maps/search/?api=1&query=24.7103488,46.6878464'
    },
    specifications: [],
    features: [],
    photos: [],
    documents: [],
    floor_plans: [],
    rooms: [],
    areas: []
  };

  console.log('=== Test 1: Full payload with map object ===');
  console.log('Payload:', JSON.stringify(unitPayload, null, 2));
  const test1 = await request('POST', '/rf/units', unitPayload);
  console.log('Status:', test1.status);
  console.log('Response:', JSON.stringify(test1.data, null, 2));

  // Test 2: Minimal payload with map
  console.log('\n=== Test 2: Minimal payload with required fields ===');
  const minimalPayload = {
    name: 'Test Unit Minimal',
    category_id: 2,
    type_id: 17,
    rf_community_id: 1,
    map: {
      latitude: 24.7103488,
      longitude: 46.6878464,
      place_id: 'ChIJhz3eOQADLz4R_cqAJEJr0Qw',
      formattedAddress: 'RHSA7555, 3287 Wadi Al Junah, As Sulimaniyah, Riyadh, Saudi Arabia'
    }
  };
  console.log('Payload:', JSON.stringify(minimalPayload, null, 2));
  const test2 = await request('POST', '/rf/units', minimalPayload);
  console.log('Status:', test2.status);
  console.log('Response:', JSON.stringify(test2.data, null, 2));

  // Test 3: Try with 'category' and 'type' as objects instead of IDs
  console.log('\n=== Test 3: With category/type as nested objects ===');
  const objectPayload = {
    name: 'Test Unit Objects',
    category: { id: 2 },
    type: { id: 17 },
    rf_community: { id: 1 },
    rf_building: { id: 1 },
    map: {
      latitude: 24.7103488,
      longitude: 46.6878464,
      place_id: 'ChIJhz3eOQADLz4R_cqAJEJr0Qw',
      formattedAddress: 'RHSA7555, Riyadh, Saudi Arabia'
    }
  };
  console.log('Payload:', JSON.stringify(objectPayload, null, 2));
  const test3 = await request('POST', '/rf/units', objectPayload);
  console.log('Status:', test3.status);
  console.log('Response:', JSON.stringify(test3.data, null, 2));

  // Test 4: Try different field name variations
  console.log('\n=== Test 4: Alternative field names (community_id, building_id) ===');
  const altPayload = {
    name: 'Test Unit Alt Fields',
    category_id: 2,
    type_id: 17,
    community_id: 1,
    building_id: 1,
    status_id: 26,
    map: {
      latitude: 24.7103488,
      longitude: 46.6878464,
      place_id: 'ChIJhz3eOQADLz4R_cqAJEJr0Qw',
      formattedAddress: 'RHSA7555, Riyadh, Saudi Arabia'
    }
  };
  console.log('Payload:', JSON.stringify(altPayload, null, 2));
  const test4 = await request('POST', '/rf/units', altPayload);
  console.log('Status:', test4.status);
  console.log('Response:', JSON.stringify(test4.data, null, 2));

  // Test 5: With location instead of map
  console.log('\n=== Test 5: Using "location" field instead of "map" ===');
  const locationPayload = {
    name: 'Test Unit Location',
    category_id: 2,
    type_id: 17,
    rf_community_id: 1,
    location: {
      latitude: 24.7103488,
      longitude: 46.6878464,
      place_id: 'ChIJhz3eOQADLz4R_cqAJEJr0Qw',
      formattedAddress: 'RHSA7555, Riyadh, Saudi Arabia'
    }
  };
  console.log('Payload:', JSON.stringify(locationPayload, null, 2));
  const test5 = await request('POST', '/rf/units', locationPayload);
  console.log('Status:', test5.status);
  console.log('Response:', JSON.stringify(test5.data, null, 2));

  // Save all requests
  fs.writeFileSync('./create-unit-correct-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to create-unit-correct-log.json`);
}

main().catch(console.error);
