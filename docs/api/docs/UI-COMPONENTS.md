# Atar UI Component Map

> Generated: 2026-04-12T17:40:09.959Z
> Source: React bundle analysis (pretty-js.split/signals.json)

## Overview

| Metric | Count |
|--------|-------|
| Frontend Routes | 43 |
| API Endpoints | 136 |
| API Bindings | 32 |
| Modules | 9 |

---

## Modules

### Contacts

**Base Path:** `/contacts`
**Icon:** ContactsBook2Line
**Permission:** Contacts

#### Routes (7)

| Route | Component |
|-------|-----------|
| `/contacts` | ContactsPage |
| `/contacts/:param/form` | ContactsFormPage |
| `/contacts/:param` | ContactsPage |
| `/contacts/:param/form` | ContactsFormPage |
| `/contacts/:param` | ContactsPage |
| `/contacts/:param` | ContactsPage |
| `/contacts/:param` | ContactsPage |

#### API Endpoints (8)

- `/api-management/notifications?per_page=${t}&page=${e}`
- `/api-management/notifications/mark-all-as-read`
- `/api-management/notifications/unread-count`
- `/api-management/rf/common-lists`
- `/api-management/rf/requests/change-status/canceled`
- `/api-management/rf/users/requests/professionals?rf_request_id=${e}`
- `/api-management/new/complaints/${e}/assign`
- `/api-management/new/complaints/${e}/cancel`

---

### Dashboard

**Base Path:** `/dashboard`
**Icon:** DashboardLine
**Permission:** Dashboard

#### Routes (13)

| Route | Component |
|-------|-----------|
| `/dashboard` | DashboardPage |
| `/dashboard/booking-contracts` | DashboardBookingcontractsPage |
| `/dashboard/directory` | DashboardDirectoryPage |
| `/dashboard/offers` | DashboardOffersPage |
| `/dashboard/bookings` | DashboardBookingsPage |
| `/dashboard/issues` | DashboardIssuesPage |
| `/dashboard/power-bi-reports` | DashboardPowerbireportsPage |
| `/dashboard/reports` | DashboardReportsPage |
| `/dashboard/suggestions` | DashboardSuggestionsPage |
| `/dashboard/system-reports` | DashboardSystemreportsPage |
| `/dashboard/system-reports/Lease` | DashboardSystemreportsLeasePage |
| `/dashboard/system-reports/maintenance` | DashboardSystemreportsMaintenancePage |
| `/dashboard/visits` | DashboardVisitsPage |

#### API Endpoints (9)

- `/api-management/notifications/unread-count`
- `/api-management/leases/${e}`
- `/api-management/new/complaints?search=${e}&page=${t}&status`
- `/api-management/new/complaints/${e}/resolve`
- `/api-management/rf/announcements`
- `/api-management/dashboard/requires-attention`
- `/api-management/rf/announcements/${e?.id}`
- `/api-management/rf/announcements/${e}`
- `/api-management/rf/announcements/${t}`

---

### Leasing

**Base Path:** `/leasing`
**Icon:** DraftLine
**Permission:** Leases

#### Routes (5)

| Route | Component |
|-------|-----------|
| `/leasing` | LeasingPage |
| `/leasing/apps` | LeasingAppsPage |
| `/leasing/leases` | LeasingLeasesPage |
| `/leasing/quotes` | LeasingQuotesPage |
| `/leasing/visits` | LeasingVisitsPage |

#### API Endpoints (1)

- `/api-management/notifications/unread-count`

---

### Marketplace

**Base Path:** `/marketplace`
**Icon:** HomeLine
**Permission:** MarketPlaces

#### Routes (3)

| Route | Component |
|-------|-----------|
| `/marketplace` | MarketplacePage |
| `/marketplace/customers` | MarketplaceCustomersPage |
| `/marketplace/listing` | MarketplaceListingPage |

#### API Endpoints (1)

- `/api-management/notifications/unread-count`

---

### Other

**Base Path:** `/other`
**Icon:** N/A
**Permission:** N/A

#### Routes (2)

| Route | Component |
|-------|-----------|
| `/more` | MorePage |
| `/pricing` | PricingPage |

#### API Endpoints (2)

- `/api-management/notifications/unread-count`
- `/api-management/contacts/${e}/accept-privacy-policy`

---

### Properties

**Base Path:** `/properties-list`
**Icon:** CommunityLine
**Permission:** Properties

#### Routes (4)

| Route | Component |
|-------|-----------|
| `/properties-list` | PropertieslistPage |
| `/properties-list/buildings` | PropertieslistBuildingsPage |
| `/properties-list/communities` | PropertieslistCommunitiesPage |
| `/properties-list/units` | PropertieslistUnitsPage |

#### API Endpoints (1)

- `/api-management/notifications/unread-count`

---

### Requests

**Base Path:** `/requests`
**Icon:** HammerLine
**Permission:** HomeServices

#### Routes (6)

| Route | Component |
|-------|-----------|
| `/requests` | RequestsPage |
| `/requests?type=:param` | Requests?type=:paramPage |
| `/requests?type=:param` | Requests?type=:paramPage |
| `/requests/:param?type=:param` | RequestsPage |
| `/requests/create?type=:param` | RequestsCreate?type=:paramPage |
| `/requests/history?type=:param` | RequestsHistory?type=:paramPage |

#### API Endpoints (4)

- `/api-management/notifications/unread-count`
- `/api-management/rf/communities/${e}`
- `/api-management/rf/communities?is_paginate=1`
- `/api-management/rf/users/requests/sub-categories?has_types=true&category_id=`

---

### Transactions

**Base Path:** `/transactions`
**Icon:** CalculatorLine
**Permission:** Transactions

#### Routes (1)

| Route | Component |
|-------|-----------|
| `/transactions` | TransactionsPage |

#### API Endpoints (1)

- `/api-management/notifications/unread-count`

---

### Visitor Access

**Base Path:** `/visitor-access`
**Icon:** ContactsBookLine
**Permission:** VisitorAccess

#### Routes (2)

| Route | Component |
|-------|-----------|
| `/visitor-access` | VisitoraccessPage |
| `/visitor-access/visitor-details/:param` | VisitoraccessVisitordetailsPage |

#### API Endpoints (7)

- `/api-management/notifications/unread-count`
- `/api-management/marketplace/admin/visits`
- `/api-management/marketplace/admin/visits/${e}`
- `/api-management/rf/users/visitor-access`
- `/api-management/rf/users/visitor-access/${e}`
- `/api-management/rf/users/visitor-access/${e}/approve`
- `/api-management/rf/users/visitor-access/${e}/reject`

---

## Permission System

The application uses a role-based access control (RBAC) system.

### Actions

| Action | Description |
|--------|-------------|
| View | `VIEW` |
| Create | `CREATE` |
| Update | `UPDATE` |
| Delete | `DELETE` |

### Subjects (Resources)

| Subject | Description |
|---------|-------------|
| Dashboard | `Dashboard` |
| Properties | `Properties` |
| MarketPlaces | `MarketPlaces` |
| Customers | `Customers` |
| Listings | `Listings` |
| Visits | `Visits` |
| BookingAndContracts | `BookingAndContracts` |
| Quotes | `Quotes` |
| Applications | `Applications` |
| Leases | `Leases` |
| HomeServices | `HomeServices` |
| NeighbourhoodServices | `NeighbourhoodServices` |
| VisitorAccess | `VisitorAccess` |
| Bookings | `Bookings` |
| Transactions | `Transactions` |
| Offers | `Offers` |
| Directories | `Directories` |
| Suggestions | `Suggestions` |
| Tenants | `Tenants` |
| Owners | `Owners` |
| Managers | `Managers` |
| ServiceProfessionals | `ServiceProfessionals` |
| Reports | `Reports` |
| SystemReports | `SystemReports` |
| PowerBiReports | `PowerBiReports` |

## Feature Flags

The following feature flags control functionality:

| Flag | Purpose |
|------|---------|
| `ENABLE_REQUESTS` | Controls requests module visibility |
| `ENABLE_BOOKING_REQUESTS` | Controls booking_requests module visibility |
| `ENABLE_OFFERS` | Controls offers module visibility |
| `ENABLE_DIRECTORY` | Controls directory module visibility |
| `ENABLE_SUGGESTION` | Controls suggestion module visibility |
| `ENABLE_TENANTS` | Controls tenants module visibility |
| `ENABLE_OWNERS` | Controls owners module visibility |
| `ENABLE_MANGERS` | Controls mangers module visibility |
| `ENABLE_PROFESSIONALS` | Controls professionals module visibility |

## API Bindings

The following variables are bound to API endpoints:

| Variable | Method | Endpoint |
|----------|--------|----------|
| `_$` | POST | `/api-management/rf/${t}` |
| `b$` | POST | `/api-management/rf/${e}/attach/property/${t}` |
| `C$` | POST | `/api-management/rf/companies/change-status/${t}` |
| `c3` | POST | `/api-management/rf/requests/categories/change-status/${e}` |
| `d3` | GET | `/api-management/rf/requests/categories` |
| `DU` | PUT | `/api-management/request-category` |
| `e` | POST | `/api-management/rf/requests/change-status/canceled` |
| `E6` | PUT | `/api-management/rf/facilities/${t}` |
| `EU` | POST | `/api-management/request-category` |
| `GJ` | POST | `/api-management/marketplace/admin/settings/sales/store` |
| `h3` | POST | `/api-management/rf/requests/service-settings/updateOrCreate` |
| `i3` | GET | `/api-management/rf/requests/types/${e}` |
| `J` | GET | `/api-management/marketplace/admin/settings/visits` |
| `j6` | POST | `/api-management/rf/facilities` |
| `k$` | GET | `/api-management/rf/users/rates/${e}` |
| `KJ` | GET | `/api-management/marketplace/admin/settings/sales` |
| `L$` | GET | `/api-management/rf/attach/community/${e}?is_paginate=1&query=${n}&page=${t}` |
| `l3` | PUT | `/api-management/rf/requests/types/${e}` |
| `M$` | GET | `/api-management/rf/${e}/${t}` |
| `o3` | POST | `/api-management/rf/requests/sub-categories/change-status/${e}` |

*... and 12 more bindings*

## API Endpoints by Domain

### RF (88 endpoints)

- `/api-management/rf/leads/${e}`
- `/api-management/rf/leases`
- `/api-management/rf/admins`
- `/api-management/rf/communities/${e}`
- `/api-management/rf/leads`
- `/api-management/rf/leases/${e}`
- `/api-management/rf/requests/categories`
- `/api-management/rf/${t}/${e?.id}`
- `/api-management/rf/announcements`
- `/api-management/rf/communities?is_paginate=1`
- `/api-management/rf/companies/${e}`
- `/api-management/rf/facilities/${e}`
- `/api-management/rf/requests/types/${e}`
- `/api-management/rf/sub-leases/${e}`
- `/api-management/rf/${e}`
- *... and 73 more*

### MARKETPLACE (29 endpoints)

- `/api-management/marketplace/admin/visits`
- `/api-management/marketplace/admin/bookings/change-status/send-contract/${e}`
- `/api-management/marketplace/admin/communities?is_paginate=1&is_market_place=0`
- `/api-management/marketplace/admin/communities?is_paginate=1&is_market_place=0&is_off_plan_sale=0`
- `/api-management/marketplace/admin/communities?is_paginate=1&is_market_place=1`
- `/api-management/marketplace/admin/communities/list/${e}`
- `/api-management/marketplace/admin/communities/resend/bulk-payments/${e}`
- `/api-management/marketplace/admin/communities/resend/bulk-reminder/${e}`
- `/api-management/marketplace/admin/communities/resend/payment-schedules/failed/${e}`
- `/api-management/marketplace/admin/communities/unlist/${e}`
- `/api-management/marketplace/admin/communities/update-sales-information/${e}`
- `/api-management/marketplace/admin/settings/banks`
- `/api-management/marketplace/admin/settings/banks/store`
- `/api-management/marketplace/admin/settings/sales`
- `/api-management/marketplace/admin/settings/sales/store`
- *... and 14 more*

### DASHBOARD (2 endpoints)

- `/api-management/dashboard/require-attentions/expiringLeases?type=${e}&page=${t}`
- `/api-management/dashboard/requires-attention`

### CONTACTS (2 endpoints)

- `/api-management/contacts?role=${t}&search=${e}&sort_dir=latest&page=${n}&active=1`
- `/api-management/contacts/${e}/accept-privacy-policy`

### NOTIFICATIONS (4 endpoints)

- `/api-management/notifications?per_page=${t}&page=${e}`
- `/api-management/notifications/${e}/mark-as-read`
- `/api-management/notifications/mark-all-as-read`
- `/api-management/notifications/unread-count`

### INTEGRATIONS (2 endpoints)

- `/api-management/integrations/powerbi/reports?powerbi_report_type_id=${e}`
- `/api-management/integrations/powerbi/types`

### OTHER (9 endpoints)

- `/api-management/request-category`
- `/api-management/countries`
- `/api-management/leases/${e}`
- `/api-management/new/complaints?search=${e}&page=${t}&status`
- `/api-management/new/complaints/${e}/assign`
- `/api-management/new/complaints/${e}/cancel`
- `/api-management/new/complaints/${e}/resolve`
- `/api-management/request-category/${e}`
- `/api-management/request-sub-category/${e}`

## Navigation Structure

```
/
├── dashboard/
│   ├── visits
│   ├── booking-contracts
│   ├── bookings
│   ├── offers
│   ├── directory
│   ├── suggestions
│   ├── reports
│   ├── system-reports
│   └── power-bi-reports
├── properties-list/
│   ├── communities
│   ├── buildings
│   └── units
├── marketplace/
│   ├── customers
│   └── listing
├── leasing/
│   ├── visits
│   ├── apps
│   ├── quotes
│   └── leases
├── requests/
│   └── ?type={homeServices|neighbourhoodServices}
├── visitor-access/
│   └── visitor-details/:id
├── transactions/
├── contacts/
│   ├── :type (Tenant|Owner|Manager|ServiceProfessional)
│   └── :type/form
└── more/
```

## Usage Notes

1. **Permissions**: All routes check `ability.can(Action, Subject)` before rendering
2. **Feature Flags**: Module visibility depends on `planFeatures` configuration
3. **Module IDs**: Some features depend on `isModuleEnabled(MODULE_ID)`
4. **Locale**: All text uses i18n keys like `sidebar.dashboard`

