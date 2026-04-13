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
  console.log('ATAR API - CREATE LEASE WITH TENANT OBJECT');
  console.log('='.repeat(60));

  // Get units
  const units = await request('GET', '/rf/units');
  const unit = units.data?.data?.[0];
  console.log('Unit:', unit?.id, unit?.name);

  // Get tenant details
  console.log('\n--- GET /rf/tenants/4 ---');
  const tenantDetails = await request('GET', '/rf/tenants/4');
  console.log('Tenant details:', JSON.stringify(tenantDetails.data, null, 2));

  // Try to get rental types
  console.log('\n--- Explore rental_type options ---');

  // From lease-create-spec.json, rental_contract_type has:
  // 13 = Yearly, 14 = Monthly, 15 = Daily
  // Maybe rental_type should match these exactly or be strings like "yearly", "monthly", "daily"

  // Test different rental_type values
  const rentalTypes = ['yearly', 'monthly', 'daily', '13', '14', '15', 'Yearly Rental', 'ايجار سنوي'];

  for (const rt of rentalTypes) {
    console.log(`\n--- Testing rental_type = "${rt}" ---`);
    const payload = {
      created_at: '2026-04-11',
      start_date: '2026-04-15',
      end_date: '2027-04-14',
      handover_date: '2026-04-15',
      number_of_years: 1,
      number_of_months: 0,

      lease_unit_type: 2, // Residential category

      tenant_type: 'individual',
      tenant_id: 4,
      tenant: {
        id: 4,
        name: 'Khalid Test',
        national_id: '2233445566', // From our creation
        phone_number: '512345678',
        phone_country_code: 'SA',
        email: 'khalid.test@example.com',
      },

      autoGenerateLeaseNumber: true,
      contract_number: 'LEASE-' + Date.now(),

      rental_type: rt,
      rental_contract_type_id: 13,
      payment_schedule_id: 4,
      fit_out_status_id: 2,

      deposit_amount: 5000,
      annual_rent: 50000,
      rf_status_id: 30,

      units: [{
        id: unit.id,
        amount_type: 'fixed', // Try as string
        amount: 50000,
      }]
    };

    const result = await request('POST', '/rf/leases', payload);

    if (result.status === 200 || result.status === 201) {
      console.log('SUCCESS! Created lease with rental_type:', rt);
      console.log('Response:', JSON.stringify(result.data, null, 2).slice(0, 500));
      break;
    } else {
      // Check which errors remain
      const errors = result.data?.errors || {};
      const errorKeys = Object.keys(errors);
      console.log('Remaining error fields:', errorKeys.join(', '));
      if (!errors.rental_type) {
        console.log('rental_type ACCEPTED! But other errors:', JSON.stringify(errors, null, 2).slice(0, 400));
      }
    }
  }

  // Try amount_type values
  console.log('\n\n--- Testing amount_type values ---');
  const amountTypes = ['fixed', 'percentage', 'yearly', 'monthly', 'annual', 'per_unit', 'flat'];

  for (const at of amountTypes) {
    console.log(`\nTrying amount_type = "${at}"...`);
    const payload = {
      created_at: '2026-04-11',
      start_date: '2026-04-15',
      end_date: '2027-04-14',
      handover_date: '2026-04-15',
      number_of_years: 1,
      number_of_months: 0,
      lease_unit_type: 2,
      tenant_type: 'individual',
      tenant_id: 4,
      tenant: {
        id: 4,
        name: 'Khalid Test',
        national_id: '2233445566',
      },
      autoGenerateLeaseNumber: true,
      contract_number: 'LEASE-' + Date.now(),
      rental_type: 'yearly', // We'll see if this causes issues
      rental_contract_type_id: 13,
      payment_schedule_id: 4,
      deposit_amount: 5000,
      rf_status_id: 30,
      units: [{
        id: unit.id,
        amount_type: at,
        amount: 50000,
      }]
    };

    const result = await request('POST', '/rf/leases', payload);
    const errors = result.data?.errors || {};

    if (!errors['units.0.amount_type']) {
      console.log('amount_type ACCEPTED:', at);
      if (result.status === 200 || result.status === 201) {
        console.log('LEASE CREATED!');
        break;
      } else {
        console.log('But other errors:', Object.keys(errors).join(', '));
      }
    }
  }

  // Final check
  console.log('\n\n### CURRENT STATE ###');
  const leases = await request('GET', '/rf/leases');
  console.log('Leases:', JSON.stringify(leases.data?.data, null, 2));

  fs.writeFileSync('./create-lease-with-tenant-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to log file`);
}

main().catch(console.error);
