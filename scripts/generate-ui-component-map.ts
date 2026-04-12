/**
 * UI Component Mapping Generator
 *
 * Extracts and documents React components and their API dependencies
 * from the extracted bundle signals.
 */

import * as fs from 'fs';
import * as path from 'path';

// ============================================================================
// TYPES
// ============================================================================

interface SignalRef {
  key: string;
  count: number;
  refs: string[];
}

interface SignalsData {
  meta: {
    generatedAt: string;
    totalChunks: number;
  };
  result: {
    routes: SignalRef[];
    apiBindings: SignalRef[];
    apiEndpoints: SignalRef[];
  };
}

interface RouteMapping {
  path: string;
  module: string;
  parentPath?: string;
  permission?: string;
  apiEndpoints: string[];
  chunkRefs: string[];
}

interface ComponentMapping {
  name: string;
  route: string;
  module: string;
  apis: string[];
  permissions: string[];
  features: string[];
}

interface ModuleSummary {
  name: string;
  routes: string[];
  endpoints: string[];
  components: string[];
}

// ============================================================================
// CONSTANTS
// ============================================================================

const MODULES = {
  dashboard: {
    basePath: '/dashboard',
    name: 'Dashboard',
    icon: 'DashboardLine',
    permission: 'Dashboard'
  },
  properties: {
    basePath: '/properties-list',
    name: 'Properties',
    icon: 'CommunityLine',
    permission: 'Properties',
    subModules: ['communities', 'buildings', 'units']
  },
  marketplace: {
    basePath: '/marketplace',
    name: 'Marketplace',
    icon: 'HomeLine',
    permission: 'MarketPlaces',
    subModules: ['customers', 'listing']
  },
  leasing: {
    basePath: '/leasing',
    name: 'Leasing',
    icon: 'DraftLine',
    permission: 'Leases',
    subModules: ['visits', 'apps', 'quotes', 'leases']
  },
  requests: {
    basePath: '/requests',
    name: 'Requests',
    icon: 'HammerLine',
    permission: 'HomeServices'
  },
  transactions: {
    basePath: '/transactions',
    name: 'Transactions',
    icon: 'CalculatorLine',
    permission: 'Transactions'
  },
  contacts: {
    basePath: '/contacts',
    name: 'Contacts',
    icon: 'ContactsBook2Line',
    permission: 'Contacts',
    subModules: ['Tenant', 'Owner', 'Manager', 'ServiceProfessional']
  },
  visitorAccess: {
    basePath: '/visitor-access',
    name: 'Visitor Access',
    icon: 'ContactsBookLine',
    permission: 'VisitorAccess'
  }
};

// Permission constants extracted from bundle
const PERMISSIONS = {
  View: 'VIEW',
  Create: 'CREATE',
  Update: 'UPDATE',
  Delete: 'DELETE'
};

const SUBJECTS = {
  Dashboard: 'Dashboard',
  Properties: 'Properties',
  MarketPlaces: 'MarketPlaces',
  Customers: 'Customers',
  Listings: 'Listings',
  Visits: 'Visits',
  BookingAndContracts: 'BookingAndContracts',
  Quotes: 'Quotes',
  Applications: 'Applications',
  Leases: 'Leases',
  HomeServices: 'HomeServices',
  NeighbourhoodServices: 'NeighbourhoodServices',
  VisitorAccess: 'VisitorAccess',
  Bookings: 'Bookings',
  Transactions: 'Transactions',
  Offers: 'Offers',
  Directories: 'Directories',
  Suggestions: 'Suggestions',
  Tenants: 'Tenants',
  Owners: 'Owners',
  Managers: 'Managers',
  ServiceProfessionals: 'ServiceProfessionals',
  Reports: 'Reports',
  SystemReports: 'SystemReports',
  PowerBiReports: 'PowerBiReports'
};

// Feature flags extracted from bundle
const FEATURE_FLAGS = [
  'ENABLE_REQUESTS',
  'ENABLE_BOOKING_REQUESTS',
  'ENABLE_OFFERS',
  'ENABLE_DIRECTORY',
  'ENABLE_SUGGESTION',
  'ENABLE_TENANTS',
  'ENABLE_OWNERS',
  'ENABLE_MANGERS',
  'ENABLE_PROFESSIONALS'
];

// Module IDs from bundle
const MODULE_IDS = {
  VISITOR: 'VISITOR',
  FACILITIES: 'FACILITIES',
  OFFERS: 'OFFERS',
  DIRECTORY: 'DIRECTORY'
};

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================

function determineModule(routePath: string): string {
  for (const [key, config] of Object.entries(MODULES)) {
    if (routePath.startsWith(config.basePath)) {
      return key;
    }
  }
  return 'other';
}

function normalizeRoute(route: string): string {
  // Convert dynamic params like ${t} to :param format
  return route
    .replace(/\$\{[^}]+\}/g, ':param')
    .replace(/\$\{[^}]+\?\.[^}]+\}/g, ':param');
}

function extractEndpointFromBinding(binding: string): string {
  // Format: "variable -> method /api-management/path"
  const match = binding.match(/->\s*\w+\s+(.+)$/);
  return match ? match[1] : binding;
}

function matchRouteToEndpoints(
  route: string,
  endpoints: SignalRef[],
  routeChunks: string[]
): string[] {
  const matchedEndpoints: string[] = [];

  // Get chunk files from route refs
  const chunkFiles = routeChunks
    .map(ref => ref.split(':')[0])
    .filter(Boolean);

  // Find endpoints that appear in the same chunks
  for (const endpoint of endpoints) {
    const endpointChunks = endpoint.refs
      .map(ref => ref.split(':')[0])
      .filter(Boolean);

    const hasOverlap = chunkFiles.some(chunk =>
      endpointChunks.includes(chunk)
    );

    if (hasOverlap) {
      matchedEndpoints.push(endpoint.key);
    }
  }

  return matchedEndpoints;
}

// ============================================================================
// MAIN GENERATOR
// ============================================================================

async function generateUIComponentMap(): Promise<void> {
  const projectRoot = path.join(__dirname, '..');
  const signalsPath = path.join(projectRoot, 'pretty-js.split', 'signals.json');
  const outputDir = path.join(projectRoot, 'src', 'api', 'docs');

  // Ensure output directory exists
  if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
  }

  // Load signals data
  const signalsData: SignalsData = JSON.parse(
    fs.readFileSync(signalsPath, 'utf-8')
  );

  const { routes, apiBindings, apiEndpoints } = signalsData.result;

  console.log(`Processing ${routes.length} routes, ${apiBindings.length} bindings, ${apiEndpoints.length} endpoints`);

  // Build route mappings
  const routeMappings: RouteMapping[] = routes.map(route => {
    const normalizedPath = normalizeRoute(route.key);
    const module = determineModule(normalizedPath);
    const matchedEndpoints = matchRouteToEndpoints(
      route.key,
      apiEndpoints,
      route.refs
    );

    return {
      path: normalizedPath,
      module,
      apiEndpoints: matchedEndpoints,
      chunkRefs: route.refs
    };
  });

  // Group by module
  const moduleGroups: Record<string, RouteMapping[]> = {};
  for (const mapping of routeMappings) {
    if (!moduleGroups[mapping.module]) {
      moduleGroups[mapping.module] = [];
    }
    moduleGroups[mapping.module].push(mapping);
  }

  // Generate module summaries
  const moduleSummaries: ModuleSummary[] = Object.entries(moduleGroups).map(
    ([moduleName, routes]) => ({
      name: moduleName,
      routes: routes.map(r => r.path),
      endpoints: [...new Set(routes.flatMap(r => r.apiEndpoints))],
      components: extractComponentNames(moduleName, routes)
    })
  );

  // Build comprehensive component mapping
  const componentMappings = buildComponentMappings(routeMappings, apiEndpoints);

  // Write outputs
  const outputData = {
    meta: {
      generatedAt: new Date().toISOString(),
      sourceFile: 'pretty-js.split/signals.json',
      totalRoutes: routes.length,
      totalEndpoints: apiEndpoints.length,
      totalBindings: apiBindings.length
    },
    modules: MODULES,
    permissions: {
      actions: PERMISSIONS,
      subjects: SUBJECTS
    },
    featureFlags: FEATURE_FLAGS,
    moduleIds: MODULE_IDS,
    routeMappings,
    moduleSummaries,
    apiBindings: apiBindings.map(b => ({
      binding: b.key,
      endpoint: extractEndpointFromBinding(b.key),
      refs: b.refs
    })),
    apiEndpointsByModule: groupEndpointsByModule(apiEndpoints)
  };

  // Write JSON output
  const jsonPath = path.join(outputDir, 'ui-component-map.json');
  fs.writeFileSync(jsonPath, JSON.stringify(outputData, null, 2));
  console.log(`Written: ${jsonPath}`);

  // Write Markdown documentation
  const mdPath = path.join(outputDir, 'UI-COMPONENTS.md');
  fs.writeFileSync(mdPath, generateMarkdownDoc(outputData));
  console.log(`Written: ${mdPath}`);

  // Summary
  console.log('\nUI Component Map Generated:');
  console.log(`- ${routeMappings.length} routes mapped`);
  console.log(`- ${moduleSummaries.length} modules identified`);
  console.log(`- ${apiEndpoints.length} API endpoints catalogued`);
  console.log(`- ${apiBindings.length} API bindings documented`);
}

function extractComponentNames(moduleName: string, routes: RouteMapping[]): string[] {
  // Derive component names from routes
  const components: string[] = [];

  for (const route of routes) {
    const parts = route.path.split('/').filter(Boolean);
    if (parts.length > 0) {
      // Convert path to PascalCase component name
      const componentName = parts
        .map(part => {
          if (part.startsWith(':')) return '';
          return part.charAt(0).toUpperCase() + part.slice(1).replace(/-/g, '');
        })
        .filter(Boolean)
        .join('') + 'Page';

      if (componentName && !components.includes(componentName)) {
        components.push(componentName);
      }
    }
  }

  return components;
}

function buildComponentMappings(
  routeMappings: RouteMapping[],
  endpoints: SignalRef[]
): ComponentMapping[] {
  return routeMappings.map(route => {
    const moduleConfig = Object.values(MODULES).find(
      m => route.path.startsWith(m.basePath)
    );

    return {
      name: deriveComponentName(route.path),
      route: route.path,
      module: route.module,
      apis: route.apiEndpoints,
      permissions: moduleConfig ? [moduleConfig.permission] : [],
      features: deriveFeatures(route.module)
    };
  });
}

function deriveComponentName(routePath: string): string {
  const parts = routePath.split('/').filter(p => p && !p.startsWith(':'));
  return parts
    .map(p => p.charAt(0).toUpperCase() + p.slice(1).replace(/-/g, ''))
    .join('') + 'Page';
}

function deriveFeatures(module: string): string[] {
  const featureMap: Record<string, string[]> = {
    requests: ['ENABLE_REQUESTS'],
    dashboard: [],
    properties: [],
    marketplace: [],
    leasing: [],
    transactions: [],
    contacts: ['ENABLE_TENANTS', 'ENABLE_OWNERS', 'ENABLE_PROFESSIONALS'],
    visitorAccess: ['ENABLE_REQUESTS']
  };

  return featureMap[module] || [];
}

function groupEndpointsByModule(endpoints: SignalRef[]): Record<string, string[]> {
  const groups: Record<string, string[]> = {
    rf: [],
    marketplace: [],
    dashboard: [],
    contacts: [],
    notifications: [],
    integrations: [],
    other: []
  };

  for (const endpoint of endpoints) {
    const path = endpoint.key;

    if (path.includes('/rf/')) {
      groups.rf.push(path);
    } else if (path.includes('/marketplace/')) {
      groups.marketplace.push(path);
    } else if (path.includes('/dashboard/')) {
      groups.dashboard.push(path);
    } else if (path.includes('/contacts')) {
      groups.contacts.push(path);
    } else if (path.includes('/notifications')) {
      groups.notifications.push(path);
    } else if (path.includes('/integrations/')) {
      groups.integrations.push(path);
    } else {
      groups.other.push(path);
    }
  }

  return groups;
}

function generateMarkdownDoc(data: any): string {
  let md = `# Atar UI Component Map

> Generated: ${data.meta.generatedAt}
> Source: React bundle analysis (${data.meta.sourceFile})

## Overview

| Metric | Count |
|--------|-------|
| Frontend Routes | ${data.meta.totalRoutes} |
| API Endpoints | ${data.meta.totalEndpoints} |
| API Bindings | ${data.meta.totalBindings} |
| Modules | ${data.moduleSummaries.length} |

---

## Modules

`;

  for (const module of data.moduleSummaries) {
    const config = data.modules[module.name] || {};
    md += `### ${config.name || module.name.charAt(0).toUpperCase() + module.name.slice(1)}

**Base Path:** \`${config.basePath || '/' + module.name}\`
**Icon:** ${config.icon || 'N/A'}
**Permission:** ${config.permission || 'N/A'}

#### Routes (${module.routes.length})

| Route | Component |
|-------|-----------|
`;
    for (const route of module.routes) {
      const componentName = deriveComponentName(route);
      md += `| \`${route}\` | ${componentName} |\n`;
    }

    md += `
#### API Endpoints (${module.endpoints.length})

`;
    if (module.endpoints.length > 0) {
      for (const endpoint of module.endpoints.slice(0, 10)) {
        md += `- \`${endpoint}\`\n`;
      }
      if (module.endpoints.length > 10) {
        md += `- ... and ${module.endpoints.length - 10} more\n`;
      }
    } else {
      md += `*No direct API endpoints detected*\n`;
    }

    md += '\n---\n\n';
  }

  // Permission System
  md += `## Permission System

The application uses a role-based access control (RBAC) system.

### Actions

| Action | Description |
|--------|-------------|
`;
  for (const [key, value] of Object.entries(data.permissions.actions)) {
    md += `| ${key} | \`${value}\` |\n`;
  }

  md += `
### Subjects (Resources)

| Subject | Description |
|---------|-------------|
`;
  for (const [key, value] of Object.entries(data.permissions.subjects)) {
    md += `| ${key} | \`${value}\` |\n`;
  }

  // Feature Flags
  md += `
## Feature Flags

The following feature flags control functionality:

| Flag | Purpose |
|------|---------|
`;
  for (const flag of data.featureFlags) {
    md += `| \`${flag}\` | Controls ${flag.replace('ENABLE_', '').toLowerCase()} module visibility |\n`;
  }

  // API Bindings
  md += `
## API Bindings

The following variables are bound to API endpoints:

| Variable | Method | Endpoint |
|----------|--------|----------|
`;
  for (const binding of data.apiBindings.slice(0, 20)) {
    const parts = binding.binding.split('->').map((s: string) => s.trim());
    const variable = parts[0];
    const methodEndpoint = parts[1] || '';
    const methodMatch = methodEndpoint.match(/^(\w+)\s+(.+)$/);
    const method = methodMatch ? methodMatch[1].toUpperCase() : 'GET';
    const endpoint = methodMatch ? methodMatch[2] : methodEndpoint;

    md += `| \`${variable}\` | ${method === 'LO' ? 'GET' : method === 'CO' ? 'POST' : method === 'UO' ? 'PUT' : method === 'PO' ? 'PUT' : method} | \`${endpoint}\` |\n`;
  }

  if (data.apiBindings.length > 20) {
    md += `\n*... and ${data.apiBindings.length - 20} more bindings*\n`;
  }

  // API Endpoints by Module
  md += `
## API Endpoints by Domain

`;
  for (const [domain, endpoints] of Object.entries(data.apiEndpointsByModule)) {
    const eps = endpoints as string[];
    if (eps.length === 0) continue;

    md += `### ${domain.toUpperCase()} (${eps.length} endpoints)

`;
    for (const endpoint of eps.slice(0, 15)) {
      md += `- \`${endpoint}\`\n`;
    }
    if (eps.length > 15) {
      md += `- *... and ${eps.length - 15} more*\n`;
    }
    md += '\n';
  }

  // Navigation Structure
  md += `## Navigation Structure

\`\`\`
/
├── dashboard/
│   ├── visits
│   ├── booking-contracts
│   ├── bookings
│   ├── offers
│   ├── directory
│   ├── suggestions
│   ├── reports
│   ├── system-reports
│   └── power-bi-reports
├── properties-list/
│   ├── communities
│   ├── buildings
│   └── units
├── marketplace/
│   ├── customers
│   └── listing
├── leasing/
│   ├── visits
│   ├── apps
│   ├── quotes
│   └── leases
├── requests/
│   └── ?type={homeServices|neighbourhoodServices}
├── visitor-access/
│   └── visitor-details/:id
├── transactions/
├── contacts/
│   ├── :type (Tenant|Owner|Manager|ServiceProfessional)
│   └── :type/form
└── more/
\`\`\`

## Usage Notes

1. **Permissions**: All routes check \`ability.can(Action, Subject)\` before rendering
2. **Feature Flags**: Module visibility depends on \`planFeatures\` configuration
3. **Module IDs**: Some features depend on \`isModuleEnabled(MODULE_ID)\`
4. **Locale**: All text uses i18n keys like \`sidebar.dashboard\`

`;

  return md;
}

// Run generator
generateUIComponentMap().catch(console.error);
