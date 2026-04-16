# PRD-005: Neighbourhood Services

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M3 - Service Operations |
| **Estimated Effort** | 1 week |
| **Dependencies** | PRD-003, PRD-004 |
| **Related Pages** | settings-neighbourhood-service-*, requests-neighbourhood-services |

## Problem Statement

Neighbourhood services are community-wide services (landscaping, security patrol, common area cleaning) that benefit all residents. These differ from unit-specific services and require different configuration.

## Goals

1. Configure neighbourhood service categories
2. Set up service schedules (recurring)
3. Track service completion
4. Enable resident reporting of issues
5. Manage service providers

## User Stories

### US-001: Configure Neighbourhood Service
**As a** property manager
**I want to** configure neighbourhood services
**So that** community areas are maintained

**Acceptance Criteria:**
- Create service categories
- Define service areas/zones
- Set service frequency (daily, weekly, monthly)
- Assign service providers
- Set quality checkpoints

### US-002: Schedule Services
**As a** property manager
**I want to** schedule recurring services
**So that** maintenance is consistent

**Acceptance Criteria:**
- Set recurring schedule
- Assign to specific days/times
- Configure notifications
- Track schedule compliance

### US-003: Report Issue
**As a** resident
**I want to** report neighbourhood issues
**So that** problems are addressed

**Acceptance Criteria:**
- Select issue category
- Describe problem
- Add photos
- Pin location on map
- Track resolution status

### US-004: Track Completion
**As a** property manager
**I want to** track service completion
**So that** quality is maintained

**Acceptance Criteria:**
- Mark services complete
- Add completion photos
- Rate service quality
- Track provider performance

## Technical Requirements

### Database Schema

```sql
-- neighbourhood_service_categories table
CREATE TABLE neighbourhood_service_categories (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    name VARCHAR(100),
    name_ar VARCHAR(100),
    description TEXT,
    icon VARCHAR(50),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- neighbourhood_services table
CREATE TABLE neighbourhood_services (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    category_id BIGINT REFERENCES neighbourhood_service_categories(id),
    community_id BIGINT REFERENCES communities(id),
    name VARCHAR(255),
    description TEXT,

    -- Schedule
    frequency ENUM('daily', 'weekly', 'biweekly', 'monthly'),
    schedule_days JSON, -- [1,3,5] for Mon, Wed, Fri
    schedule_time TIME,

    -- Provider
    provider_id BIGINT REFERENCES contacts(id),

    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- neighbourhood_service_logs table
CREATE TABLE neighbourhood_service_logs (
    id BIGINT PRIMARY KEY,
    service_id BIGINT REFERENCES neighbourhood_services(id),
    scheduled_date DATE,
    completed_at TIMESTAMP,
    completed_by BIGINT REFERENCES users(id),
    status ENUM('scheduled', 'completed', 'missed', 'rescheduled'),
    notes TEXT,
    photos JSON,
    quality_rating INT,
    created_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/settings/neighbourhood-services/categories | List categories |
| POST | /api/settings/neighbourhood-services/categories | Create category |
| GET | /api/neighbourhood-services | List services |
| POST | /api/neighbourhood-services | Create service |
| PUT | /api/neighbourhood-services/{id} | Update service |
| POST | /api/neighbourhood-services/{id}/complete | Mark complete |
| GET | /api/neighbourhood-services/{id}/logs | Get service logs |
| POST | /api/neighbourhood-issues | Report issue |

### UI Components

1. **Neighbourhood Services Settings** (`/settings/neighbourhood-service`)
2. **Service Schedule Calendar** - Visual schedule view
3. **Issue Reporting Form** - For residents
4. **Service Completion Form** - For providers

## Captured Page Analysis

- `settings-neighbourhood-service` - Main settings
- `settings-neighbourhood-service-flow` - Service workflow
- `requests-neighbourhood-services` - Issue list

## Testing Requirements

1. **Unit Tests** - Schedule generation
2. **Feature Tests** - Service lifecycle
3. **E2E Tests** - Report and resolve issue

## References

- Captured Pages: `docs/pages/settings-neighbourhood-*`, `docs/pages/requests-neighbourhood-*`
