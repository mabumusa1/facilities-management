# PRD-014: Marketplace Customers

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M7 - Marketplace |
| **Estimated Effort** | 2 weeks |
| **Dependencies** | PRD-013 (Marketplace Core) |
| **Related Pages** | marketplace-customers, marketplace-upload-leads-* |

## Problem Statement

Property managers need to manage prospective tenants (leads) who inquire about available units. This includes capturing leads from multiple sources, tracking their journey, and converting them to tenants.

## Goals

1. Capture leads from multiple sources
2. Manage customer pipeline (CRM)
3. Track customer interactions
4. Support bulk lead import
5. Convert leads to applications/leases

## User Stories

### US-001: Capture Lead from Inquiry
**As a** system
**I want to** auto-capture leads from inquiries
**So that** no prospect is lost

**Acceptance Criteria:**
- Create customer from listing inquiry
- Capture contact details
- Link to interested listing
- Assign to agent
- Send auto-response

### US-002: Manual Lead Creation
**As a** property manager
**I want to** manually add leads
**So that** I can track all prospects

**Acceptance Criteria:**
- Enter contact details
- Select source (walk-in, referral, etc.)
- Set preferences
- Assign to agent
- Add notes

### US-003: Bulk Import Leads
**As a** property manager
**I want to** import leads from spreadsheet
**So that** I can migrate existing data

**Acceptance Criteria:**
- Upload CSV/Excel file
- Map columns to fields
- Validate data
- Show errors for correction
- Import valid records
- De-duplicate

### US-004: Pipeline Management
**As a** property manager
**I want to** manage lead pipeline
**So that** I can track progress

**Acceptance Criteria:**
- Kanban board view
- Status columns (New, Contacted, Qualified, etc.)
- Drag to change status
- Filter by agent, date, source
- Quick actions

### US-005: Track Interactions
**As a** property manager
**I want to** log customer interactions
**So that** I have full history

**Acceptance Criteria:**
- Log calls, emails, meetings
- Record notes
- Schedule follow-ups
- Set reminders
- View timeline

### US-006: Convert to Tenant
**As a** property manager
**I want to** convert lead to tenant
**So that** the journey is complete

**Acceptance Criteria:**
- Create lease application from lead
- Pre-fill with lead data
- Link customer to application
- Update customer status
- Track conversion metrics

## Technical Requirements

### Database Schema

```sql
-- marketplace_customers table (from PRD-013, detailed here)
CREATE TABLE marketplace_customers (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    contact_id BIGINT REFERENCES contacts(id),

    -- Contact info (duplicated for non-contact leads)
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(50),

    -- Source
    source ENUM('marketplace', 'referral', 'walk_in', 'phone', 'social', 'import', 'other'),
    source_listing_id BIGINT REFERENCES marketplace_listings(id),
    source_details TEXT,

    -- Pipeline status
    status ENUM('new', 'contacted', 'qualified', 'viewing', 'negotiating', 'application', 'converted', 'lost') DEFAULT 'new',
    status_changed_at TIMESTAMP,
    lost_reason VARCHAR(255),

    -- Assignment
    assigned_to BIGINT REFERENCES users(id),
    assigned_at TIMESTAMP,

    -- Preferences
    preferred_budget_min DECIMAL(10,2),
    preferred_budget_max DECIMAL(10,2),
    preferred_move_in DATE,
    preferred_unit_types JSON,
    preferred_communities JSON,
    preferred_bedrooms INT,
    notes TEXT,

    -- Conversion
    converted_lease_id BIGINT REFERENCES leases(id),
    converted_at TIMESTAMP,

    -- Scoring
    lead_score INT DEFAULT 0,
    last_activity_at TIMESTAMP,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- customer_interactions table
CREATE TABLE customer_interactions (
    id BIGINT PRIMARY KEY,
    customer_id BIGINT REFERENCES marketplace_customers(id),
    type ENUM('call', 'email', 'meeting', 'visit', 'note', 'sms', 'whatsapp'),
    direction ENUM('inbound', 'outbound'),
    subject VARCHAR(255),
    content TEXT,
    outcome VARCHAR(255),
    follow_up_date DATE,
    follow_up_note TEXT,
    performed_by BIGINT REFERENCES users(id),
    performed_at TIMESTAMP,
    created_at TIMESTAMP
);

-- lead_import_batches table
CREATE TABLE lead_import_batches (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    file_name VARCHAR(255),
    total_rows INT,
    imported_count INT DEFAULT 0,
    error_count INT DEFAULT 0,
    duplicate_count INT DEFAULT 0,
    status ENUM('processing', 'completed', 'failed'),
    error_details JSON,
    imported_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    completed_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/marketplace/customers | List customers |
| POST | /api/marketplace/customers | Create customer |
| GET | /api/marketplace/customers/{id} | Get customer |
| PUT | /api/marketplace/customers/{id} | Update customer |
| DELETE | /api/marketplace/customers/{id} | Delete customer |
| POST | /api/marketplace/customers/{id}/assign | Assign to agent |
| POST | /api/marketplace/customers/{id}/status | Update status |
| GET | /api/marketplace/customers/{id}/interactions | Get interactions |
| POST | /api/marketplace/customers/{id}/interactions | Log interaction |
| POST | /api/marketplace/customers/{id}/convert | Convert to application |
| POST | /api/marketplace/customers/import | Start bulk import |
| GET | /api/marketplace/customers/import/{batch_id} | Get import status |
| GET | /api/marketplace/customers/pipeline | Pipeline summary |

### UI Components

1. **Customers List** (`/marketplace/customers`)
   - Table view
   - Kanban toggle
   - Filters
   - Bulk actions

2. **Kanban Board**
   - Status columns
   - Draggable cards
   - Quick actions

3. **Customer Detail** (`/marketplace/customers/{id}`)
   - Contact info
   - Preferences
   - Timeline
   - Interested listings
   - Actions

4. **Add Interaction Form**
   - Type selector
   - Content/notes
   - Follow-up date
   - Outcome

5. **Bulk Import** (`/marketplace/upload-leads`)
   - File upload
   - Column mapping
   - Preview
   - Error handling
   - Progress

6. **Import Errors** (`/marketplace/upload-leads-errors`)
   - Error list
   - Row details
   - Fix and retry

## Captured Page Analysis

- `marketplace-customers` - Customer list
- `marketplace-upload-leads` - Bulk import
- `marketplace-upload-leads-errors` - Import errors

## Validation Rules

| Field | Rules |
|-------|-------|
| email | Required if no phone, valid email |
| phone | Required if no email, valid phone |
| status | Required, valid enum |
| preferred_budget_min | Numeric, >= 0 |
| preferred_budget_max | Numeric, >= min |

## Testing Requirements

1. **Unit Tests** - Lead scoring, de-duplication
2. **Feature Tests** - CRUD, pipeline, import
3. **E2E Tests** - Capture lead, nurture, convert

## References

- Captured Pages: `docs/pages/marketplace-customers`, `docs/pages/marketplace-upload-leads*`
- Marketplace Core: PRD-013
