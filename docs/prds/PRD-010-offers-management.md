# PRD-010: Offers Management

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M6 - Communication |
| **Estimated Effort** | 1-2 weeks |
| **Dependencies** | None |
| **Related Pages** | communication-offers, dashboard-offers-*, settings-offers |

## Problem Statement

Property managers need to create and manage promotional offers for tenants (discounts, special deals from partners, community events). This improves tenant engagement and satisfaction.

## Goals

1. Create and publish offers to tenants
2. Target offers to specific communities/units
3. Track offer engagement and redemptions
4. Manage partner/vendor offers
5. Set offer validity periods

## User Stories

### US-001: Create Offer
**As a** property manager
**I want to** create promotional offers
**So that** tenants benefit from deals

**Acceptance Criteria:**
- Enter offer title and description
- Upload offer image/banner
- Set discount details (%, amount, code)
- Select target audience
- Set validity period
- Save as draft or publish

### US-002: Target Audience
**As a** property manager
**I want to** target specific tenants
**So that** offers are relevant

**Acceptance Criteria:**
- All tenants option
- Filter by community
- Filter by building
- Filter by unit type
- Custom tenant selection

### US-003: Track Engagement
**As a** property manager
**I want to** track offer performance
**So that** I know what works

**Acceptance Criteria:**
- View count
- Click/tap count
- Redemption count
- Engagement rate
- Compare offers

### US-004: Manage Partner Offers
**As a** property manager
**I want to** manage vendor partnerships
**So that** tenants get diverse offers

**Acceptance Criteria:**
- Add partner/vendor info
- Link offers to partners
- Track partner performance
- Partner contact details

### US-005: Tenant View Offers
**As a** tenant
**I want to** view available offers
**So that** I can benefit from deals

**Acceptance Criteria:**
- See active offers
- View offer details
- Copy promo codes
- Mark as redeemed
- Save favorites

## Technical Requirements

### Database Schema

```sql
-- offers table
CREATE TABLE offers (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    title VARCHAR(255) NOT NULL,
    title_ar VARCHAR(255),
    description TEXT,
    description_ar TEXT,
    image_url VARCHAR(500),

    -- Offer details
    offer_type ENUM('discount_percent', 'discount_amount', 'free_item', 'special_deal'),
    discount_value DECIMAL(10,2),
    promo_code VARCHAR(50),
    terms_conditions TEXT,

    -- Partner
    partner_id BIGINT REFERENCES offer_partners(id),
    partner_name VARCHAR(255),
    partner_contact VARCHAR(255),

    -- Targeting
    target_type ENUM('all', 'communities', 'buildings', 'units', 'custom'),
    target_ids JSON, -- Array of community/building/unit IDs

    -- Validity
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    max_redemptions INT,
    current_redemptions INT DEFAULT 0,

    -- Status
    status ENUM('draft', 'published', 'expired', 'cancelled') DEFAULT 'draft',
    published_at TIMESTAMP,

    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- offer_partners table
CREATE TABLE offer_partners (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    name VARCHAR(255),
    category VARCHAR(100),
    contact_name VARCHAR(255),
    contact_email VARCHAR(255),
    contact_phone VARCHAR(50),
    logo_url VARCHAR(500),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- offer_views table
CREATE TABLE offer_views (
    id BIGINT PRIMARY KEY,
    offer_id BIGINT REFERENCES offers(id),
    contact_id BIGINT REFERENCES contacts(id),
    viewed_at TIMESTAMP,
    clicked_at TIMESTAMP
);

-- offer_redemptions table
CREATE TABLE offer_redemptions (
    id BIGINT PRIMARY KEY,
    offer_id BIGINT REFERENCES offers(id),
    contact_id BIGINT REFERENCES contacts(id),
    redeemed_at TIMESTAMP,
    notes TEXT
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/offers | List offers |
| POST | /api/offers | Create offer |
| GET | /api/offers/{id} | Get offer |
| PUT | /api/offers/{id} | Update offer |
| DELETE | /api/offers/{id} | Delete offer |
| POST | /api/offers/{id}/publish | Publish offer |
| POST | /api/offers/{id}/cancel | Cancel offer |
| GET | /api/offers/{id}/analytics | Get analytics |
| POST | /api/offers/{id}/redeem | Record redemption |
| GET | /api/offers/partners | List partners |
| POST | /api/offers/partners | Create partner |

### UI Components

1. **Offers List** (`/offers` or `/communication/offers`)
   - Offer cards with images
   - Status filters
   - Create offer button

2. **Create/Edit Offer** (`/offers/create`)
   - Title and description
   - Image upload
   - Offer type and value
   - Target audience selector
   - Validity dates

3. **Offer Detail**
   - Offer preview
   - Analytics dashboard
   - Redemption list
   - Edit/Cancel actions

4. **Partner Management** (`/settings/offers`)
   - Partner list
   - Add partner form
   - Partner offers

5. **Tenant Offers View**
   - Available offers carousel
   - Offer detail modal
   - Redeem/Save buttons

## Captured Page Analysis

- `communication-offers` - Offer listing
- `dashboard-offers` - Dashboard widget
- `dashboard-offers-create` - Create form
- `dashboard-offers-view` - Offer detail
- `settings-offers` - Offer settings

## Validation Rules

| Field | Rules |
|-------|-------|
| title | Required, max:255 |
| start_date | Required, >= today |
| end_date | Required, > start_date |
| discount_value | Required if discount type |
| promo_code | Unique per tenant |

## Notifications

| Event | Recipients | Channel |
|-------|-----------|---------|
| Offer Published | Target tenants | Push, Email |
| Offer Expiring | Target tenants | Push |

## Testing Requirements

1. **Unit Tests** - Targeting logic, validation
2. **Feature Tests** - CRUD, publishing, analytics
3. **E2E Tests** - Create offer, tenant views, redemption

## References

- Captured Pages: `docs/pages/communication-offers`, `docs/pages/dashboard-offers-*`
