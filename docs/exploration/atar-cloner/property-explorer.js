const https = require('https');
const fs = require('fs');
const path = require('path');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://api.goatar.com/api-management',
  outputDir: './property-exploration',
  requestDelay: 300,
};

const results = {
  timestamp: new Date().toISOString(),
  communities: { list: [], create: null, validation: null },
  buildings: { list: [], create: null, validation: null },
  units: { list: [], create: null, validation: null },
  workflows: [],
  allRequests: []
};

// Setup
if (!fs.existsSync(CONFIG.outputDir)) {
  fs.mkdirSync(CONFIG.outputDir, { recursive: true });
}

function log(msg, type = 'info') {
  const icons = {
    info: '\x1b[36m[INFO]\x1b[0m',
    success: '\x1b[32m[OK]\x1b[0m',
    error: '\x1b[31m[ERR]\x1b[0m',
    warn: '\x1b[33m[WARN]\x1b[0m',
    data: '\x1b[35m[DATA]\x1b[0m',
    create: '\x1b[33m[CREATE]\x1b[0m'
  };
  console.log(`${icons[type] || icons.info} ${msg}`);
}

const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

// HTTP request helper
async function request(method, endpoint, body = null, description = '') {
  return new Promise((resolve) => {
    const url = endpoint.startsWith('http') ? endpoint : CONFIG.baseUrl + endpoint;
    const urlObj = new URL(url);

    const options = {
      hostname: urlObj.hostname,
      port: 443,
      path: urlObj.pathname + urlObj.search,
      method: method,
      headers: {
        'Authorization': `Bearer ${CONFIG.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Tenant': CONFIG.tenant,
        'Accept-Language': 'en'
      }
    };

    const bodyStr = body ? JSON.stringify(body) : null;
    if (bodyStr) {
      options.headers['Content-Length'] = Buffer.byteLength(bodyStr);
    }

    const startTime = Date.now();

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        const duration = Date.now() - startTime;
        let parsedBody;
        try {
          parsedBody = JSON.parse(data);
        } catch (e) {
          parsedBody = data;
        }

        const result = {
          description,
          request: { method, endpoint, body },
          response: {
            statusCode: res.statusCode,
            headers: res.headers,
            body: parsedBody,
            duration
          },
          timestamp: new Date().toISOString()
        };

        results.allRequests.push(result);
        resolve(result);
      });
    });

    req.on('error', (err) => {
      resolve({
        description,
        request: { method, endpoint, body },
        response: { error: err.message },
        timestamp: new Date().toISOString()
      });
    });

    req.setTimeout(30000);
    if (bodyStr) req.write(bodyStr);
    req.end();
  });
}

// ============ EXPLORATION FUNCTIONS ============

async function exploreCommunities() {
  log('\n========== EXPLORING COMMUNITIES ==========', 'info');

  // List communities with different params
  const listEndpoints = [
    '/rf/communities?is_paginate=1',
    '/rf/communities?is_active=1',
    '/rf/communities',
    '/marketplace/admin/communities?is_paginate=1&is_market_place=0',
    '/marketplace/admin/communities?is_paginate=1&is_market_place=1',
  ];

  for (const endpoint of listEndpoints) {
    log(`GET ${endpoint}`, 'info');
    const res = await request('GET', endpoint, null, `List communities: ${endpoint}`);
    log(`  -> ${res.response.statusCode} (${res.response.duration}ms)`,
        res.response.statusCode === 200 ? 'success' : 'error');

    if (res.response.body?.data) {
      results.communities.list.push({
        endpoint,
        count: Array.isArray(res.response.body.data) ? res.response.body.data.length : 'object',
        sample: Array.isArray(res.response.body.data) ? res.response.body.data[0] : res.response.body.data
      });
    }
    await sleep(CONFIG.requestDelay);
  }

  // Try to create a community (capture validation)
  log('\nTrying to CREATE community (empty body for validation)...', 'create');
  const createEmpty = await request('POST', '/rf/communities', {}, 'Create community - empty body');
  results.communities.validation = createEmpty.response.body;
  log(`  -> ${createEmpty.response.statusCode}`, createEmpty.response.statusCode === 422 ? 'warn' : 'error');

  // Try with partial data
  log('Trying to CREATE community (partial data)...', 'create');
  const createPartial = await request('POST', '/rf/communities', {
    name_en: 'Test Community',
    name_ar: 'مجتمع تجريبي'
  }, 'Create community - partial data');
  log(`  -> ${createPartial.response.statusCode}`, 'info');

  // Try store endpoint
  log('Trying /rf/communities/store...', 'create');
  const storeEmpty = await request('POST', '/rf/communities/store', {}, 'Store community - empty');
  log(`  -> ${storeEmpty.response.statusCode}`, 'info');

  return results.communities;
}

async function exploreBuildings() {
  log('\n========== EXPLORING BUILDINGS ==========', 'info');

  // List buildings
  const listEndpoints = [
    '/rf/buildings',
    '/rf/buildings?is_active=1',
    '/rf/buildings?is_paginate=1',
  ];

  for (const endpoint of listEndpoints) {
    log(`GET ${endpoint}`, 'info');
    const res = await request('GET', endpoint, null, `List buildings: ${endpoint}`);
    log(`  -> ${res.response.statusCode} (${res.response.duration}ms)`,
        res.response.statusCode === 200 ? 'success' : 'error');

    if (res.response.body?.data) {
      results.buildings.list.push({
        endpoint,
        count: Array.isArray(res.response.body.data) ? res.response.body.data.length : 'object',
        sample: Array.isArray(res.response.body.data) ? res.response.body.data[0] : res.response.body.data
      });
    }
    await sleep(CONFIG.requestDelay);
  }

  // Try to create a building
  log('\nTrying to CREATE building (empty body)...', 'create');
  const createEmpty = await request('POST', '/rf/buildings', {}, 'Create building - empty');
  results.buildings.validation = createEmpty.response.body;
  log(`  -> ${createEmpty.response.statusCode}`, 'info');

  log('Trying /rf/buildings/store...', 'create');
  const storeEmpty = await request('POST', '/rf/buildings/store', {}, 'Store building - empty');
  log(`  -> ${storeEmpty.response.statusCode}`, 'info');

  // Try with partial data
  log('Trying to CREATE building (with community_id)...', 'create');
  const createPartial = await request('POST', '/rf/buildings', {
    name_en: 'Test Building',
    name_ar: 'مبنى تجريبي',
    community_id: 1
  }, 'Create building - partial');
  log(`  -> ${createPartial.response.statusCode}`, 'info');

  return results.buildings;
}

async function exploreUnits() {
  log('\n========== EXPLORING UNITS ==========', 'info');

  // List units
  const listEndpoints = [
    '/rf/units',
    '/rf/units?is_paginate=1',
    '/rf/units?is_active=1',
    '/marketplace/admin/units',
    '/marketplace/admin/units?is_paginate=1',
  ];

  for (const endpoint of listEndpoints) {
    log(`GET ${endpoint}`, 'info');
    const res = await request('GET', endpoint, null, `List units: ${endpoint}`);
    log(`  -> ${res.response.statusCode} (${res.response.duration}ms)`,
        res.response.statusCode === 200 ? 'success' : 'error');

    if (res.response.body?.data) {
      results.units.list.push({
        endpoint,
        count: Array.isArray(res.response.body.data) ? res.response.body.data.length : 'object',
        sample: Array.isArray(res.response.body.data) ? res.response.body.data[0] : res.response.body.data
      });
    }
    await sleep(CONFIG.requestDelay);
  }

  // Try to create a unit
  log('\nTrying to CREATE unit (empty body)...', 'create');
  const createEmpty = await request('POST', '/rf/units', {}, 'Create unit - empty');
  results.units.validation = createEmpty.response.body;
  log(`  -> ${createEmpty.response.statusCode}`, 'info');

  log('Trying /rf/units/store...', 'create');
  const storeEmpty = await request('POST', '/rf/units/store', {}, 'Store unit - empty');
  log(`  -> ${storeEmpty.response.statusCode}`, 'info');

  // Try with partial data
  log('Trying to CREATE unit (partial data)...', 'create');
  const createPartial = await request('POST', '/rf/units', {
    name_en: 'Unit 101',
    name_ar: 'وحدة 101',
    building_id: 1,
    unit_type: 'apartment'
  }, 'Create unit - partial');
  log(`  -> ${createPartial.response.statusCode}`, 'info');

  return results.units;
}

async function explorePropertyTypes() {
  log('\n========== EXPLORING PROPERTY TYPES & LOOKUPS ==========', 'info');

  const lookupEndpoints = [
    '/rf/unit-types',
    '/rf/property-types',
    '/rf/amenities',
    '/rf/facilities',
    '/rf/floor-plans',
    '/rf/rental-types',
    '/rf/payment-schedules',
    '/rf/contract-types',
    '/rf/unit-statuses',
    '/rf/common-lists',
    '/rf/common-lists?type=unit_types',
    '/rf/common-lists?type=property_types',
    '/rf/common-lists?type=amenities',
  ];

  const lookups = {};

  for (const endpoint of lookupEndpoints) {
    log(`GET ${endpoint}`, 'info');
    const res = await request('GET', endpoint, null, `Lookup: ${endpoint}`);
    log(`  -> ${res.response.statusCode}`,
        res.response.statusCode === 200 ? 'success' : 'error');

    if (res.response.statusCode === 200 && res.response.body?.data) {
      lookups[endpoint] = res.response.body.data;
    }
    await sleep(CONFIG.requestDelay);
  }

  return lookups;
}

async function exploreDetailEndpoints() {
  log('\n========== EXPLORING DETAIL ENDPOINTS ==========', 'info');

  // Try to get details for ID 1 (might exist or give us error structure)
  const detailEndpoints = [
    '/rf/communities/1',
    '/rf/communities/1/buildings',
    '/rf/communities/1/units',
    '/rf/buildings/1',
    '/rf/buildings/1/units',
    '/rf/units/1',
    '/rf/units/1/leases',
    '/rf/units/1/transactions',
  ];

  const details = {};

  for (const endpoint of detailEndpoints) {
    log(`GET ${endpoint}`, 'info');
    const res = await request('GET', endpoint, null, `Detail: ${endpoint}`);
    log(`  -> ${res.response.statusCode}`,
        res.response.statusCode === 200 ? 'success' : 'warn');

    details[endpoint] = {
      statusCode: res.response.statusCode,
      body: res.response.body
    };
    await sleep(CONFIG.requestDelay);
  }

  return details;
}

async function exploreUpdateEndpoints() {
  log('\n========== EXPLORING UPDATE ENDPOINTS ==========', 'info');

  // Try PUT/PATCH to understand update validation
  const updateTests = [
    { method: 'PUT', endpoint: '/rf/communities/1', body: {} },
    { method: 'PATCH', endpoint: '/rf/communities/1', body: {} },
    { method: 'PUT', endpoint: '/rf/buildings/1', body: {} },
    { method: 'PATCH', endpoint: '/rf/buildings/1', body: {} },
    { method: 'PUT', endpoint: '/rf/units/1', body: {} },
    { method: 'PATCH', endpoint: '/rf/units/1', body: {} },
    { method: 'POST', endpoint: '/rf/communities/1/update', body: {} },
    { method: 'POST', endpoint: '/rf/buildings/1/update', body: {} },
    { method: 'POST', endpoint: '/rf/units/1/update', body: {} },
  ];

  const updates = {};

  for (const test of updateTests) {
    log(`${test.method} ${test.endpoint}`, 'info');
    const res = await request(test.method, test.endpoint, test.body, `Update: ${test.method} ${test.endpoint}`);
    log(`  -> ${res.response.statusCode}`, 'info');

    updates[`${test.method}_${test.endpoint}`] = {
      statusCode: res.response.statusCode,
      body: res.response.body
    };
    await sleep(CONFIG.requestDelay);
  }

  return updates;
}

async function exploreDeleteEndpoints() {
  log('\n========== EXPLORING DELETE ENDPOINTS ==========', 'info');

  // Try DELETE to understand delete validation (using ID 999999 to avoid actual deletion)
  const deleteTests = [
    { method: 'DELETE', endpoint: '/rf/communities/999999' },
    { method: 'DELETE', endpoint: '/rf/buildings/999999' },
    { method: 'DELETE', endpoint: '/rf/units/999999' },
    { method: 'POST', endpoint: '/rf/communities/999999/delete', body: {} },
    { method: 'POST', endpoint: '/rf/buildings/999999/delete', body: {} },
    { method: 'POST', endpoint: '/rf/units/999999/delete', body: {} },
  ];

  const deletes = {};

  for (const test of deleteTests) {
    log(`${test.method} ${test.endpoint}`, 'info');
    const res = await request(test.method, test.endpoint, test.body || null, `Delete: ${test.method} ${test.endpoint}`);
    log(`  -> ${res.response.statusCode}`, 'info');

    deletes[`${test.method}_${test.endpoint}`] = {
      statusCode: res.response.statusCode,
      body: res.response.body
    };
    await sleep(CONFIG.requestDelay);
  }

  return deletes;
}

async function createTestData() {
  log('\n========== CREATING TEST DATA ==========', 'create');

  const testData = {
    community: null,
    building: null,
    units: []
  };

  // First get required lookups
  log('Getting required lookups...', 'info');
  const commonLists = await request('GET', '/rf/common-lists', null, 'Get common lists');

  // Attempt to create a community with full data
  log('\nCreating test community with full data...', 'create');
  const communityData = {
    name_en: 'Test Community ' + Date.now(),
    name_ar: 'مجتمع تجريبي ' + Date.now(),
    description_en: 'A test community for API exploration',
    description_ar: 'مجتمع تجريبي لاستكشاف API',
    address_en: '123 Test Street',
    address_ar: 'شارع التجربة 123',
    city_id: 1,
    district_id: 1,
    latitude: 24.7136,
    longitude: 46.6753,
    is_active: true,
    status: 'active'
  };

  const communityRes = await request('POST', '/rf/communities', communityData, 'Create full community');
  testData.community = communityRes;
  log(`  -> ${communityRes.response.statusCode}`,
      communityRes.response.statusCode === 200 || communityRes.response.statusCode === 201 ? 'success' : 'warn');

  if (communityRes.response.body?.errors) {
    log('  Validation errors:', 'data');
    console.log(JSON.stringify(communityRes.response.body.errors, null, 2));
  }

  // Try /store endpoint
  log('\nTrying /rf/communities/store...', 'create');
  const communityStoreRes = await request('POST', '/rf/communities/store', communityData, 'Store full community');
  log(`  -> ${communityStoreRes.response.statusCode}`, 'info');

  // Create building
  log('\nCreating test building...', 'create');
  const buildingData = {
    name_en: 'Test Building ' + Date.now(),
    name_ar: 'مبنى تجريبي ' + Date.now(),
    description_en: 'A test building',
    description_ar: 'مبنى تجريبي',
    community_id: communityRes.response.body?.data?.id || 1,
    number_of_floors: 5,
    number_of_units: 20,
    year_built: 2020,
    is_active: true
  };

  const buildingRes = await request('POST', '/rf/buildings', buildingData, 'Create full building');
  testData.building = buildingRes;
  log(`  -> ${buildingRes.response.statusCode}`,
      buildingRes.response.statusCode === 200 || buildingRes.response.statusCode === 201 ? 'success' : 'warn');

  if (buildingRes.response.body?.errors) {
    log('  Validation errors:', 'data');
    console.log(JSON.stringify(buildingRes.response.body.errors, null, 2));
  }

  // Try /store endpoint
  log('\nTrying /rf/buildings/store...', 'create');
  const buildingStoreRes = await request('POST', '/rf/buildings/store', buildingData, 'Store full building');
  log(`  -> ${buildingStoreRes.response.statusCode}`, 'info');

  // Create units
  log('\nCreating test units...', 'create');
  const unitTypes = ['apartment', 'studio', 'villa', 'office'];

  for (let i = 0; i < 3; i++) {
    const unitData = {
      name_en: `Unit ${100 + i}`,
      name_ar: `وحدة ${100 + i}`,
      unit_number: `${100 + i}`,
      building_id: buildingRes.response.body?.data?.id || 1,
      community_id: communityRes.response.body?.data?.id || 1,
      unit_type: unitTypes[i % unitTypes.length],
      floor_number: i + 1,
      bedrooms: i + 1,
      bathrooms: i + 1,
      area: 100 + (i * 50),
      area_unit: 'sqm',
      rental_price: 50000 + (i * 10000),
      sale_price: 500000 + (i * 100000),
      is_active: true,
      is_available: true,
      status: 'available'
    };

    const unitRes = await request('POST', '/rf/units', unitData, `Create unit ${i + 1}`);
    testData.units.push(unitRes);
    log(`  Unit ${i + 1} -> ${unitRes.response.statusCode}`,
        unitRes.response.statusCode === 200 || unitRes.response.statusCode === 201 ? 'success' : 'warn');

    if (unitRes.response.body?.errors) {
      console.log(JSON.stringify(unitRes.response.body.errors, null, 2));
    }

    // Also try store endpoint
    const unitStoreRes = await request('POST', '/rf/units/store', unitData, `Store unit ${i + 1}`);
    log(`  Unit ${i + 1} (store) -> ${unitStoreRes.response.statusCode}`, 'info');

    await sleep(CONFIG.requestDelay);
  }

  return testData;
}

async function exploreFiltersAndSearch() {
  log('\n========== EXPLORING FILTERS & SEARCH ==========', 'info');

  const filterTests = [
    '/rf/units?status=available',
    '/rf/units?status=occupied',
    '/rf/units?unit_type=apartment',
    '/rf/units?bedrooms=2',
    '/rf/units?min_price=10000&max_price=100000',
    '/rf/units?community_id=1',
    '/rf/units?building_id=1',
    '/rf/units?search=test',
    '/rf/units?sort_by=price&sort_order=asc',
    '/rf/units?sort_by=created_at&sort_order=desc',
    '/rf/buildings?community_id=1',
    '/rf/buildings?search=test',
    '/rf/communities?search=test',
    '/rf/communities?city_id=1',
  ];

  const filters = {};

  for (const endpoint of filterTests) {
    log(`GET ${endpoint}`, 'info');
    const res = await request('GET', endpoint, null, `Filter: ${endpoint}`);
    log(`  -> ${res.response.statusCode}`,
        res.response.statusCode === 200 ? 'success' : 'warn');

    filters[endpoint] = {
      statusCode: res.response.statusCode,
      count: res.response.body?.data?.length || 0
    };
    await sleep(CONFIG.requestDelay);
  }

  return filters;
}

async function exploreExcelImportExport() {
  log('\n========== EXPLORING EXCEL IMPORT/EXPORT ==========', 'info');

  const excelEndpoints = [
    { method: 'GET', endpoint: '/rf/units/export' },
    { method: 'GET', endpoint: '/rf/units/export/excel' },
    { method: 'GET', endpoint: '/rf/units/template' },
    { method: 'GET', endpoint: '/rf/buildings/export' },
    { method: 'GET', endpoint: '/rf/communities/export' },
    { method: 'POST', endpoint: '/rf/units/import', body: {} },
    { method: 'POST', endpoint: '/rf/excel-sheets', body: {} },
  ];

  const excel = {};

  for (const test of excelEndpoints) {
    log(`${test.method} ${test.endpoint}`, 'info');
    const res = await request(test.method, test.endpoint, test.body || null, `Excel: ${test.method} ${test.endpoint}`);
    log(`  -> ${res.response.statusCode}`, 'info');

    excel[`${test.method}_${test.endpoint}`] = {
      statusCode: res.response.statusCode,
      body: typeof res.response.body === 'object' ? res.response.body : 'binary/file'
    };
    await sleep(CONFIG.requestDelay);
  }

  return excel;
}

// ============ MAIN ============

async function main() {
  console.log('\n' + '='.repeat(60));
  console.log('  ATAR PROPERTY EXPLORER');
  console.log('  Deep exploration of Properties module');
  console.log('='.repeat(60));
  console.log(`\nTenant: ${CONFIG.tenant}`);
  console.log(`Output: ${CONFIG.outputDir}`);
  console.log('='.repeat(60) + '\n');

  // Run all explorations
  const explorations = {
    communities: await exploreCommunities(),
    buildings: await exploreBuildings(),
    units: await exploreUnits(),
    propertyTypes: await explorePropertyTypes(),
    details: await exploreDetailEndpoints(),
    updates: await exploreUpdateEndpoints(),
    deletes: await exploreDeleteEndpoints(),
    testData: await createTestData(),
    filters: await exploreFiltersAndSearch(),
    excel: await exploreExcelImportExport()
  };

  results.explorations = explorations;

  // Save results
  fs.writeFileSync(
    path.join(CONFIG.outputDir, 'exploration-results.json'),
    JSON.stringify(results, null, 2)
  );

  // Save all requests for swagger generation
  fs.writeFileSync(
    path.join(CONFIG.outputDir, 'all-requests.json'),
    JSON.stringify(results.allRequests, null, 2)
  );

  // Generate report
  let report = `# Property Exploration Report\n\n`;
  report += `**Generated:** ${results.timestamp}\n\n`;
  report += `**Total Requests:** ${results.allRequests.length}\n\n`;

  report += `## Summary\n\n`;
  report += `| Category | Requests | Success | Errors |\n`;
  report += `|----------|----------|---------|--------|\n`;

  const categories = {};
  results.allRequests.forEach(r => {
    const cat = r.description.split(':')[0] || 'Other';
    if (!categories[cat]) categories[cat] = { total: 0, success: 0, errors: 0 };
    categories[cat].total++;
    if (r.response.statusCode >= 200 && r.response.statusCode < 300) {
      categories[cat].success++;
    } else {
      categories[cat].errors++;
    }
  });

  Object.entries(categories).forEach(([cat, stats]) => {
    report += `| ${cat} | ${stats.total} | ${stats.success} | ${stats.errors} |\n`;
  });

  report += `\n## Validation Errors Discovered\n\n`;

  if (results.communities.validation?.errors) {
    report += `### Community Creation\n\`\`\`json\n${JSON.stringify(results.communities.validation.errors, null, 2)}\n\`\`\`\n\n`;
  }
  if (results.buildings.validation?.errors) {
    report += `### Building Creation\n\`\`\`json\n${JSON.stringify(results.buildings.validation.errors, null, 2)}\n\`\`\`\n\n`;
  }
  if (results.units.validation?.errors) {
    report += `### Unit Creation\n\`\`\`json\n${JSON.stringify(results.units.validation.errors, null, 2)}\n\`\`\`\n\n`;
  }

  report += `\n## All Requests\n\n`;
  results.allRequests.forEach((r, i) => {
    report += `### ${i + 1}. ${r.request.method} ${r.request.endpoint}\n`;
    report += `- **Status:** ${r.response.statusCode}\n`;
    report += `- **Duration:** ${r.response.duration}ms\n`;
    if (r.request.body && Object.keys(r.request.body).length > 0) {
      report += `- **Request Body:**\n\`\`\`json\n${JSON.stringify(r.request.body, null, 2)}\n\`\`\`\n`;
    }
    report += `- **Response:**\n\`\`\`json\n${JSON.stringify(r.response.body, null, 2).substring(0, 1000)}${JSON.stringify(r.response.body).length > 1000 ? '...' : ''}\n\`\`\`\n\n`;
  });

  fs.writeFileSync(
    path.join(CONFIG.outputDir, 'EXPLORATION-REPORT.md'),
    report
  );

  console.log('\n' + '='.repeat(60));
  console.log('  EXPLORATION COMPLETE!');
  console.log('='.repeat(60));
  console.log(`\n  Total Requests: ${results.allRequests.length}`);
  console.log(`  Successful: ${results.allRequests.filter(r => r.response.statusCode >= 200 && r.response.statusCode < 300).length}`);
  console.log(`  Errors: ${results.allRequests.filter(r => r.response.statusCode >= 400).length}`);
  console.log(`\n  Results: ${CONFIG.outputDir}/`);
  console.log('    - exploration-results.json');
  console.log('    - all-requests.json');
  console.log('    - EXPLORATION-REPORT.md');
  console.log('='.repeat(60) + '\n');
}

main().catch(err => {
  log(`Fatal error: ${err.message}`, 'error');
  console.error(err);
  process.exit(1);
});
