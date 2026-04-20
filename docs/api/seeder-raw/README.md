# Seeder Data from goatar.com API

**Date Captured:** 2026-04-20
**API Base URL:** `https://api.goatar.com/api-management`
**Tenancy API:** `https://api.goatar.com/tenancy/api`

## File Structure

```
seeder-raw/
├── en/           # English locale responses
│   ├── cities.json
│   ├── countries.json
│   ├── districts.json
│   ├── statuses.json
│   ├── common-lists.json
│   ├── modules.json
│   ├── leads-create.json
│   ├── leases-create.json
│   ├── units-create.json
│   ├── request-categories.json
│   ├── communities-create.json
│   ├── manager-roles.json
│   ├── powerbi-types.json
│   └── plans.json
└── ar/           # Arabic locale responses
    └── (same files as en/)
```

## Files Summary

| File | Records | Description |
|------|---------|-------------|
| `districts.json` | 1,049 | All Saudi districts with city_id references |
| `statuses.json` | 64 | All entity statuses (requests, leases, units, etc.) |
| `countries.json` | 45 | Countries with ISO codes, dial codes, currencies |
| `common-lists.json` | 35 | Common list items (cancellation reasons, business types) |
| `cities.json` | 26 | Saudi Arabia cities with IDs |
| `units-create.json` | 5 sections | Unit specifications, categories, types, amenities |
| `manager-roles.json` | 5 | Manager role definitions with service types |
| `powerbi-types.json` | 5 | PowerBI report types |
| `request-categories.json` | 4 | Request categories with 11 sub-categories |
| `modules.json` | 4 | Feature modules (Offers, Facilities, Visitor Access, Directory) |
| `leads-create.json` | 3 sections | Lead statuses, sources, priorities |
| `communities-create.json` | 3 sections | Community amenities (26), countries, currencies |
| `leases-create.json` | 2 sections | Lease specifications (fit_out, payment_schedule, contract_type) |
| `plans.json` | varies | Subscription plans with features |

## Locale Differences

The API returns localized `name` fields based on the `X-App-Locale` header:

| Field | English (en) | Arabic (ar) |
|-------|--------------|-------------|
| City name | "Riyadh" | "الرياض" |
| Status name | "New" | "جديد" |
| Module title | "Offers" | "العروض" |
| District name | "Al Diriyah" | "الدرعية" |

**Note:** Some responses include both `name_ar` and `name_en` fields, but the `name` field changes based on locale.

## API Endpoints Used

| Endpoint | Base URL | Description |
|----------|----------|-------------|
| `GET /countries` | api-management | Countries list |
| `GET /cities/all` | tenancy | All cities |
| `GET /districts/all` | tenancy | All districts |
| `GET /rf/statuses` | api-management | All statuses |
| `GET /rf/common-lists` | api-management | Common lookup lists |
| `GET /rf/modules` | api-management | Feature modules |
| `GET /rf/leads/create` | api-management | Lead form options |
| `GET /rf/leases/create` | api-management | Lease form options |
| `GET /rf/units/create` | api-management | Unit form options |
| `GET /rf/requests/categories` | api-management | Request categories |
| `GET /rf/communities/create` | api-management | Community form options |
| `GET /rf/admins/manager-roles` | api-management | Manager roles |
| `GET /integrations/powerbi/types` | api-management | PowerBI report types |
| `GET /plans` | api-management | Subscription plans |

## Data Relationships

### Geographic Hierarchy
```
Country (countries.json)
  └── City (cities.json) [country_code = SA]
        └── District (districts.json) [city_id]
```

### Unit Structure
```
Unit Categories (units-create.json → categories)
├── Residential (id: 2)
│   └── Types: Apartment, Penthouse, Duplex Apartment, Villa, Townhouse, Land
└── Commercial (id: 3)
    └── Types: Store, F&B Outlet, Warehouse, Storage, Office, Land, Showroom, Kiosk, etc.

Unit Specifications (units-create.json → specifications)
├── unit_residential: Bedrooms, Bathrooms, Floor No, Parking, AC Type, Kitchen Type, etc.
├── unit_commercial: Floor No, Width, Length, Fit-out Status, Direction, Utilities
├── land_residential: Width, Length, Direction, Utilities
└── land_commercial: Width, Length, Direction, Utilities
```

### Lease Structure
```
Rental Contract Types (leases-create.json → rental_contract_type)
├── Yearly Rental (id: 13)
│   └── Payment Schedules: Monthly, Quarterly, Semi-Annual, Annual
├── Monthly Rental (id: 14)
│   └── Payment Schedules: Monthly Payment, Upfront Payment
└── Daily Rental (id: 15)
    └── Payment Schedules: Upfront Payment
```

### Status Groups (statuses.json)
| ID Range | Entity | Statuses |
|----------|--------|----------|
| 1-10 | Request | New, Assigned, Completed, Cancelled, Started, Accepted, Quote stages, Rejected |
| 11-17 | Visitor Access | New, Waiting, Approved, Rejected, Cancelled, Checked In, Checked Out |
| 19-22 | Facility Booking | Awaiting Approval, Booked, Rejected, Canceled |
| 23-26 | Unit | Sold, Sold & Leased, Leased, Vacant |
| 27-34 | Lease | New, Approved, Canceled, Active, Expired, Terminated, Closed |
| 35-38 | Scheduled Maintenance | Scheduled, Completed, Cancelled, Rejected |
| 39-49 | Sales Booking | Pre-Booking Created → Ownership Transferred |
| 52-58 | Offers | New, Approved, Rejected, Quote stages |
| 65-69 | Transactions | Pending, Paid, Sent, Completed |

## API Authentication

```
Authorization: Bearer {token}
X-Tenant: {tenant_id}
X-App-Locale: en|ar
```

## Usage for Laravel Seeders

1. For bilingual support, use both EN and AR JSON files
2. Create database columns: `name`, `name_ar`, `name_en`
3. Map EN response to `name_en`, AR response to `name_ar`
4. For `countries.json`, data is in `message` key (different response format)
5. Seed in dependency order:
   - `countries` → `cities` → `districts`
   - `statuses`, `common-lists` (independent)
   - `unit categories/types/specifications` from `units-create.json`
   - `lease specifications` from `leases-create.json`
   - `request categories/sub-categories` from `request-categories.json`
   - `amenities` from `communities-create.json`
   - `manager roles` from `manager-roles.json`
   - `lead statuses/sources/priorities` from `leads-create.json`
   - `modules` from `modules.json`
   - `powerbi types` from `powerbi-types.json`
   - `plans` from `plans.json`
