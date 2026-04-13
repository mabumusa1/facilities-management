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

    const requestLog = { timestamp: new Date().toISOString(), method, endpoint, url, body, response: null };

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
  console.log('Creating owner with correct fields, then creating unit...\n');

  // Create owner with correct fields
  console.log('=== Creating Owner with Correct Fields ===');
  const ownerData = {
    first_name: 'Mohammed',
    last_name: 'Al-Saud',
    phone_country_code: 'SA',
    phone_number: '500000002',  // Without +966 prefix
    email: 'owner1@example.com',
    national_id: '1122334455',
  };
  const ownerRes = await request('POST', '/rf/owners', ownerData);
  console.log('POST /rf/owners:', ownerRes.status);
  console.log(JSON.stringify(ownerRes.data, null, 2));

  let ownerId = ownerRes.data?.data?.id;

  if (ownerRes.status === 200 || ownerRes.status === 201) {
    console.log(`\nOwner created successfully! ID: ${ownerId}`);
  } else if (ownerRes.data?.errors) {
    console.log('\nOwner creation failed with errors:', JSON.stringify(ownerRes.data.errors));

    // Try with different phone number format
    console.log('\nTrying different phone formats...');
    const phoneFormats = [
      { phone_number: '966500000003', phone_country_code: 'SA' },
      { phone_number: '0500000003', phone_country_code: 'SA' },
      { phone_number: '500000003', phone_country_code: 'SA' },
    ];

    for (const phoneFormat of phoneFormats) {
      const testOwner = {
        first_name: 'Test',
        last_name: 'Owner',
        ...phoneFormat,
        email: `owner_${Date.now()}@example.com`,
        national_id: String(Date.now()).slice(-10),
      };
      const testRes = await request('POST', '/rf/owners', testOwner);
      console.log(`Phone: ${phoneFormat.phone_number} => ${testRes.status}`);
      if (testRes.status === 200 || testRes.status === 201) {
        console.log('Success!');
        ownerId = testRes.data?.data?.id;
        console.log(JSON.stringify(testRes.data, null, 2));
        break;
      } else if (testRes.data?.errors) {
        console.log('Errors:', JSON.stringify(testRes.data.errors).substring(0, 200));
      }
    }
  }

  // Try creating unit with owner_id if we have one
  if (ownerId) {
    console.log(`\n=== Creating Unit with owner_id=${ownerId} ===`);
    const unitWithOwner = {
      name: 'Apartment With Owner',
      rf_community_id: 1,
      rf_building_id: 1,
      category_id: 2,
      type_id: 17,
      owner_id: ownerId
    };
    const unitRes = await request('POST', '/rf/units', unitWithOwner);
    console.log('POST /rf/units:', unitRes.status);
    console.log(JSON.stringify(unitRes.data, null, 2));
  }

  // List owners to see what we have
  console.log('\n=== Current Owners ===');
  const owners = await request('GET', '/rf/owners');
  console.log('Owners:', JSON.stringify(owners.data, null, 2));

  // Try creating unit with rf_owner_id instead
  if (ownerId) {
    console.log('\n=== Trying rf_owner_id field ===');
    const unitRfOwner = {
      name: 'Apartment RF Owner',
      rf_community_id: 1,
      rf_building_id: 1,
      category_id: 2,
      type_id: 17,
      rf_owner_id: ownerId
    };
    const unitRes2 = await request('POST', '/rf/units', unitRfOwner);
    console.log('POST /rf/units (rf_owner_id):', unitRes2.status);
    console.log(JSON.stringify(unitRes2.data, null, 2));
  }

  // Save all
  fs.writeFileSync('./complete-owner-log.json', JSON.stringify(allRequests, null, 2));
  console.log(`\nSaved ${allRequests.length} requests to complete-owner-log.json`);
}

main().catch(console.error);
