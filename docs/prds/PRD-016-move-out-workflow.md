# PRD-016: Move-out Workflow

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M2 - Leasing |
| **Estimated Effort** | 2 weeks |
| **Dependencies** | Leases, Transactions |
| **Related Pages** | dashboard-move-out-*, leasing-* |

## Problem Statement

When a lease ends or a tenant requests early termination, property managers need a structured workflow to handle the move-out process including inspections, deposit settlements, and unit turnover.

## Goals

1. Initiate move-out process from lease
2. Schedule and conduct move-out inspection
3. Document unit condition and damages
4. Calculate deposit deductions
5. Process refunds or additional charges
6. Complete lease termination
7. Prepare unit for next tenant

## User Stories

### US-001: Initiate Move-out
**As a** property manager
**I want to** initiate a move-out process
**So that** I can track the lease termination

**Acceptance Criteria:**
- Trigger from expiring lease or tenant request
- Set expected move-out date
- Notify tenant of process
- Create move-out checklist
- Block unit from new listings

### US-002: Schedule Inspection
**As a** property manager
**I want to** schedule a move-out inspection
**So that** I can assess unit condition

**Acceptance Criteria:**
- Select inspection date/time
- Assign inspector
- Notify tenant of appointment
- Send inspection checklist
- Allow tenant to be present

### US-003: Conduct Inspection
**As an** inspector
**I want to** document unit condition
**So that** damages can be assessed

**Acceptance Criteria:**
- Room-by-room checklist
- Condition ratings
- Photo documentation
- Damage descriptions
- Meter readings
- Compare with move-in condition

### US-004: Calculate Settlement
**As a** property manager
**I want to** calculate the deposit settlement
**So that** I can process financial closure

**Acceptance Criteria:**
- Start with security deposit
- Deduct documented damages
- Deduct unpaid rent/utilities
- Add cleaning/repair charges
- Calculate net refund/amount due
- Generate settlement statement

### US-005: Process Refund
**As a** property manager
**I want to** process the deposit refund
**So that** the tenant receives their money

**Acceptance Criteria:**
- Approve settlement calculation
- Select refund method
- Record refund transaction
- Generate receipt
- Notify tenant

### US-006: Complete Move-out
**As a** property manager
**I want to** complete the move-out process
**So that** the unit is ready for turnover

**Acceptance Criteria:**
- Collect all keys/access cards
- Terminate lease
- Update unit status to vacant
- Create turnover tasks
- Archive tenant records
- Enable unit for new listing

### US-007: Early Termination
**As a** tenant
**I want to** request early lease termination
**So that** I can move out before lease end

**Acceptance Criteria:**
- Submit termination request
- Specify reason and desired date
- Acknowledge penalties
- Manager approval workflow
- Calculate early termination fees

## Technical Requirements

### Database Schema

```sql
-- move_outs table
CREATE TABLE move_outs (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    lease_id BIGINT REFERENCES leases(id),
    unit_id BIGINT REFERENCES units(id),
    contact_id BIGINT REFERENCES contacts(id),

    -- Type
    type ENUM('expiry', 'early_termination', 'eviction') DEFAULT 'expiry',

    -- Dates
    notice_date DATE,
    expected_move_out_date DATE,
    actual_move_out_date DATE,

    -- Status
    status ENUM(
        'initiated',
        'inspection_scheduled',
        'inspection_completed',
        'settlement_pending',
        'settlement_approved',
        'refund_processed',
        'completed',
        'cancelled'
    ) DEFAULT 'initiated',

    -- Inspection
    inspection_date TIMESTAMP,
    inspector_id BIGINT REFERENCES users(id),
    inspection_notes TEXT,
    inspection_photos JSON,

    -- Settlement
    security_deposit DECIMAL(10,2),
    damage_charges DECIMAL(10,2) DEFAULT 0,
    unpaid_rent DECIMAL(10,2) DEFAULT 0,
    cleaning_charges DECIMAL(10,2) DEFAULT 0,
    other_deductions DECIMAL(10,2) DEFAULT 0,
    net_refund DECIMAL(10,2),

    -- Keys
    keys_returned BOOLEAN DEFAULT false,
    keys_returned_date DATE,

    -- Early termination
    early_termination_fee DECIMAL(10,2),
    early_termination_reason TEXT,

    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- move_out_inspection_items table
CREATE TABLE move_out_inspection_items (
    id BIGINT PRIMARY KEY,
    move_out_id BIGINT REFERENCES move_outs(id),
    room VARCHAR(100),
    item VARCHAR(255),
    move_in_condition ENUM('excellent', 'good', 'fair', 'poor'),
    move_out_condition ENUM('excellent', 'good', 'fair', 'poor'),
    damage_description TEXT,
    repair_cost DECIMAL(10,2),
    photos JSON,
    created_at TIMESTAMP
);

-- move_out_deductions table
CREATE TABLE move_out_deductions (
    id BIGINT PRIMARY KEY,
    move_out_id BIGINT REFERENCES move_outs(id),
    description VARCHAR(255),
    amount DECIMAL(10,2),
    type ENUM('damage', 'cleaning', 'rent', 'utility', 'other'),
    supporting_document VARCHAR(500),
    created_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/move-outs | List move-outs |
| POST | /api/move-outs | Initiate move-out |
| GET | /api/move-outs/{id} | Get move-out details |
| PUT | /api/move-outs/{id} | Update move-out |
| POST | /api/move-outs/{id}/schedule-inspection | Schedule inspection |
| POST | /api/move-outs/{id}/inspection | Submit inspection |
| GET | /api/move-outs/{id}/inspection-items | Get inspection items |
| POST | /api/move-outs/{id}/inspection-items | Add inspection item |
| POST | /api/move-outs/{id}/calculate-settlement | Calculate settlement |
| POST | /api/move-outs/{id}/approve-settlement | Approve settlement |
| POST | /api/move-outs/{id}/process-refund | Process refund |
| POST | /api/move-outs/{id}/complete | Complete move-out |
| POST | /api/move-outs/{id}/cancel | Cancel move-out |
| POST | /api/leases/{id}/request-early-termination | Request early termination |

### UI Components

1. **Move-out Dashboard** (`/dashboard/move-out`)
   - Pending move-outs count
   - Upcoming inspections
   - Pending settlements
   - Recent completions

2. **Move-out List** (`/dashboard/move-out/tenants`)
   - Table with all move-outs
   - Filter by status
   - Quick actions

3. **Move-out Detail** (`/dashboard/move-out/{id}`)
   - Status timeline
   - Tenant/Unit info
   - Inspection details
   - Settlement calculation
   - Action buttons per status

4. **Inspection Form** (Modal or Page)
   - Room selector
   - Item checklist
   - Photo upload
   - Damage notes
   - Cost input

5. **Settlement Calculator** (In Detail Page)
   - Deposit amount
   - Itemized deductions
   - Add deduction form
   - Net calculation
   - Approve button

6. **Early Termination Request** (Tenant Portal)
   - Reason selection
   - Preferred date
   - Penalty acknowledgment
   - Submit request

## State Machine

```
[Initiated] --schedule--> [Inspection Scheduled] --inspect--> [Inspection Completed]
                                                                      |
                                                                      v
[Cancelled] <--cancel-- [Any State]               [Settlement Pending] --approve--> [Settlement Approved]
                                                                                            |
                                                                                            v
                                                                        [Refund Processed] --complete--> [Completed]
```

## Captured Page Analysis

### dashboard-move-out-main (Dashboard)
- Move-out summary cards
- Pending actions list
- Quick filters

### dashboard-move-out-tenants (List)
- Tenant table
- Status badges
- Action buttons

### dashboard-move-out-details (Detail)
- Tenant info card
- Timeline
- Settlement section
- Inspection results

## Workflow Steps

1. **Initiate** - Create move-out record from lease
2. **Schedule Inspection** - Set date, assign inspector
3. **Conduct Inspection** - Document condition, photos
4. **Calculate Settlement** - Add all deductions
5. **Approve Settlement** - Manager approval
6. **Process Refund** - Create refund transaction
7. **Complete** - Keys collected, lease terminated

## Validation Rules

| Field | Rules |
|-------|-------|
| expected_move_out_date | Required, >= today |
| inspection_date | Required for inspection, >= today |
| damage_charges | Numeric, >= 0 |
| net_refund | Can be negative (tenant owes) |

## Notifications

| Event | Recipients | Channel |
|-------|-----------|---------|
| Move-out Initiated | Tenant | Email, SMS |
| Inspection Scheduled | Tenant | Email, SMS |
| Settlement Ready | Tenant | Email |
| Refund Processed | Tenant | Email, SMS |

## Testing Requirements

1. **Unit Tests**
   - Settlement calculation
   - Status transitions
   - Deduction totals

2. **Feature Tests**
   - Full move-out workflow
   - Early termination flow
   - Refund processing

3. **E2E Tests**
   - Initiate to complete
   - Inspection documentation
   - Settlement approval

## References

- Captured Pages: `docs/pages/dashboard-move-out-*`
- API Spec: `docs/api/queries/leasing/move-out/`
- Lease Workflow: `docs/api/docs/BUSINESS-WORKFLOWS.md`
