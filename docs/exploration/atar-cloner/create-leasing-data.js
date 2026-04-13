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
  console.log('ATAR API - CREATE LEASING DATA');
  console.log('='.repeat(60));

  // ============================================
  // STEP 1: CREATE TENANT WITH CORRECT FORMAT
  // ============================================
  console.log('\n\n### STEP 1: CREATE TENANT ###');

  // Try with corrected format
  console.log('\n--- POST /rf/tenants - Test 1: Corrected format ---');
  const tenantPayload1 = {
    name: 'Test Tenant One',
    first_name: 'Test',
    last_name: 'One',
    email: 'tenant.one@example.com',
    phone_number: '500000011', // Without + sign
    phone_country_code: '+966', // Country code separate
    phone_iso_code: 'SA',
    type: 'individual',
    national_id: '9876543210', // Different ID
  };
  const createTenant1 = await request('POST', '/rf/tenants', tenantPayload1);
  console.log('Status:', createTenant1.status);
  console.log('Response:', JSON.stringify(createTenant1.data, null, 2).slice(0, 800));

  // Try another format
  if (createTenant1.status !== 200 && createTenant1.status !== 201) {
    console.log('\n--- POST /rf/tenants - Test 2: Alternative format ---');
    const tenantPayload2 = {
      name: 'Ahmed Test Tenant',
      first_name: 'Ahmed',
      last_name: 'Test',
      email: 'ahmed.tenant@example.com',
      phone_number: '966500000012', // With country code prefix
      phone_country_code: '966',
      type: 'individual',
      national_id: '1122334455',
    };
    const createTenant2 = await request('POST', '/rf/tenants', tenantPayload2);
    console.log('Status:', createTenant2.status);
    console.log('Response:', JSON.stringify(createTenant2.data, null, 2).slice(0, 800));
  }

  // ============================================
  // STEP 2: EXPLORE BOOKINGS ENDPOINT
  // ============================================
  console.log('\n\n### STEP 2: EXPLORE BOOKINGS ###');

  // GET /rf/bookings/create to see form spec
  console.log('\n--- GET /rf/bookings/create ---');
  const bookingCreate = await request('GET', '/rf/bookings/create');
  console.log('Status:', bookingCreate.status);
  if (bookingCreate.status === 200) {
    fs.writeFileSync('./booking-create-spec.json', JSON.stringify(bookingCreate.data, null, 2));
    console.log('Saved to booking-create-spec.json');
    console.log('Keys:', Object.keys(bookingCreate.data?.data || {}));
  } else {
    console.log('Response:', JSON.stringify(bookingCreate.data, null, 2).slice(0, 300));
  }

  // ============================================
  // STEP 3: CREATE LEASE WITH CORRECT FIELDS
  // ============================================
  console.log('\n\n### STEP 3: CREATE LEASE ###');

  // Get units first
  const units = await request('GET', '/rf/units');
  const unit = units.data?.data?.[0];

  if (unit) {
    console.log('Using unit:', unit.id, unit.name);

    // Create lease with ALL required fields
    console.log('\n--- POST /rf/leases - Full payload ---');
    const leasePayload = {
      // Required fields from error messages
      created_at: '2026-04-11',
      handover_date: '2026-04-15',
      lease_unit_type: 'single', // or 'multiple'?
      number_of_years: 1,
      number_of_months: 0,
      units: [unit.id], // Array of unit IDs
      tenant_type: 'individual', // or 'company'
      autoGenerateLeaseNumber: true,
      contract_number: 'LEASE-2026-001',
      rental_type: 'yearly', // or from rental_contract_type

      // Additional fields from lease-create-spec.json
      rental_contract_type_id: 13, // Yearly rental
      payment_schedule_id: 4, // Monthly
      fit_out_status_id: 2, // Fitted-out

      // Financial info
      annual_rent: 50000,
      deposit_amount: 5000,

      // Tenant info (if creating inline)
      tenant_name: 'Lease Test Tenant',
      tenant_phone: '500000020',
      tenant_phone_country_code: '966',
      tenant_email: 'lease.tenant@example.com',
      tenant_national_id: '5566778899',

      // Status
      rf_status_id: 30, // New lease
    };

    const createLease = await request('POST', '/rf/leases', leasePayload);
    console.log('Status:', createLease.status);
    console.log('Response:', JSON.stringify(createLease.data, null, 2).slice(0, 1000));

    if (createLease.data?.errors) {
      console.log('\nFull errors:', JSON.stringify(createLease.data.errors, null, 2));
    }
  }

  // ============================================
  // STEP 4: EXPLORE VISIT CREATION VIA MARKETPLACE
  // ============================================
  console.log('\n\n### STEP 4: MARKETPLACE VISITS ###');

  // Try creating a visit via marketplace
  console.log('\n--- GET /marketplace/admin/visits/create ---');
  const mpVisitCreate = await request('GET', '/marketplace/admin/visits/create');
  console.log('Status:', mpVisitCreate.status);
  if (mpVisitCreate.status === 200) {
    fs.writeFileSync('./marketplace-visit-create-spec.json', JSON.stringify(mpVisitCreate.data, null, 2));
    console.log('Saved to marketplace-visit-create-spec.json');
    console.log('Keys:', Object.keys(mpVisitCreate.data?.data || {}));
  } else {
    console.log('Response:', JSON.stringify(mpVisitCreate.data, null, 2).slice(0, 300));
  }

  // Try POST /marketplace/admin/visits
  console.log('\n--- POST /marketplace/admin/visits - Test visit ---');
  const visitPayload = {
    unit_id: unit?.id,
    visitor_name: 'Test Visitor',
    visitor_phone: '500000030',
    visitor_email: 'visitor@example.com',
    visit_date: '2026-04-15',
    visit_time: '10:00',
    notes: 'Test visit for Unit ' + unit?.name,
  };
  const createVisit = await request('POST', '/marketplace/admin/visits', visitPayload);
  console.log('Status:', createVisit.status);
  console.log('Response:', JSON.stringify(createVisit.data, null, 2).slice(0, 800));

  // ============================================
  // STEP 5: CHECK CURRENT DATA
  // ============================================
  console.log('\n\n### STEP 5: CHECK CURRENT DATA ###');

  console.log('\n--- GET /rf/tenants ---');
  const tenants = await request('GET', '/rf/tenants');
  console.log('Tenants count:', tenants.data?.data?.length || 0);
  if (tenants.data?.data?.length > 0) {
    console.log('First tenant:', JSON.stringify(tenants.data.data[0], null, 2));
  }

  console.log('\n--- GET /rf/leases ---');
  const leases = await request('GET', '/rf/leases');
  console.log('Leases count:', leases.data?.data?.length || 0);

  console.log('\n--- GET /marketplace/admin/visits ---');
  const visits = await request('GET', '/marketplace/admin/visits');
  console.log('Visits count:', visits.data?.data?.length || 0);

  // Save all requests
  fs.writeFileSync('./create-leasing-data-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\n\nSaved ${allRequests.length} requests to create-leasing-data-log.json`);
}

main().catch(console.error);
