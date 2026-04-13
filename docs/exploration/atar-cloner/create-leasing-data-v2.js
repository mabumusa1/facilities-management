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
        'Accept-Language': 'en',
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
  console.log('='.repeat(60));
  console.log('ATAR API - CREATE LEASING DATA V2');
  console.log('='.repeat(60));

  // ============================================
  // STEP 1: TRY DIFFERENT TENANT FORMATS
  // ============================================
  console.log('\n\n### STEP 1: CREATE TENANT ###');

  // Based on error: phone_country_code max 2 chars, likely ISO code like "SA"
  console.log('\n--- POST /rf/tenants - Test: ISO code format ---');
  const tenantPayload = {
    name: 'Khalid Test',
    first_name: 'Khalid',
    last_name: 'Test',
    email: 'khalid.test@example.com',
    phone_number: '512345678', // 9 digits without country code
    phone_country_code: 'SA', // ISO country code (2 chars)
    type: 'individual',
    national_id: '2233445566',
  };
  const createTenant = await request('POST', '/rf/tenants', tenantPayload);
  console.log('Status:', createTenant.status);
  console.log('Response:', JSON.stringify(createTenant.data, null, 2).slice(0, 800));

  let tenantId = createTenant.data?.data?.id;
  console.log('Tenant ID:', tenantId);

  // ============================================
  // STEP 2: GET COMMON LISTS FOR ENUM VALUES
  // ============================================
  console.log('\n\n### STEP 2: GET COMMON LISTS ###');

  console.log('\n--- GET /rf/common-lists ---');
  const commonLists = await request('GET', '/rf/common-lists');
  console.log('Status:', commonLists.status);
  if (commonLists.status === 200) {
    fs.writeFileSync('./common-lists.json', JSON.stringify(commonLists.data, null, 2));
    console.log('Saved to common-lists.json');
    console.log('Keys:', Object.keys(commonLists.data?.data || {}));
  }

  // ============================================
  // STEP 3: CREATE LEASE WITH CORRECTED FORMAT
  // ============================================
  console.log('\n\n### STEP 3: CREATE LEASE ###');

  // Get units
  const units = await request('GET', '/rf/units');
  const unit = units.data?.data?.[0];

  if (unit) {
    console.log('Using unit:', unit.id, unit.name);

    // Lease with CORRECTED structure based on errors
    console.log('\n--- POST /rf/leases - Corrected structure ---');
    const leasePayload = {
      // Basic required fields
      created_at: '2026-04-11',
      start_date: '2026-04-15',
      end_date: '2027-04-14',
      handover_date: '2026-04-15',

      // Duration
      number_of_years: 1,
      number_of_months: 0,

      // Unit type as integer (1=single, 2=multiple?)
      lease_unit_type: 1,

      // Units as array of objects with id and amount_type
      units: [
        {
          id: unit.id,
          amount_type: 1, // Guess: 1=fixed, 2=percentage?
          amount: 50000, // Annual rent
        }
      ],

      // Tenant info
      tenant_type: 'individual',
      tenant: {
        name: 'Lease Tenant',
        first_name: 'Lease',
        last_name: 'Tenant',
        email: 'lease.test.tenant@example.com',
        phone_number: '555000001',
        phone_country_code: 'SA',
        national_id: '7788990011',
      },

      // Contract settings
      autoGenerateLeaseNumber: true,
      contract_number: 'LEASE-2026-002',
      rental_type: 13, // From rental_contract_type in lease-create-spec.json (Yearly=13)

      // Other specs
      rental_contract_type_id: 13,
      payment_schedule_id: 4,
      fit_out_status_id: 2,

      // Financial
      deposit_amount: 5000,
      annual_rent: 50000,

      // Status
      rf_status_id: 30,
    };

    const createLease = await request('POST', '/rf/leases', leasePayload);
    console.log('Status:', createLease.status);
    console.log('Response:', JSON.stringify(createLease.data, null, 2).slice(0, 1200));

    if (createLease.data?.errors) {
      console.log('\nErrors:', JSON.stringify(createLease.data.errors, null, 2));
    }
  }

  // ============================================
  // STEP 4: EXPLORE MORE SPECIFICATION ENDPOINTS
  // ============================================
  console.log('\n\n### STEP 4: EXPLORE SPECS ###');

  // Try to get specifications
  const specEndpoints = [
    '/rf/specifications',
    '/rf/common-lists/lease-unit-types',
    '/rf/common-lists/rental-types',
    '/rf/common-lists/amount-types',
    '/rf/leases/specifications',
  ];

  for (const endpoint of specEndpoints) {
    console.log(`\n--- GET ${endpoint} ---`);
    const result = await request('GET', endpoint);
    console.log('Status:', result.status);
    if (result.status === 200) {
      console.log('Found!', JSON.stringify(result.data, null, 2).slice(0, 400));
    }
  }

  // ============================================
  // STEP 5: CHECK CURRENT STATE
  // ============================================
  console.log('\n\n### STEP 5: CURRENT STATE ###');

  console.log('\n--- GET /rf/tenants ---');
  const tenants = await request('GET', '/rf/tenants');
  console.log('Tenants:', JSON.stringify(tenants.data?.data, null, 2));

  console.log('\n--- GET /rf/leases ---');
  const leases = await request('GET', '/rf/leases');
  console.log('Leases:', JSON.stringify(leases.data?.data, null, 2));

  // Save all requests
  fs.writeFileSync('./create-leasing-data-v2-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\n\nSaved ${allRequests.length} requests to create-leasing-data-v2-log.json`);
}

main().catch(console.error);
