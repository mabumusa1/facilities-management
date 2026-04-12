/**
 * PRD Agent Configuration
 *
 * Defines all PRDs, labels, milestones, and repository settings
 */

export interface LabelConfig {
  name: string;
  color: string;
  description: string;
}

export interface MilestoneConfig {
  title: string;
  description: string;
}

export interface PRDConfig {
  id: number;
  title: string;
  milestone: string;
  labels: string[];
  dependsOn: number[];
  template: 'foundation' | 'data-model' | 'workflow' | 'module-ui';
  source: string;
  overview?: string;
}

export interface RepoConfig {
  owner: string;
  name: string;
  description: string;
  isPublic: boolean;
}

export const CONFIG = {
  repo: {
    owner: '', // Will be set dynamically
    name: 'facilities-management',
    description: 'PRDs for building a facilities management system with Laravel',
    isPublic: true
  } as RepoConfig,

  labels: [
    { name: 'prd', color: '0E8A16', description: 'Product Requirement Document' },
    { name: 'foundation', color: '5319E7', description: 'Foundational system requirements' },
    { name: 'module', color: '1D76DB', description: 'Feature module' },
    { name: 'priority:critical', color: 'B60205', description: 'Must have for MVP' },
    { name: 'priority:high', color: 'D93F0B', description: 'High priority' },
    { name: 'priority:medium', color: 'FBCA04', description: 'Medium priority' },
    { name: 'priority:low', color: '0E8A16', description: 'Nice to have' },
    { name: 'type:rbac', color: 'BFD4F2', description: 'RBAC related' },
    { name: 'type:data-model', color: 'D4C5F9', description: 'Data model/entities' },
    { name: 'type:workflow', color: 'FEF2C0', description: 'Business workflow' },
    { name: 'type:api', color: 'C2E0C6', description: 'API specification' }
  ] as LabelConfig[],

  milestones: [
    { title: 'M0 - Foundation', description: 'RBAC, Auth, Reference Data' },
    { title: 'M1 - Core Models', description: 'Properties, Contacts, Transactions' },
    { title: 'M2 - Leasing', description: 'Full leasing workflow' },
    { title: 'M3 - Operations', description: 'Requests, Visitor Access, Bookings' },
    { title: 'M4 - Marketplace', description: 'Sales and marketplace features' },
    { title: 'M5 - Analytics', description: 'Dashboard, Reports, Notifications' }
  ] as MilestoneConfig[],

  prds: [
    // =========================================================================
    // FOUNDATION PRDs (M0) - Create First
    // =========================================================================
    {
      id: 1,
      title: 'PRD: User Roles & Contact Types',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:rbac', 'priority:critical'],
      dependsOn: [],
      template: 'foundation',
      source: 'ROLES-PERMISSIONS.md',
      overview: 'Define the 4 user contact types (Owner, Tenant, Admin, Professional) with their attributes and relationships.'
    },
    {
      id: 2,
      title: 'PRD: Manager Roles & Capabilities',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:rbac', 'priority:critical'],
      dependsOn: [1],
      template: 'foundation',
      source: 'ROLES-PERMISSIONS.md',
      overview: 'Define the 5 manager roles (Admin, Accounting, Service, Marketing, Sales/Leasing) with their capability matrix.'
    },
    {
      id: 3,
      title: 'PRD: Permission Actions & Subjects',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:rbac', 'priority:critical'],
      dependsOn: [1, 2],
      template: 'foundation',
      source: 'ROLES-PERMISSIONS.md',
      overview: 'Define CRUD permission actions (VIEW, CREATE, UPDATE, DELETE) and 23 permission subjects.'
    },
    {
      id: 4,
      title: 'PRD: RBAC Implementation',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:rbac', 'priority:critical'],
      dependsOn: [1, 2, 3],
      template: 'foundation',
      source: 'ROLES-PERMISSIONS.md',
      overview: 'Implement role-based access control using Laravel Policies and Spatie Permission package.'
    },
    {
      id: 5,
      title: 'PRD: Authentication & Authorization',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:workflow', 'priority:critical'],
      dependsOn: [4],
      template: 'workflow',
      source: 'ROLES-PERMISSIONS.md',
      overview: 'Implement authentication flows including login, verification, and professional verification.'
    },
    {
      id: 6,
      title: 'PRD: Multi-Tenant Architecture',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:data-model', 'priority:critical'],
      dependsOn: [],
      template: 'foundation',
      source: 'ENTITY-RELATIONSHIPS.md',
      overview: 'Design multi-tenant architecture with tenant isolation via X-Tenant header.'
    },
    {
      id: 7,
      title: 'PRD: Reference Data & Lookups',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:data-model', 'priority:critical'],
      dependsOn: [6],
      template: 'data-model',
      source: 'ENTITY-RELATIONSHIPS.md',
      overview: 'Define master lookup tables: Countries, Currencies, Cities, Districts, Unit Categories, Facility Types.'
    },
    {
      id: 8,
      title: 'PRD: Status System',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:data-model', 'priority:critical'],
      dependsOn: [6],
      template: 'data-model',
      source: 'ENTITY-RELATIONSHIPS.md',
      overview: 'Define unified status ID system with 69 distinct status codes across all domains.'
    },
    {
      id: 9,
      title: 'PRD: Feature Flags System',
      milestone: 'M0 - Foundation',
      labels: ['prd', 'foundation', 'type:data-model', 'priority:high'],
      dependsOn: [6],
      template: 'data-model',
      source: 'UI-COMPONENTS.md',
      overview: 'Implement feature flags (ENABLE_REQUESTS, ENABLE_OFFERS, etc.) for module visibility control.'
    },

    // =========================================================================
    // CORE MODEL PRDs (M1)
    // =========================================================================
    {
      id: 10,
      title: 'PRD: Communities Entity',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:data-model', 'priority:critical'],
      dependsOn: [6, 7],
      template: 'data-model',
      source: 'queries/properties',
      overview: 'Define Community entity with CRUD operations, status management, and amenities.'
    },
    {
      id: 11,
      title: 'PRD: Buildings Entity',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:data-model', 'priority:critical'],
      dependsOn: [10],
      template: 'data-model',
      source: 'queries/properties',
      overview: 'Define Building entity belonging to Community with floors and unit capacity.'
    },
    {
      id: 12,
      title: 'PRD: Units Entity',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:data-model', 'priority:critical'],
      dependsOn: [11],
      template: 'data-model',
      source: 'queries/properties',
      overview: 'Define Unit entity with categories, types, status, pricing, and media uploads.'
    },
    {
      id: 13,
      title: 'PRD: Properties Module UI',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:workflow', 'priority:critical'],
      dependsOn: [10, 11, 12],
      template: 'module-ui',
      source: 'UI-COMPONENTS.md',
      overview: 'Implement Properties module with list views, forms, and navigation for Communities/Buildings/Units.'
    },
    {
      id: 14,
      title: 'PRD: Contacts - Owners',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:data-model', 'priority:critical'],
      dependsOn: [4, 10],
      template: 'data-model',
      source: 'queries/contacts',
      overview: 'Define Owner contact type with property ownership relationships.'
    },
    {
      id: 15,
      title: 'PRD: Contacts - Tenants',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:data-model', 'priority:critical'],
      dependsOn: [4, 12],
      template: 'data-model',
      source: 'queries/contacts',
      overview: 'Define Tenant contact type with invitation flow and family members.'
    },
    {
      id: 16,
      title: 'PRD: Contacts - Admins',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:data-model', 'priority:critical'],
      dependsOn: [4],
      template: 'data-model',
      source: 'queries/contacts',
      overview: 'Define Admin contact type with manager roles and scope-based access.'
    },
    {
      id: 17,
      title: 'PRD: Contacts - Professionals',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:data-model', 'priority:critical'],
      dependsOn: [4],
      template: 'data-model',
      source: 'queries/contacts',
      overview: 'Define Service Professional contact type with verification and category assignment.'
    },
    {
      id: 18,
      title: 'PRD: Contacts Module UI',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:workflow', 'priority:critical'],
      dependsOn: [14, 15, 16, 17],
      template: 'module-ui',
      source: 'UI-COMPONENTS.md',
      overview: 'Implement Contacts module with tabbed views for each contact type and CRUD forms.'
    },
    {
      id: 19,
      title: 'PRD: Transactions Entity',
      milestone: 'M1 - Core Models',
      labels: ['prd', 'module', 'type:data-model', 'priority:critical'],
      dependsOn: [15],
      template: 'data-model',
      source: 'queries/transactions',
      overview: 'Define Transaction entity with categories, types, and payment tracking.'
    },

    // =========================================================================
    // LEASING PRDs (M2)
    // =========================================================================
    {
      id: 20,
      title: 'PRD: Leases Entity & Lifecycle',
      milestone: 'M2 - Leasing',
      labels: ['prd', 'module', 'type:data-model', 'type:workflow', 'priority:critical'],
      dependsOn: [12, 15, 19],
      template: 'data-model',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Define Lease entity with lifecycle states (Draft, Active, Expired, Closed) and transaction generation.'
    },
    {
      id: 21,
      title: 'PRD: Lease Creation Wizard',
      milestone: 'M2 - Leasing',
      labels: ['prd', 'module', 'type:workflow', 'priority:critical'],
      dependsOn: [20],
      template: 'workflow',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Implement multi-step lease creation with unit selection, tenant assignment, and terms configuration.'
    },
    {
      id: 22,
      title: 'PRD: Lease Renewal Flow',
      milestone: 'M2 - Leasing',
      labels: ['prd', 'module', 'type:workflow', 'priority:high'],
      dependsOn: [20],
      template: 'workflow',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Implement lease renewal process with expiry notifications and auto-renewal options.'
    },
    {
      id: 23,
      title: 'PRD: Lease Termination & Move-out',
      milestone: 'M2 - Leasing',
      labels: ['prd', 'module', 'type:workflow', 'priority:high'],
      dependsOn: [20],
      template: 'workflow',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Implement lease termination and move-out workflows with deposit handling.'
    },
    {
      id: 24,
      title: 'PRD: Sub-leases',
      milestone: 'M2 - Leasing',
      labels: ['prd', 'module', 'type:data-model', 'priority:medium'],
      dependsOn: [20],
      template: 'data-model',
      source: 'queries/leasing',
      overview: 'Define Sub-lease entity linked to parent lease with separate tenant assignment.'
    },
    {
      id: 25,
      title: 'PRD: Quotes & Applications',
      milestone: 'M2 - Leasing',
      labels: ['prd', 'module', 'type:workflow', 'priority:high'],
      dependsOn: [12, 15],
      template: 'workflow',
      source: 'queries/leasing',
      overview: 'Implement quote generation and application workflow leading to lease creation.'
    },
    {
      id: 26,
      title: 'PRD: Leasing Module UI',
      milestone: 'M2 - Leasing',
      labels: ['prd', 'module', 'type:workflow', 'priority:critical'],
      dependsOn: [20, 21, 22, 23, 24, 25],
      template: 'module-ui',
      source: 'UI-COMPONENTS.md',
      overview: 'Implement Leasing module with tabs for Visits, Applications, Quotes, and Leases.'
    },

    // =========================================================================
    // OPERATIONS PRDs (M3)
    // =========================================================================
    {
      id: 27,
      title: 'PRD: Service Request Categories',
      milestone: 'M3 - Operations',
      labels: ['prd', 'module', 'type:data-model', 'priority:high'],
      dependsOn: [7],
      template: 'data-model',
      source: 'queries/requests',
      overview: 'Define service request categories, sub-categories, and types hierarchy.'
    },
    {
      id: 28,
      title: 'PRD: Service Requests Entity',
      milestone: 'M3 - Operations',
      labels: ['prd', 'module', 'type:data-model', 'priority:high'],
      dependsOn: [27, 15, 17],
      template: 'data-model',
      source: 'queries/requests',
      overview: 'Define Service Request entity with scheduling, assignments, and attachments.'
    },
    {
      id: 29,
      title: 'PRD: Service Request Lifecycle',
      milestone: 'M3 - Operations',
      labels: ['prd', 'module', 'type:workflow', 'priority:high'],
      dependsOn: [28],
      template: 'workflow',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Implement request lifecycle (New, Assigned, In Progress, Resolved) with 11+ status transitions.'
    },
    {
      id: 30,
      title: 'PRD: Service Requests Module UI',
      milestone: 'M3 - Operations',
      labels: ['prd', 'module', 'type:workflow', 'priority:high'],
      dependsOn: [28, 29],
      template: 'module-ui',
      source: 'UI-COMPONENTS.md',
      overview: 'Implement Requests module with Home Services and Common Area tabs.'
    },
    {
      id: 31,
      title: 'PRD: Visitor Access Entity',
      milestone: 'M3 - Operations',
      labels: ['prd', 'module', 'type:data-model', 'priority:medium'],
      dependsOn: [12, 15],
      template: 'data-model',
      source: 'queries/requests',
      overview: 'Define Visitor Access Request entity with guest details and visit period.'
    },
    {
      id: 32,
      title: 'PRD: Visitor Access Workflow',
      milestone: 'M3 - Operations',
      labels: ['prd', 'module', 'type:workflow', 'priority:medium'],
      dependsOn: [31],
      template: 'workflow',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Implement visitor access workflow (Pending, Approved, Checked In, Checked Out).'
    },
    {
      id: 33,
      title: 'PRD: Facilities Entity',
      milestone: 'M3 - Operations',
      labels: ['prd', 'module', 'type:data-model', 'priority:medium'],
      dependsOn: [10],
      template: 'data-model',
      source: 'queries/properties',
      overview: 'Define Facility entity belonging to Community with booking availability.'
    },
    {
      id: 34,
      title: 'PRD: Facility Bookings',
      milestone: 'M3 - Operations',
      labels: ['prd', 'module', 'type:workflow', 'priority:medium'],
      dependsOn: [33, 15],
      template: 'workflow',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Implement facility booking workflow with 8-state lifecycle.'
    },

    // =========================================================================
    // MARKETPLACE PRDs (M4)
    // =========================================================================
    {
      id: 35,
      title: 'PRD: Marketplace Listings',
      milestone: 'M4 - Marketplace',
      labels: ['prd', 'module', 'type:data-model', 'priority:medium'],
      dependsOn: [12],
      template: 'data-model',
      source: 'queries/marketplace',
      overview: 'Define unit listing entity for marketplace with public visibility settings.'
    },
    {
      id: 36,
      title: 'PRD: Marketplace Customers',
      milestone: 'M4 - Marketplace',
      labels: ['prd', 'module', 'type:data-model', 'priority:medium'],
      dependsOn: [4],
      template: 'data-model',
      source: 'queries/marketplace',
      overview: 'Define Marketplace Customer entity for prospective buyers/renters.'
    },
    {
      id: 37,
      title: 'PRD: Visit Scheduling',
      milestone: 'M4 - Marketplace',
      labels: ['prd', 'module', 'type:workflow', 'priority:medium'],
      dependsOn: [35, 36],
      template: 'workflow',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Implement property visit scheduling with calendar integration.'
    },
    {
      id: 38,
      title: 'PRD: Sales & Booking Contracts',
      milestone: 'M4 - Marketplace',
      labels: ['prd', 'module', 'type:workflow', 'priority:medium'],
      dependsOn: [35, 36, 19],
      template: 'workflow',
      source: 'BUSINESS-WORKFLOWS.md',
      overview: 'Implement sales workflow with booking deposits and contract signing.'
    },
    {
      id: 39,
      title: 'PRD: Marketplace Admin Settings',
      milestone: 'M4 - Marketplace',
      labels: ['prd', 'module', 'type:data-model', 'priority:medium'],
      dependsOn: [35],
      template: 'data-model',
      source: 'queries/marketplace',
      overview: 'Define marketplace settings for deposits, payment terms, and bank accounts.'
    },

    // =========================================================================
    // ANALYTICS PRDs (M5)
    // =========================================================================
    {
      id: 40,
      title: 'PRD: Dashboard Overview',
      milestone: 'M5 - Analytics',
      labels: ['prd', 'module', 'type:workflow', 'priority:high'],
      dependsOn: [20, 28],
      template: 'module-ui',
      source: 'UI-COMPONENTS.md',
      overview: 'Implement dashboard with KPIs, charts, and requires-attention widgets.'
    },
    {
      id: 41,
      title: 'PRD: Reports - Lease & Maintenance',
      milestone: 'M5 - Analytics',
      labels: ['prd', 'module', 'type:workflow', 'priority:medium'],
      dependsOn: [20, 28],
      template: 'workflow',
      source: 'UI-COMPONENTS.md',
      overview: 'Implement system reports for lease status and maintenance tracking.'
    },
    {
      id: 42,
      title: 'PRD: Power BI Integration',
      milestone: 'M5 - Analytics',
      labels: ['prd', 'module', 'type:api', 'priority:low'],
      dependsOn: [40],
      template: 'workflow',
      source: 'queries/dashboard',
      overview: 'Integrate Power BI for advanced analytics and embedded reports.'
    },
    {
      id: 43,
      title: 'PRD: Notifications System',
      milestone: 'M5 - Analytics',
      labels: ['prd', 'module', 'type:workflow', 'priority:high'],
      dependsOn: [4],
      template: 'workflow',
      source: 'UI-COMPONENTS.md',
      overview: 'Implement notification system with in-app, email, and push channels.'
    },
    {
      id: 44,
      title: 'PRD: Announcements & Directory',
      milestone: 'M5 - Analytics',
      labels: ['prd', 'module', 'type:data-model', 'priority:medium'],
      dependsOn: [10],
      template: 'data-model',
      source: 'queries/dashboard',
      overview: 'Implement announcements and community directory features.'
    }
  ] as PRDConfig[]
};
