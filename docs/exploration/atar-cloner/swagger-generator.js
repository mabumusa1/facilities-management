const fs = require('fs');
const path = require('path');

const RESULTS_DIR = './api-probe-results';

// Load probe results
const allResults = JSON.parse(fs.readFileSync(path.join(RESULTS_DIR, '_all_results.json'), 'utf8'));
const schemas = JSON.parse(fs.readFileSync(path.join(RESULTS_DIR, '_schemas.json'), 'utf8'));
const validationErrors = JSON.parse(fs.readFileSync(path.join(RESULTS_DIR, '_validation_errors.json'), 'utf8'));

// OpenAPI 3.0 structure
const swagger = {
  openapi: '3.0.3',
  info: {
    title: 'Atar Property Management API',
    description: `
# Atar API Documentation

Auto-generated from real API responses captured on ${allResults.timestamp}.

## Base URLs
- **Management API**: https://api.goatar.com/api-management
- **Tenancy API**: https://api.goatar.com/tenancy/api

## Authentication
All endpoints require Bearer token authentication:
\`\`\`
Authorization: Bearer <token>
X-Tenant: <tenant_name>
\`\`\`

## Response Format
Most endpoints return a standard response format:
\`\`\`json
{
  "code": 200,
  "message": "",
  "data": [...],
  "meta": [...]
}
\`\`\`
    `,
    version: '1.0.0',
    contact: {
      name: 'Atar Support',
      email: 'info@goatar.com',
      url: 'https://goatar.com'
    }
  },
  servers: [
    {
      url: 'https://api.goatar.com/api-management',
      description: 'Management API'
    },
    {
      url: 'https://api.goatar.com/tenancy/api',
      description: 'Tenancy API'
    }
  ],
  tags: [
    { name: 'Dashboard', description: 'Dashboard and statistics' },
    { name: 'Admins', description: 'Admin management' },
    { name: 'Leases', description: 'Lease management' },
    { name: 'Leads', description: 'Lead/customer management' },
    { name: 'Contacts', description: 'Contact management' },
    { name: 'Requests', description: 'Service requests' },
    { name: 'Transactions', description: 'Financial transactions' },
    { name: 'Properties', description: 'Buildings, facilities, units' },
    { name: 'Marketplace', description: 'Marketplace settings' },
    { name: 'Visitor Access', description: 'Visitor management' },
    { name: 'Notifications', description: 'Notifications' },
    { name: 'Announcements', description: 'Announcements' },
    { name: 'Common', description: 'Common data lists' },
    { name: 'Tenancy', description: 'Tenancy/user endpoints' }
  ],
  paths: {},
  components: {
    securitySchemes: {
      bearerAuth: {
        type: 'http',
        scheme: 'bearer',
        bearerFormat: 'API Token'
      },
      tenantHeader: {
        type: 'apiKey',
        in: 'header',
        name: 'X-Tenant'
      }
    },
    schemas: {
      StandardResponse: {
        type: 'object',
        properties: {
          code: { type: 'integer', example: 200 },
          message: { type: 'string', example: '' },
          data: { type: 'object' },
          meta: { type: 'array', items: {} }
        }
      },
      ValidationError: {
        type: 'object',
        properties: {
          message: { type: 'string', example: 'The given data was invalid.' },
          errors: {
            type: 'object',
            additionalProperties: {
              type: 'array',
              items: { type: 'string' }
            }
          }
        }
      },
      Admin: {
        type: 'object',
        properties: {
          id: { type: 'integer' },
          name: { type: 'string' },
          image: { type: 'string', nullable: true },
          phone_number: { type: 'string', example: '+966500000000' },
          phone_country_code: { type: 'string', example: 'SA' },
          national_id: { type: 'string', nullable: true },
          email: { type: 'string', format: 'email' },
          role: { type: 'string', example: 'Admins' },
          created_at: { type: 'string', format: 'date-time' },
          types: { type: 'array', items: {} }
        }
      },
      ManagerRole: {
        type: 'object',
        properties: {
          id: { type: 'integer' },
          role: { type: 'string' },
          name_ar: { type: 'string' },
          name_en: { type: 'string' },
          types: { type: 'string', nullable: true }
        }
      },
      LeaseStatistics: {
        type: 'object',
        properties: {
          totalLeases: { type: 'integer' },
          newLeases: { type: 'integer' },
          activeLeases: { type: 'integer' },
          expiredLeases: { type: 'integer' },
          terminatedLeases: { type: 'integer' },
          percentNewLeases: { type: 'integer' },
          percentActiveLeases: { type: 'integer' }
        }
      },
      DashboardAttention: {
        type: 'object',
        properties: {
          requests_approval: { type: 'integer' },
          pending_complaints: { type: 'integer' },
          expiring_leases: { type: 'integer' },
          overdue_recipes: { type: 'integer' }
        }
      },
      // Request schemas from validation errors
      AdminCreateRequest: {
        type: 'object',
        required: ['first_name', 'last_name', 'phone_country_code', 'phone_number'],
        properties: {
          first_name: { type: 'string', description: 'First name (required)' },
          last_name: { type: 'string', description: 'Last name (required)' },
          phone_country_code: { type: 'string', description: 'Phone country code (required)', example: 'SA' },
          phone_number: { type: 'string', description: 'Phone number (required)' },
          email: { type: 'string', format: 'email' },
          national_id: { type: 'string' }
        }
      },
      LeaseMoveOutRequest: {
        type: 'object',
        required: ['rf_lease_id', 'end_at'],
        properties: {
          rf_lease_id: { type: 'integer', description: 'Lease ID (required)' },
          end_at: { type: 'string', format: 'date', description: 'End date (required)' }
        }
      },
      LeaseTerminateRequest: {
        type: 'object',
        required: ['rf_lease_id', 'end_at'],
        properties: {
          rf_lease_id: { type: 'integer', description: 'Lease ID (required)' },
          end_at: { type: 'string', format: 'date', description: 'End date (required)' }
        }
      },
      LeaseRenewRequest: {
        type: 'object',
        required: [
          'rental_contract_type_id', 'created_at', 'start_date', 'end_date',
          'rf_lease_id', 'number_of_years', 'number_of_months',
          'autoGenerateLeaseNumber', 'contract_number', 'rental_type',
          'payment_schedule_id', 'units'
        ],
        properties: {
          rf_lease_id: { type: 'integer', description: 'Existing lease ID' },
          rental_contract_type_id: { type: 'integer' },
          created_at: { type: 'string', format: 'date' },
          start_date: { type: 'string', format: 'date' },
          end_date: { type: 'string', format: 'date' },
          number_of_years: { type: 'integer', description: 'Lease duration in years' },
          number_of_months: { type: 'integer', description: 'Lease duration in months' },
          autoGenerateLeaseNumber: { type: 'boolean' },
          contract_number: { type: 'string', description: 'Unique lease number' },
          rental_type: { type: 'string' },
          payment_schedule_id: { type: 'integer' },
          units: { type: 'array', items: { type: 'integer' } }
        }
      },
      RequestCancelRequest: {
        type: 'object',
        required: ['rf_request_id'],
        properties: {
          rf_request_id: { type: 'integer', description: 'Request ID to cancel' }
        }
      },
      BankSettingsRequest: {
        type: 'object',
        required: ['beneficiary_name', 'bank_name', 'account_number', 'iban'],
        properties: {
          beneficiary_name: { type: 'string' },
          bank_name: { type: 'string' },
          account_number: {
            type: 'string',
            description: 'Must be numeric, minimum 14 digits'
          },
          iban: { type: 'string' }
        }
      },
      SalesSettingsRequest: {
        type: 'object',
        required: ['deposit_time_limit_days', 'cash_contract_signing_days', 'bank_contract_signing_days'],
        properties: {
          deposit_time_limit_days: { type: 'integer', description: 'Signing deadline in days' },
          cash_contract_signing_days: { type: 'integer' },
          bank_contract_signing_days: { type: 'integer' }
        }
      },
      VisitSettingsRequest: {
        type: 'object',
        required: ['is_all_day', 'days'],
        properties: {
          is_all_day: { type: 'boolean' },
          days: { type: 'array', items: { type: 'string' } }
        }
      }
    }
  },
  security: [
    { bearerAuth: [], tenantHeader: [] }
  ]
};

// Helper to determine tag from path
function getTag(path) {
  if (path.includes('/dashboard')) return 'Dashboard';
  if (path.includes('/admins')) return 'Admins';
  if (path.includes('/leases')) return 'Leases';
  if (path.includes('/leads')) return 'Leads';
  if (path.includes('/contacts')) return 'Contacts';
  if (path.includes('/requests')) return 'Requests';
  if (path.includes('/transactions')) return 'Transactions';
  if (path.includes('/buildings') || path.includes('/facilities') || path.includes('/communities')) return 'Properties';
  if (path.includes('/marketplace')) return 'Marketplace';
  if (path.includes('/visitor')) return 'Visitor Access';
  if (path.includes('/notifications')) return 'Notifications';
  if (path.includes('/announcements')) return 'Announcements';
  if (path.includes('/common') || path.includes('/statuses') || path.includes('/modules') || path.includes('/countries')) return 'Common';
  if (path.includes('/me') || path.includes('/cities') || path.includes('/districts')) return 'Tenancy';
  return 'Other';
}

// Build paths from results
allResults.endpoints.forEach(endpoint => {
  const { request, response } = endpoint;
  const method = request.method.toLowerCase();
  let apiPath = request.path;

  // Normalize path
  if (!apiPath.startsWith('/')) apiPath = '/' + apiPath;

  // Initialize path if not exists
  if (!swagger.paths[apiPath]) {
    swagger.paths[apiPath] = {};
  }

  const operation = {
    tags: [getTag(apiPath)],
    summary: request.description || `${request.method} ${apiPath}`,
    operationId: `${method}${apiPath.replace(/[\/:-]/g, '_')}`,
    responses: {}
  };

  // Add request body for POST/PUT/PATCH
  if (['post', 'put', 'patch'].includes(method)) {
    // Try to find matching request schema
    const schemaKey = apiPath.replace(/[\/:-]/g, '_').replace(/^_/, '');
    let requestSchema = '$ref: "#/components/schemas/object"';

    if (apiPath.includes('/admins/check-validate')) {
      requestSchema = { $ref: '#/components/schemas/AdminCreateRequest' };
    } else if (apiPath.includes('/move-out')) {
      requestSchema = { $ref: '#/components/schemas/LeaseMoveOutRequest' };
    } else if (apiPath.includes('/terminate')) {
      requestSchema = { $ref: '#/components/schemas/LeaseTerminateRequest' };
    } else if (apiPath.includes('/renew/store')) {
      requestSchema = { $ref: '#/components/schemas/LeaseRenewRequest' };
    } else if (apiPath.includes('/canceled')) {
      requestSchema = { $ref: '#/components/schemas/RequestCancelRequest' };
    } else if (apiPath.includes('/banks/store')) {
      requestSchema = { $ref: '#/components/schemas/BankSettingsRequest' };
    } else if (apiPath.includes('/sales/store')) {
      requestSchema = { $ref: '#/components/schemas/SalesSettingsRequest' };
    } else if (apiPath.includes('/visits/store')) {
      requestSchema = { $ref: '#/components/schemas/VisitSettingsRequest' };
    } else {
      requestSchema = { type: 'object' };
    }

    operation.requestBody = {
      required: true,
      content: {
        'application/json': {
          schema: requestSchema
        }
      }
    };
  }

  // Add response schemas
  if (response.success) {
    operation.responses['200'] = {
      description: 'Successful response',
      content: {
        'application/json': {
          schema: { $ref: '#/components/schemas/StandardResponse' }
        }
      }
    };

    // Add example from actual response
    if (response.body) {
      operation.responses['200'].content['application/json'].example = response.body;
    }
  } else if (response.statusCode === 422 || response.statusCode === 400) {
    operation.responses[response.statusCode.toString()] = {
      description: 'Validation error',
      content: {
        'application/json': {
          schema: { $ref: '#/components/schemas/ValidationError' },
          example: response.body
        }
      }
    };
  } else if (response.statusCode === 404) {
    operation.responses['404'] = {
      description: 'Not found'
    };
  } else if (response.statusCode === 405) {
    operation.responses['405'] = {
      description: 'Method not allowed'
    };
  } else if (response.statusCode === 500) {
    operation.responses['500'] = {
      description: 'Server error',
      content: {
        'application/json': {
          example: response.body
        }
      }
    };
  }

  // Add common error responses
  operation.responses['401'] = { description: 'Unauthorized' };
  operation.responses['403'] = { description: 'Forbidden' };

  swagger.paths[apiPath][method] = operation;
});

// Write OpenAPI spec
const outputPath = './atar-api-swagger.yaml';
const yamlContent = generateYaml(swagger);
fs.writeFileSync(outputPath, yamlContent);

// Also write JSON version
fs.writeFileSync('./atar-api-swagger.json', JSON.stringify(swagger, null, 2));

console.log('='.repeat(60));
console.log('  SWAGGER GENERATION COMPLETE');
console.log('='.repeat(60));
console.log(`\nGenerated:`);
console.log(`  - atar-api-swagger.yaml`);
console.log(`  - atar-api-swagger.json`);
console.log(`\nEndpoints: ${Object.keys(swagger.paths).length}`);
console.log(`Schemas: ${Object.keys(swagger.components.schemas).length}`);
console.log('='.repeat(60));

// Simple YAML generator
function generateYaml(obj, indent = 0) {
  const spaces = '  '.repeat(indent);
  let yaml = '';

  if (Array.isArray(obj)) {
    if (obj.length === 0) return '[]';
    for (const item of obj) {
      if (typeof item === 'object' && item !== null) {
        yaml += `${spaces}-\n${generateYaml(item, indent + 1).replace(/^/, spaces + '  ').replace(/\n/g, '\n')}`;
      } else {
        yaml += `${spaces}- ${formatValue(item)}\n`;
      }
    }
  } else if (typeof obj === 'object' && obj !== null) {
    for (const [key, value] of Object.entries(obj)) {
      if (value === null) {
        yaml += `${spaces}${key}: null\n`;
      } else if (Array.isArray(value)) {
        if (value.length === 0) {
          yaml += `${spaces}${key}: []\n`;
        } else {
          yaml += `${spaces}${key}:\n${generateYaml(value, indent + 1)}`;
        }
      } else if (typeof value === 'object') {
        yaml += `${spaces}${key}:\n${generateYaml(value, indent + 1)}`;
      } else {
        yaml += `${spaces}${key}: ${formatValue(value)}\n`;
      }
    }
  }

  return yaml;
}

function formatValue(val) {
  if (typeof val === 'string') {
    if (val.includes('\n') || val.includes(':') || val.includes('#') || val.includes("'") || val.startsWith(' ')) {
      return `"${val.replace(/"/g, '\\"').replace(/\n/g, '\\n')}"`;
    }
    if (val === '' || val === 'true' || val === 'false' || !isNaN(val)) {
      return `"${val}"`;
    }
    return val;
  }
  return String(val);
}
