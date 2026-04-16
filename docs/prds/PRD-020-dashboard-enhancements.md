# PRD-020: Dashboard Enhancements

## Overview

| Field | Value |
|-------|-------|
| **Priority** | Medium |
| **Milestone** | M5 - Enhancements |
| **Estimated Effort** | 2 weeks |
| **Dependencies** | Core modules (Properties, Leasing, Financial) |
| **Related Pages** | dashboard-*, home-* |

## Problem Statement

The default dashboard needs to provide relevant, actionable information at a glance. Different user roles need different widgets and layouts.

## Goals

1. Role-based dashboard layouts
2. Customizable widget arrangement
3. Real-time data updates
4. Quick action shortcuts
5. Drill-down capabilities

## User Stories

### US-001: View Role-Specific Dashboard
**As a** user
**I want to** see a dashboard relevant to my role
**So that** I see what matters to me

**Acceptance Criteria:**
- Property Manager: Portfolio overview, tasks, alerts
- Finance: Revenue, collections, arrears
- Operations: Requests, maintenance, occupancy
- Tenant: My unit, payments, requests

### US-002: Customize Dashboard
**As a** user
**I want to** customize my dashboard
**So that** I can focus on what I need

**Acceptance Criteria:**
- Add/remove widgets
- Rearrange widgets (drag-drop)
- Resize widgets
- Save layout
- Reset to default

### US-003: View Alerts and Tasks
**As a** user
**I want to** see important alerts
**So that** I don't miss critical items

**Acceptance Criteria:**
- Overdue tasks
- Expiring leases
- Pending approvals
- System notifications
- Click to take action

### US-004: Quick Actions
**As a** user
**I want to** access common actions quickly
**So that** I'm more efficient

**Acceptance Criteria:**
- Create request
- Record payment
- Schedule visit
- Send notification
- Context-sensitive actions

### US-005: Drill-down Details
**As a** user
**I want to** drill down from widgets
**So that** I can investigate details

**Acceptance Criteria:**
- Click chart segment for details
- Click count to see list
- Maintain filter context
- Back to dashboard easily

## Technical Requirements

### Database Schema

```sql
-- dashboard_layouts table
CREATE TABLE dashboard_layouts (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id),
    user_id BIGINT REFERENCES users(id),

    -- Layout config
    widgets JSON, -- [{id, type, position, size, config}, ...]
    columns INT DEFAULT 12, -- grid columns

    is_default BOOLEAN DEFAULT false,
    for_role VARCHAR(50), -- null = user-specific, 'manager', 'finance', etc.

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- dashboard_widgets (available widgets)
CREATE TABLE dashboard_widgets (
    id BIGINT PRIMARY KEY,

    name VARCHAR(100),
    description TEXT,
    component VARCHAR(100), -- React component name

    -- Widget config
    default_size JSON, -- {cols, rows}
    min_size JSON,
    max_size JSON,

    -- Data source
    data_endpoint VARCHAR(255),
    refresh_interval INT, -- seconds

    -- Permissions
    required_permissions JSON, -- ['view:transactions', ...]
    available_for_roles JSON,

    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | /api/dashboard | Get user dashboard |
| PUT | /api/dashboard/layout | Update layout |
| POST | /api/dashboard/reset | Reset to default |
| GET | /api/dashboard/widgets | Available widgets |
| GET | /api/dashboard/widget/{type}/data | Widget data |
| GET | /api/dashboard/alerts | Active alerts |
| GET | /api/dashboard/tasks | Pending tasks |

### Widget Types

```javascript
const widgets = {
  // Portfolio widgets
  'portfolio-summary': {
    title: 'Portfolio Summary',
    data: '/api/dashboard/portfolio-summary',
    size: { cols: 4, rows: 2 },
  },
  'occupancy-chart': {
    title: 'Occupancy Rate',
    data: '/api/dashboard/occupancy',
    size: { cols: 4, rows: 3 },
    chart: 'pie',
  },
  'units-by-status': {
    title: 'Units by Status',
    data: '/api/dashboard/units-status',
    size: { cols: 4, rows: 2 },
    chart: 'bar',
  },

  // Financial widgets
  'revenue-chart': {
    title: 'Monthly Revenue',
    data: '/api/dashboard/revenue',
    size: { cols: 6, rows: 3 },
    chart: 'area',
  },
  'collections-summary': {
    title: 'Collections',
    data: '/api/dashboard/collections',
    size: { cols: 3, rows: 2 },
  },
  'arrears-summary': {
    title: 'Arrears',
    data: '/api/dashboard/arrears',
    size: { cols: 3, rows: 2 },
  },

  // Operations widgets
  'requests-summary': {
    title: 'Service Requests',
    data: '/api/dashboard/requests-summary',
    size: { cols: 4, rows: 2 },
  },
  'requests-by-status': {
    title: 'Requests by Status',
    data: '/api/dashboard/requests-status',
    size: { cols: 4, rows: 3 },
    chart: 'donut',
  },

  // Alerts and tasks
  'alerts-list': {
    title: 'Alerts',
    data: '/api/dashboard/alerts',
    size: { cols: 4, rows: 4 },
  },
  'tasks-list': {
    title: 'My Tasks',
    data: '/api/dashboard/tasks',
    size: { cols: 4, rows: 4 },
  },
  'expiring-leases': {
    title: 'Expiring Leases',
    data: '/api/dashboard/expiring-leases',
    size: { cols: 4, rows: 3 },
  },

  // Calendar
  'calendar-widget': {
    title: 'Calendar',
    data: '/api/dashboard/calendar',
    size: { cols: 4, rows: 4 },
  },

  // Quick actions
  'quick-actions': {
    title: 'Quick Actions',
    size: { cols: 4, rows: 2 },
    static: true,
  },
};
```

### Role-Based Default Layouts

```javascript
const defaultLayouts = {
  'property_manager': [
    { widget: 'portfolio-summary', position: { x: 0, y: 0 } },
    { widget: 'occupancy-chart', position: { x: 4, y: 0 } },
    { widget: 'alerts-list', position: { x: 8, y: 0 } },
    { widget: 'revenue-chart', position: { x: 0, y: 3 } },
    { widget: 'requests-summary', position: { x: 6, y: 3 } },
    { widget: 'expiring-leases', position: { x: 0, y: 6 } },
    { widget: 'tasks-list', position: { x: 4, y: 6 } },
  ],
  'finance': [
    { widget: 'revenue-chart', position: { x: 0, y: 0 } },
    { widget: 'collections-summary', position: { x: 6, y: 0 } },
    { widget: 'arrears-summary', position: { x: 9, y: 0 } },
    { widget: 'transactions-list', position: { x: 0, y: 3 } },
    { widget: 'invoices-pending', position: { x: 6, y: 3 } },
  ],
  'operations': [
    { widget: 'requests-summary', position: { x: 0, y: 0 } },
    { widget: 'requests-by-status', position: { x: 4, y: 0 } },
    { widget: 'calendar-widget', position: { x: 8, y: 0 } },
    { widget: 'tasks-list', position: { x: 0, y: 3 } },
    { widget: 'maintenance-schedule', position: { x: 4, y: 3 } },
  ],
  'tenant': [
    { widget: 'my-unit', position: { x: 0, y: 0 } },
    { widget: 'my-payments', position: { x: 4, y: 0 } },
    { widget: 'my-requests', position: { x: 8, y: 0 } },
    { widget: 'community-announcements', position: { x: 0, y: 3 } },
    { widget: 'community-offers', position: { x: 6, y: 3 } },
  ],
};
```

### UI Components

1. **Dashboard Container**
   - Grid layout
   - Drag-drop support
   - Edit mode toggle
   - Save/Reset buttons

2. **Widget Shell**
   - Header with title
   - Refresh button
   - Expand/collapse
   - Settings gear

3. **Widget Library**
   - Available widgets list
   - Preview
   - Add button

4. **Alert/Task Lists**
   - Priority indicators
   - Quick actions
   - Mark complete
   - Dismiss

## Captured Page Analysis

- `dashboard` - Main dashboard
- `dashboard-*` - Various dashboard widgets
- `home-*` - Home page variations

## Testing Requirements

1. **Unit Tests** - Widget data fetching
2. **Feature Tests** - Layout CRUD
3. **E2E Tests** - Customize dashboard, drag-drop
4. **Performance Tests** - Multiple widget loading

## References

- Captured Pages: `docs/pages/dashboard-*`, `docs/pages/home-*`
