const https = require('https');
const fs = require('fs');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://api.goatar.com/api-management',
};

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

    const requestLog = { timestamp: new Date().toISOString(), method, endpoint, url, body, headers: extraHeaders, response: null };

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        let parsedData;
        try { parsedData = JSON.parse(data); }
        catch { parsedData = data; }
        requestLog.response = { status: res.statusCode, headers: res.headers, data: parsedData };
        allRequests.push(requestLog);
        resolve({ status: res.statusCode, headers: res.headers, data: parsedData });
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
  console.log('Minimal unit creation test with various approaches...\n');

  // 1. Test with Accept-Language to get English errors
  console.log('=== Test 1: English Accept-Language ===');
  const test1 = await request('POST', '/rf/units', {
    name: 'Test Unit EN',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  }, { 'Accept-Language': 'en' });
  console.log('Status:', test1.status);
  console.log('Response:', JSON.stringify(test1.data, null, 2));

  // 2. Check API version headers
  console.log('\n=== Test 2: API Version Header ===');
  const test2 = await request('POST', '/rf/units', {
    name: 'Test Unit V2',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  }, { 'X-API-Version': '2.0', 'Accept-Language': 'en' });
  console.log('Status:', test2.status);
  console.log('Response:', JSON.stringify(test2.data, null, 2));

  // 3. Try with different content types
  console.log('\n=== Test 3: Content-Type variations ===');
  // Note: We'll keep JSON but try form data style values
  const test3 = await request('POST', '/rf/units', {
    "name": "Test Unit Form",
    "rf_community_id": 1,
    "rf_building_id": 1,
    "category_id": 2,
    "type_id": 17,
    "_method": "POST"  // Some APIs use method override
  });
  console.log('Status:', test3.status);
  console.log('Response:', JSON.stringify(test3.data, null, 2));

  // 4. Check if the unit list endpoint gives us any hints about field structure
  console.log('\n=== Test 4: Check unit list structure ===');
  const unitList = await request('GET', '/rf/units');
  console.log('Status:', unitList.status);
  console.log('Response:', JSON.stringify(unitList.data, null, 2));

  // 5. Try HEAD request to see headers
  console.log('\n=== Test 5: HEAD request ===');
  const headRes = await request('HEAD', '/rf/units');
  console.log('Status:', headRes.status);
  console.log('Headers:', JSON.stringify(headRes.headers, null, 2));

  // 6. Try with all possible fields from the spec
  console.log('\n=== Test 6: Full spec fields ===');
  const fullSpec = {
    name: 'Full Spec Unit',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
    owner_id: 3,
    rf_status_id: 26,
    specifications: [],
    amenities: [],
    images: [],
    documents: [],
    description: 'Test unit description',
    floor_no: 1,
    area: 100,
    price: 50000,
  };
  const test6 = await request('POST', '/rf/units', fullSpec);
  console.log('Status:', test6.status);
  console.log('Response:', JSON.stringify(test6.data, null, 2));

  // 7. Check if maybe community needs to be marketplace enabled
  console.log('\n=== Test 7: Check community marketplace status ===');
  const community = await request('GET', '/rf/communities/1');
  console.log('Community:', JSON.stringify(community.data?.data, null, 2));

  // 8. Check if there are any required headers we're missing
  console.log('\n=== Test 8: With Referer and Origin ===');
  const test8 = await request('POST', '/rf/units', {
    name: 'Test Unit Headers',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  }, {
    'Origin': 'https://goatar.com',
    'Referer': 'https://goatar.com/testbusiness123/units/create',
    'X-Requested-With': 'XMLHttpRequest'
  });
  console.log('Status:', test8.status);
  console.log('Response:', JSON.stringify(test8.data, null, 2));

  // 9. Check if there's a CSRF requirement
  console.log('\n=== Test 9: Get CSRF token first ===');
  const csrfRes = await request('GET', '/csrf');
  console.log('CSRF endpoint:', csrfRes.status);
  if (csrfRes.status === 200) {
    console.log('CSRF:', JSON.stringify(csrfRes.data, null, 2));
  }

  // Save all
  fs.writeFileSync('./minimal-unit-test-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to minimal-unit-test-log.json`);
}

main().catch(console.error);
