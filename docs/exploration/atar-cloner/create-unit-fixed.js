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
  console.log('Creating unit with correct rf_specification_id format...\n');

  // Test 1: Residential apartment with proper specifications
  console.log('=== Test 1: Apartment with rf_specification_id ===');
  const apartmentData = {
    name: 'Apartment 101',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,  // Residential
    type_id: 17,     // Apartment
    specifications: [
      { rf_specification_id: 4, value: 100 },  // Net_Unit_Area
      { rf_specification_id: 5, value: 2 },    // Bedrooms
      { rf_specification_id: 6, value: 2 },    // Bathrooms
      { rf_specification_id: 7, value: 1 },    // Guest_Rooms
      { rf_specification_id: 8, value: 1 },    // Lounges
      { rf_specification_id: 9, value: 3 },    // Floor_No
      { rf_specification_id: 10, value: 1 },   // Parking_Spaces
    ]
  };
  console.log('Request:', JSON.stringify(apartmentData, null, 2));
  const res1 = await request('POST', '/rf/units', apartmentData);
  console.log('Response:', res1.status);
  console.log('Data:', JSON.stringify(res1.data, null, 2));

  if (res1.status === 200 || res1.status === 201) {
    console.log('\n*** SUCCESS! ***\n');
  } else if (res1.data?.errors && Object.keys(res1.data.errors).length > 0) {
    console.log('\nValidation errors found. Analyzing...');
    const errors = res1.data.errors;
    console.log('Missing/invalid fields:', Object.keys(errors));
  }

  // Test 2: Try without specifications
  console.log('\n=== Test 2: Apartment without specifications ===');
  const apartmentNoSpecs = {
    name: 'Apartment 102',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  };
  console.log('Request:', JSON.stringify(apartmentNoSpecs, null, 2));
  const res2 = await request('POST', '/rf/units', apartmentNoSpecs);
  console.log('Response:', res2.status);
  console.log('Data:', JSON.stringify(res2.data, null, 2));

  // Test 3: Try with empty specifications array
  console.log('\n=== Test 3: Apartment with empty specifications array ===');
  const apartmentEmptySpecs = {
    name: 'Apartment 103',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
    specifications: []
  };
  console.log('Request:', JSON.stringify(apartmentEmptySpecs, null, 2));
  const res3 = await request('POST', '/rf/units', apartmentEmptySpecs);
  console.log('Response:', res3.status);
  console.log('Data:', JSON.stringify(res3.data, null, 2));

  // Test 4: Try with amenities
  console.log('\n=== Test 4: Apartment with amenities ===');
  const apartmentWithAmenities = {
    name: 'Apartment 104',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
    specifications: [
      { rf_specification_id: 4, value: 100 },
      { rf_specification_id: 5, value: 2 },
      { rf_specification_id: 6, value: 2 },
    ],
    amenities: [55, 56, 61]  // Smart access, Parking, Kitchen
  };
  console.log('Request:', JSON.stringify(apartmentWithAmenities, null, 2));
  const res4 = await request('POST', '/rf/units', apartmentWithAmenities);
  console.log('Response:', res4.status);
  console.log('Data:', JSON.stringify(res4.data, null, 2));

  // Test 5: Commercial office
  console.log('\n=== Test 5: Commercial Office ===');
  const officeData = {
    name: 'Office 201',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 3,  // Commercial
    type_id: 30,     // Office
    specifications: [
      { rf_specification_id: 4, value: 50 },  // Net_Unit_Area
      { rf_specification_id: 9, value: 5 },   // Floor_No
    ]
  };
  console.log('Request:', JSON.stringify(officeData, null, 2));
  const res5 = await request('POST', '/rf/units', officeData);
  console.log('Response:', res5.status);
  console.log('Data:', JSON.stringify(res5.data, null, 2));

  // Final check - list all units
  console.log('\n=== Final Unit List ===');
  const unitsList = await request('GET', '/rf/units');
  console.log('Total units:', unitsList.data?.data?.length || 0);
  if (unitsList.data?.data?.length > 0) {
    console.log('Units:');
    unitsList.data.data.forEach(unit => {
      console.log(`  - ${unit.id}: ${unit.name}`);
    });
  }
}

main();
