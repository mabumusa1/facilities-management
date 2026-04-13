const https = require('https');
const fs = require('fs');
const path = require('path');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
  baseUrl: 'https://api.goatar.com/api-management',
  outputDir: './seed-data-results',
  delay: 300,
};

const results = {
  timestamp: new Date().toISOString(),
  lookups: {},
  created: { communities: [], buildings: [], units: [] },
  validationDiscovery: [],
  allRequests: []
};

if (!fs.existsSync(CONFIG.outputDir)) {
  fs.mkdirSync(CONFIG.outputDir, { recursive: true });
}

const log = (msg, type = 'info') => {
  const icons = {
    info: '\x1b[36m[INFO]\x1b[0m',
    success: '\x1b[32m[OK]\x1b[0m',
    error: '\x1b[31m[ERR]\x1b[0m',
    warn: '\x1b[33m[WARN]\x1b[0m',
    create: '\x1b[35m[CREATE]\x1b[0m'
  };
  console.log(`${icons[type] || icons.info} ${msg}`);
};

const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));

async function request(method, endpoint, body = null) {
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
    if (bodyStr) options.headers['Content-Length'] = Buffer.byteLength(bodyStr);

    const startTime = Date.now();
    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        let parsed;
        try { parsed = JSON.parse(data); } catch (e) { parsed = data; }
        const result = {
          method, endpoint, body,
          status: res.statusCode,
          response: parsed,
          duration: Date.now() - startTime
        };
        results.allRequests.push(result);
        resolve(result);
      });
    });
    req.on('error', (err) => resolve({ method, endpoint, body, status: 0, error: err.message }));
    req.setTimeout(30000);
    if (bodyStr) req.write(bodyStr);
    req.end();
  });
}

// ============ LOOKUP FETCHERS ============

async function fetchLookups() {
  log('\n========== FETCHING LOOKUPS ==========', 'info');

  // Countries (includes currencies)
  log('Fetching countries/currencies...', 'info');
  const countries = await request('GET', '/rf/countries');
  if (countries.status === 200) {
    results.lookups.countries = countries.response.data;
    log(`  Found ${countries.response.data?.length || 0} countries`, 'success');
  }

  // Cities
  log('Fetching cities...', 'info');
  const cities = await request('GET', 'https://api.goatar.com/tenancy/api/cities/all');
  if (cities.status === 200) {
    results.lookups.cities = cities.response.data;
    log(`  Found ${cities.response.data?.length || 0} cities`, 'success');
  }

  // Districts
  log('Fetching districts...', 'info');
  const districts = await request('GET', 'https://api.goatar.com/tenancy/api/districts/all');
  if (districts.status === 200) {
    results.lookups.districts = districts.response.data;
    log(`  Found ${districts.response.data?.length || 0} districts`, 'success');
  }

  // Try to find property categories and types
  log('Searching for property categories/types...', 'info');

  // Try various patterns to find category/type data
  const tryEndpoints = [
    '/rf/categories',
    '/rf/property-categories',
    '/rf/unit-categories',
    '/categories',
    '/property-categories',
  ];

  for (const ep of tryEndpoints) {
    const res = await request('GET', ep);
    if (res.status === 200 && res.response?.data?.length > 0) {
      log(`  Found categories at ${ep}`, 'success');
      results.lookups.categories = res.response.data;
      break;
    }
    await sleep(100);
  }

  return results.lookups;
}

// ============ DISCOVERY BY ITERATION ============

async function discoverFieldsByIteration() {
  log('\n========== DISCOVERING VALID FIELD VALUES ==========', 'info');

  const countryId = results.lookups.countries?.[0]?.id || 1;
  const currencyId = results.lookups.countries?.[0]?.id || 1;
  const cityId = results.lookups.cities?.[0]?.id || 1;
  const districtId = results.lookups.districts?.[0]?.id || 1;

  // Try creating a community to understand all required fields
  log('\nDiscovering community fields...', 'create');

  const communityTests = [
    { name: 'Test Community 1', country_id: countryId, currency_id: currencyId, city_id: cityId, district_id: districtId },
    { name: 'Test Community 2', country_id: 'SA', currency_id: 1, city_id: cityId, district_id: districtId },
    { name: 'Test Community 3', country_id: 1, currency_id: 1, city_id: 1, district_id: 1 },
  ];

  for (let i = 0; i < communityTests.length; i++) {
    const body = communityTests[i];
    log(`  Attempt ${i + 1}: ${JSON.stringify(body).substring(0, 80)}...`, 'info');
    const res = await request('POST', '/rf/communities', body);
    log(`    -> ${res.status}`, res.status === 200 || res.status === 201 ? 'success' : 'warn');

    results.validationDiscovery.push({
      entity: 'community',
      attempt: i + 1,
      body,
      status: res.status,
      response: res.response
    });

    if (res.status === 200 || res.status === 201) {
      log(`    CREATED! ID: ${res.response?.data?.id}`, 'success');
      results.created.communities.push(res.response?.data);
      break;
    } else if (res.response?.errors) {
      log(`    Missing fields: ${Object.keys(res.response.errors).join(', ')}`, 'warn');
    }

    await sleep(CONFIG.delay);
  }

  // If we created a community, try to create a building
  if (results.created.communities.length > 0) {
    const communityId = results.created.communities[0].id;
    log(`\nDiscovering building fields (community_id: ${communityId})...`, 'create');

    const buildingTests = [
      { name: 'Test Building 1', rf_community_id: communityId },
      { name: 'Test Building 2', rf_community_id: communityId, total_units: 10 },
      { name: 'Test Building 3', rf_community_id: communityId, number_of_floors: 5, total_units: 20 },
    ];

    for (let i = 0; i < buildingTests.length; i++) {
      const body = buildingTests[i];
      log(`  Attempt ${i + 1}: ${JSON.stringify(body).substring(0, 80)}...`, 'info');
      const res = await request('POST', '/rf/buildings', body);
      log(`    -> ${res.status}`, res.status === 200 || res.status === 201 ? 'success' : 'warn');

      results.validationDiscovery.push({
        entity: 'building',
        attempt: i + 1,
        body,
        status: res.status,
        response: res.response
      });

      if (res.status === 200 || res.status === 201) {
        log(`    CREATED! ID: ${res.response?.data?.id}`, 'success');
        results.created.buildings.push(res.response?.data);
        break;
      } else if (res.response?.errors) {
        log(`    Missing fields: ${Object.keys(res.response.errors).join(', ')}`, 'warn');
      }

      await sleep(CONFIG.delay);
    }
  }

  // Discover unit fields - try different category/type combinations
  log('\nDiscovering unit fields...', 'create');

  const communityId = results.created.communities[0]?.id;
  const buildingId = results.created.buildings[0]?.id;

  // Try common category/type IDs
  const categoryIds = [1, 2, 3, 4, 5];
  const typeIds = [1, 2, 3, 4, 5];

  for (const catId of categoryIds) {
    for (const typeId of typeIds) {
      const body = {
        name: `Unit-Cat${catId}-Type${typeId}`,
        category_id: catId,
        type_id: typeId,
        rf_community_id: communityId || 1,
        rf_building_id: buildingId
      };

      log(`  Trying category_id=${catId}, type_id=${typeId}...`, 'info');
      const res = await request('POST', '/rf/units', body);

      results.validationDiscovery.push({
        entity: 'unit',
        body,
        status: res.status,
        response: res.response
      });

      if (res.status === 200 || res.status === 201) {
        log(`    CREATED! category_id=${catId}, type_id=${typeId} works!`, 'success');
        results.created.units.push(res.response?.data);
        results.lookups.validCategoryId = catId;
        results.lookups.validTypeId = typeId;
        break;
      } else if (res.response?.errors) {
        const errorKeys = Object.keys(res.response.errors);
        // If category_id or type_id is not in errors, they might be valid
        if (!errorKeys.includes('category_id') && !errorKeys.includes('type_id')) {
          log(`    category_id=${catId}, type_id=${typeId} are valid! Missing: ${errorKeys.join(', ')}`, 'warn');
          results.lookups.validCategoryId = catId;
          results.lookups.validTypeId = typeId;
        }
      }

      await sleep(150);
    }

    if (results.lookups.validCategoryId) break;
  }
}

// ============ SEED DATA WITH DISCOVERED VALUES ============

async function seedWithDiscoveredValues() {
  log('\n========== SEEDING DATA WITH DISCOVERED VALUES ==========', 'create');

  const categoryId = results.lookups.validCategoryId || 1;
  const typeId = results.lookups.validTypeId || 1;
  const communityId = results.created.communities[0]?.id;
  const buildingId = results.created.buildings[0]?.id;

  if (!communityId) {
    log('No community created, cannot seed more data', 'error');
    return;
  }

  // Create more buildings
  log('\nCreating additional buildings...', 'create');
  for (let i = 1; i <= 3; i++) {
    const body = {
      name: `Building ${String.fromCharCode(64 + i)}`,
      rf_community_id: communityId,
      number_of_floors: 3 + i,
      total_units: 10 * i
    };
    const res = await request('POST', '/rf/buildings', body);
    if (res.status === 200 || res.status === 201) {
      log(`  Building ${i}: Created (ID: ${res.response?.data?.id})`, 'success');
      results.created.buildings.push(res.response?.data);
    } else {
      log(`  Building ${i}: ${res.status} - ${JSON.stringify(res.response?.errors || res.response?.message)}`, 'warn');
    }
    await sleep(CONFIG.delay);
  }

  // Create units with full data
  log('\nCreating units...', 'create');
  const unitConfigs = [
    { name: 'Unit 101', bedrooms: 1, bathrooms: 1, area: 50, rent: 25000 },
    { name: 'Unit 102', bedrooms: 2, bathrooms: 1, area: 80, rent: 35000 },
    { name: 'Unit 201', bedrooms: 2, bathrooms: 2, area: 100, rent: 45000 },
    { name: 'Unit 202', bedrooms: 3, bathrooms: 2, area: 120, rent: 55000 },
    { name: 'Unit 301', bedrooms: 3, bathrooms: 3, area: 150, rent: 65000 },
    { name: 'Penthouse 401', bedrooms: 4, bathrooms: 3, area: 200, rent: 100000 },
  ];

  for (const config of unitConfigs) {
    const targetBuilding = results.created.buildings[Math.floor(Math.random() * results.created.buildings.length)];

    const body = {
      name: config.name,
      category_id: categoryId,
      type_id: typeId,
      rf_community_id: communityId,
      rf_building_id: targetBuilding?.id || buildingId,
      bedrooms: config.bedrooms,
      bathrooms: config.bathrooms,
      area: config.area,
      yearly_rent: config.rent,
      status: 'available',
      is_active: true
    };

    const res = await request('POST', '/rf/units', body);
    if (res.status === 200 || res.status === 201) {
      log(`  ${config.name}: Created (ID: ${res.response?.data?.id})`, 'success');
      results.created.units.push(res.response?.data);
    } else {
      log(`  ${config.name}: ${res.status}`, 'warn');
      if (res.response?.errors) {
        console.log(`    Errors: ${JSON.stringify(res.response.errors)}`);
      }
    }
    await sleep(CONFIG.delay);
  }
}

// ============ LIST CREATED DATA ============

async function listCreatedData() {
  log('\n========== VERIFYING CREATED DATA ==========', 'info');

  // List communities
  const communities = await request('GET', '/rf/communities');
  log(`Communities: ${communities.response?.data?.length || 0}`, 'info');
  if (communities.response?.data?.length > 0) {
    communities.response.data.forEach(c => {
      log(`  - ${c.id}: ${c.name}`, 'success');
    });
    results.final = results.final || {};
    results.final.communities = communities.response.data;
  }

  // List buildings
  const buildings = await request('GET', '/rf/buildings');
  log(`Buildings: ${buildings.response?.data?.length || 0}`, 'info');
  if (buildings.response?.data?.length > 0) {
    buildings.response.data.forEach(b => {
      log(`  - ${b.id}: ${b.name}`, 'success');
    });
    results.final = results.final || {};
    results.final.buildings = buildings.response.data;
  }

  // List units
  const units = await request('GET', '/rf/units');
  log(`Units: ${units.response?.data?.length || 0}`, 'info');
  if (units.response?.data?.length > 0) {
    units.response.data.forEach(u => {
      log(`  - ${u.id}: ${u.name}`, 'success');
    });
    results.final = results.final || {};
    results.final.units = units.response.data;
  }
}

// ============ MAIN ============

async function main() {
  console.log('\n' + '='.repeat(60));
  console.log('  ATAR DATA SEEDER');
  console.log('  Creating test data to explore all workflows');
  console.log('='.repeat(60) + '\n');

  await fetchLookups();
  await discoverFieldsByIteration();
  await seedWithDiscoveredValues();
  await listCreatedData();

  // Save results
  fs.writeFileSync(
    path.join(CONFIG.outputDir, 'seed-results.json'),
    JSON.stringify(results, null, 2)
  );

  // Generate report
  let report = `# Data Seeding Report\n\n`;
  report += `**Timestamp:** ${results.timestamp}\n\n`;
  report += `## Lookups Discovered\n\n`;
  report += `- Countries: ${results.lookups.countries?.length || 0}\n`;
  report += `- Cities: ${results.lookups.cities?.length || 0}\n`;
  report += `- Districts: ${results.lookups.districts?.length || 0}\n`;
  report += `- Valid Category ID: ${results.lookups.validCategoryId || 'Not found'}\n`;
  report += `- Valid Type ID: ${results.lookups.validTypeId || 'Not found'}\n\n`;

  report += `## Created Data\n\n`;
  report += `- Communities: ${results.created.communities.length}\n`;
  report += `- Buildings: ${results.created.buildings.length}\n`;
  report += `- Units: ${results.created.units.length}\n\n`;

  report += `## Validation Discovery\n\n`;
  results.validationDiscovery.forEach((v, i) => {
    report += `### ${v.entity} - Attempt ${v.attempt || i + 1}\n`;
    report += `- Status: ${v.status}\n`;
    report += `- Body: \`${JSON.stringify(v.body)}\`\n`;
    if (v.response?.errors) {
      report += `- Errors:\n\`\`\`json\n${JSON.stringify(v.response.errors, null, 2)}\n\`\`\`\n`;
    }
    report += '\n';
  });

  fs.writeFileSync(
    path.join(CONFIG.outputDir, 'SEED-REPORT.md'),
    report
  );

  console.log('\n' + '='.repeat(60));
  console.log('  SEEDING COMPLETE');
  console.log('='.repeat(60));
  console.log(`\n  Created:`);
  console.log(`    Communities: ${results.created.communities.length}`);
  console.log(`    Buildings: ${results.created.buildings.length}`);
  console.log(`    Units: ${results.created.units.length}`);
  console.log(`\n  Results: ${CONFIG.outputDir}/`);
  console.log('='.repeat(60) + '\n');
}

main().catch(err => {
  log(`Fatal: ${err.message}`, 'error');
  console.error(err);
});
