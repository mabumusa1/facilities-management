# Atar API Response Reference

> Auto-generated from API query captures

Generated: 2026-04-12T17:09:08.641Z

---

## Summary

- **Total Endpoints:** 53
- **Total Fields Documented:** 568
- **Modules:** 7

## Table of Contents

- [common](#common) (10 endpoints)
- [contacts](#contacts) (11 endpoints)
- [leasing](#leasing) (7 endpoints)
- [marketplace](#marketplace) (7 endpoints)
- [properties](#properties) (11 endpoints)
- [requests](#requests) (3 endpoints)
- [transactions](#transactions) (4 endpoints)

---

## common

### GET `dashboard/requires-attention`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| requests_approval | `number` |
| pending_complaints | `number` |
| expiring_leases | `number` |
| overdue_recipes | `number` |

---

### GET `notifications`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `string` |
| text | `string` |
| data | `object` |
| type | `string` |
| read | `string` |
| created_at | `string` |

---

### GET `notifications/unread-count`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| count | `number` |

---

### GET `rf/modules`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| title | `string` |
| is_active | `string` |

---

### GET `rf/statuses`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| created_at | `null?` |
| priority | `string` |

---

### GET `countries`

**Type:** other

---

### GET `rf/common-lists`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| created_at | `null?` |
| priority | `string` |

---

### GET `rf/announcements`

**Type:** list | **Paginated:** Yes

---

### GET `rf/leads`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| phone_number | `string` |
| email | `string` |
| created_at | `string` |
| updated_at | `string` |
| lead_last_contact_at | `null?` |
| interested | `null?` |
| role | `string` |
| status | `object` |
| source | `object` |
| priority | `object` |
| lead_owner | `null?` |

---

### GET `integrations/powerbi/types`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| title | `string` |
| comming_soon | `string` |
| is_active | `string` |

---

## contacts

### GET `rf/owners`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| image | `null?` |
| phone_number | `string` |
| created_at | `string` |
| units | `array<mixed?>` |

---

### GET `rf/owners`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| image | `null?` |
| phone_number | `string` |
| created_at | `string` |
| units | `array<mixed?>` |

---

### GET `rf/owners`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| image | `null?` |
| phone_number | `string` |
| created_at | `string` |
| units | `array<mixed?>` |

---

### GET `rf/owners/3`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| first_name | `string` |
| last_name | `string` |
| image | `null?` |
| email | `string` |
| georgian_birthdate | `null?` |
| gender | `null?` |
| national_id | `string` |
| phone_number | `string` |
| national_phone_number | `string` |
| phone_country_code | `string` |
| nationality | `null?` |
| created_at | `string` |
| active | `string` |
| account_creation_date | `string` |
| last_active | `null?` |
| units | `array<mixed?>` |
| active_requests | `array<mixed?>` |
| transaction | `array<mixed?>` |
| ... | *2 more fields* |

---

### GET `rf/tenants`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| image | `null?` |
| phone_number | `string` |
| invited | `string` |
| created_at | `string` |
| units | `array<mixed?>` |
| accepted_invite | `number` |

---

### GET `rf/tenants`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| image | `null?` |
| phone_number | `string` |
| invited | `string` |
| created_at | `string` |
| units | `array<mixed?>` |
| accepted_invite | `number` |

---

### GET `rf/tenants/23`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| first_name | `string` |
| last_name | `string` |
| image | `null?` |
| email | `string` |
| georgian_birthdate | `null?` |
| gender | `null?` |
| national_id | `null?` |
| phone_number | `string` |
| national_phone_number | `string` |
| phone_country_code | `string` |
| nationality | `null?` |
| created_at | `string` |
| active | `string` |
| account_creation_date | `string` |
| last_active | `null?` |
| units | `array<mixed?>` |
| leases | `array<mixed?>` |
| active_requests | `array<mixed?>` |
| ... | *6 more fields* |

---

### GET `rf/admins`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| image | `null?` |
| phone_number | `string` |
| phone_country_code | `string` |
| national_id | `null?` |
| email | `string` |
| role | `string` |
| created_at | `string` |
| types | `array<mixed?>` |

---

### GET `rf/admins/2`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| first_name | `string` |
| last_name | `string` |
| image | `null?` |
| email | `string` |
| georgian_birthdate | `null?` |
| gender | `null?` |
| national_id | `null?` |
| full_phone_number | `string` |
| phone_number | `string` |
| phone_country_code | `string` |
| nationality | `null?` |
| role | `string` |
| selects | `object` |
| created_at | `string` |
| last_login_at | `null?` |
| active | `string` |
| types | `array<mixed?>` |

---

### GET `rf/admins/manager-roles`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| role | `string` |
| name_ar | `string` |
| name_en | `string` |
| types | `null?` |

---

### GET `rf/professionals`

**Type:** list | **Paginated:** Yes

---

## leasing

### GET `rf/leases`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| contract_number | `string` |
| lease_unit_type | `object` |
| tenant | `object` |
| units | `array<object>` |
| building | `object` |
| community | `object` |
| status | `object` |
| updated_at | `string` |
| duration | `string` |
| days_remaining | `number` |
| pdf_url | `string` |
| is_old | `string` |

---

### GET `rf/leases`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| contract_number | `string` |
| lease_unit_type | `object` |
| tenant | `object` |
| units | `array<object>` |
| building | `object` |
| community | `object` |
| status | `object` |
| updated_at | `string` |
| duration | `string` |
| days_remaining | `number` |
| pdf_url | `string` |
| is_old | `string` |

---

### GET `rf/leases`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| contract_number | `string` |
| lease_unit_type | `object` |
| tenant | `object` |
| units | `array<object>` |
| building | `object` |
| community | `object` |
| status | `object` |
| updated_at | `string` |
| duration | `string` |
| days_remaining | `number` |
| pdf_url | `string` |
| is_old | `string` |

---

### GET `rf/leases`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| contract_number | `string` |
| lease_unit_type | `object` |
| tenant | `object` |
| units | `array<object>` |
| building | `object` |
| community | `object` |
| status | `object` |
| updated_at | `string` |
| duration | `string` |
| days_remaining | `number` |
| pdf_url | `string` |
| is_old | `string` |

---

### GET `rf/leases/1`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| contract_number | `string` |
| tenant | `object` |
| units | `array<object>` |
| status | `object` |
| security_deposit_amount | `null?` |
| security_deposit_due_date | `null?` |
| lease_unit_type | `object` |
| start_date | `string` |
| end_date | `string` |
| handover_date | `string` |
| tenant_type | `string` |
| user | `object` |
| created_by | `object` |
| deal_owner | `null?` |
| rental_type | `string` |
| rental_total_amount | `string` |
| rental_contract_type | `object` |
| legal_representative | `null?` |
| fit_out_status | `null?` |
| ... | *22 more fields* |

---

### GET `rf/leases/statistics`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| totalLeases | `number` |
| newLeases | `number` |
| activeLeases | `number` |
| expiredLeases | `number` |
| terminatedLeases | `number` |
| percentNewLeases | `number` |
| percentActiveLeases | `number` |
| percentExpiredLeases | `number` |
| percentTerminatedLeases | `number` |
| activeCommercialLeases | `number` |
| activeResidentialLeases | `number` |
| currentMonthCollection | `number` |
| currentYearCollection | `number` |
| calculatePaidCollectionForCurrentMonth | `number` |
| calculatePaidCollectionForCurrentYear | `number` |

---

### GET `rf/sub-leases`

**Type:** list | **Paginated:** Yes

---

## marketplace

### GET `marketplace/admin/settings/banks`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| beneficiary_name | `string` |
| bank_name | `string` |
| account_number | `string` |
| iban | `string` |

---

### GET `marketplace/admin/settings/sales`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| deposit_time_limit_days | `number` |
| cash_contract_signing_days | `number` |
| bank_contract_signing_days | `number` |

---

### GET `marketplace/admin/settings/visits`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| days | `array<string>` |
| start_time | `null?` |
| end_time | `null?` |
| is_all_day | `string` |
| created_at | `string` |

---

### GET `marketplace/admin/units`

**Type:** list | **Paginated:** Yes

---

### GET `marketplace/admin/units`

**Type:** list | **Paginated:** Yes

---

### GET `marketplace/admin/visits`

**Type:** list | **Paginated:** Yes

---

### GET `marketplace/admin/visits`

**Type:** list | **Paginated:** Yes

---

## properties

### GET `rf/communities`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| city | `object` |
| district | `object` |
| sales_commission_rate | `string` |
| rental_commission_rate | `string` |
| buildings_count | `string` |
| units_count | `string` |
| map | `null?` |
| images | `array<mixed?>` |
| is_selected_property | `boolean` |
| count_selected_property | `number` |
| requests_count | `string` |
| total_income | `number` |
| is_market_place | `string` |
| is_buy | `number` |
| community_marketplace_type | `string` |
| is_off_plan_sale | `string` |

---

### GET `rf/communities`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| city | `object` |
| district | `object` |
| sales_commission_rate | `string` |
| rental_commission_rate | `string` |
| buildings_count | `string` |
| units_count | `string` |
| map | `null?` |
| images | `array<mixed?>` |
| is_selected_property | `boolean` |
| count_selected_property | `number` |
| requests_count | `string` |
| total_income | `number` |
| is_market_place | `string` |
| is_buy | `number` |
| community_marketplace_type | `string` |
| is_off_plan_sale | `string` |

---

### GET `rf/communities/15`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| description | `null?` |
| country | `object` |
| currency | `object` |
| city | `object` |
| district | `object` |
| amenities | `array<mixed?>` |
| map | `null?` |
| images | `array<mixed?>` |
| documents | `array<mixed?>` |
| buildings_count | `number` |
| units_count | `number` |
| requests_count | `number` |
| total_income | `number` |
| sales_commission_rate | `string` |
| rental_commission_rate | `string` |
| product_code | `null?` |
| license_number | `null?` |
| license_issue_date | `null?` |
| ... | *11 more fields* |

---

### GET `rf/buildings`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| community | `object` |
| city | `object` |
| district | `object` |
| units_count | `number` |
| map | `null?` |
| year_build | `null?` |
| images | `array<mixed?>` |
| is_selected_property | `number` |
| count_selected_property | `number` |

---

### GET `rf/buildings`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| community | `object` |
| city | `object` |
| district | `object` |
| units_count | `number` |
| map | `null?` |
| year_build | `null?` |
| images | `array<mixed?>` |
| is_selected_property | `number` |
| count_selected_property | `number` |

---

### GET `rf/buildings/4`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| community | `object` |
| city | `object` |
| district | `object` |
| no_floors | `string` |
| year_build | `null?` |
| map | `null?` |
| images | `array<mixed?>` |
| documents | `array<mixed?>` |
| units | `number` |

---

### GET `rf/units`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| category | `object` |
| type | `object` |
| rf_community | `object` |
| rf_building | `object` |
| owner | `null?` |
| tenant | `null?` |
| status | `object` |
| photos | `array<mixed?>` |
| is_market_place | `string` |
| city | `object` |
| district | `object` |
| market_rent | `null?` |
| net_area | `null?` |
| floor_no | `null?` |
| map | `object` |
| is_off_plan_sale | `string` |

---

### GET `rf/units`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| category | `object` |
| type | `object` |
| rf_community | `object` |
| rf_building | `object` |
| owner | `null?` |
| tenant | `null?` |
| status | `object` |
| photos | `array<mixed?>` |
| is_market_place | `string` |
| city | `object` |
| district | `object` |
| market_rent | `null?` |
| net_area | `null?` |
| floor_no | `null?` |
| map | `object` |
| is_off_plan_sale | `string` |

---

### GET `rf/units`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| category | `object` |
| type | `object` |
| rf_community | `object` |
| rf_building | `object` |
| owner | `null?` |
| tenant | `null?` |
| status | `object` |
| photos | `array<mixed?>` |
| is_market_place | `string` |
| city | `object` |
| district | `object` |
| market_rent | `null?` |
| net_area | `null?` |
| floor_no | `null?` |
| map | `object` |
| is_off_plan_sale | `string` |

---

### GET `rf/units/3`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| category | `object` |
| type | `object` |
| status | `object` |
| rf_community | `object` |
| rf_building | `object` |
| year_build | `string` |
| map | `object` |
| photos | `array<mixed?>` |
| floor_plans | `array<mixed?>` |
| documents | `array<mixed?>` |
| specifications | `array<mixed?>` |
| net_area | `null?` |
| marketplace | `object` |
| rooms | `array<mixed?>` |
| areas | `array<mixed?>` |
| owner | `null?` |
| tenant | `null?` |
| merge_document | `array<mixed?>` |
| ... | *9 more fields* |

---

### GET `rf/facilities`

**Type:** list | **Paginated:** Yes

---

## requests

### GET `rf/requests/categories`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name | `string` |
| description | `string` |
| status | `string` |
| has_sub_categories | `string` |
| sub_categories | `array<object>` |
| serviceSettings | `object` |
| icon | `object` |

---

### GET `rf/requests/sub-categories`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name_ar | `string` |
| name_en | `string` |
| name | `string` |
| start | `null?` |
| end | `null?` |
| is_all_day | `null?` |
| working_days | `null?` |
| status | `string` |
| requests_count | `number` |
| types_count | `number` |
| request | `array<mixed?>` |
| icon | `object` |
| terms_and_conditions | `null?` |

---

### GET `rf/requests/sub-categories/1`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| name_ar | `string` |
| name_en | `string` |
| name | `string` |
| start | `null?` |
| end | `null?` |
| is_all_day | `null?` |
| working_days | `array<mixed?>` |
| status | `string` |
| requests_count | `number` |
| request | `array<mixed?>` |
| selects | `object` |
| featured | `array<mixed?>` |
| icon | `object` |
| terms_and_conditions | `null?` |

---

## transactions

### GET `rf/transactions`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| images | `null?` |
| payments | `array<mixed?>` |
| unit | `null?` |
| amount | `number` |
| tax_amount | `string` |
| rental_amount | `string` |
| additional_fees_amount | `string` |
| vat | `string` |
| lease_number | `string` |
| additional_fees | `array<mixed?>` |
| amount_fmt | `string` |
| category | `object` |
| subcategory | `object` |
| due_on | `string` |
| assignee | `string` |
| assignee_id | `number` |
| assignee_active | `string` |
| details | `null?` |
| payments_sum | `string` |
| ... | *9 more fields* |

---

### GET `rf/transactions`

**Type:** list | **Paginated:** Yes

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| images | `null?` |
| payments | `array<mixed?>` |
| unit | `null?` |
| amount | `number` |
| tax_amount | `string` |
| rental_amount | `string` |
| additional_fees_amount | `string` |
| vat | `string` |
| lease_number | `string` |
| additional_fees | `array<mixed?>` |
| amount_fmt | `string` |
| category | `object` |
| subcategory | `object` |
| due_on | `string` |
| assignee | `string` |
| assignee_id | `number` |
| assignee_active | `string` |
| details | `null?` |
| payments_sum | `string` |
| ... | *9 more fields* |

---

### GET `invoice-settings`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| id | `number` |
| company_name | `string` |
| logo | `null?` |
| address | `string` |
| vat | `string` |
| instructions | `null?` |
| notes | `null?` |
| vat_number | `null?` |
| cr_number | `null?` |

---

### GET `reports/performance/units`

**Type:** detail

**Response Fields:**

| Field | Type |
|-------|------|
| vacant | `number` |
| sold | `number` |
| leased | `number` |
| soldAndLeased | `number` |

---
