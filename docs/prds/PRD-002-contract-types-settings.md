# PRD-002: Contract Types Settings

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High (Blocker) |
| **Milestone** | M2 - Leasing |
| **Estimated Effort** | 1 week |
| **Dependencies** | None |
| **Blocks** | PRD-001 (Quotes), Lease Creation |
| **Related Pages** | settings-leasing-contract-types, settings-leasing-contract-types-add |

## Problem Statement

The leasing module requires contract types to be configured before quotes or leases can be created. Currently, there is no UI to manage contract types, which blocks the entire leasing workflow. This was identified as a critical bug during the scanner exploration (see `docs/bugs/CONTRACT-TYPES-API-BUG.md`).

## Goals

1. Provide UI to create and manage contract types
2. Allow configuration of contract type attributes
3. Enable contract types to be used in quotes and leases
4. Support different contract types for residential vs commercial

## User Stories

### US-001: List Contract Types
**As a** property manager
**I want to** see all configured contract types
**So that** I can manage leasing options

**Acceptance Criteria:**
- View list of all contract types
- See name, category, and status
- Filter by category (residential/commercial)
- Sort by name or creation date

### US-002: Create Contract Type
**As a** property manager
**I want to** create a new contract type
**So that** I can offer different leasing options

**Acceptance Criteria:**
- Enter contract type name
- Select category (residential/commercial/mixed)
- Configure default duration
- Set default payment frequency
- Add description
- Activate/deactivate type

### US-003: Edit Contract Type
**As a** property manager
**I want to** modify an existing contract type
**So that** I can update leasing options

**Acceptance Criteria:**
- Edit all contract type fields
- See usage count (quotes/leases using this type)
- Warn if type is in use before deactivating

### US-004: Delete Contract Type
**As a** property manager
**I want to** delete unused contract types
**So that** I can keep my options clean

**Acceptance Criteria:**
- Only allow deletion of unused types
- Show error if type is in use
- Soft delete with recovery option

## Technical Requirements

### Database Schema

```sql
-- contract_types table
CREATE TABLE contract_types (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    name VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255),
    category ENUM('residential', 'commercial', 'mixed') DEFAULT 'residential',
    description TEXT,

    -- Defaults
    default_duration_months INT DEFAULT 12,
    default_payment_frequency ENUM('monthly', 'quarterly', 'yearly') DEFAULT 'monthly',

    -- Settings
    requires_security_deposit BOOLEAN DEFAULT true,
    security_deposit_months INT DEFAULT 1,
    allows_sublease BOOLEAN DEFAULT false,

    -- Status
    is_active BOOLEAN DEFAULT true,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    deleted_at TIMESTAMP
);

-- Add index for tenant scoping
CREATE INDEX idx_contract_types_tenant ON contract_types(tenant_id, is_active);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/leasing/contract-types | List contract types |
| POST | /api/settings/leasing/contract-types | Create contract type |
| GET | /api/settings/leasing/contract-types/{id} | Get contract type |
| PUT | /api/settings/leasing/contract-types/{id} | Update contract type |
| DELETE | /api/settings/leasing/contract-types/{id} | Delete contract type |
| POST | /api/settings/leasing/contract-types/{id}/activate | Activate type |
| POST | /api/settings/leasing/contract-types/{id}/deactivate | Deactivate type |

### UI Components

1. **Contract Types List Page** (`/settings/leasing/contract-types`)
   - Table with columns: Name, Category, Default Duration, Status, Actions
   - "Add Contract Type" button
   - Quick toggle for active/inactive
   - Edit and delete actions

2. **Add/Edit Contract Type Form** (`/settings/leasing/contract-types/AddNewSubcategory`)
   - Form fields:
     - Name (EN/AR)
     - Category dropdown
     - Default duration (months)
     - Payment frequency dropdown
     - Security deposit toggle + months
     - Sublease allowed toggle
     - Description textarea
   - Save and Cancel buttons

## Captured Page Analysis

### settings-leasing-contract-types (List Page)
- **URL:** `/settings/leasing/contract-types`
- **Key Elements:**
  - Table with contract types
  - Add button
  - Edit/Delete actions per row
  - Category filter tabs

### settings-leasing-contract-types-add (Create Form)
- **URL:** `/settings/leasing/contract-types/AddNewSubcategory`
- **Key Elements:**
  - Form with name input
  - Category selector
  - Duration input
  - Toggle switches for settings
  - Save/Cancel buttons

## Validation Rules

| Field | Rules |
|-------|-------|
| name | Required, max:255, unique per tenant |
| category | Required, in: residential, commercial, mixed |
| default_duration_months | Required, integer, min:1, max:120 |
| default_payment_frequency | Required, in: monthly, quarterly, yearly |
| security_deposit_months | Required if requires_security_deposit, min:0, max:12 |

## Testing Requirements

1. **Unit Tests**
   - Contract type creation
   - Validation rules
   - Soft delete behavior

2. **Feature Tests**
   - CRUD operations
   - Usage check before delete
   - Activation/deactivation

3. **E2E Tests**
   - Create contract type and use in quote
   - Edit active contract type
   - Attempt delete of used type

## Implementation Notes

1. This is a **critical blocker** - must be implemented first
2. Seed default contract types for new tenants:
   - "Standard Residential" (12 months, monthly)
   - "Short-term Residential" (6 months, monthly)
   - "Commercial Lease" (24 months, quarterly)
3. Add to settings navigation menu under "Leasing"

## References

- Captured Pages: `docs/pages/settings-leasing-contract-types*`
- Bug Report: `docs/bugs/CONTRACT-TYPES-API-BUG.md`
- API Spec: `docs/api/queries/settings/leasing/`
