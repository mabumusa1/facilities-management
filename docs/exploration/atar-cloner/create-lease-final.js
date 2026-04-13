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

async function testLeaseCreation(testName, leasePayload) {
  console.log(`\n--- TEST: ${testName} ---`);
  const result = await request('POST', '/rf/leases', leasePayload);
  console.log('Status:', result.status);

  if (result.status === 200 || result.status === 201) {
    console.log('SUCCESS! Response:', JSON.stringify(result.data, null, 2).slice(0, 800));
    return true;
  } else {
    console.log('Errors:', JSON.stringify(result.data?.errors || result.data, null, 2).slice(0, 600));
    return false;
  }
}

async function main() {
  console.log('='.repeat(60));
  console.log('ATAR API - CREATE LEASE FINAL TESTS');
  console.log('='.repeat(60));

  // Get units and tenants
  const units = await request('GET', '/rf/units');
  const tenants = await request('GET', '/rf/tenants');

  const unit = units.data?.data?.[0];
  const tenant = tenants.data?.data?.[0];

  console.log('Using unit:', unit?.id, unit?.name);
  console.log('Using tenant:', tenant?.id, tenant?.name);

  if (!unit || !tenant) {
    console.log('No unit or tenant available!');
    return;
  }

  // Base payload with required fields
  const basePayload = {
    created_at: '2026-04-11',
    start_date: '2026-04-15',
    end_date: '2027-04-14',
    handover_date: '2026-04-15',
    number_of_years: 1,
    number_of_months: 0,
    tenant_type: 'individual',
    tenant_id: tenant.id,
    autoGenerateLeaseNumber: true,
    contract_number: 'LEASE-TEST-' + Date.now(),
    rf_status_id: 30,
    rental_contract_type_id: 13,
    payment_schedule_id: 4,
    fit_out_status_id: 2,
    deposit_amount: 5000,
    annual_rent: 50000,
  };

  // Test 1: Try lease_unit_type as string "single" or "residential"
  await testLeaseCreation('lease_unit_type=single, rental_type=13, amount_type=fixed', {
    ...basePayload,
    lease_unit_type: 'single',
    rental_type: 13,
    units: [{
      id: unit.id,
      amount_type: 'fixed',
      amount: 50000,
    }]
  });

  // Test 2: Try lease_unit_type = residential category ID
  await testLeaseCreation('lease_unit_type=2 (residential), rental_type=yearly', {
    ...basePayload,
    contract_number: 'LEASE-TEST-' + Date.now(),
    lease_unit_type: 2,
    rental_type: 'yearly',
    units: [{
      id: unit.id,
      amount_type: 'fixed',
      amount: 50000,
    }]
  });

  // Test 3: Try with rental_type = "annual"
  await testLeaseCreation('rental_type=annual', {
    ...basePayload,
    contract_number: 'LEASE-TEST-' + Date.now(),
    lease_unit_type: 2,
    rental_type: 'annual',
    units: [{
      id: unit.id,
      amount_type: 'fixed',
      amount: 50000,
    }]
  });

  // Test 4: Try unit type ID from unit
  await testLeaseCreation('lease_unit_type from unit (17)', {
    ...basePayload,
    contract_number: 'LEASE-TEST-' + Date.now(),
    lease_unit_type: unit.type?.id || 17,
    rental_type: 'annual',
    units: [{
      id: unit.id,
      amount_type: 'fixed',
      amount: 50000,
    }]
  });

  // Test 5: Try amount_type as "yearly"
  await testLeaseCreation('amount_type=yearly', {
    ...basePayload,
    contract_number: 'LEASE-TEST-' + Date.now(),
    lease_unit_type: unit.category?.id || 2,
    rental_type: 'annual',
    units: [{
      id: unit.id,
      amount_type: 'yearly',
      amount: 50000,
    }]
  });

  // Test 6: Try with rf_unit_type_id instead
  await testLeaseCreation('rf_unit_type_id', {
    ...basePayload,
    contract_number: 'LEASE-TEST-' + Date.now(),
    rf_unit_type_id: unit.type?.id,
    lease_unit_type: unit.category?.id,
    rental_type: 'annual',
    units: [{
      id: unit.id,
      amount_type: 'annual',
      amount: 50000,
    }]
  });

  // Test 7: Minimal payload to see what's really required
  console.log('\n--- TEST: Minimal payload ---');
  const minimalPayload = {
    start_date: '2026-04-15',
    end_date: '2027-04-14',
    tenant_id: tenant.id,
    units: [{ id: unit.id }],
  };
  const minResult = await request('POST', '/rf/leases', minimalPayload);
  console.log('Status:', minResult.status);
  console.log('Errors:', JSON.stringify(minResult.data?.errors || minResult.data, null, 2).slice(0, 800));

  // Final check
  console.log('\n\n### FINAL STATE ###');
  const leases = await request('GET', '/rf/leases');
  console.log('Leases count:', leases.data?.data?.length || 0);
  if (leases.data?.data?.length > 0) {
    console.log('First lease:', JSON.stringify(leases.data.data[0], null, 2));
  }

  // Save logs
  fs.writeFileSync('./create-lease-final-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to create-lease-final-log.json`);
}

main().catch(console.error);
