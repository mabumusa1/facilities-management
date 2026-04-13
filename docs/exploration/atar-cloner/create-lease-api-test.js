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
  console.log('ATAR API - CREATE LEASE VIA API TEST');
  console.log('Based on captured browser payload structure');
  console.log('='.repeat(60));

  // Get units
  const units = await request('GET', '/rf/units');
  const availableUnits = units.data?.data?.filter(u => u.status?.id === 23); // Status 23 = Available
  const unit = availableUnits?.[0] || units.data?.data?.[0];
  console.log('Using unit:', unit?.id, unit?.name, '- Status:', unit?.status?.name_en);

  // Get tenants
  const tenants = await request('GET', '/rf/tenants');
  const tenant = tenants.data?.data?.[0];
  console.log('Using tenant:', tenant?.id, tenant?.name);

  if (!unit || !tenant) {
    console.log('No available unit or tenant!');
    return;
  }

  // Correct lease payload based on captured browser submission
  // Key discoveries:
  // - rental_type: "detailed" (not "yearly" or "annual")
  // - units[].rental_annual_type: "total"
  // - lease_unit_type: 2 (Residential)
  // - rental_contract_type_id: 13 (Yearly Rental)
  // - payment_schedule_id: 7 (Annual)

  const timestamp = Date.now();
  const leasePayload = {
    // Dates
    created_at: '2026-04-11',
    start_date: '2026-04-20',
    end_date: '2027-04-20',
    handover_date: '2026-04-20',

    // Duration
    number_of_years: 1,
    number_of_months: 0,

    // Lease type
    lease_unit_type: 2, // Residential

    // Tenant
    tenant_type: 'individual',
    tenant_id: tenant.id,
    tenant: {
      id: tenant.id,
      name: tenant.name,
      national_id: tenant.national_id,
      phone_number: tenant.phone_number,
      email: tenant.email,
    },

    // Contract settings
    autoGenerateLeaseNumber: true,

    // Rental type - KEY DISCOVERY!
    rental_type: 'detailed', // NOT 'yearly' - this was the missing piece
    rental_contract_type_id: 13, // Yearly Rental
    payment_schedule_id: 7, // Annual (not 4 which was Monthly)

    // Escalation
    lease_escalations_type: 'fixed',

    // Financial
    rental_total_amount: 60000,

    // Status
    rf_status_id: 30, // New

    // Units array with correct structure
    units: [{
      id: unit.id,
      rental_annual_type: 'total', // KEY DISCOVERY!
      annual_rental_amount: 60000,
    }]
  };

  console.log('\n--- Creating lease with corrected payload ---');
  console.log('Payload:', JSON.stringify(leasePayload, null, 2));

  const result = await request('POST', '/rf/leases', leasePayload);
  console.log('\nStatus:', result.status);

  if (result.status === 200 || result.status === 201) {
    console.log('SUCCESS! Lease created via API!');
    console.log('Response:', JSON.stringify(result.data, null, 2).slice(0, 1000));
  } else {
    console.log('Errors:', JSON.stringify(result.data?.errors || result.data, null, 2));
  }

  // Save logs
  fs.writeFileSync('./create-lease-api-test-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to create-lease-api-test-log.json`);
}

main().catch(console.error);
