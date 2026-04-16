# PRD-013: Marketplace Core Module

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M7 - Marketplace |
| **Estimated Effort** | 4-6 weeks |
| **Dependencies** | Units, Contacts |
| **Related Pages** | marketplace-*, mp-admin-* (20+ pages) |

## Problem Statement

Property managers need a marketplace to list available units for rent, manage customer inquiries, track property visits, and facilitate the pre-leasing process. The marketplace serves as the front-end for lead generation and unit marketing.

## Goals

1. List available units on a public marketplace
2. Manage customer leads and inquiries
3. Schedule and track property visits
4. Track customer journey from inquiry to lease
5. Provide analytics on listing performance
6. Support off-plan unit listings

## User Stories

### US-001: List Unit on Marketplace
**As a** property manager
**I want to** list a unit on the marketplace
**So that** potential tenants can discover it

**Acceptance Criteria:**
- Select unit from available units
- Add listing title and description
- Upload photos and virtual tour links
- Set asking rent and terms
- Choose visibility (public/private)
- Set featured status
- Publish or save as draft

### US-002: Manage Listings
**As a** property manager
**I want to** manage my marketplace listings
**So that** I can keep them updated

**Acceptance Criteria:**
- View all listings with status
- Edit listing details
- Pause/unpublish listings
- Mark as rented when leased
- Bulk actions for multiple listings
- View listing analytics

### US-003: Capture Customer Leads
**As a** marketplace system
**I want to** capture customer inquiries
**So that** property managers can follow up

**Acceptance Criteria:**
- Inquiry form on listing page
- Capture name, email, phone
- Associate inquiry with listing
- Assign to property manager
- Send auto-response to customer
- Create lead in CRM

### US-004: Schedule Property Visit
**As a** property manager
**I want to** schedule visits for interested customers
**So that** they can view units in person

**Acceptance Criteria:**
- Schedule from customer profile
- Select unit and time slot
- Send confirmation to customer
- Add to calendar
- Track visit status (scheduled, completed, no-show)
- Collect feedback after visit

### US-005: Track Customer Journey
**As a** property manager
**I want to** track customer journey
**So that** I can convert leads to tenants

**Acceptance Criteria:**
- Customer timeline showing all interactions
- Track: inquiry, visit, application, lease
- Add notes and follow-ups
- Set reminders for actions
- Mark conversion status

### US-006: Upload Bulk Leads
**As a** property manager
**I want to** upload leads from external sources
**So that** I can manage all prospects in one place

**Acceptance Criteria:**
- Upload CSV file with leads
- Map columns to fields
- Validate data before import
- Show errors and allow correction
- Import valid leads
- De-duplicate existing contacts

### US-007: Marketplace Analytics
**As a** property manager
**I want to** see marketplace performance
**So that** I can optimize my listings

**Acceptance Criteria:**
- Views per listing
- Inquiries per listing
- Conversion rates
- Time on market
- Comparison with similar units
- Export analytics data

## Technical Requirements

### Database Schema

```sql
-- marketplace_listings table
CREATE TABLE marketplace_listings (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    unit_id BIGINT REFERENCES units(id),

    -- Listing details
    title VARCHAR(255) NOT NULL,
    description TEXT,
    asking_rent DECIMAL(10,2),
    rent_frequency ENUM('monthly', 'yearly'),
    available_from DATE,

    -- Media
    photos JSON, -- [{url, caption, order}]
    virtual_tour_url VARCHAR(500),
    floor_plan_url VARCHAR(500),

    -- Status
    status ENUM('draft', 'published', 'paused', 'rented', 'expired') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT false,
    featured_until DATE,

    -- Analytics
    view_count INT DEFAULT 0,
    inquiry_count INT DEFAULT 0,

    -- Visibility
    visibility ENUM('public', 'private', 'unlisted') DEFAULT 'public',

    published_at TIMESTAMP,
    expires_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- marketplace_customers table (leads/prospects)
CREATE TABLE marketplace_customers (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    contact_id BIGINT REFERENCES contacts(id),

    -- Source tracking
    source ENUM('marketplace', 'referral', 'walk_in', 'import', 'other'),
    source_listing_id BIGINT REFERENCES marketplace_listings(id),

    -- Status
    status ENUM('new', 'contacted', 'qualified', 'viewing', 'application', 'converted', 'lost'),
    assigned_to BIGINT REFERENCES users(id),

    -- Preferences
    preferred_budget_min DECIMAL(10,2),
    preferred_budget_max DECIMAL(10,2),
    preferred_move_in DATE,
    preferred_unit_types JSON,
    notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- marketplace_visits table
CREATE TABLE marketplace_visits (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    customer_id BIGINT REFERENCES marketplace_customers(id),
    unit_id BIGINT REFERENCES units(id),
    listing_id BIGINT REFERENCES marketplace_listings(id),

    -- Scheduling
    scheduled_at TIMESTAMP,
    duration_minutes INT DEFAULT 30,

    -- Status
    status ENUM('scheduled', 'confirmed', 'completed', 'no_show', 'cancelled'),

    -- Feedback
    customer_interest_level INT, -- 1-5
    customer_feedback TEXT,
    agent_notes TEXT,

    conducted_by BIGINT REFERENCES users(id),
    completed_at TIMESTAMP,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- marketplace_inquiries table
CREATE TABLE marketplace_inquiries (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    listing_id BIGINT REFERENCES marketplace_listings(id),
    customer_id BIGINT REFERENCES marketplace_customers(id),

    message TEXT,
    contact_preference ENUM('email', 'phone', 'whatsapp'),

    -- Response tracking
    responded BOOLEAN DEFAULT false,
    responded_at TIMESTAMP,
    responded_by BIGINT REFERENCES users(id),

    created_at TIMESTAMP
);

-- listing_views for analytics
CREATE TABLE listing_views (
    id BIGINT PRIMARY KEY,
    listing_id BIGINT REFERENCES marketplace_listings(id),
    viewer_ip VARCHAR(45),
    viewer_session VARCHAR(255),
    viewed_at TIMESTAMP,
    duration_seconds INT
);
```

### API Endpoints

**Listings:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/marketplace/listings | List all listings |
| POST | /api/marketplace/listings | Create listing |
| GET | /api/marketplace/listings/{id} | Get listing details |
| PUT | /api/marketplace/listings/{id} | Update listing |
| DELETE | /api/marketplace/listings/{id} | Delete listing |
| POST | /api/marketplace/listings/{id}/publish | Publish listing |
| POST | /api/marketplace/listings/{id}/pause | Pause listing |
| POST | /api/marketplace/listings/{id}/feature | Feature listing |

**Customers:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/marketplace/customers | List customers |
| POST | /api/marketplace/customers | Create customer |
| GET | /api/marketplace/customers/{id} | Get customer |
| PUT | /api/marketplace/customers/{id} | Update customer |
| POST | /api/marketplace/customers/{id}/assign | Assign to agent |
| POST | /api/marketplace/customers/import | Bulk import |

**Visits:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/marketplace/visits | List visits |
| POST | /api/marketplace/visits | Schedule visit |
| PUT | /api/marketplace/visits/{id} | Update visit |
| POST | /api/marketplace/visits/{id}/confirm | Confirm visit |
| POST | /api/marketplace/visits/{id}/complete | Complete visit |
| POST | /api/marketplace/visits/{id}/cancel | Cancel visit |

**Analytics:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/marketplace/analytics/overview | Dashboard stats |
| GET | /api/marketplace/analytics/listings/{id} | Listing stats |
| GET | /api/marketplace/analytics/conversion | Conversion funnel |

### UI Components

1. **Listings Dashboard** (`/marketplace`)
   - Stats cards (active listings, inquiries, visits)
   - Recent inquiries
   - Today's visits
   - Quick actions

2. **Listings List** (`/marketplace/listings`)
   - Grid or table view
   - Filters: status, community, price range
   - Sort by date, views, price
   - Bulk actions

3. **Create/Edit Listing** (`/marketplace/listings/create`)
   - Unit selector
   - Rich text description editor
   - Photo uploader with drag-drop
   - Pricing section
   - Publish settings

4. **Customers List** (`/marketplace/customers`)
   - Table with pipeline view option
   - Status badges
   - Quick actions
   - Bulk assign

5. **Customer Detail** (`/marketplace/customers/{id}`)
   - Contact info
   - Timeline of interactions
   - Scheduled visits
   - Related listings
   - Notes section

6. **Visits Calendar** (`/marketplace/visits`)
   - Calendar view with day/week/month
   - Visit cards with details
   - Drag to reschedule
   - Quick status update

7. **Lead Upload** (`/marketplace/upload-leads`)
   - File upload zone
   - Column mapping interface
   - Preview and validation
   - Error display
   - Import progress

## Captured Page Analysis

### marketplace (Dashboard)
- **URL:** `/marketplace`
- Quick stats
- Recent activity
- Pending actions

### marketplace-listing-list (Listings)
- **URL:** `/marketplace/listings`
- Grid of listing cards with photos
- Filter sidebar
- Add listing button

### marketplace-customers (Customers/Leads)
- **URL:** `/marketplace/customers`
- Pipeline view (Kanban)
- Table view toggle
- Customer cards

### marketplace-admin-visits (Visits)
- **URL:** `/marketplace/admin/visits`
- Calendar interface
- List view
- Visit cards

### marketplace-upload-leads (Bulk Import)
- **URL:** `/marketplace/upload-leads`
- File drop zone
- Column mapper
- Preview table
- Import button

## State Machines

**Listing Status:**
```
[Draft] --publish--> [Published] --pause--> [Paused]
                          |                     |
                          v                     v
                      [Rented]             [Published]
                          |
                          v
                      [Expired]
```

**Customer Status:**
```
[New] --> [Contacted] --> [Qualified] --> [Viewing] --> [Application] --> [Converted]
  |           |               |               |               |
  +-----+-----+-------+-------+-------+-------+---------------+
        |
        v
      [Lost]
```

## Validation Rules

| Field | Rules |
|-------|-------|
| listing.title | Required, max:255 |
| listing.asking_rent | Required, numeric, min:0 |
| listing.unit_id | Required, unit must be available |
| customer.email | Required, valid email, unique per tenant |
| customer.phone | Required, valid phone |
| visit.scheduled_at | Required, future date |

## Testing Requirements

1. **Unit Tests**
   - Listing creation and validation
   - Customer status transitions
   - Visit scheduling logic

2. **Feature Tests**
   - Full listing lifecycle
   - Customer journey tracking
   - Visit workflow
   - Bulk import with validation

3. **E2E Tests**
   - Create listing and receive inquiry
   - Schedule and complete visit
   - Convert customer to tenant

## References

- Captured Pages: `docs/pages/marketplace-*`, `docs/pages/mp-*`
- API Spec: `docs/api/queries/marketplace/`
- Entity Relationships: `docs/api/docs/ENTITY-RELATIONSHIPS.md`
