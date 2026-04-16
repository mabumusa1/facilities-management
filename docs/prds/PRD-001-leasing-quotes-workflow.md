# PRD-001: Leasing Quotes Workflow

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M2 - Leasing |
| **Estimated Effort** | 2-3 weeks |
| **Dependencies** | PRD-002 (Contract Types Settings) |
| **Related Pages** | leasing-quotes, leasing-quotes-create, leasing-quotes-main, leasing-quote-details |

## Problem Statement

Property managers need to create and send quotes to prospective tenants before converting them to lease applications. Currently, the leasing module supports applications and leases but lacks the quotes workflow that precedes them.

## Goals

1. Enable property managers to create quotes for prospective tenants
2. Allow quotes to be sent to tenants via email/SMS
3. Track quote status (draft, sent, accepted, rejected, expired)
4. Convert accepted quotes to lease applications
5. Support multiple quote versions and revisions

## User Stories

### US-001: Create Quote
**As a** property manager
**I want to** create a quote for a prospective tenant
**So that** I can provide them with rental terms before formalizing the application

**Acceptance Criteria:**
- Select unit from available units list
- Select or create prospect contact
- Choose contract type (residential, commercial, etc.)
- Set lease duration (start date, end date)
- Define rent amount and payment frequency
- Add additional charges (utilities, parking, etc.)
- Add special terms and conditions
- Save as draft or send immediately

### US-002: Send Quote
**As a** property manager
**I want to** send a quote to a prospective tenant
**So that** they can review and accept the terms

**Acceptance Criteria:**
- Send via email with PDF attachment
- Send via SMS with link to view
- Track delivery status
- Set quote expiration date
- Allow resending if needed

### US-003: Quote Status Tracking
**As a** property manager
**I want to** see the status of all quotes
**So that** I can follow up on pending quotes

**Acceptance Criteria:**
- List view with filtering by status
- Status indicators: Draft, Sent, Viewed, Accepted, Rejected, Expired
- Sort by creation date, expiration date, amount
- Search by tenant name, unit number

### US-004: Convert Quote to Application
**As a** property manager
**I want to** convert an accepted quote to a lease application
**So that** I can proceed with the formal leasing process

**Acceptance Criteria:**
- One-click conversion from quote detail page
- Pre-fill application with quote data
- Link quote to resulting application
- Update quote status to "Converted"

### US-005: Quote Revisions
**As a** property manager
**I want to** create a revised quote
**So that** I can adjust terms based on negotiations

**Acceptance Criteria:**
- Create new version from existing quote
- Track revision history
- Compare versions side by side
- Only latest version can be accepted

## Technical Requirements

### Database Schema

```sql
-- quotes table
CREATE TABLE quotes (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    quote_number VARCHAR(50) UNIQUE,
    unit_id BIGINT REFERENCES units(id),
    contact_id BIGINT REFERENCES contacts(id),
    contract_type_id BIGINT REFERENCES contract_types(id),
    status ENUM('draft', 'sent', 'viewed', 'accepted', 'rejected', 'expired', 'converted'),
    version INT DEFAULT 1,
    parent_quote_id BIGINT REFERENCES quotes(id),

    -- Lease terms
    start_date DATE,
    end_date DATE,
    rent_amount DECIMAL(10,2),
    payment_frequency ENUM('monthly', 'quarterly', 'yearly'),
    security_deposit DECIMAL(10,2),

    -- Additional charges (JSON)
    additional_charges JSON,

    -- Terms
    special_terms TEXT,

    -- Tracking
    sent_at TIMESTAMP,
    viewed_at TIMESTAMP,
    responded_at TIMESTAMP,
    expires_at TIMESTAMP,

    -- Conversion
    lease_application_id BIGINT REFERENCES lease_applications(id),

    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- quote_state_history table
CREATE TABLE quote_state_history (
    id BIGINT PRIMARY KEY,
    quote_id BIGINT REFERENCES quotes(id),
    from_status VARCHAR(50),
    to_status VARCHAR(50),
    changed_by BIGINT REFERENCES users(id),
    notes TEXT,
    created_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/leasing/quotes | List quotes with filters |
| POST | /api/leasing/quotes | Create new quote |
| GET | /api/leasing/quotes/{id} | Get quote details |
| PUT | /api/leasing/quotes/{id} | Update quote |
| DELETE | /api/leasing/quotes/{id} | Delete draft quote |
| POST | /api/leasing/quotes/{id}/send | Send quote to tenant |
| POST | /api/leasing/quotes/{id}/resend | Resend quote |
| POST | /api/leasing/quotes/{id}/accept | Accept quote (tenant action) |
| POST | /api/leasing/quotes/{id}/reject | Reject quote |
| POST | /api/leasing/quotes/{id}/revise | Create revision |
| POST | /api/leasing/quotes/{id}/convert | Convert to application |
| GET | /api/leasing/quotes/{id}/history | Get state history |

### UI Components

1. **Quote List Page** (`/leasing/quotes`)
   - Table with sortable columns
   - Status badges with colors
   - Quick actions dropdown
   - Filter sidebar

2. **Create Quote Page** (`/leasing/quotes/create`)
   - Multi-step form wizard
   - Unit selector with availability check
   - Contact selector with search
   - Contract type dropdown
   - Date pickers for lease duration
   - Currency input for amounts
   - Dynamic additional charges section

3. **Quote Detail Page** (`/leasing/quotes/{id}`)
   - Quote summary card
   - Status timeline
   - Action buttons based on status
   - Revision history
   - Linked application (if converted)

4. **Quote PDF Template**
   - Company branding
   - Quote details
   - Terms and conditions
   - Signature placeholder

## Captured Page Analysis

### leasing-quotes (List Page)
- **URL:** `/leasing/quotes`
- **Key Elements:**
  - Data table with columns: Quote #, Tenant, Unit, Amount, Status, Date
  - Filter by status, date range
  - "Create Quote" button
  - Row actions: View, Edit, Send, Delete

### leasing-quotes-create (Create Form)
- **URL:** `/leasing/quotes/create`
- **Key Elements:**
  - Step 1: Select Unit
  - Step 2: Select/Create Tenant
  - Step 3: Contract Details (type, duration, amount)
  - Step 4: Additional Charges
  - Step 5: Review & Send

### leasing-quote-details (Detail Page)
- **URL:** `/leasing/quotes/{id}`
- **Key Elements:**
  - Quote summary
  - Status with timeline
  - "Send Quote" / "Resend" buttons
  - "Convert to Application" button
  - Edit/Delete actions for drafts

## State Machine

```
[Draft] --send--> [Sent] --view--> [Viewed]
                    |                  |
                    v                  v
               [Expired]          [Accepted] --convert--> [Converted]
                    |                  |
                    v                  v
               [Rejected]         [Rejected]
```

## Validation Rules

| Field | Rules |
|-------|-------|
| unit_id | Required, must be available |
| contact_id | Required |
| contract_type_id | Required |
| start_date | Required, >= today |
| end_date | Required, > start_date |
| rent_amount | Required, > 0 |
| payment_frequency | Required, in: monthly, quarterly, yearly |
| expires_at | Required for send, > sent_at |

## Testing Requirements

1. **Unit Tests**
   - Quote creation with valid/invalid data
   - State transitions
   - Quote number generation

2. **Feature Tests**
   - Full quote lifecycle
   - Quote to application conversion
   - Quote revision workflow
   - Authorization checks

3. **E2E Tests**
   - Create and send quote
   - Accept quote and convert
   - Filter and search quotes

## Open Questions

1. Should quotes require approval before sending?
2. What is the default expiration period?
3. Can a quote be for multiple units?
4. Should we integrate with electronic signature?

## References

- Captured Pages: `docs/pages/leasing-quotes*`
- API Spec: `docs/api/queries/leasing/quotes/`
- Entity Relationships: `docs/api/docs/ENTITY-RELATIONSHIPS.md`
