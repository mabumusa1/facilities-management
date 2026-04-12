# Mutation Extraction Plan

## Overview
This document tracks the progress of capturing POST, PUT, PATCH, DELETE API endpoints from the Atar property management platform.

**Goal:** Capture all mutation endpoints, extract validation rules, and generate OpenAPI documentation.

**Run Command:** `npx playwright test --project=all-mutation-agents`

---

## Progress Summary

| Module | Agent | Covered | Missing | Status |
|--------|-------|---------|---------|--------|
| Properties | ✅ | 7 | 11 | Partial |
| Contacts | ✅ | 8 | 7 | Partial |
| Leasing | ✅ | 4 | 7 | Partial |
| Marketplace | ✅ | 9 | 9 | Partial |
| Requests | ✅ | 8 | 6 | Partial |
| Transactions | ❌ | 0 | 10 | Not Started |
| Settings | ❌ | 0 | 12 | Not Started |
| Documents | ❌ | 0 | 5 | Not Started |

**Total: 36 covered / 103 identified (35%)**

---

## Module Details

### 1. Properties Module
**Agent:** `tests/agents/mutations/properties.mutation.agent.spec.ts`
**Output:** `src/api/mutations/properties/`

#### Covered Endpoints
- [x] `POST /rf/communities` - Create community
- [x] `PUT /rf/communities/{id}` - Update community
- [x] `POST /rf/buildings` - Create building
- [x] `PUT /rf/buildings/{id}` - Update building
- [x] `POST /rf/units` - Create unit (with complete map object)
- [x] `PUT /rf/units/{id}` - Update unit
- [x] `PUT /rf/units/{id}` - Update unit status

#### Missing Endpoints
- [ ] `DELETE /rf/communities/{id}` - Delete community
- [ ] `DELETE /rf/buildings/{id}` - Delete building
- [ ] `DELETE /rf/units/{id}` - Delete unit
- [ ] `POST /rf/units/bulk-update` - Bulk update units
- [ ] `POST /rf/units/bulk-delete` - Bulk delete units
- [ ] `POST /rf/units/import` - Import units via file
- [ ] `POST /rf/facilities` - Create facility
- [ ] `PUT /rf/facilities/{id}` - Update facility
- [ ] `DELETE /rf/facilities/{id}` - Delete facility
- [ ] `POST /rf/units/images` - Upload unit images
- [ ] `DELETE /rf/units/images/{id}` - Delete unit image

#### Critical Learnings
- Unit `map` object must contain ALL 8 fields: `latitude`, `longitude`, `place_id`, `districtName`, `formattedAddress`, `latitudeDelta`, `longitudeDelta`, `mapsLink`
- Partial map object causes generic 400 error
- Use flat fields like `category_id: 2` not nested `{"category": {"id": 2}}`

---

### 2. Contacts Module
**Agent:** `tests/agents/mutations/contacts.mutation.agent.spec.ts`
**Output:** `src/api/mutations/contacts/`

#### Covered Endpoints
- [x] `POST /rf/owners` - Create owner
- [x] `PUT /rf/owners/{id}` - Update owner
- [x] `POST /rf/tenants` - Create individual tenant
- [x] `POST /rf/tenants` - Create company tenant
- [x] `PUT /rf/tenants/{id}` - Update tenant
- [x] `POST /rf/admins` - Create manager/admin
- [x] `POST /rf/admins/check-validate` - Validate admin data
- [x] `POST /rf/professionals` - Create service professional

#### Missing Endpoints
- [ ] `DELETE /rf/owners/{id}` - Delete owner
- [ ] `DELETE /rf/tenants/{id}` - Delete tenant
- [ ] `DELETE /rf/admins/{id}` - Delete admin
- [ ] `DELETE /rf/professionals/{id}` - Delete professional
- [ ] `PUT /rf/professionals/{id}` - Update professional
- [ ] `POST /rf/tenants/{id}/family-members` - Add family member
- [ ] `DELETE /rf/tenants/{id}/family-members/{memberId}` - Remove family member

#### Critical Learnings
- `national_id` must be unique across owners/tenants
- Phone numbers stored WITHOUT country prefix (e.g., `"500000002"` not `"+966500000002"`)
- `phone_country_code` uses ISO country code (e.g., `"SA"`)

---

### 3. Leasing Module
**Agent:** `tests/agents/mutations/leasing.mutation.agent.spec.ts`
**Output:** `src/api/mutations/leasing/`

#### Covered Endpoints
- [x] `POST /rf/leases/create` - Create lease
- [x] `POST /rf/leases/change-status/move-out` - Process tenant move-out
- [x] `POST /rf/leases/change-status/terminate` - Terminate lease
- [x] `POST /rf/leases/renew/store` - Renew lease

#### Missing Endpoints
- [ ] `PUT /rf/leases/{id}` - Update lease details
- [ ] `DELETE /rf/leases/{id}` - Delete lease
- [ ] `POST /rf/leases/change-status/suspend` - Suspend lease
- [ ] `POST /rf/leases/change-status/reactivate` - Reactivate lease
- [ ] `POST /rf/leases/{id}/addendum` - Create lease addendum
- [ ] `POST /rf/sub-leases` - Create sub-lease
- [ ] `DELETE /rf/sub-leases/{id}` - Delete sub-lease

#### Critical Learnings
- `rental_type` MUST be `"detailed"` (NOT "yearly", "annual", or numeric)
- `rental_contract_type_id`: 13=Yearly, 14=Monthly, 15=Daily
- Payment schedules by rental type:
  - Yearly (13): 4=Monthly, 5=Quarterly, 6=Semi-Annual, 7=Annual
  - Monthly (14): 16=Monthly Payment, 17=Upfront Payment
  - Daily (15): 18=Upfront Payment
- Unit status MUST be 26 (Available) or 23 (Sold) - cannot use 25 (Rented)
- `units[].amount_type` is REQUIRED

---

### 4. Marketplace Module
**Agent:** `tests/agents/mutations/marketplace.mutation.agent.spec.ts`
**Output:** `src/api/mutations/marketplace/`

#### Covered Endpoints
- [x] `POST /marketplace/admin/settings/banks/store` - Store bank settings
- [x] `POST /marketplace/admin/settings/sales/store` - Store sales settings
- [x] `POST /marketplace/admin/settings/visits/store` - Store visit settings
- [x] `POST /marketplace/admin/communities/list/{id}` - List community
- [x] `POST /marketplace/admin/communities/unlist/{id}` - Unlist community
- [x] `POST /marketplace/admin/units/prices-visibility/{id}` - Toggle unit price visibility
- [x] `POST /marketplace/admin/visits/assign/owner-visit/{id}` - Assign visit
- [x] `POST /marketplace/admin/visits/completed/{id}` - Mark visit completed
- [x] `POST /marketplace/admin/visits/rejected/{id}` - Reject visit

#### Missing Endpoints
- [ ] `PUT /marketplace/admin/settings/banks/{id}` - Update bank setting
- [ ] `DELETE /marketplace/admin/settings/banks/{id}` - Delete bank setting
- [ ] `POST /marketplace/admin/offers` - Create offer/deal
- [ ] `PUT /marketplace/admin/offers/{id}` - Update offer
- [ ] `DELETE /marketplace/admin/offers/{id}` - Delete offer
- [ ] `POST /marketplace/admin/listings` - Create listing
- [ ] `PUT /marketplace/admin/listings/{id}` - Update listing
- [ ] `DELETE /marketplace/admin/listings/{id}` - Delete listing
- [ ] `POST /marketplace/admin/visits/cancel/{id}` - Cancel visit

#### Critical Learnings
- Bank `account_number` must be at least 14 digits
- `iban` format validation is strict

---

### 5. Requests Module
**Agent:** `tests/agents/mutations/requests.mutation.agent.spec.ts`
**Output:** `src/api/mutations/requests/`

#### Covered Endpoints
- [x] `POST /rf/requests/service-settings/updateOrCreate` - Create/update service settings
- [x] `POST /rf/requests/change-status/pending` - Change status to pending
- [x] `POST /rf/requests/change-status/in-progress` - Change status to in-progress
- [x] `POST /rf/requests/change-status/completed` - Change status to completed
- [x] `POST /rf/requests/change-status/canceled` - Change status to canceled
- [x] `POST /rf/requests/change-status/approved` - Change status to approved
- [x] `POST /rf/requests/change-status/rejected` - Change status to rejected
- [x] `POST /rf/requests` - Create new request

#### Missing Endpoints
- [ ] `PUT /rf/requests/{id}` - Update request details
- [ ] `DELETE /rf/requests/{id}` - Delete request
- [ ] `POST /rf/requests/{id}/assign` - Assign request to professional
- [ ] `POST /rf/requests/{id}/reassign` - Reassign request
- [ ] `DELETE /rf/requests/service-settings/{id}` - Delete service settings
- [ ] `POST /rf/request-categories` - Create request category

---

### 6. Transactions Module (NOT STARTED)
**Agent:** `tests/agents/mutations/transactions.mutation.agent.spec.ts` ❌
**Output:** `src/api/mutations/transactions/`

#### Endpoints to Cover
- [ ] `POST /rf/transactions` - Record transaction/payment
- [ ] `PUT /rf/transactions/{id}` - Update transaction
- [ ] `POST /rf/invoices` - Create invoice
- [ ] `PUT /rf/invoices/{id}` - Update invoice
- [ ] `POST /rf/invoices/mark-paid` - Mark invoice as paid
- [ ] `POST /rf/payments` - Record payment
- [ ] `PUT /rf/payments/{id}` - Update payment
- [ ] `POST /rf/payments/refund` - Process refund
- [ ] `POST /rf/payment-plans` - Create payment plan
- [ ] `PUT /rf/payment-plans/{id}` - Update payment plan

---

### 7. Settings Module (NOT STARTED)
**Agent:** `tests/agents/mutations/settings.mutation.agent.spec.ts` ❌
**Output:** `src/api/mutations/settings/`

#### Endpoints to Cover
- [ ] `POST /rf/settings` - Update general settings
- [ ] `PUT /rf/settings/{key}` - Update specific setting
- [ ] `PUT /notifications` - Update notification settings (captured: 200)
- [ ] `POST /rf/settings/company-info` - Update company information
- [ ] `POST /rf/settings/email-templates` - Create email template
- [ ] `PUT /rf/settings/email-templates/{id}` - Update email template
- [ ] `POST /rf/settings/sms-templates` - Create SMS template
- [ ] `PUT /rf/settings/sms-templates/{id}` - Update SMS template
- [ ] `DELETE /rf/settings/api-keys/{id}` - Delete API key
- [ ] `POST /rf/roles` - Create role
- [ ] `PUT /rf/roles/{id}` - Update role
- [ ] `DELETE /rf/roles/{id}` - Delete role

---

### 8. Documents Module (NOT STARTED)
**Agent:** `tests/agents/mutations/documents.mutation.agent.spec.ts` ❌
**Output:** `src/api/mutations/documents/`

#### Endpoints to Cover
- [ ] `POST /rf/documents` - Upload document
- [ ] `DELETE /rf/documents/{id}` - Delete document
- [ ] `POST /rf/directory` - Create directory entry
- [ ] `PUT /rf/directory/{id}` - Update directory entry
- [ ] `DELETE /rf/directory/{id}` - Delete directory entry

---

## Reference Data

### Status IDs
| ID | Name (AR) | Name (EN) |
|----|-----------|-----------|
| 23 | مباعة | Sold |
| 24 | مباعة و مؤجرة | Sold & Rented |
| 25 | مؤجرة | Rented |
| 26 | متاحة | Available (default) |
| 30 | عقد جديد | New Contract |
| 31 | عقد ساري | Active Contract |
| 32 | عقد منتهي | Expired Contract |
| 33 | عقد ملغي | Canceled Contract |
| 34 | عقد مغلق | Closed Contract |

### Unit Categories & Types
**Residential (category_id: 2):**
- 17: Apartment, 18: Penthouse, 19: Duplex Apartment
- 20: Duplex Villa, 21: Floor, 22: Villa
- 24: Townhouse, 25: Land

**Commercial (category_id: 3):**
- 26: Retail Store, 27: F&B, 28: Warehouse
- 29: Storage, 30: Office, 31: Land
- 135-140: Showroom, Kiosk, Executive Office, Shared Office, Building, Tower

---

## Run Commands

```bash
# Run all mutation agents
npx playwright test --project=all-mutation-agents

# Run specific module
npx playwright test --project=properties-mutation-agent
npx playwright test --project=contacts-mutation-agent
npx playwright test --project=leasing-mutation-agent
npx playwright test --project=marketplace-mutation-agent
npx playwright test --project=requests-mutation-agent

# View test report
npx playwright show-report
```

---

## Output Structure

```
src/api/mutations/
├── properties/
│   ├── captures.json         # Raw request/response data
│   ├── openapi.json          # OpenAPI 3.0 spec
│   ├── field-requirements.json # Validation rules
│   └── summary.json          # Statistics
├── contacts/
├── leasing/
├── marketplace/
├── requests/
├── transactions/  (TODO)
├── settings/      (TODO)
└── documents/     (TODO)
```

---

## Next Steps (Priority Order)

1. **Create transactions.mutation.agent.spec.ts** - Financial operations are critical
2. **Create settings.mutation.agent.spec.ts** - Core system configuration
3. **Add DELETE operations** to existing agents (properties, contacts)
4. **Extend leasing agent** with suspend/reactivate operations
5. **Create documents.mutation.agent.spec.ts** - Document management
