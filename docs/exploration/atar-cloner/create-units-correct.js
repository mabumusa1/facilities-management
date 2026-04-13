const https = require('https');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://api.goatar.com/api-management',
};

// Correct category/type mappings from /rf/units/create spec
const UNIT_TYPES = {
  residential: {
    category_id: 2,
    types: [
      { id: 17, name: 'شقة', type: 'apartment' },
      { id: 18, name: 'بنتهاوس', type: 'penthouse' },
      { id: 19, name: 'شقة دوبلكس', type: 'duplex_apartment' },
      { id: 20, name: 'فيلا دوبلكس', type: 'duplex_villa' },
      { id: 21, name: 'دور', type: 'floor_apartment' },
      { id: 22, name: 'فيلا', type: 'villa' },
      { id: 24, name: 'تاون هاوس', type: 'townhouse' },
      { id: 25, name: 'أرض', type: 'land' },
    ]
  },
  commercial: {
    category_id: 3,
    types: [
      { id: 26, name: 'محل', type: 'retail_store' },
      { id: 27, name: 'مطعم /مقهى', type: 'f&b_outlet' },
      { id: 28, name: 'مستودع', type: 'warehouse' },
      { id: 29, name: 'مخزن', type: 'storage' },
      { id: 30, name: 'مكتب', type: 'office' },
      { id: 31, name: 'أرض', type: 'land' },
    ]
  }
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
  console.log('Creating units with correct category/type mappings...\n');

  // First verify we have buildings
  const buildings = await request('GET', '/rf/buildings');
  console.log('Existing buildings:', buildings.data?.data?.length || 0);

  if (!buildings.data?.data?.length) {
    console.log('No buildings found. Creating a building first...');
    const buildingRes = await request('POST', '/rf/buildings', {
      name: 'Test Building',
      rf_community_id: 1
    });
    console.log('Building creation:', buildingRes.status, JSON.stringify(buildingRes.data));
  }

  const buildingId = buildings.data?.data?.[0]?.id || 1;
  const communityId = 1;

  console.log(`\nUsing community_id=${communityId}, building_id=${buildingId}\n`);

  // Test 1: Create a residential apartment
  console.log('=== Test 1: Residential Apartment ===');
  const apartmentData = {
    name: 'Apartment 101',
    rf_community_id: communityId,
    rf_building_id: buildingId,
    category_id: 2,  // Residential
    type_id: 17      // Apartment (valid for category 2)
  };
  console.log('Request:', JSON.stringify(apartmentData));
  const res1 = await request('POST', '/rf/units', apartmentData);
  console.log('Response:', res1.status);
  console.log('Data:', JSON.stringify(res1.data, null, 2));

  if (res1.status !== 200 && res1.status !== 201) {
    // Maybe we need more fields - check validation errors
    if (res1.data?.errors) {
      console.log('\nMissing fields detected. Adding more data...');
      const extendedData = {
        ...apartmentData,
        area: 120,
        bedrooms: 2,
        bathrooms: 2,
        yearly_rent: 60000,
        status_id: 26,  // Available status
      };
      console.log('Extended request:', JSON.stringify(extendedData));
      const res1b = await request('POST', '/rf/units', extendedData);
      console.log('Extended response:', res1b.status);
      console.log('Data:', JSON.stringify(res1b.data, null, 2));
    }
  }

  // Test 2: Create a commercial office
  console.log('\n=== Test 2: Commercial Office ===');
  const officeData = {
    name: 'Office 201',
    rf_community_id: communityId,
    rf_building_id: buildingId,
    category_id: 3,  // Commercial
    type_id: 30      // Office (valid for category 3)
  };
  console.log('Request:', JSON.stringify(officeData));
  const res2 = await request('POST', '/rf/units', officeData);
  console.log('Response:', res2.status);
  console.log('Data:', JSON.stringify(res2.data, null, 2));

  // Test 3: Create a villa
  console.log('\n=== Test 3: Residential Villa ===');
  const villaData = {
    name: 'Villa A1',
    rf_community_id: communityId,
    rf_building_id: buildingId,
    category_id: 2,  // Residential
    type_id: 22      // Villa (valid for category 2)
  };
  console.log('Request:', JSON.stringify(villaData));
  const res3 = await request('POST', '/rf/units', villaData);
  console.log('Response:', res3.status);
  console.log('Data:', JSON.stringify(res3.data, null, 2));

  // List all units to see what we created
  console.log('\n=== Listing All Units ===');
  const unitsList = await request('GET', '/rf/units');
  console.log('Total units:', unitsList.data?.data?.length || 0);
  if (unitsList.data?.data) {
    unitsList.data.data.forEach(unit => {
      console.log(`  - ${unit.id}: ${unit.name} (cat=${unit.category_id}, type=${unit.type_id})`);
    });
  }
}

main();
