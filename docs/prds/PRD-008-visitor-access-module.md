# PRD-008: Visitor Access Module

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M5 - Visitor & Facilities |
| **Estimated Effort** | 2-3 weeks |
| **Dependencies** | Units, Contacts |
| **Related Pages** | visitor-access, visitor-access-history, visitor-access-details |

## Problem Statement

Property managers and tenants need to manage visitor access to the property. This includes pre-registering visitors, generating access codes/QR codes, tracking visitor history, and managing security checkpoints.

## Goals

1. Allow tenants to pre-register expected visitors
2. Generate unique access codes/QR codes for visitors
3. Track visitor check-in and check-out
4. Provide visitor history for security audits
5. Enable managers to manage visitor settings

## User Stories

### US-001: Pre-register Visitor
**As a** tenant
**I want to** pre-register an expected visitor
**So that** they can access the property without issues

**Acceptance Criteria:**
- Enter visitor name and phone number
- Select visit date and expected time
- Specify purpose of visit
- Generate access code automatically
- Receive confirmation with code

### US-002: Generate QR Code
**As a** system
**I want to** generate a unique QR code for each visitor
**So that** security can quickly verify access

**Acceptance Criteria:**
- QR code contains visitor ID
- Code is scannable by security app
- Code expires after visit date
- Code shows visitor details when scanned

### US-003: Check-in Visitor
**As a** security guard
**I want to** check in a visitor at the gate
**So that** I can verify their authorization

**Acceptance Criteria:**
- Scan QR code or enter access code
- Verify visitor identity
- Record check-in time
- Print visitor badge (optional)
- Notify tenant of arrival

### US-004: Check-out Visitor
**As a** security guard
**I want to** check out a visitor when they leave
**So that** I can track time spent on property

**Acceptance Criteria:**
- Scan QR code or enter visitor ID
- Record check-out time
- Calculate visit duration
- Archive visit record

### US-005: View Visitor History
**As a** property manager
**I want to** view all visitor history
**So that** I can audit access patterns

**Acceptance Criteria:**
- List all past visitors
- Filter by date range, unit, status
- Search by visitor name or code
- Export to CSV/PDF

### US-006: Cancel Visitor Access
**As a** tenant
**I want to** cancel a pre-registered visitor
**So that** they cannot access if plans change

**Acceptance Criteria:**
- Cancel from visitor detail page
- Invalidate access code immediately
- Send cancellation notification to visitor

## Technical Requirements

### Database Schema

```sql
-- visitor_accesses table (exists, enhance)
ALTER TABLE visitor_accesses ADD COLUMN (
    access_code VARCHAR(10) UNIQUE,
    qr_code_data TEXT,
    visit_purpose VARCHAR(255),
    expected_arrival TIME,
    expected_departure TIME,
    actual_arrival TIMESTAMP,
    actual_departure TIMESTAMP,
    checked_in_by BIGINT REFERENCES users(id),
    checked_out_by BIGINT REFERENCES users(id),
    vehicle_plate VARCHAR(20),
    vehicle_type VARCHAR(50),
    notes TEXT,
    status ENUM('pending', 'approved', 'checked_in', 'checked_out', 'cancelled', 'expired') DEFAULT 'pending'
);

-- visitor_access_logs for audit trail
CREATE TABLE visitor_access_logs (
    id BIGINT PRIMARY KEY,
    visitor_access_id BIGINT REFERENCES visitor_accesses(id),
    action ENUM('created', 'approved', 'checked_in', 'checked_out', 'cancelled'),
    performed_by BIGINT REFERENCES users(id),
    performed_at TIMESTAMP,
    ip_address VARCHAR(45),
    device_info TEXT
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/visitor-access | List visitors with filters |
| POST | /api/visitor-access | Pre-register visitor |
| GET | /api/visitor-access/{id} | Get visitor details |
| PUT | /api/visitor-access/{id} | Update visitor |
| DELETE | /api/visitor-access/{id} | Cancel visitor |
| POST | /api/visitor-access/{id}/approve | Approve visitor (manager) |
| POST | /api/visitor-access/{id}/check-in | Check in visitor |
| POST | /api/visitor-access/{id}/check-out | Check out visitor |
| GET | /api/visitor-access/history | Get visitor history |
| GET | /api/visitor-access/by-code/{code} | Lookup by access code |
| GET | /api/visitor-access/{id}/qr-code | Get QR code image |

### UI Components

1. **Visitor List Page** (`/visitor-access`)
   - Table with today's expected visitors
   - Quick check-in/check-out buttons
   - Filter by status, date
   - "Pre-register Visitor" button

2. **Pre-register Form** (Modal or Page)
   - Visitor name input
   - Phone number with validation
   - Date picker
   - Time range picker
   - Purpose dropdown/text
   - Vehicle details (optional)
   - Submit generates code

3. **Visitor Detail Page** (`/visitor-access/{id}`)
   - Visitor information card
   - QR code display
   - Access code in large text
   - Timeline of actions
   - Check-in/Check-out buttons
   - Cancel button

4. **History Page** (`/visitor-access/history`)
   - Full history table
   - Advanced filters
   - Date range picker
   - Export buttons

5. **Security Scanner Page** (Mobile-optimized)
   - QR code scanner
   - Manual code entry
   - Visitor details display
   - Quick action buttons

## Captured Page Analysis

### visitor-access (Main List)
- **URL:** `/visitor-access`
- **Key Elements:**
  - Table: Visitor Name, Unit, Date, Time, Status, Code
  - Today/Upcoming toggle
  - Search bar
  - "Pre-register" button
  - Filter by status

### visitor-access-history (History)
- **URL:** `/visitor-access/history`
- **Key Elements:**
  - Full history table
  - Date range filter
  - Status filter
  - Export button

### visitor-access-details (Detail View)
- **URL:** `/visitor-access/{id}`
- **Key Elements:**
  - Visitor card with photo placeholder
  - Large QR code
  - Access code display
  - Check-in/out times
  - Action buttons

## State Machine

```
[Pre-registered] --approve--> [Approved] --check_in--> [Checked In] --check_out--> [Checked Out]
       |                           |
       v                           v
  [Cancelled]                 [Expired]
```

## Validation Rules

| Field | Rules |
|-------|-------|
| visitor_name | Required, max:255 |
| visitor_phone | Required, valid phone format |
| unit_id | Required, must belong to tenant |
| visit_date | Required, >= today |
| expected_arrival | Required, valid time |
| visit_purpose | Required, max:255 |

## Notifications

| Event | Recipients | Channel |
|-------|-----------|---------|
| Visitor Pre-registered | Visitor | SMS with code |
| Visitor Approved | Tenant, Visitor | SMS, Push |
| Visitor Checked In | Tenant | Push |
| Visitor Cancelled | Visitor | SMS |

## Testing Requirements

1. **Unit Tests**
   - Access code generation uniqueness
   - QR code generation
   - Status transitions

2. **Feature Tests**
   - Pre-registration flow
   - Check-in/check-out flow
   - History filtering
   - Authorization checks

3. **E2E Tests**
   - Full visitor lifecycle
   - QR code scanning simulation
   - Export functionality

## Security Considerations

1. Access codes should be random, non-sequential
2. QR codes should be signed to prevent forgery
3. Codes expire automatically after visit date
4. Rate limit code lookups to prevent brute force

## References

- Captured Pages: `docs/pages/visitor-access*`
- API Spec: `docs/api/queries/visitor-access/`
- Settings: `docs/pages/settings-visitor*`
