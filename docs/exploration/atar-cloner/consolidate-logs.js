const fs = require('fs');
const path = require('path');

// Read all log files and consolidate
const logFiles = [
  'unit-investigation-log.json',
  'subscription-investigation-log.json',
  'owners-investigation-log.json',
  'complete-owner-log.json',
  'activate-property-log.json',
  'minimal-unit-test-log.json',
  'owner-creation-log.json',
];

const allLogs = [];

for (const file of logFiles) {
  try {
    const data = JSON.parse(fs.readFileSync(file, 'utf8'));
    allLogs.push({
      source: file,
      requests: data
    });
    console.log(`Loaded ${data.length} requests from ${file}`);
  } catch (e) {
    console.log(`Could not load ${file}: ${e.message}`);
  }
}

// Save consolidated
fs.writeFileSync('all-api-logs.json', JSON.stringify(allLogs, null, 2));
console.log(`\nSaved consolidated logs to all-api-logs.json`);

// Create a summary of unique endpoints
const endpoints = new Map();
for (const log of allLogs) {
  for (const req of log.requests) {
    const key = `${req.method} ${req.endpoint}`;
    if (!endpoints.has(key)) {
      endpoints.set(key, {
        method: req.method,
        endpoint: req.endpoint,
        statuses: new Set(),
        hasErrors: false,
      });
    }
    const entry = endpoints.get(key);
    entry.statuses.add(req.response?.status);
    if (req.response?.data?.errors && Object.keys(req.response.data.errors).length > 0) {
      entry.hasErrors = true;
    }
  }
}

// Convert to array and save
const endpointSummary = Array.from(endpoints.entries()).map(([key, value]) => ({
  endpoint: key,
  statuses: Array.from(value.statuses),
  hasErrors: value.hasErrors,
})).sort((a, b) => a.endpoint.localeCompare(b.endpoint));

fs.writeFileSync('endpoint-summary.json', JSON.stringify(endpointSummary, null, 2));
console.log(`Saved endpoint summary (${endpointSummary.length} unique endpoints) to endpoint-summary.json`);

// Print summary
console.log('\n=== Endpoint Summary ===\n');
for (const ep of endpointSummary) {
  const statusStr = ep.statuses.join(', ');
  const errorStr = ep.hasErrors ? ' [HAS VALIDATION ERRORS]' : '';
  console.log(`${ep.endpoint}: ${statusStr}${errorStr}`);
}
