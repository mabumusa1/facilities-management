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

function logResult(name, result) {
  console.log(`\n=== ${name} ===`);
  console.log(`Status: ${result.status}`);
  if (result.status === 200) {
    const data = result.data?.data;
    if (Array.isArray(data)) {
      console.log(`Found ${data.length} items`);
      if (data.length > 0) {
        console.log('First item structure:', JSON.stringify(data[0], null, 2).slice(0, 500));
      }
    } else if (data) {
      console.log('Data structure:', JSON.stringify(data, null, 2).slice(0, 800));
    }
  } else {
    console.log('Response:', JSON.stringify(result.data, null, 2).slice(0, 300));
  }
}

async function main() {
  console.log('='.repeat(60));
  console.log('ATAR API - LEASING MODULE EXPLORATION');
  console.log('='.repeat(60));

  // ============================================
  // LEASING MAIN ENDPOINTS
  // ============================================
  console.log('\n\n### LEASING MAIN ENDPOINTS ###');

  const leasingEndpoints = [
    '/rf/leasing',
    '/rf/leases',
    '/rf/lease',
    '/rf/leasing/dashboard',
    '/rf/leasing/stats',
    '/rf/leasing/overview',
  ];

  for (const ep of leasingEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // VISITS
  // ============================================
  console.log('\n\n### VISITS ENDPOINTS ###');

  const visitEndpoints = [
    '/rf/visits',
    '/rf/leasing/visits',
    '/rf/visit',
    '/rf/visits/create',
    '/rf/visits/calendar',
    '/rf/visits/scheduled',
    '/rf/visits/completed',
    '/rf/visits/cancelled',
    '/rf/visits/pending',
  ];

  for (const ep of visitEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // APPLICATIONS
  // ============================================
  console.log('\n\n### APPLICATIONS ENDPOINTS ###');

  const applicationEndpoints = [
    '/rf/applications',
    '/rf/leasing/applications',
    '/rf/application',
    '/rf/applications/create',
    '/rf/applications/pending',
    '/rf/applications/approved',
    '/rf/applications/rejected',
    '/rf/rental-applications',
    '/rf/tenant-applications',
  ];

  for (const ep of applicationEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // QUOTES / QUOTATIONS
  // ============================================
  console.log('\n\n### QUOTES ENDPOINTS ###');

  const quoteEndpoints = [
    '/rf/quotes',
    '/rf/quotations',
    '/rf/leasing/quotes',
    '/rf/leasing/quotations',
    '/rf/quote',
    '/rf/quotation',
    '/rf/quotes/create',
    '/rf/quotations/create',
    '/rf/rental-quotes',
    '/rf/offers',
    '/rf/leasing/offers',
  ];

  for (const ep of quoteEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // LEASES / CONTRACTS
  // ============================================
  console.log('\n\n### LEASES/CONTRACTS ENDPOINTS ###');

  const leaseEndpoints = [
    '/rf/leases',
    '/rf/contracts',
    '/rf/leasing/contracts',
    '/rf/lease',
    '/rf/contract',
    '/rf/leases/create',
    '/rf/contracts/create',
    '/rf/leases/active',
    '/rf/leases/expired',
    '/rf/leases/pending',
    '/rf/rental-contracts',
    '/rf/tenancy-contracts',
  ];

  for (const ep of leaseEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // TENANTS
  // ============================================
  console.log('\n\n### TENANTS ENDPOINTS ###');

  const tenantEndpoints = [
    '/rf/tenants',
    '/rf/tenants/create',
    '/rf/tenant',
    '/rf/leasing/tenants',
    '/rf/renters',
  ];

  for (const ep of tenantEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // REQUESTS / INQUIRIES
  // ============================================
  console.log('\n\n### REQUESTS/INQUIRIES ENDPOINTS ###');

  const requestEndpoints = [
    '/rf/requests',
    '/rf/inquiries',
    '/rf/leasing/requests',
    '/rf/leasing/inquiries',
    '/rf/rental-requests',
    '/rf/booking-requests',
    '/rf/unit-requests',
  ];

  for (const ep of requestEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // PAYMENTS / INVOICES
  // ============================================
  console.log('\n\n### PAYMENTS/INVOICES ENDPOINTS ###');

  const paymentEndpoints = [
    '/rf/payments',
    '/rf/invoices',
    '/rf/leasing/payments',
    '/rf/leasing/invoices',
    '/rf/rent-payments',
    '/rf/rent-invoices',
    '/rf/receipts',
  ];

  for (const ep of paymentEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // STATUSES FOR LEASING
  // ============================================
  console.log('\n\n### LEASING STATUSES ###');

  const statusEndpoints = [
    '/rf/statuses?type=visit',
    '/rf/statuses?type=application',
    '/rf/statuses?type=quote',
    '/rf/statuses?type=quotation',
    '/rf/statuses?type=lease',
    '/rf/statuses?type=contract',
    '/rf/statuses?type=payment',
    '/rf/statuses?type=invoice',
    '/rf/statuses?type=request',
  ];

  for (const ep of statusEndpoints) {
    const result = await request('GET', ep);
    logResult(`GET ${ep}`, result);
  }

  // ============================================
  // MODULES CHECK
  // ============================================
  console.log('\n\n### MODULES ###');
  const modules = await request('GET', '/rf/modules');
  logResult('GET /rf/modules', modules);

  // Save all requests
  fs.writeFileSync('./leasing-exploration-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\n\nSaved ${allRequests.length} requests to leasing-exploration-log.json`);

  // Summary
  console.log('\n' + '='.repeat(60));
  console.log('SUMMARY - Working Endpoints (200 status)');
  console.log('='.repeat(60));
  const working = allRequests.filter(r => r.response?.status === 200);
  for (const req of working) {
    const itemCount = Array.isArray(req.response?.data?.data) ? ` (${req.response.data.data.length} items)` : '';
    console.log(`✅ ${req.method} ${req.endpoint}${itemCount}`);
  }
}

main().catch(console.error);
