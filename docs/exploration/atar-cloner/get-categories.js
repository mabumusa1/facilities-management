const https = require('https');

const CONFIG = {
  token: '1|f8Jy1HaDbByQkDBd9bGJl23QilSb1206S4n4qgXX',
  tenant: 'testbusiness123',
};

async function request(url) {
  return new Promise((resolve) => {
    const urlObj = new URL(url);
    const options = {
      hostname: urlObj.hostname,
      port: 443,
      path: urlObj.pathname + urlObj.search,
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${CONFIG.token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Tenant': CONFIG.tenant,
      }
    };

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        try { resolve(JSON.parse(data)); } catch { resolve(data); }
      });
    });
    req.on('error', resolve);
    req.end();
  });
}

async function main() {
  console.log('Fetching property categories...\n');

  // Get categories
  const categories = await request('https://api.goatar.com/api-management/categories');
  console.log('Categories:');
  console.log(JSON.stringify(categories, null, 2));

  // Also try to get the types for each category
  if (categories?.data) {
    for (const cat of categories.data) {
      console.log(`\n\nTypes for category ${cat.id} (${cat.name}):`);
      const types = await request(`https://api.goatar.com/api-management/categories/${cat.id}/types`);
      console.log(JSON.stringify(types, null, 2));
    }
  }
}

main();
