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
  console.log('ATAR API - LEASING MODULE EXPLORATION (ACTUAL ENDPOINTS)');
  console.log('='.repeat(60));

  // ============================================
  // LEASES - From Swagger
  // ============================================
  console.log('\n\n### LEASES ###');

  // GET /rf/leases - List leases
  console.log('\n--- GET /rf/leases ---');
  const leases = await request('GET', '/rf/leases');
  console.log('Status:', leases.status);
  console.log('Data:', JSON.stringify(leases.data, null, 2).slice(0, 500));

  // GET /rf/leases/create - Get lease creation form spec
  console.log('\n--- GET /rf/leases/create ---');
  const leaseCreate = await request('GET', '/rf/leases/create');
  console.log('Status:', leaseCreate.status);
  if (leaseCreate.status === 200) {
    fs.writeFileSync('./lease-create-spec.json', JSON.stringify(leaseCreate.data, null, 2));
    console.log('Saved full spec to lease-create-spec.json');
    console.log('Keys:', Object.keys(leaseCreate.data?.data || {}));
  }

  // GET /rf/leases/statistics
  console.log('\n--- GET /rf/leases/statistics ---');
  const leaseStats = await request('GET', '/rf/leases/statistics');
  console.log('Status:', leaseStats.status);
  console.log('Data:', JSON.stringify(leaseStats.data, null, 2).slice(0, 500));

  // GET /rf/sub-leases
  console.log('\n--- GET /rf/sub-leases ---');
  const subLeases = await request('GET', '/rf/sub-leases');
  console.log('Status:', subLeases.status);
  console.log('Data:', JSON.stringify(subLeases.data, null, 2).slice(0, 500));

  // ============================================
  // LEADS (Visits/Applications in leasing)
  // ============================================
  console.log('\n\n### LEADS ###');

  // GET /rf/leads
  console.log('\n--- GET /rf/leads ---');
  const leads = await request('GET', '/rf/leads');
  console.log('Status:', leads.status);
  console.log('Data:', JSON.stringify(leads.data, null, 2).slice(0, 500));

  // Try to get lead creation spec
  console.log('\n--- GET /rf/leads/create ---');
  const leadCreate = await request('GET', '/rf/leads/create');
  console.log('Status:', leadCreate.status);
  if (leadCreate.status === 200) {
    fs.writeFileSync('./lead-create-spec.json', JSON.stringify(leadCreate.data, null, 2));
    console.log('Saved full spec to lead-create-spec.json');
  } else {
    console.log('Response:', JSON.stringify(leadCreate.data, null, 2).slice(0, 300));
  }

  // ============================================
  // MARKETPLACE VISITS
  // ============================================
  console.log('\n\n### MARKETPLACE VISITS ###');

  // GET /marketplace/admin/visits
  console.log('\n--- GET /marketplace/admin/visits ---');
  const mpVisits = await request('GET', '/marketplace/admin/visits');
  console.log('Status:', mpVisits.status);
  console.log('Data:', JSON.stringify(mpVisits.data, null, 2).slice(0, 500));

  // GET /marketplace/admin/settings/visits
  console.log('\n--- GET /marketplace/admin/settings/visits ---');
  const mpVisitSettings = await request('GET', '/marketplace/admin/settings/visits');
  console.log('Status:', mpVisitSettings.status);
  console.log('Data:', JSON.stringify(mpVisitSettings.data, null, 2).slice(0, 500));

  // ============================================
  // TENANTS
  // ============================================
  console.log('\n\n### TENANTS ###');

  // GET /rf/tenants
  console.log('\n--- GET /rf/tenants ---');
  const tenants = await request('GET', '/rf/tenants');
  console.log('Status:', tenants.status);
  console.log('Data:', JSON.stringify(tenants.data, null, 2).slice(0, 500));

  // ============================================
  // CONTACTS STATISTICS (includes tenant stats)
  // ============================================
  console.log('\n\n### CONTACTS STATISTICS ###');

  console.log('\n--- GET /rf/contacts/statistics ---');
  const contactStats = await request('GET', '/rf/contacts/statistics');
  console.log('Status:', contactStats.status);
  console.log('Data:', JSON.stringify(contactStats.data, null, 2).slice(0, 800));

  // ============================================
  // TRANSACTIONS (payments related to leases)
  // ============================================
  console.log('\n\n### TRANSACTIONS ###');

  console.log('\n--- GET /rf/transactions/ ---');
  const transactions = await request('GET', '/rf/transactions/');
  console.log('Status:', transactions.status);
  console.log('Data:', JSON.stringify(transactions.data, null, 2).slice(0, 500));

  // ============================================
  // USER REQUESTS
  // ============================================
  console.log('\n\n### USER REQUESTS ###');

  console.log('\n--- GET /rf/users/requests ---');
  const userRequests = await request('GET', '/rf/users/requests');
  console.log('Status:', userRequests.status);
  console.log('Data:', JSON.stringify(userRequests.data, null, 2).slice(0, 500));

  console.log('\n--- GET /rf/users/requests/types ---');
  const requestTypes = await request('GET', '/rf/users/requests/types');
  console.log('Status:', requestTypes.status);
  console.log('Data:', JSON.stringify(requestTypes.data, null, 2).slice(0, 800));

  console.log('\n--- GET /rf/requests/categories ---');
  const requestCats = await request('GET', '/rf/requests/categories');
  console.log('Status:', requestCats.status);
  console.log('Data:', JSON.stringify(requestCats.data, null, 2).slice(0, 800));

  // ============================================
  // STATUSES for leasing
  // ============================================
  console.log('\n\n### STATUSES ###');

  console.log('\n--- GET /rf/statuses ---');
  const statuses = await request('GET', '/rf/statuses');
  console.log('Status:', statuses.status);
  if (statuses.status === 200) {
    fs.writeFileSync('./all-statuses.json', JSON.stringify(statuses.data, null, 2));
    console.log('Saved to all-statuses.json');
    // Show different status types
    const types = [...new Set(statuses.data?.data?.map(s => s.type) || [])];
    console.log('Status types:', types);
  }

  // Save all requests
  fs.writeFileSync('./leasing-exploration-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\n\nSaved ${allRequests.length} requests to leasing-exploration-log.json`);
}

main().catch(console.error);
