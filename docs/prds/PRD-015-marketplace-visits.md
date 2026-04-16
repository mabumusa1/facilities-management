# PRD-015: Marketplace Visits

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M7 - Marketplace |
| **Estimated Effort** | 1-2 weeks |
| **Dependencies** | PRD-013, PRD-014 |
| **Related Pages** | marketplace-admin-visits-*, mp-admin-visits-*, dashboard-visits |

## Problem Statement

Property managers need to schedule, conduct, and track property viewing visits for prospective tenants. This is a critical step in the leasing journey.

## Goals

1. Schedule property visits
2. Send confirmations and reminders
3. Track visit outcomes
4. Collect feedback
5. Analyze visit performance

## User Stories

### US-001: Schedule Visit
**As a** property manager
**I want to** schedule a property visit
**So that** prospects can view units

**Acceptance Criteria:**
- Select customer
- Select unit(s) to view
- Pick date and time
- Set duration
- Send confirmation

### US-002: Calendar View
**As a** property manager
**I want to** see all visits on calendar
**So that** I can manage my schedule

**Acceptance Criteria:**
- Day/week/month views
- Color by status
- Click for details
- Drag to reschedule
- Filter by agent/unit

### US-003: Confirm/Cancel Visit
**As a** customer or manager
**I want to** confirm or cancel visits
**So that** schedules are accurate

**Acceptance Criteria:**
- Confirm via link
- Cancel with reason
- Reschedule option
- Auto-notifications

### US-004: Conduct Visit
**As a** agent
**I want to** log visit completion
**So that** outcomes are recorded

**Acceptance Criteria:**
- Check in customer
- Record actual time
- Note customer interest level
- Add observations
- Schedule follow-up

### US-005: Collect Feedback
**As a** system
**I want to** collect visit feedback
**So that** we improve service

**Acceptance Criteria:**
- Send feedback request
- Rate experience
- Comment on units
- Capture preferences
- Thank customer

## Technical Requirements

### Database Schema

```sql
-- marketplace_visits table (from PRD-013, detailed here)
CREATE TABLE marketplace_visits (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    customer_id BIGINT REFERENCES marketplace_customers(id),

    -- Units (can visit multiple)
    units JSON, -- [{unit_id, listing_id, interest_level, notes}]
    primary_unit_id BIGINT REFERENCES units(id),

    -- Scheduling
    scheduled_date DATE NOT NULL,
    scheduled_time TIME NOT NULL,
    duration_minutes INT DEFAULT 30,

    -- Status
    status ENUM('scheduled', 'confirmed', 'in_progress', 'completed', 'no_show', 'cancelled', 'rescheduled') DEFAULT 'scheduled',
    status_reason TEXT,

    -- Confirmation
    confirmed_at TIMESTAMP,
    confirmation_method ENUM('link', 'call', 'email', 'sms'),

    -- Completion
    actual_start_time TIMESTAMP,
    actual_end_time TIMESTAMP,
    overall_interest_level INT, -- 1-5

    -- Feedback
    customer_feedback TEXT,
    customer_rating INT, -- 1-5
    agent_notes TEXT,

    -- Follow-up
    follow_up_date DATE,
    follow_up_type VARCHAR(50),
    follow_up_notes TEXT,

    -- Assignment
    conducted_by BIGINT REFERENCES users(id),

    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- visit_reminders table
CREATE TABLE visit_reminders (
    id BIGINT PRIMARY KEY,
    visit_id BIGINT REFERENCES marketplace_visits(id),
    reminder_type ENUM('confirmation', 'reminder_24h', 'reminder_1h'),
    sent_at TIMESTAMP,
    channel ENUM('email', 'sms', 'push'),
    status ENUM('sent', 'delivered', 'failed')
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/marketplace/visits | List visits |
| POST | /api/marketplace/visits | Schedule visit |
| GET | /api/marketplace/visits/{id} | Get visit |
| PUT | /api/marketplace/visits/{id} | Update visit |
| POST | /api/marketplace/visits/{id}/confirm | Confirm visit |
| POST | /api/marketplace/visits/{id}/cancel | Cancel visit |
| POST | /api/marketplace/visits/{id}/reschedule | Reschedule |
| POST | /api/marketplace/visits/{id}/check-in | Check in |
| POST | /api/marketplace/visits/{id}/complete | Complete visit |
| POST | /api/marketplace/visits/{id}/feedback | Submit feedback |
| GET | /api/marketplace/visits/calendar | Calendar data |
| GET | /api/marketplace/visits/analytics | Visit analytics |

### UI Components

1. **Visits List** (`/marketplace/admin/visits`)
   - Table with filters
   - Quick status updates
   - Calendar toggle

2. **Calendar View**
   - Full calendar component
   - Visit cards
   - Drag to reschedule

3. **Schedule Visit Form**
   - Customer selector
   - Unit selector (multi)
   - Date/time picker
   - Duration
   - Notes

4. **Visit Detail**
   - Visit info
   - Customer details
   - Unit list
   - Status timeline
   - Actions

5. **Complete Visit Form**
   - Interest levels per unit
   - Overall rating
   - Notes
   - Follow-up scheduling

## Captured Page Analysis

- `marketplace-admin-visits` - Visit list
- `marketplace-admin-visits-details` - Visit detail
- `mp-admin-visits-dashboard` - Dashboard
- `dashboard-visits` - Dashboard widget

## Notifications

| Event | Recipients | Channel |
|-------|-----------|---------|
| Visit Scheduled | Customer | Email, SMS |
| Visit Confirmed | Agent | Push |
| Reminder 24h | Customer | Email, SMS |
| Reminder 1h | Customer | Push |
| Visit Cancelled | Customer, Agent | Email |
| Feedback Request | Customer | Email |

## Testing Requirements

1. **Unit Tests** - Scheduling logic, reminders
2. **Feature Tests** - Full visit lifecycle
3. **E2E Tests** - Schedule, conduct, feedback

## References

- Captured Pages: `docs/pages/marketplace-admin-visits*`, `docs/pages/mp-admin-visits*`
- Marketplace: PRD-013, PRD-014
