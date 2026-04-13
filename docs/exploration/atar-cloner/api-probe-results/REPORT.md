# API Probe Results

## Summary
- **Timestamp:** 2026-04-10T22:03:41.972Z
- **Tenant:** testbusiness123
- **Total Endpoints:** 44
- **Successful:** 31
- **Validation Errors:** 9
- **Failed:** 4

## Endpoints Probed

| Method | Path | Status | Duration |
|--------|------|--------|----------|
| GET | `/dashboard/requires-attention` | 200 | 1312ms |
| GET | `/rf/admins` | 200 | 370ms |
| GET | `/rf/admins/manager-roles` | 200 | 435ms |
| GET | `/rf/leases` | 200 | 562ms |
| GET | `/rf/leases/statistics` | 200 | 524ms |
| GET | `/rf/sub-leases` | 200 | 520ms |
| GET | `/rf/leads` | 200 | 491ms |
| GET | `/rf/contacts/statistics` | 200 | 514ms |
| GET | `/rf/tenants` | 200 | 560ms |
| GET | `/rf/requests/categories` | 200 | 523ms |
| GET | `/rf/users/requests` | 200 | 333ms |
| GET | `/rf/users/requests/types` | 200 | 502ms |
| GET | `/request-category` | 404 | 324ms |
| GET | `/rf/transactions/` | 200 | 338ms |
| GET | `/rf/buildings` | 200 | 412ms |
| GET | `/rf/facilities` | 200 | 607ms |
| GET | `/rf/communities/off-plan-sale` | 500 | 326ms |
| GET | `/rf/communities/edaat/product-codes` | 400 | 520ms |
| GET | `/marketplace/admin/settings/banks` | 200 | 525ms |
| GET | `/marketplace/admin/settings/sales` | 200 | 729ms |
| GET | `/marketplace/admin/settings/visits` | 200 | 517ms |
| GET | `/marketplace/admin/units` | 200 | 326ms |
| GET | `/marketplace/admin/visits` | 200 | 516ms |
| GET | `/rf/users/visitor-access` | 200 | 519ms |
| GET | `/notifications/unread-count` | 200 | 333ms |
| GET | `/rf/announcements` | 200 | 520ms |
| GET | `/rf/common-lists` | 200 | 525ms |
| GET | `/rf/statuses` | 200 | 513ms |
| GET | `/rf/modules` | 200 | 327ms |
| GET | `/countries` | 200 | 524ms |
| GET | `/integrations/powerbi/types` | 200 | 317ms |
| GET | `/me` | 200 | 1278ms |
| GET | `/cities/all` | 200 | 337ms |
| GET | `/districts/all` | 200 | 478ms |
| POST | `/rf/admins/check-validate` | 422 | 327ms |
| POST | `/rf/leases/create` | 405 | 287ms |
| POST | `/rf/leases/change-status/move-out` | 422 | 359ms |
| POST | `/rf/leases/change-status/terminate` | 422 | 377ms |
| POST | `/rf/leases/renew/store` | 422 | 511ms |
| POST | `/rf/requests/change-status/canceled` | 422 | 542ms |
| POST | `/marketplace/admin/settings/banks/store` | 422 | 515ms |
| POST | `/marketplace/admin/settings/sales/store` | 422 | 429ms |
| POST | `/marketplace/admin/settings/visits/store` | 422 | 673ms |
| POST | `/notifications/mark-all-as-read` | 405 | 578ms |

## Validation Errors (Required Fields)

### GET__rf_communities_edaat_product_codes
```json
{
  "code": 400,
  "message": "حدث خطأ ما, برجاء المحاوله لاحقا.",
  "errors": []
}
```

### POST__rf_admins_check_validate
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "first_name": [
      "الحقل الاسم مطلوب."
    ],
    "last_name": [
      "الحقل اسم العائلة مطلوب."
    ],
    "phone_country_code": [
      "الحقل phone country code مطلوب."
    ],
    "phone_number": [
      "الحقل رقم الجوال مطلوب."
    ]
  }
}
```

### POST__rf_leases_change_status_move_out
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "rf_lease_id": [
      "الحقل rf lease id مطلوب."
    ],
    "end_at": [
      "الحقل end at مطلوب."
    ]
  }
}
```

### POST__rf_leases_change_status_terminate
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "rf_lease_id": [
      "الحقل rf lease id مطلوب."
    ],
    "end_at": [
      "الحقل end at مطلوب."
    ]
  }
}
```

### POST__rf_leases_renew_store
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "rental_contract_type_id": [
      "الحقل rental contract type id مطلوب."
    ],
    "created_at": [
      "الحقل created at مطلوب."
    ],
    "start_date": [
      "الحقل start date مطلوب."
    ],
    "end_date": [
      "الحقل end date مطلوب."
    ],
    "rf_lease_id": [
      "الحقل rf lease id مطلوب."
    ],
    "number_of_years": [
      "يرجى إضافة مدة الإيجار بالسنوات."
    ],
    "number_of_months": [
      "يرجى إضافة مدة الإيجار بالشهور."
    ],
    "autoGenerateLeaseNumber": [
      "الحقل auto generate lease number مطلوب."
    ],
    "contract_number": [
      "يرجى إدخال رقم إيجار فريد."
    ],
    "rental_type": [
      "الحقل rental type مطلوب."
    ],
    "payment_schedule_id": [
      "الحقل payment schedule id مطلوب."
    ],
    "units": [
      "الحقل units مطلوب."
    ]
  }
}
```

### POST__rf_requests_change_status_canceled
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "rf_request_id": [
      "الحقل rf request id مطلوب."
    ]
  }
}
```

### POST__marketplace_admin_settings_banks_store
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "beneficiary_name": [
      "الحقل beneficiary name مطلوب."
    ],
    "bank_name": [
      "الحقل bank name مطلوب."
    ],
    "account_number": [
      "الحقل account number مطلوب.",
      "يجب ان يحتوي رقم الحساب على ارقام فقط",
      "يجب ان يكون رقم الحساب اكثر من 14 رقم"
    ],
    "iban": [
      "الحقل iban مطلوب."
    ]
  }
}
```

### POST__marketplace_admin_settings_sales_store
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "deposit_time_limit_days": [
      "مدة التوقيع مطلوبة."
    ],
    "cash_contract_signing_days": [
      "validation.marketplace_sales.cash_signing.required"
    ],
    "bank_contract_signing_days": [
      "validation.marketplace_sales.bank_signing.required"
    ]
  }
}
```

### POST__marketplace_admin_settings_visits_store
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "is_all_day": [
      "الحقل is all day مطلوب."
    ],
    "days": [
      "الحقل days مطلوب."
    ]
  }
}
```

## Extracted Schemas

See `_schemas.json` for detailed schema information extracted from successful responses.
