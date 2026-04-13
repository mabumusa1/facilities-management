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
  console.log('Finding unit-specific statuses and required fields...\n');

  // Check statuses with type filter
  console.log('=== Status endpoints ===');
  const statusEndpoints = [
    '/statuses?type=unit',
    '/statuses?type=property',
    '/rf/statuses',
    '/rf/statuses?type=unit',
    '/rf/unit-statuses',
    '/rf/unit/statuses',
    '/rf/units/statuses',
    '/rf/lookups/statuses',
    '/rf/lookups',
  ];

  for (const ep of statusEndpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: ${res.status}`);
      console.log(JSON.stringify(res.data, null, 2).substring(0, 500));
      console.log('');
    }
  }

  // Get all statuses to see what types exist
  console.log('\n=== All status types ===');
  const allStatuses = await request('GET', '/statuses');
  if (allStatuses.data?.data) {
    const types = [...new Set(allStatuses.data.data.map(s => s.type))];
    console.log('Types found:', types);

    // Find any unit/property related ones
    const unitStatuses = allStatuses.data.data.filter(s =>
      s.type?.includes('unit') ||
      s.type?.includes('property') ||
      s.name_en?.toLowerCase().includes('available') ||
      s.name_en?.toLowerCase().includes('occupied') ||
      s.name_en?.toLowerCase().includes('vacant')
    );
    if (unitStatuses.length > 0) {
      console.log('Unit-related statuses:', JSON.stringify(unitStatuses, null, 2));
    }
  }

  // Try to find lookup values
  console.log('\n=== Lookup endpoints ===');
  const lookupEndpoints = [
    '/rf/lookups/all',
    '/rf/lookups/unit-status',
    '/rf/lookups/unit_status',
    '/lookups',
    '/lookups/all',
    '/rf/settings/lookups',
  ];

  for (const ep of lookupEndpoints) {
    const res = await request('GET', ep);
    if (res.status === 200) {
      console.log(`${ep}: ${res.status}`);
      console.log(JSON.stringify(res.data, null, 2).substring(0, 1000));
    }
  }

  // Try different required field combinations to get validation errors
  console.log('\n=== Testing required fields ===');

  // Test with missing fields one by one
  const baseUnit = {
    name: 'Test Unit',
    rf_community_id: 1,
    rf_building_id: 1,
    category_id: 2,
    type_id: 17,
  };

  // Add various fields
  const testFields = [
    { unit_no: '101' },
    { unit_code: 'APT-101' },
    { floor_no: 1 },
    { price: 50000 },
    { rent_price: 50000 },
    { yearly_rent: 50000 },
    { monthly_rent: 4000 },
    { area: 100 },
    { size: 100 },
    { is_available: true },
    { is_active: true },
    { is_published: true },
    { for_rent: true },
    { for_sale: false },
    { listing_type: 'rent' },
    { property_type: 'apartment' },
    { property_category: 'residential' },
    { owner_id: 1 },
    { created_by: 1 },
    { assigned_to: 1 },
  ];

  for (const field of testFields) {
    const testData = { ...baseUnit, ...field };
    const res = await request('POST', '/rf/units', testData);
    const fieldName = Object.keys(field)[0];
    if (res.status !== 400 || (res.data?.errors && Object.keys(res.data.errors).length > 0)) {
      console.log(`${fieldName}: ${res.status} - ${JSON.stringify(res.data).substring(0, 200)}`);
    }
  }

  // Try creating through the building's community
  console.log('\n=== Check community details ===');
  const community = await request('GET', '/rf/communities/1');
  console.log('Community:', JSON.stringify(community.data, null, 2).substring(0, 1500));
}

main();
