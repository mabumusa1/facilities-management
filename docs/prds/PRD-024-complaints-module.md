# PRD-024: Complaints Module

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M6 - Communication |
| **Estimated Effort** | 1-2 weeks |
| **Dependencies** | Contacts module |
| **Related Pages** | complaints-*, communication-complaints-* |

## Problem Statement

Tenants need a formal channel to file complaints about services, neighbors, facilities, or management. Property managers need to track, investigate, and resolve complaints systematically.

## Goals

1. Enable tenants to submit complaints
2. Route complaints to appropriate handlers
3. Track complaint resolution
4. Ensure SLA compliance
5. Analyze complaint trends

## User Stories

### US-001: Submit Complaint
**As a** tenant
**I want to** file a formal complaint
**So that** issues are addressed officially

**Acceptance Criteria:**
- Select complaint category
- Describe issue in detail
- Attach evidence (photos, documents)
- Option for anonymous submission
- Receive reference number

### US-002: Route Complaints
**As a** system
**I want to** route complaints automatically
**So that** the right person handles them

**Acceptance Criteria:**
- Route by category
- Route by severity
- Assign to handler/team
- Escalation rules
- Notify assigned handler

### US-003: Investigate Complaint
**As a** handler
**I want to** investigate complaints
**So that** I can resolve them properly

**Acceptance Criteria:**
- View complaint details
- Add investigation notes
- Request additional info
- Contact involved parties
- Update status

### US-004: Resolve Complaint
**As a** handler
**I want to** resolve and close complaints
**So that** they're properly concluded

**Acceptance Criteria:**
- Document resolution
- Categorize outcome
- Notify complainant
- Request satisfaction feedback
- Close complaint

### US-005: Escalate Complaint
**As a** handler/system
**I want to** escalate complaints
**So that** serious issues get attention

**Acceptance Criteria:**
- Manual escalation option
- Auto-escalate on SLA breach
- Notify escalation chain
- Track escalation history
- Priority boost

### US-006: Analyze Complaints
**As a** manager
**I want to** analyze complaint patterns
**So that** I can improve service

**Acceptance Criteria:**
- Complaints by category
- Resolution times
- Satisfaction scores
- Trend analysis
- Handler performance

## Technical Requirements

### Database Schema

```sql
-- complaints table
CREATE TABLE complaints (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    reference_number VARCHAR(20) UNIQUE,

    -- Complainant
    contact_id BIGINT REFERENCES contacts(id),
    unit_id BIGINT REFERENCES units(id),
    is_anonymous BOOLEAN DEFAULT false,

    -- Complaint details
    category ENUM('maintenance', 'noise', 'neighbor', 'staff', 'facility', 'billing', 'security', 'other'),
    subcategory VARCHAR(100),
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',

    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    attachments JSON,

    -- Related entities
    related_unit_id BIGINT REFERENCES units(id), -- e.g., noisy neighbor
    related_contact_id BIGINT REFERENCES contacts(id), -- e.g., staff member
    related_request_id BIGINT REFERENCES requests(id),

    -- Status
    status ENUM('submitted', 'assigned', 'investigating', 'pending_info', 'resolved', 'closed', 'escalated') DEFAULT 'submitted',
    priority INT DEFAULT 0,

    -- Assignment
    assigned_to BIGINT REFERENCES users(id),
    assigned_at TIMESTAMP,
    escalated_to BIGINT REFERENCES users(id),
    escalation_level INT DEFAULT 0,

    -- Resolution
    resolution TEXT,
    resolution_category ENUM('resolved_favor', 'resolved_against', 'mediated', 'no_action', 'invalid'),
    resolved_by BIGINT REFERENCES users(id),
    resolved_at TIMESTAMP,

    -- Feedback
    satisfaction_rating INT, -- 1-5
    satisfaction_comment TEXT,
    feedback_requested_at TIMESTAMP,
    feedback_received_at TIMESTAMP,

    -- SLA
    sla_due_at TIMESTAMP,
    sla_breached BOOLEAN DEFAULT false,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- complaint_activities table
CREATE TABLE complaint_activities (
    id BIGINT PRIMARY KEY,
    complaint_id BIGINT REFERENCES complaints(id),

    activity_type ENUM('status_change', 'assignment', 'note', 'escalation', 'info_request', 'info_received', 'contact'),
    description TEXT,
    old_value VARCHAR(255),
    new_value VARCHAR(255),
    attachments JSON,

    performed_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP
);

-- complaint_sla_rules table
CREATE TABLE complaint_sla_rules (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),

    category VARCHAR(100),
    severity VARCHAR(20),

    response_hours INT,
    resolution_hours INT,
    escalation_hours INT,

    escalation_to BIGINT REFERENCES users(id),

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/complaints | List complaints |
| POST | /api/complaints | Submit complaint |
| GET | /api/complaints/{id} | Get complaint |
| PUT | /api/complaints/{id} | Update complaint |
| POST | /api/complaints/{id}/assign | Assign handler |
| POST | /api/complaints/{id}/status | Update status |
| POST | /api/complaints/{id}/escalate | Escalate |
| POST | /api/complaints/{id}/resolve | Resolve |
| POST | /api/complaints/{id}/close | Close |
| POST | /api/complaints/{id}/note | Add note |
| POST | /api/complaints/{id}/request-info | Request info from complainant |
| POST | /api/complaints/{id}/feedback | Submit satisfaction feedback |
| GET | /api/complaints/{id}/activities | Get activity log |
| GET | /api/complaints/analytics | Get analytics |

### SLA Configuration

```php
return [
    'default_response_hours' => 24,
    'default_resolution_hours' => 72,

    'severity_multipliers' => [
        'low' => 2.0,      // 2x standard SLA
        'medium' => 1.0,   // standard
        'high' => 0.5,     // half standard
        'critical' => 0.25, // quarter standard
    ],

    'auto_escalation' => [
        'enabled' => true,
        'on_sla_breach' => true,
        'hours_before_sla' => 4, // escalate 4 hours before SLA breach
    ],

    'categories' => [
        'maintenance' => ['response' => 24, 'resolution' => 72],
        'noise' => ['response' => 12, 'resolution' => 48],
        'neighbor' => ['response' => 24, 'resolution' => 72],
        'staff' => ['response' => 12, 'resolution' => 48],
        'security' => ['response' => 4, 'resolution' => 24],
        'billing' => ['response' => 24, 'resolution' => 72],
    ],
];
```

### UI Components

1. **Submit Complaint** (`/complaints/new`)
   - Category selector
   - Subject and description
   - File upload
   - Anonymous toggle
   - Submit button

2. **Complaints List** (`/complaints`)
   - Complaint cards/table
   - Status filters
   - Category filters
   - Search
   - Sort by date/priority

3. **Complaint Detail**
   - Full information
   - Activity timeline
   - Action buttons
   - Resolution form
   - Feedback section

4. **Handler Dashboard** (`/dashboard/complaints`)
   - Assigned complaints
   - SLA countdown
   - Quick actions
   - Priority view

5. **Analytics** (`/complaints/analytics`)
   - Category breakdown
   - Resolution times
   - Satisfaction trends
   - Handler performance

## Notifications

| Event | Recipients | Channel |
|-------|-----------|------------|
| Complaint Submitted | Complainant, Handler | Email, Push |
| Complaint Assigned | Handler | Email, Push |
| Status Updated | Complainant | Email, Push |
| Info Requested | Complainant | Email, Push |
| SLA Warning | Handler, Escalation | Push |
| Complaint Resolved | Complainant | Email |
| Feedback Requested | Complainant | Email |

## Captured Page Analysis

- `complaints-list` - Complaints list
- `complaints-details` - Complaint detail
- `communication-complaints` - Communication view
- `dashboard-complaints` - Dashboard widget

## Testing Requirements

1. **Unit Tests** - SLA calculation, routing logic
2. **Feature Tests** - Full lifecycle, escalation
3. **E2E Tests** - Submit, investigate, resolve
4. **Schedule Tests** - SLA checks, auto-escalation

## References

- Captured Pages: `docs/pages/complaints-*`, `docs/pages/communication-complaints-*`
