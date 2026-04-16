# PRD-019: Power BI Integration

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Low |
| **Milestone** | M9 - Reporting |
| **Estimated Effort** | 2 weeks |
| **Dependencies** | PRD-018 (Reports) |
| **Related Pages** | reports-powerbi, settings-integrations |

## Problem Statement

Enterprise clients need advanced analytics capabilities beyond built-in reports. Power BI integration allows them to connect their existing BI infrastructure to property management data.

## Goals

1. Expose data via secure API for Power BI
2. Provide pre-built Power BI templates
3. Support scheduled data refresh
4. Implement row-level security
5. Document data model

## User Stories

### US-001: Generate API Key
**As an** admin
**I want to** generate API credentials for Power BI
**So that** I can connect securely

**Acceptance Criteria:**
- Generate API key and secret
- Set expiration date
- Define scope/permissions
- Revoke credentials
- View usage logs

### US-002: Connect Power BI
**As a** data analyst
**I want to** connect Power BI to the system
**So that** I can create custom visualizations

**Acceptance Criteria:**
- Use API credentials
- Browse available datasets
- Preview data structure
- Configure refresh schedule

### US-003: Use Templates
**As a** data analyst
**I want to** use pre-built templates
**So that** I can get started quickly

**Acceptance Criteria:**
- Download .pbix template files
- Templates for common reports
- Pre-configured visualizations
- Documentation included

### US-004: Row-Level Security
**As an** admin
**I want to** data to respect permissions
**So that** users only see authorized data

**Acceptance Criteria:**
- Filter by tenant
- Filter by community permissions
- Filter by role
- Audit data access

## Technical Requirements

### Database Schema

```sql
-- api_credentials table
CREATE TABLE api_credentials (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    user_id BIGINT REFERENCES users(id),

    name VARCHAR(100),
    description TEXT,

    api_key VARCHAR(64) UNIQUE NOT NULL,
    api_secret_hash VARCHAR(255) NOT NULL,

    -- Permissions
    scopes JSON, -- ['read:properties', 'read:leases', 'read:transactions']
    ip_whitelist JSON,

    -- Validity
    expires_at TIMESTAMP,
    is_active BOOLEAN DEFAULT true,

    -- Usage tracking
    last_used_at TIMESTAMP,
    request_count INT DEFAULT 0,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- api_access_logs table
CREATE TABLE api_access_logs (
    id BIGINT PRIMARY KEY,
    credential_id BIGINT REFERENCES api_credentials(id),

    endpoint VARCHAR(255),
    method VARCHAR(10),
    ip_address VARCHAR(45),
    user_agent TEXT,

    response_code INT,
    response_time_ms INT,

    created_at TIMESTAMP
);
```

### API Endpoints (OData Compatible)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | /api/credentials | Create API credentials |
| GET | /api/credentials | List credentials |
| DELETE | /api/credentials/{id} | Revoke credentials |
| GET | /api/credentials/{id}/logs | Access logs |
| GET | /odata/communities | Communities dataset |
| GET | /odata/buildings | Buildings dataset |
| GET | /odata/units | Units dataset |
| GET | /odata/leases | Leases dataset |
| GET | /odata/transactions | Transactions dataset |
| GET | /odata/requests | Service requests dataset |
| GET | /odata/$metadata | OData metadata |

### OData Implementation

```php
// config/odata.php
return [
    'datasets' => [
        'communities' => [
            'model' => \App\Models\Community::class,
            'fields' => ['id', 'name', 'code', 'address', 'city', 'units_count', 'occupied_count'],
            'relations' => ['buildings'],
            'scopes' => ['active'],
        ],
        'units' => [
            'model' => \App\Models\Unit::class,
            'fields' => ['id', 'number', 'type', 'bedrooms', 'size', 'status', 'monthly_rent'],
            'relations' => ['building', 'community', 'currentLease'],
        ],
        'leases' => [
            'model' => \App\Models\Lease::class,
            'fields' => ['id', 'start_date', 'end_date', 'monthly_rent', 'status', 'deposit_amount'],
            'relations' => ['unit', 'tenant'],
        ],
        'transactions' => [
            'model' => \App\Models\Transaction::class,
            'fields' => ['id', 'date', 'type', 'category', 'amount', 'status'],
            'relations' => ['unit', 'lease'],
        ],
    ],
];
```

### Power BI Template Content

```yaml
# Templates to provide
templates:
  - name: "Property Overview"
    file: "property-overview.pbix"
    datasets: [communities, buildings, units]
    visualizations:
      - Occupancy by community (pie chart)
      - Units by type (bar chart)
      - Occupancy trend (line chart)
      - Property map

  - name: "Financial Dashboard"
    file: "financial-dashboard.pbix"
    datasets: [transactions, leases, units]
    visualizations:
      - Revenue by month (area chart)
      - Income vs Expenses (bar chart)
      - Arrears aging (table)
      - Collection rate (gauge)

  - name: "Operational Metrics"
    file: "operational-metrics.pbix"
    datasets: [requests, units, communities]
    visualizations:
      - Requests by category (donut)
      - Resolution time trend (line)
      - Open requests by community (bar)
      - SLA compliance (gauge)
```

### UI Components

1. **Integration Settings** (`/settings/integrations/powerbi`)
   - API credentials management
   - Create/revoke keys
   - View access logs

2. **Templates Download**
   - Template list
   - Preview images
   - Download buttons
   - Setup instructions

3. **Data Documentation**
   - Available datasets
   - Field descriptions
   - Relationships
   - Example queries

## Security Requirements

1. **Authentication**
   - API key + secret (HTTP Basic Auth)
   - Token expiration
   - IP whitelisting option

2. **Authorization**
   - Scope-based access
   - Row-level security via tenant_id
   - Rate limiting

3. **Audit**
   - Log all API access
   - Track data exports
   - Alert on anomalies

## Testing Requirements

1. **Unit Tests** - OData query parsing
2. **Feature Tests** - Credential management
3. **Integration Tests** - Power BI connectivity
4. **Security Tests** - Authentication, authorization

## References

- Captured Pages: `docs/pages/reports-powerbi`, `docs/pages/settings-integrations`
- OData Specification: https://www.odata.org/
