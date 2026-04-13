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
  console.log('Checking for owners, landlords, and other requirements...\n');

  // Check owner/landlord endpoints
  console.log('=== Owner/Landlord Endpoints ===');
  const ownerEndpoints = [
    '/rf/owners',
    '/rf/landlords',
    '/rf/property-owners',
    '/rf/unit-owners',
    '/owners',
    '/landlords',
    '/rf/contacts',
    '/contacts',
    '/rf/customers',
    '/rf/clients',
  ];

  for (const ep of ownerEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
    }
  }

  // Check tenants endpoint
  console.log('\n=== Tenants Endpoints ===');
  const tenantEndpoints = [
    '/rf/tenants',
    '/tenants',
    '/rf/residents',
    '/residents',
  ];

  for (const ep of tenantEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
    }
  }

  // Check if there's a user/profile required
  console.log('\n=== User/Profile Endpoints ===');
  const userEndpoints = [
    '/rf/users',
    '/users',
    '/rf/admins',
    '/admins',
    '/rf/staff',
    '/rf/employees',
  ];

  for (const ep of userEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
    }
  }

  // Maybe units require a floor to be created first?
  console.log('\n=== Floor Endpoints ===');
  const floorEndpoints = [
    '/rf/floors',
    '/floors',
    '/rf/buildings/1/floors',
    '/rf/levels',
  ];

  for (const ep of floorEndpoints) {
    const res = await request('GET', ep);
    console.log(`${ep}: ${res.status}`);
    if (res.status === 200) {
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
    }
  }

  // Try creating with owner_id field
  console.log('\n=== Testing with owner_id ===');
  const testOwnerIds = [0, 1, null];
  for (const ownerId of testOwnerIds) {
    const body = {
      name: `Test Unit Owner ${ownerId}`,
      rf_community_id: 1,
      rf_building_id: 1,
      category_id: 2,
      type_id: 17,
      owner_id: ownerId
    };
    const res = await request('POST', '/rf/units', body);
    console.log(`owner_id=${ownerId}: ${res.status}`);
    if (res.status !== 400) {
      console.log(JSON.stringify(res.data).substring(0, 200));
    }
  }

  // Try different buildings
  console.log('\n=== Testing with different buildings ===');
  for (let buildingId = 1; buildingId <= 4; buildingId++) {
    const body = {
      name: `Test Unit Building ${buildingId}`,
      rf_community_id: 1,
      rf_building_id: buildingId,
      category_id: 2,
      type_id: 17
    };
    const res = await request('POST', '/rf/units', body);
    console.log(`building_id=${buildingId}: ${res.status}`);
    if (res.status !== 400) {
      console.log(JSON.stringify(res.data).substring(0, 200));
    }
  }

  // Try without building_id
  console.log('\n=== Testing without building_id ===');
  const noBuilding = {
    name: 'Test Unit No Building',
    rf_community_id: 1,
    category_id: 2,
    type_id: 17
  };
  const resNoBuilding = await request('POST', '/rf/units', noBuilding);
  console.log('Without building:', resNoBuilding.status);
  console.log(JSON.stringify(resNoBuilding.data).substring(0, 200));

  // Save all requests
  fs.writeFileSync('./owners-investigation-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to owners-investigation-log.json`);
}

main().catch(console.error);
