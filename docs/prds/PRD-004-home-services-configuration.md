# PRD-004: Home Services Configuration

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M3 - Service Operations |
| **Estimated Effort** | 1-2 weeks |
| **Dependencies** | PRD-003 (Service Request Settings) |
| **Related Pages** | settings-home-service-*, requests-home-services |

## Problem Statement

Home services are paid services offered to tenants (cleaning, laundry, pest control, etc.). Property managers need to configure these services with pricing, availability, and service providers.

## Goals

1. Configure home service categories and types
2. Set pricing tiers and add-ons
3. Define service availability per community
4. Assign service providers/vendors
5. Enable tenants to request and pay for services

## User Stories

### US-001: Configure Home Service Category
**As a** property manager
**I want to** configure home service categories
**So that** services are organized

**Acceptance Criteria:**
- Create categories (Cleaning, Laundry, Pest Control)
- Set category icon and description
- Enable/disable per community
- Set display order

### US-002: Add Service Type
**As a** property manager
**I want to** add specific service types within a category
**So that** tenants can choose the right service

**Acceptance Criteria:**
- Add service type (e.g., "Deep Cleaning", "Regular Cleaning")
- Set base price and pricing model
- Configure duration estimate
- Add service description
- Upload service image

### US-003: Configure Pricing
**As a** property manager
**I want to** set flexible pricing
**So that** services are priced appropriately

**Acceptance Criteria:**
- Set pricing model (flat rate, hourly, per unit)
- Configure price tiers by unit size
- Add optional add-ons with prices
- Set minimum order value
- Configure discounts

### US-004: Set Availability
**As a** property manager
**I want to** set service availability
**So that** tenants know when to book

**Acceptance Criteria:**
- Set available days of week
- Set available time slots
- Configure lead time (advance booking)
- Set blackout dates
- Limit bookings per slot

### US-005: Assign Service Provider
**As a** property manager
**I want to** assign vendors to services
**So that** requests are routed correctly

**Acceptance Criteria:**
- Link service professional contacts
- Set primary and backup providers
- Configure auto-assignment rules
- Set provider availability

## Technical Requirements

### Database Schema

```sql
-- home_service_categories table
CREATE TABLE home_service_categories (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    name VARCHAR(100),
    name_ar VARCHAR(100),
    description TEXT,
    icon VARCHAR(50),
    image_url VARCHAR(500),
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- home_service_types table
CREATE TABLE home_service_types (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    category_id BIGINT REFERENCES home_service_categories(id),
    name VARCHAR(100),
    name_ar VARCHAR(100),
    description TEXT,
    image_url VARCHAR(500),

    -- Pricing
    pricing_model ENUM('flat', 'hourly', 'per_sqm', 'tiered'),
    base_price DECIMAL(10,2),
    hourly_rate DECIMAL(10,2),
    minimum_charge DECIMAL(10,2),

    -- Duration
    estimated_duration_minutes INT,

    -- Provider
    default_provider_id BIGINT REFERENCES contacts(id),

    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- home_service_addons table
CREATE TABLE home_service_addons (
    id BIGINT PRIMARY KEY,
    service_type_id BIGINT REFERENCES home_service_types(id),
    name VARCHAR(100),
    name_ar VARCHAR(100),
    price DECIMAL(10,2),
    is_active BOOLEAN DEFAULT true
);

-- home_service_availability table
CREATE TABLE home_service_availability (
    id BIGINT PRIMARY KEY,
    service_type_id BIGINT REFERENCES home_service_types(id),
    community_id BIGINT REFERENCES communities(id),
    day_of_week INT, -- 0-6
    start_time TIME,
    end_time TIME,
    max_bookings_per_slot INT DEFAULT 5,
    lead_time_hours INT DEFAULT 24,
    is_active BOOLEAN DEFAULT true
);

-- community_home_services (enable/disable per community)
CREATE TABLE community_home_services (
    id BIGINT PRIMARY KEY,
    community_id BIGINT REFERENCES communities(id),
    category_id BIGINT REFERENCES home_service_categories(id),
    is_enabled BOOLEAN DEFAULT true
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/home-services/categories | List categories |
| POST | /api/settings/home-services/categories | Create category |
| PUT | /api/settings/home-services/categories/{id} | Update category |
| GET | /api/settings/home-services/categories/{id}/types | List service types |
| POST | /api/settings/home-services/types | Create service type |
| PUT | /api/settings/home-services/types/{id} | Update service type |
| GET | /api/settings/home-services/types/{id}/addons | List add-ons |
| POST | /api/settings/home-services/types/{id}/addons | Add add-on |
| PUT | /api/settings/home-services/types/{id}/availability | Set availability |
| PUT | /api/communities/{id}/home-services | Enable/disable services |

### UI Components

1. **Home Services Settings Main** (`/settings/home-service`)
   - Category cards
   - Enable/disable toggles
   - Add category button

2. **Category Detail** (`/settings/home-service/{category-id}`)
   - Service types list
   - Add service type
   - Community enablement

3. **Service Type Form** (`/settings/home-service/add-subcategory`)
   - Name and description
   - Pricing configuration
   - Add-ons section
   - Provider assignment

4. **Availability Settings**
   - Weekly schedule grid
   - Time slot configuration
   - Blackout dates calendar

## Captured Page Analysis

- `settings-home-service` - Main settings page
- `settings-home-service-category` - Category management
- `settings-home-service-details` - Service type details
- `settings-home-service-add-subcategory` - Add service form
- `settings-home-service-flow` - Service workflow configuration

## Validation Rules

| Field | Rules |
|-------|-------|
| category.name | Required, max:100, unique per tenant |
| service_type.name | Required, max:100 |
| service_type.base_price | Required, min:0 |
| addon.price | Required, min:0 |
| availability.start_time | Required, valid time |
| availability.end_time | Required, > start_time |

## Testing Requirements

1. **Unit Tests** - Pricing calculations, availability checks
2. **Feature Tests** - CRUD operations, community enablement
3. **E2E Tests** - Configure service and book as tenant

## References

- Captured Pages: `docs/pages/settings-home-service-*`
- Service Requests: PRD-003
