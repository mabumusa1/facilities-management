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
  console.log('ATAR API - FULL LEASING MODULE EXPLORATION');
  console.log('='.repeat(60));

  // ============================================
  // PART 1: TENANT CREATION
  // ============================================
  console.log('\n\n### PART 1: TENANT CREATION ###');

  // GET /rf/tenants/create - Get tenant creation form spec
  console.log('\n--- GET /rf/tenants/create ---');
  const tenantCreate = await request('GET', '/rf/tenants/create');
  console.log('Status:', tenantCreate.status);
  if (tenantCreate.status === 200) {
    fs.writeFileSync('./tenant-create-spec.json', JSON.stringify(tenantCreate.data, null, 2));
    console.log('Saved full spec to tenant-create-spec.json');
    console.log('Keys:', Object.keys(tenantCreate.data?.data || {}));
  } else {
    console.log('Response:', JSON.stringify(tenantCreate.data, null, 2).slice(0, 300));
  }

  // Try creating a tenant
  console.log('\n--- POST /rf/tenants - Create Test Tenant ---');
  const tenantPayload = {
    name: 'Test Tenant',
    first_name: 'Test',
    last_name: 'Tenant',
    email: 'tenant1@example.com',
    phone_number: '+966500000010',
    phone_iso_code: 'SA',
    // Try both individual and company types
    type: 'individual',
    national_id: '1234567890',
  };
  const createTenant = await request('POST', '/rf/tenants', tenantPayload);
  console.log('Status:', createTenant.status);
  console.log('Response:', JSON.stringify(createTenant.data, null, 2).slice(0, 500));

  // ============================================
  // PART 2: VISITS
  // ============================================
  console.log('\n\n### PART 2: VISITS ###');

  // Try GET /rf/visits
  console.log('\n--- GET /rf/visits ---');
  const rfVisits = await request('GET', '/rf/visits');
  console.log('Status:', rfVisits.status);
  console.log('Response:', JSON.stringify(rfVisits.data, null, 2).slice(0, 300));

  // Try GET /rf/visits/create
  console.log('\n--- GET /rf/visits/create ---');
  const visitCreate = await request('GET', '/rf/visits/create');
  console.log('Status:', visitCreate.status);
  if (visitCreate.status === 200) {
    fs.writeFileSync('./visit-create-spec.json', JSON.stringify(visitCreate.data, null, 2));
    console.log('Saved full spec to visit-create-spec.json');
  } else {
    console.log('Response:', JSON.stringify(visitCreate.data, null, 2).slice(0, 300));
  }

  // ============================================
  // PART 3: APPLICATIONS
  // ============================================
  console.log('\n\n### PART 3: APPLICATIONS ###');

  // Try various application endpoints
  const appEndpoints = [
    '/rf/applications',
    '/rf/apps',
    '/rf/leasing/applications',
    '/rf/leasing/apps',
    '/marketplace/admin/applications',
    '/marketplace/admin/apps',
  ];

  for (const endpoint of appEndpoints) {
    console.log(`\n--- GET ${endpoint} ---`);
    const result = await request('GET', endpoint);
    console.log('Status:', result.status);
    if (result.status === 200) {
      console.log('Found! Data:', JSON.stringify(result.data, null, 2).slice(0, 300));
    }
  }

  // ============================================
  // PART 4: QUOTES
  // ============================================
  console.log('\n\n### PART 4: QUOTES ###');

  // Try various quote endpoints
  const quoteEndpoints = [
    '/rf/quotes',
    '/rf/leasing/quotes',
    '/marketplace/admin/quotes',
  ];

  for (const endpoint of quoteEndpoints) {
    console.log(`\n--- GET ${endpoint} ---`);
    const result = await request('GET', endpoint);
    console.log('Status:', result.status);
    if (result.status === 200) {
      console.log('Found! Data:', JSON.stringify(result.data, null, 2).slice(0, 300));
    }
  }

  // ============================================
  // PART 5: CONTACTS ENDPOINTS
  // ============================================
  console.log('\n\n### PART 5: CONTACTS ENDPOINTS ###');

  // GET /rf/contacts to understand contact types
  console.log('\n--- GET /rf/contacts ---');
  const contacts = await request('GET', '/rf/contacts');
  console.log('Status:', contacts.status);
  console.log('Response:', JSON.stringify(contacts.data, null, 2).slice(0, 500));

  // GET /rf/contacts/create
  console.log('\n--- GET /rf/contacts/create ---');
  const contactCreate = await request('GET', '/rf/contacts/create');
  console.log('Status:', contactCreate.status);
  if (contactCreate.status === 200) {
    fs.writeFileSync('./contact-create-spec.json', JSON.stringify(contactCreate.data, null, 2));
    console.log('Saved full spec to contact-create-spec.json');
    console.log('Keys:', Object.keys(contactCreate.data?.data || {}));
  } else {
    console.log('Response:', JSON.stringify(contactCreate.data, null, 2).slice(0, 300));
  }

  // ============================================
  // PART 6: LEASE CREATION ATTEMPT
  // ============================================
  console.log('\n\n### PART 6: LEASE CREATION ###');

  // Get current units
  console.log('\n--- GET /rf/units ---');
  const units = await request('GET', '/rf/units');
  console.log('Status:', units.status);
  console.log('Units:', JSON.stringify(units.data?.data?.slice?.(0, 2), null, 2));

  // Try creating a lease if we have units
  if (units.data?.data?.length > 0) {
    const unit = units.data.data[0];
    console.log('\n--- POST /rf/leases - Attempt to create lease ---');

    // Based on lease-create-spec.json
    const leasePayload = {
      rf_unit_id: unit.id,
      // Tenant info - try without tenant first to see error
      tenant_name: 'Test Lease Tenant',
      tenant_phone: '+966500000020',
      tenant_email: 'lease.tenant@example.com',
      // Contract details
      rental_contract_type_id: 13, // Yearly rental
      payment_schedule_id: 4, // Monthly
      fit_out_status_id: 2, // Fitted-out
      // Dates
      start_date: '2026-04-15',
      end_date: '2027-04-14',
      // Financial
      annual_rent: 50000,
      deposit_amount: 5000,
      // Status
      rf_status_id: 30, // New lease
    };

    const createLease = await request('POST', '/rf/leases', leasePayload);
    console.log('Status:', createLease.status);
    console.log('Response:', JSON.stringify(createLease.data, null, 2).slice(0, 800));

    if (createLease.data?.errors) {
      console.log('\nFull errors:', JSON.stringify(createLease.data.errors, null, 2));
    }
  }

  // ============================================
  // PART 7: EXPLORE MORE ENDPOINTS
  // ============================================
  console.log('\n\n### PART 7: MORE ENDPOINTS ###');

  const moreEndpoints = [
    '/rf/leases/available-units',
    '/rf/bookings',
    '/rf/reservations',
    '/rf/contracts',
    '/rf/rental-applications',
    '/marketplace/admin/leases',
    '/marketplace/admin/bookings',
  ];

  for (const endpoint of moreEndpoints) {
    console.log(`\n--- GET ${endpoint} ---`);
    const result = await request('GET', endpoint);
    console.log('Status:', result.status);
    if (result.status === 200) {
      console.log('Found! Data:', JSON.stringify(result.data, null, 2).slice(0, 300));
    }
  }

  // Save all requests
  fs.writeFileSync('./leasing-full-exploration-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\n\nSaved ${allRequests.length} requests to leasing-full-exploration-log.json`);
}

main().catch(console.error);
