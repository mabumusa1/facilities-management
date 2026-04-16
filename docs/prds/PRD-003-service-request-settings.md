# PRD-003: Service Request Settings

## Overview

| Field | Value |
|-------|-------|
| **Priority** | High |
| **Milestone** | M3 - Service Operations |
| **Estimated Effort** | 2 weeks |
| **Dependencies** | None |
| **Related Pages** | settings-service-request, settings-home-service-*, settings-unit-services-* |

## Problem Statement

Property managers need to configure service request categories, subcategories, and workflows before tenants can submit requests. The system needs flexible configuration to support different service types: Unit Services, Common Area Services, Home Services, and Neighbourhood Services.

## Goals

1. Allow managers to configure service categories and subcategories
2. Set up service workflows with status transitions
3. Configure SLA (response time, resolution time)
4. Assign default handlers for each service type
5. Enable/disable service types per community

## User Stories

### US-001: Manage Service Categories
**As a** property manager
**I want to** configure service categories
**So that** tenants can categorize their requests

**Acceptance Criteria:**
- Create service categories (Maintenance, Cleaning, etc.)
- Set category icon and color
- Add Arabic translation
- Activate/deactivate categories
- Reorder categories

### US-002: Manage Subcategories
**As a** property manager
**I want to** configure subcategories within each category
**So that** requests can be more specific

**Acceptance Criteria:**
- Create subcategories under a category
- Set expected response time (SLA)
- Set expected resolution time
- Assign default handler/team
- Add description and instructions
- Activate/deactivate subcategories

### US-003: Configure Service Types
**As a** property manager
**I want to** configure different service types
**So that** I can offer various services to tenants

**Service Types:**
1. **Unit Services** - Services specific to a unit (plumbing, electrical)
2. **Common Area Services** - Services for shared spaces
3. **Home Services** - Paid services (cleaning, laundry)
4. **Neighbourhood Services** - Community-wide services

**Acceptance Criteria:**
- Enable/disable each service type
- Configure categories per service type
- Set billing options for paid services
- Configure availability per community

### US-004: Configure Home Services Pricing
**As a** property manager
**I want to** set pricing for home services
**So that** tenants know the cost upfront

**Acceptance Criteria:**
- Set base price per service
- Add pricing tiers (hourly, flat rate)
- Configure materials/add-ons with prices
- Set minimum order value
- Configure payment methods
- Set service hours availability

### US-005: Service Workflow Configuration
**As a** property manager
**I want to** configure the service request workflow
**So that** requests follow the correct process

**Acceptance Criteria:**
- Define status options (New, Assigned, In Progress, Completed)
- Configure required fields per status
- Set notification triggers
- Configure auto-assignment rules
- Set escalation rules

## Technical Requirements

### Database Schema

```sql
-- service_request_categories (exists, enhance)
ALTER TABLE service_request_categories ADD COLUMN (
    service_type ENUM('unit', 'common_area', 'home', 'neighbourhood'),
    icon VARCHAR(50),
    color VARCHAR(7),
    display_order INT DEFAULT 0,
    is_paid BOOLEAN DEFAULT false
);

-- service_request_subcategories (exists, enhance)
ALTER TABLE service_request_subcategories ADD COLUMN (
    response_time_hours INT DEFAULT 24,
    resolution_time_hours INT DEFAULT 72,
    default_assignee_id BIGINT REFERENCES users(id),
    default_team_id BIGINT,
    instructions TEXT,
    requires_approval BOOLEAN DEFAULT false
);

-- home_service_pricing table
CREATE TABLE home_service_pricing (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    subcategory_id BIGINT REFERENCES service_request_subcategories(id),

    pricing_type ENUM('hourly', 'flat', 'per_unit') DEFAULT 'flat',
    base_price DECIMAL(10,2),
    hourly_rate DECIMAL(10,2),
    minimum_hours DECIMAL(4,2),
    minimum_order DECIMAL(10,2),

    -- Add-ons
    addons JSON, -- [{name, price}]

    -- Availability
    available_days JSON, -- [1,2,3,4,5] for Mon-Fri
    available_from TIME,
    available_to TIME,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- service_type_settings per community
CREATE TABLE community_service_settings (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    community_id BIGINT REFERENCES communities(id),

    unit_services_enabled BOOLEAN DEFAULT true,
    common_area_enabled BOOLEAN DEFAULT true,
    home_services_enabled BOOLEAN DEFAULT false,
    neighbourhood_enabled BOOLEAN DEFAULT false,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### API Endpoints

**Categories:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/services/categories | List categories |
| POST | /api/settings/services/categories | Create category |
| PUT | /api/settings/services/categories/{id} | Update category |
| DELETE | /api/settings/services/categories/{id} | Delete category |
| POST | /api/settings/services/categories/reorder | Reorder categories |

**Subcategories:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/services/categories/{id}/subcategories | List subcategories |
| POST | /api/settings/services/categories/{id}/subcategories | Create subcategory |
| PUT | /api/settings/services/subcategories/{id} | Update subcategory |
| DELETE | /api/settings/services/subcategories/{id} | Delete subcategory |

**Home Services:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/home-services | Get home service settings |
| PUT | /api/settings/home-services/{id}/pricing | Update pricing |
| PUT | /api/settings/home-services/{id}/availability | Update availability |

**Community Settings:**
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/communities/{id}/services | Get community service settings |
| PUT | /api/settings/communities/{id}/services | Update settings |

### UI Components

1. **Services Main Page** (`/settings/services`)
   - Tabs for each service type
   - Overview of configured categories
   - Quick enable/disable toggles

2. **Categories List** (`/settings/{service-type}/categories`)
   - Draggable category cards
   - Add category button
   - Edit/delete actions

3. **Subcategory Management** (`/settings/{service-type}/{category-id}`)
   - List of subcategories
   - SLA configuration
   - Assignee selection
   - Add subcategory form

4. **Home Service Details** (`/settings/home-service-settings/{id}`)
   - Pricing configuration
   - Add-ons management
   - Availability calendar
   - Preview of customer view

5. **Community Service Settings**
   - Toggle service types per community
   - Bulk enable/disable

## Captured Page Analysis

### settings-service-request (Main)
- Service type tabs
- Category list
- Enable/disable toggles

### settings-home-service-category (Category)
- Subcategory list
- Add subcategory button
- Reorder handles

### settings-home-service-details (Subcategory Detail)
- Name and description
- SLA settings
- Pricing configuration
- Availability times

### settings-unit-services-category (Unit Services)
- Category with subcategories
- Response time settings
- Assignee dropdown

## Validation Rules

| Field | Rules |
|-------|-------|
| category.name | Required, max:100, unique per tenant+type |
| subcategory.name | Required, max:100, unique per category |
| subcategory.response_time_hours | Required, min:1 |
| subcategory.resolution_time_hours | Required, >= response_time |
| pricing.base_price | Required if is_paid, min:0 |

## Testing Requirements

1. **Unit Tests**
   - Category CRUD operations
   - Subcategory validation
   - Pricing calculations

2. **Feature Tests**
   - Full category/subcategory lifecycle
   - Community service settings
   - SLA configuration

3. **E2E Tests**
   - Configure service and create request
   - Pricing display in tenant view

## References

- Captured Pages: `docs/pages/settings-service-*`, `docs/pages/settings-home-service-*`
- API Spec: `docs/api/queries/settings/services/`
