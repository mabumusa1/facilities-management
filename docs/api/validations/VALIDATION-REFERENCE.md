# Atar API Validation Reference

> Auto-generated from API validation error captures

Generated: 2026-04-12T16:49:59.241Z

---

## Table of Contents

- [owners](#owners)
- [tenants](#tenants)
- [admins](#admins)
- [professionals](#professionals)
- [files](#files)
- [excel-sheets](#excel-sheets)
- [leases](#leases)
- [sub-leases](#sub-leases)
- [marketplace](#marketplace)
- [communities](#communities)
- [buildings](#buildings)
- [units](#units)
- [facilities](#facilities)
- [requests](#requests)
- [announcements](#announcements)
- [invoice-settings](#invoice-settings)

---

## owners

### POST `rf/owners`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| first_name | string | `required` |
| last_name | string | `invalid`, `required` |
| phone_country_code | string | `required` |
| phone_number | string | `required` |

---

### PUT `rf/owners/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| phone_country_code | string | `required` |
| phone_number | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| last_name | string | `invalid` |

---

## tenants

### POST `rf/tenants`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| first_name | string | `required` |
| last_name | string | `required` |
| phone_country_code | string | `required` |
| phone_number | string | `required` |

---

### PUT `rf/tenants/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| phone_country_code | string | `required` |
| phone_number | string | `required` |

---

## admins

### POST `rf/admins`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| first_name | string | `required` |
| last_name | string | `invalid`, `required` |
| phone_country_code | string | `required` |
| phone_number | string | `required` |
| role | string | `required` |

---

### PUT `rf/admins/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| phone_country_code | string | `required` |
| phone_number | string | `required` |
| role | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| last_name | string | `invalid` |

---

## professionals

### POST `rf/professionals`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| first_name | string | `required` |
| last_name | string | `invalid`, `required` |
| phone_country_code | string | `required` |
| phone_number | string | `required` |

---

## files

### POST `rf/files`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| image | string | `required` |

---

## excel-sheets

### POST `rf/excel-sheets`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| file | string | `required` |
| rf_community_id | string | `required` |

---

### POST `rf/excel-sheets/land`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| rf_community_id | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| file | string | `unknown` |

---

### POST `rf/excel-sheets/leads`

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| file | string | `unknown`, `unknown` |

---

## leases

### POST `rf/leases`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| autoGenerateLeaseNumber | string | `required` |
| created_at | string | `required` |
| end_date | string | `required` |
| handover_date | string | `required` |
| lease_unit_type | string | `required` |
| payment_schedule_id | string | `required` |
| rental_contract_type_id | string | `required` |
| rental_type | string | `required`, `invalid` |
| start_date | string | `required` |
| tenant | string | `required` |
| tenant.national_id | string | `required` |
| tenant_type | string | `required` |
| units | string | `required` |
| units.0.amount_type | string | `required` |
| units.0.rental_amount | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| contract_number | string | `unknown` |
| number_of_months | string | `unknown` |
| number_of_years | string | `unknown` |

---

### DELETE `rf/leases/{id}`

---

### POST `rf/leases/change-status/move-out`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| end_at | string | `required` |
| rf_lease_id | string | `required` |

---

### POST `rf/leases/change-status/terminate`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| end_at | string | `required` |
| rf_lease_id | string | `required` |

---

### POST `rf/leases/renew/store`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| autoGenerateLeaseNumber | string | `required` |
| created_at | string | `required` |
| end_date | string | `required` |
| payment_schedule_id | string | `required` |
| rental_contract_type_id | string | `required` |
| rental_type | string | `required` |
| rf_lease_id | string | `required` |
| start_date | string | `required` |
| units | string | `required` |
| units.0.rental_amount | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| contract_number | string | `unknown` |
| number_of_months | string | `unknown` |
| number_of_years | string | `unknown` |

---

## sub-leases

### POST `rf/sub-leases`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| lease_id | string | `required` |
| tenant_type | string | `required` |

---

## marketplace

### POST `marketplace/admin/settings/banks/store`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| account_number | string | `required`, `unknown`, `unknown`, `invalid` |
| bank_name | string | `required` |
| beneficiary_name | string | `required` |
| iban | string | `required` |

---

### POST `marketplace/admin/settings/sales/store`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| bank_contract_signing_days | string | `required` |
| cash_contract_signing_days | string | `required` |
| deposit_time_limit_days | string | `required` |

---

### POST `marketplace/admin/settings/visits/store`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| days | string | `required` |
| is_all_day | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| days.0 | string | `string`, `invalid` |
| days.1 | string | `string`, `invalid` |
| days.2 | string | `string`, `invalid` |
| days.3 | string | `string`, `invalid` |
| days.4 | string | `string`, `invalid` |

---

### POST `marketplace/admin/communities/list/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| allow_cash_sale | string | `required` |

---

## communities

### POST `rf/communities`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| city_id | string | `required` |
| country_id | string | `required` |
| currency_id | string | `required` |
| district_id | string | `required` |
| name | string | `required` |

---

## buildings

### POST `rf/buildings`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| name | string | `unknown`, `required` |
| rf_community_id | string | `required` |

---

## units

### POST `rf/units`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| category_id | string | `required` |
| name | string | `required` |
| rf_community_id | string | `required` |
| type_id | string | `required` |

---

### PUT `rf/units/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| category_id | string | `required` |
| name | string | `required` |
| rf_community_id | string | `required` |
| type_id | string | `required` |

---

## facilities

### POST `rf/facilities`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| approved | string | `required` |
| booking_type | string | `required` |
| complex_id | string | `required` |
| days | string | `required` |
| gender | string | `required` |
| name_ar | string | `required` |
| name_en | string | `required` |

---

### PUT `rf/facilities/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| approved | string | `required` |
| booking_type | string | `required` |
| complex_id | string | `required` |
| days | string | `required` |
| gender | string | `required` |
| name_ar | string | `required` |
| name_en | string | `required` |

---

### POST `rf/facilities`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| approved | string | `required` |
| booking_type | string | `required` |
| complex_id | string | `required` |
| days | string | `required` |
| gender | string | `required` |
| name_ar | string | `required` |
| name_en | string | `required` |

---

## requests

### POST `rf/requests/service-settings/updateOrCreate`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| permissions | string | `required` |
| rf_category_id | string | `required` |

---

### POST `rf/requests/change-status/canceled`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| rf_request_id | string | `required` |

---

### PUT `rf/requests/types/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| fee_type | string | `required` |
| name_ar | string | `required` |
| name_en | string | `required` |
| rf_sub_category_id | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| icon | string | `unknown` |

---

### POST `rf/requests/sub-categories`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| is_all_day | string | `required` |
| name_ar | string | `required` |
| name_en | string | `required` |
| rf_category_id | string | `required` |
| terms_and_conditions | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| icon | string | `unknown` |

---

### PUT `rf/requests/sub-categories/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| terms_and_conditions | string | `required` |

**Optional Fields:**

| Field | Type | Rules |
|-------|------|-------|
| icon | string | `unknown` |

---

### POST `rf/requests/service-settings/updateOrCreate`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| permissions | string | `required` |
| rf_category_id | string | `required` |

---

## announcements

### POST `rf/announcements`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| description | string | `required` |
| end_date | string | `required` |
| end_time | string | `required` |
| is_visible | string | `required` |
| notify_user_type | string | `required` |
| start_date | string | `required` |
| start_time | string | `required` |
| title | string | `required` |

---

### PUT `rf/announcements/{id}`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| description | string | `required` |
| end_date | string | `required` |
| end_time | string | `required` |
| is_visible | string | `required` |
| notify_user_type | string | `required` |
| start_date | string | `required` |
| start_time | string | `required` |

---

## invoice-settings

### POST `invoice-settings`

**Required Fields:**

| Field | Type | Rules |
|-------|------|-------|
| address | string | `required` |
| company_name | string | `required` |
| vat | string | `required` |

---
