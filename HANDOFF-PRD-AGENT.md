# Product Owner Agent Handoff

> Session Date: 2026-04-12
> Purpose: Write PRDs as GitHub issues to rebuild the Atar property management system

## Project Context

This is a reverse engineering project that captured the API surface, UI structure, and business rules from the Atar property management platform (goatar.com). The goal is to reconstruct the system based on documented artifacts.

## Artifacts Available

### API Documentation

| File | Description |
|------|-------------|
| `src/api/openapi.json` | OpenAPI 3.0 spec (58 paths, 77 operations, 14 tags) |
| `src/api/openapi.yaml` | Same spec in YAML format |
| `src/api/docs/ui-component-map.json` | Machine-readable component mapping |

### Human-Readable Documentation

| File | Description |
|------|-------------|
| `src/api/docs/UI-COMPONENTS.md` | 43 routes, 9 modules, navigation structure, permissions |
| `src/api/docs/ROLES-PERMISSIONS.md` | RBAC system (4 actions, 23 subjects) |
| `src/api/docs/BUSINESS-WORKFLOWS.md` | Business processes, state machines, lifecycle flows |
| `src/api/docs/ENTITY-RELATIONSHIPS.md` | Data model, entity relationships, cardinality |

### Schema Data

| Directory | Description |
|-----------|-------------|
| `src/api/queries/` | GET endpoint response schemas by module |
| `src/api/mutations/` | POST/PUT endpoint request schemas by module |
| `src/api/validations/` | Form validation rules per entity |

### Raw Extracted Data

| File | Description |
|------|-------------|
| `pretty-js.split/signals.json` | Extracted routes and API bindings from React bundle |
| `pretty-js.split/signals.md` | Summary of extracted signals |
| `src/routes.json` | 280+ route definitions with dynamic params |

---

## Modules to Write PRDs For

### 1. Properties Module
- **Entities**: Communities, Buildings, Units
- **Features**: CRUD operations, status management, amenities, media uploads
- **Routes**: `/properties-list/communities`, `/properties-list/buildings`, `/properties-list/units`
- **API Prefix**: `/rf/communities`, `/rf/buildings`, `/rf/units`

### 2. Leasing Module
- **Entities**: Quotes, Applications, Leases, Sub-leases
- **Features**: Multi-step lease creation, renewals, terminations, move-out
- **Routes**: `/leasing/quotes`, `/leasing/apps`, `/leasing/leases`
- **API Prefix**: `/rf/leases`, `/rf/quotes`, `/rf/applications`

### 3. Marketplace Module
- **Entities**: Customers, Listings, Visits, Booking Contracts
- **Features**: Property listings, customer management, visit scheduling
- **Routes**: `/marketplace/customers`, `/marketplace/listing`
- **API Prefix**: `/marketplace/admin/*`

### 4. Contacts Module
- **Entities**: Tenants, Owners, Managers, Service Professionals
- **Features**: Contact CRUD, family members, document uploads
- **Routes**: `/contacts/:type`, `/contacts/:type/form`
- **API Prefix**: `/rf/tenants`, `/rf/admins`, `/rf/professionals`

### 5. Requests Module
- **Entities**: Service Requests, Categories, Sub-categories, Types
- **Features**: Request creation, assignment, status workflow, scheduling
- **Routes**: `/requests`, `/requests?type={homeServices|neighbourhoodServices}`
- **API Prefix**: `/rf/requests/*`, `/rf/users/requests/*`

### 6. Transactions Module
- **Entities**: Transactions, Invoices, Payments
- **Features**: Payment tracking, invoice generation, financial reports
- **Routes**: `/transactions`
- **API Prefix**: `/rf/transactions`

### 7. Visitor Access Module
- **Entities**: Visitor Access Requests
- **Features**: Guest registration, approval/rejection workflow
- **Routes**: `/visitor-access`, `/visitor-access/visitor-details/:id`
- **API Prefix**: `/rf/users/visitor-access`

### 8. Dashboard Module
- **Entities**: Reports, Offers, Bookings, Suggestions, Directory
- **Features**: Analytics, notifications, quick access widgets
- **Routes**: `/dashboard/*`
- **API Prefix**: `/dashboard/*`, `/rf/announcements`

### 9. Settings Module
- **Entities**: Company settings, Request categories, Integrations
- **Features**: System configuration, user management
- **Routes**: Various settings pages
- **API Prefix**: `/rf/modules`, `/rf/common-lists`

---

## Permission System

### Actions
- `VIEW` - Read access
- `CREATE` - Create new records
- `UPDATE` - Modify existing records
- `DELETE` - Remove records

### Subjects (23 total)
```
Dashboard, Properties, MarketPlaces, Customers, Listings, Visits,
BookingAndContracts, Quotes, Applications, Leases, HomeServices,
NeighbourhoodServices, VisitorAccess, Bookings, Transactions, Offers,
Directories, Suggestions, Tenants, Owners, Managers, ServiceProfessionals,
Reports, SystemReports, PowerBiReports
```

---

## Feature Flags

The system uses feature flags to control module visibility:

| Flag | Controls |
|------|----------|
| `ENABLE_REQUESTS` | Requests module |
| `ENABLE_BOOKING_REQUESTS` | Booking requests |
| `ENABLE_OFFERS` | Offers feature |
| `ENABLE_DIRECTORY` | Directory feature |
| `ENABLE_SUGGESTION` | Suggestions feature |
| `ENABLE_TENANTS` | Tenant contacts |
| `ENABLE_OWNERS` | Owner contacts |
| `ENABLE_MANGERS` | Manager contacts |
| `ENABLE_PROFESSIONALS` | Service professional contacts |

---

## API Configuration

- **Base URL**: `https://api.goatar.com/api-management`
- **Authentication**: Bearer token
- **Multi-tenancy**: `X-Tenant` header (e.g., `testbusiness123`)
- **Pagination**: `is_paginate=1&page={n}&per_page={n}`
- **Search**: `search={query}` or `query={query}`

---

## Suggested PRD Structure

For each module, create GitHub issues with:

1. **Overview**: Module purpose and scope
2. **User Stories**: As a [role], I want to [action], so that [benefit]
3. **Entities**: Data models with fields and relationships
4. **API Endpoints**: Required endpoints with request/response shapes
5. **UI Components**: Pages and forms needed
6. **Permissions**: Required RBAC rules
7. **Validation Rules**: Form validation requirements
8. **Acceptance Criteria**: Definition of done

---

## Reading the Artifacts

### To understand an entity's structure:
```bash
cat src/api/queries/{module}/summary.json
cat src/api/validations/{entity}.json
```

### To see all endpoints for a module:
```bash
grep "/{module}" src/api/openapi.json
```

### To understand business workflows:
```bash
cat src/api/docs/BUSINESS-WORKFLOWS.md
```

### To see UI routes and permissions:
```bash
cat src/api/docs/UI-COMPONENTS.md
```

---

## GitHub Repository

Create issues in the target repository with labels:
- `prd` - Product requirement document
- `module:{name}` - Module identifier
- `priority:{high|medium|low}` - Implementation priority

Suggested milestone structure:
1. **MVP Core** - Properties, Contacts, basic CRUD
2. **Leasing** - Full leasing workflow
3. **Marketplace** - Customer-facing features
4. **Operations** - Requests, Visitor Access
5. **Analytics** - Dashboard, Reports
