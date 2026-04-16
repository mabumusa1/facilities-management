# PRD-009: Facilities Booking

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M5 - Visitor & Facilities |
| **Estimated Effort** | 2-3 weeks |
| **Dependencies** | Communities, Units, Contacts |
| **Related Pages** | facilities-booking, dashboard-bookings-*, settings-facilities-* |

## Problem Statement

Communities often have shared facilities (gym, pool, meeting rooms, BBQ areas) that need to be managed. Tenants should be able to book these facilities, and managers need to track usage, set rules, and handle conflicts.

## Goals

1. Define and configure community facilities
2. Allow tenants to book facilities
3. Support time-slot based booking
4. Set booking rules (max duration, advance booking, etc.)
5. Handle booking approvals where needed
6. Track facility usage analytics

## User Stories

### US-001: Configure Facilities
**As a** property manager
**I want to** configure community facilities
**So that** tenants know what's available

**Acceptance Criteria:**
- Add facility with name and description
- Set facility category (gym, pool, hall, etc.)
- Add photos and amenities
- Set location within community
- Configure capacity
- Set operating hours
- Enable/disable facility

### US-002: Set Booking Rules
**As a** property manager
**I want to** configure booking rules
**So that** usage is fair and manageable

**Acceptance Criteria:**
- Set bookable time slots
- Define minimum/maximum duration
- Set advance booking limit (days)
- Configure booking frequency limits
- Set cancellation policy
- Require approval toggle
- Set pricing (if applicable)

### US-003: Book Facility
**As a** tenant
**I want to** book a community facility
**So that** I can use it at my preferred time

**Acceptance Criteria:**
- View available facilities
- See availability calendar
- Select date and time slot
- Specify number of guests
- Add special requests
- Confirm booking
- Receive confirmation

### US-004: Manage Bookings
**As a** property manager
**I want to** manage facility bookings
**So that** I can handle approvals and conflicts

**Acceptance Criteria:**
- View all bookings calendar
- Approve/reject pending bookings
- Cancel bookings when needed
- Handle double-booking conflicts
- Send notifications
- Check-in tenants

### US-005: Booking History
**As a** tenant or manager
**I want to** view booking history
**So that** I can track past usage

**Acceptance Criteria:**
- List of past bookings
- Filter by facility, date
- View booking details
- See cancellation history

### US-006: Facility Analytics
**As a** property manager
**I want to** see facility usage analytics
**So that** I can optimize operations

**Acceptance Criteria:**
- Usage by facility
- Peak hours analysis
- Booking vs no-show rate
- Revenue (if paid)
- Popular facilities ranking

## Technical Requirements

### Database Schema

```sql
-- facilities table (exists, enhance)
ALTER TABLE facilities ADD COLUMN (
    category_id BIGINT REFERENCES facility_categories(id),
    photos JSON,
    amenities JSON,
    capacity INT,
    location_description TEXT,
    operating_hours JSON, -- {mon: {open: "06:00", close: "22:00"}, ...}
    is_active BOOLEAN DEFAULT true
);

-- facility_categories table
CREATE TABLE facility_categories (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    name VARCHAR(100),
    name_ar VARCHAR(100),
    icon VARCHAR(50),
    display_order INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- facility_booking_rules table
CREATE TABLE facility_booking_rules (
    id BIGINT PRIMARY KEY,
    facility_id BIGINT REFERENCES facilities(id),

    -- Time slots
    slot_duration_minutes INT DEFAULT 60,
    min_duration_minutes INT DEFAULT 30,
    max_duration_minutes INT DEFAULT 180,

    -- Booking limits
    advance_booking_days INT DEFAULT 14,
    max_bookings_per_day INT DEFAULT 1,
    max_bookings_per_week INT DEFAULT 3,

    -- Cancellation
    cancellation_hours_before INT DEFAULT 24,
    cancellation_fee DECIMAL(10,2) DEFAULT 0,

    -- Approval
    requires_approval BOOLEAN DEFAULT false,

    -- Pricing
    is_paid BOOLEAN DEFAULT false,
    price_per_slot DECIMAL(10,2),
    price_per_hour DECIMAL(10,2),

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- facility_bookings table (exists, enhance)
ALTER TABLE facility_bookings ADD COLUMN (
    start_time TIMESTAMP,
    end_time TIMESTAMP,
    guests_count INT DEFAULT 1,
    special_requests TEXT,
    status ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed', 'no_show') DEFAULT 'pending',
    checked_in_at TIMESTAMP,
    checked_out_at TIMESTAMP,
    total_price DECIMAL(10,2),
    payment_status ENUM('not_required', 'pending', 'paid', 'refunded'),
    cancellation_reason TEXT,
    cancelled_by BIGINT REFERENCES users(id),
    approved_by BIGINT REFERENCES users(id)
);

-- facility_blackout_dates table
CREATE TABLE facility_blackout_dates (
    id BIGINT PRIMARY KEY,
    facility_id BIGINT REFERENCES facilities(id),
    date DATE,
    reason VARCHAR(255),
    created_at TIMESTAMP
);
```

### API Endpoints

**Facilities:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/facilities | List facilities |
| POST | /api/facilities | Create facility |
| GET | /api/facilities/{id} | Get facility |
| PUT | /api/facilities/{id} | Update facility |
| DELETE | /api/facilities/{id} | Delete facility |
| GET | /api/facilities/{id}/availability | Get availability |
| GET | /api/facilities/{id}/rules | Get booking rules |
| PUT | /api/facilities/{id}/rules | Update rules |

**Bookings:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/facility-bookings | List bookings |
| POST | /api/facility-bookings | Create booking |
| GET | /api/facility-bookings/{id} | Get booking |
| PUT | /api/facility-bookings/{id} | Update booking |
| POST | /api/facility-bookings/{id}/approve | Approve booking |
| POST | /api/facility-bookings/{id}/reject | Reject booking |
| POST | /api/facility-bookings/{id}/cancel | Cancel booking |
| POST | /api/facility-bookings/{id}/check-in | Check in |
| POST | /api/facility-bookings/{id}/check-out | Check out |
| GET | /api/facility-bookings/my | My bookings (tenant) |

**Settings:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/facility-categories | List categories |
| POST | /api/settings/facility-categories | Create category |
| GET | /api/settings/facilities/{id}/blackout-dates | Get blackout dates |
| POST | /api/settings/facilities/{id}/blackout-dates | Add blackout date |

### UI Components

1. **Facilities List** (`/facilities-booking`)
   - Facility cards with photos
   - Category filter
   - Search
   - Book now buttons

2. **Facility Detail** (`/facilities-booking/{id}`)
   - Photo gallery
   - Description and amenities
   - Operating hours
   - Availability calendar
   - Book button

3. **Booking Form** (Modal)
   - Date picker
   - Time slot selector
   - Duration selector
   - Guest count
   - Special requests
   - Price display
   - Confirm button

4. **Bookings Calendar** (`/dashboard/bookings`)
   - Calendar view (day/week/month)
   - Booking cards
   - Filter by facility
   - Quick actions

5. **Booking Detail** (`/dashboard/bookings/{id}`)
   - Booking info
   - Tenant details
   - Status timeline
   - Action buttons

6. **My Bookings** (Tenant Portal)
   - Upcoming bookings
   - Past bookings
   - Cancel option

7. **Facility Settings** (`/settings/facilities`)
   - Facilities list
   - Add facility
   - Edit rules
   - Blackout dates

## Captured Page Analysis

### facilities-booking (Main List)
- Facility cards grid
- Category tabs
- Search bar
- Book buttons

### dashboard-bookings (Calendar)
- Calendar component
- Booking cards
- Filter sidebar

### dashboard-bookings-details (Detail)
- Booking summary
- Tenant info
- Action buttons

### settings-facilities (Settings)
- Facilities table
- Add button
- Rules link

### settings-facility-details (Facility Config)
- Facility form
- Photo upload
- Rules configuration

## State Machine

```
[Pending] --approve--> [Approved] --check_in--> [Checked In] --check_out--> [Completed]
    |                      |                                                      |
    v                      v                                                      v
[Rejected]             [Cancelled]                                           [No Show]
```

## Validation Rules

| Field | Rules |
|-------|-------|
| facility.name | Required, max:255 |
| facility.capacity | Required, min:1 |
| booking.start_time | Required, future, within operating hours |
| booking.end_time | Required, > start_time, within max duration |
| booking.guests_count | Required, <= facility capacity |

## Notifications

| Event | Recipients | Channel |
|-------|-----------|---------|
| Booking Created | Tenant, Manager | Email, Push |
| Booking Approved | Tenant | Email, Push |
| Booking Rejected | Tenant | Email, Push |
| Booking Reminder | Tenant | Push (1 hour before) |
| Booking Cancelled | Tenant, Manager | Email |

## Testing Requirements

1. **Unit Tests**
   - Availability calculation
   - Booking rule validation
   - Conflict detection

2. **Feature Tests**
   - Full booking workflow
   - Approval process
   - Cancellation with refund

3. **E2E Tests**
   - Book facility as tenant
   - Approve booking as manager
   - Check-in/check-out

## References

- Captured Pages: `docs/pages/facilities-booking`, `docs/pages/dashboard-booking-*`, `docs/pages/settings-facilities-*`
- API Spec: `docs/api/queries/facilities/`
