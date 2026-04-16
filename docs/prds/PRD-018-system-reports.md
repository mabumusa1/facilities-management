# PRD-018: System Reports

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M9 - Reporting |
| **Estimated Effort** | 3-4 weeks |
| **Dependencies** | Financial module, Properties module, Leasing module |
| **Related Pages** | reports-*, dashboard-reports-* |

## Problem Statement

Property managers need comprehensive reports for financial tracking, occupancy analysis, operational metrics, and regulatory compliance. Reports must be exportable and schedulable.

## Goals

1. Generate financial reports (income, expenses, arrears)
2. Generate property reports (occupancy, units, leases)
3. Generate operational reports (requests, maintenance)
4. Support multiple export formats
5. Schedule automated reports
6. Create custom report templates

## User Stories

### US-001: Financial Reports
**As a** finance manager
**I want to** generate financial reports
**So that** I can track revenue and expenses

**Acceptance Criteria:**
- Income statement
- Expense breakdown
- Arrears/aging report
- Collection summary
- Bank reconciliation
- VAT report

### US-002: Property Reports
**As a** property manager
**I want to** generate property reports
**So that** I can monitor portfolio health

**Acceptance Criteria:**
- Occupancy report
- Vacancy analysis
- Lease expiry report
- Unit inventory
- Community summary
- Owner statements

### US-003: Operational Reports
**As a** operations manager
**I want to** generate operational reports
**So that** I can measure service quality

**Acceptance Criteria:**
- Service request summary
- Maintenance completion rates
- Response time analysis
- Contractor performance
- Visit statistics

### US-004: Export Reports
**As a** user
**I want to** export reports in multiple formats
**So that** I can use them externally

**Acceptance Criteria:**
- PDF export
- Excel export
- CSV export
- Print-friendly view
- Email report

### US-005: Schedule Reports
**As a** manager
**I want to** schedule automated reports
**So that** I receive them regularly

**Acceptance Criteria:**
- Daily/weekly/monthly schedule
- Select recipients
- Choose format
- Set time of delivery
- Enable/disable schedules

### US-006: Custom Reports
**As a** advanced user
**I want to** create custom reports
**So that** I can analyze specific data

**Acceptance Criteria:**
- Select data source
- Choose fields/columns
- Add filters
- Set grouping/sorting
- Save as template

## Technical Requirements

### Database Schema

```sql
-- report_definitions table
CREATE TABLE report_definitions (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),

    name VARCHAR(255) NOT NULL,
    description TEXT,
    category ENUM('financial', 'property', 'operational', 'custom'),

    -- Definition
    data_source VARCHAR(100), -- table/view name
    columns JSON, -- [{field, label, type, format}, ...]
    filters JSON, -- [{field, operator, value_type}, ...]
    grouping JSON, -- [field1, field2, ...]
    sorting JSON, -- [{field, direction}, ...]
    aggregations JSON, -- [{field, function}, ...]

    -- Display
    chart_type VARCHAR(50), -- bar, line, pie, table
    chart_config JSON,

    is_system BOOLEAN DEFAULT false,
    is_public BOOLEAN DEFAULT false,

    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- report_schedules table
CREATE TABLE report_schedules (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    report_definition_id BIGINT REFERENCES report_definitions(id),

    name VARCHAR(255),
    frequency ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly'),
    day_of_week INT, -- 1-7 for weekly
    day_of_month INT, -- 1-31 for monthly
    time_of_day TIME,

    -- Filters (date ranges become relative)
    filter_values JSON,

    -- Delivery
    format ENUM('pdf', 'excel', 'csv'),
    recipients JSON, -- [{type: 'user', id}, {type: 'email', address}]

    is_active BOOLEAN DEFAULT true,
    last_run_at TIMESTAMP,
    next_run_at TIMESTAMP,

    created_by BIGINT REFERENCES users(id),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- report_executions table
CREATE TABLE report_executions (
    id BIGINT PRIMARY KEY,
    report_definition_id BIGINT REFERENCES report_definitions(id),
    schedule_id BIGINT REFERENCES report_schedules(id),

    filter_values JSON,
    format VARCHAR(20),

    status ENUM('pending', 'running', 'completed', 'failed'),
    file_path VARCHAR(500),
    file_size INT,
    row_count INT,

    error_message TEXT,

    executed_by BIGINT REFERENCES users(id),
    started_at TIMESTAMP,
    completed_at TIMESTAMP,
    created_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/reports | List report definitions |
| GET | /api/reports/{id} | Get report definition |
| POST | /api/reports | Create custom report |
| PUT | /api/reports/{id} | Update report |
| DELETE | /api/reports/{id} | Delete report |
| POST | /api/reports/{id}/execute | Generate report |
| GET | /api/reports/{id}/data | Get report data (preview) |
| GET | /api/reports/executions | List report executions |
| GET | /api/reports/executions/{id}/download | Download report |
| GET | /api/reports/schedules | List schedules |
| POST | /api/reports/schedules | Create schedule |
| PUT | /api/reports/schedules/{id} | Update schedule |
| DELETE | /api/reports/schedules/{id} | Delete schedule |

### Pre-built Reports

```php
return [
    'financial' => [
        'income_statement' => [
            'name' => 'Income Statement',
            'source' => 'transactions',
            'filters' => ['date_range', 'community', 'category'],
            'grouping' => ['category', 'month'],
        ],
        'arrears_aging' => [
            'name' => 'Arrears Aging Report',
            'source' => 'invoices',
            'filters' => ['community', 'age_bracket'],
            'columns' => ['tenant', 'unit', 'current', '30_days', '60_days', '90_plus', 'total'],
        ],
        'collection_summary' => [
            'name' => 'Collection Summary',
            'source' => 'payments',
            'filters' => ['date_range', 'community', 'payment_method'],
            'grouping' => ['community', 'month'],
        ],
    ],
    'property' => [
        'occupancy_report' => [
            'name' => 'Occupancy Report',
            'source' => 'units',
            'filters' => ['community', 'building', 'unit_type'],
            'columns' => ['community', 'occupied', 'vacant', 'occupancy_rate'],
        ],
        'lease_expiry' => [
            'name' => 'Lease Expiry Report',
            'source' => 'leases',
            'filters' => ['expiry_range', 'community'],
            'columns' => ['tenant', 'unit', 'expiry_date', 'days_remaining'],
        ],
    ],
    'operational' => [
        'request_summary' => [
            'name' => 'Service Request Summary',
            'source' => 'requests',
            'filters' => ['date_range', 'community', 'category', 'status'],
            'grouping' => ['category', 'status'],
        ],
    ],
];
```

### UI Components

1. **Reports Dashboard** (`/reports`)
   - Report categories
   - Quick access to common reports
   - Recent executions

2. **Report Viewer**
   - Interactive filters
   - Data table
   - Charts/visualizations
   - Export buttons

3. **Report Builder** (`/reports/builder`)
   - Data source selector
   - Column picker
   - Filter builder
   - Preview pane

4. **Schedule Manager** (`/reports/schedules`)
   - Schedule list
   - Create/edit schedule
   - Run history

## Captured Page Analysis

- `reports-financial` - Financial reports
- `reports-properties` - Property reports
- `reports-operations` - Operational reports
- `dashboard-reports` - Reports widget

## Testing Requirements

1. **Unit Tests** - Report generation, calculations
2. **Feature Tests** - Report CRUD, scheduling
3. **E2E Tests** - Generate report, export, schedule
4. **Performance Tests** - Large dataset reports

## References

- Captured Pages: `docs/pages/reports-*`, `docs/pages/dashboard-reports-*`
