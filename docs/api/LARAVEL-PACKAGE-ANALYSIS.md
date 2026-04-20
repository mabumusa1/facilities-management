# Laravel Package Analysis Report

**Source:** goatar.com API captures, network traces, and localStorage data
**Date:** 2026-04-19
**Confidence Scale:** 0-100% (based on evidence strength)

---

## Executive Summary

Based on comprehensive analysis of API responses, validation patterns, headers, localStorage data, and network traces, this report identifies the Laravel packages likely used by the goatar.com backend.

---

## Package Analysis

### 1. Laravel Framework (Core)

| Field | Value |
|-------|-------|
| **Confidence** | 99% |
| **Evidence Type** | Response format, headers, validation patterns |

**Evidence:**

1. **Standard Laravel Response Structure**
   ```json
   {
     "code": 200,
     "message": "تم انشاء المشروع بنجاح",
     "data": { ... },
     "meta": []
   }
   ```
   *Source: `docs/api/mutations/properties/captures.json`*

2. **Laravel Validation Error Format (HTTP 422)**
   ```json
   {
     "message": "The given data was invalid.",
     "errors": {
       "name": ["الحقل الاسم مطلوب."],
       "country_id": ["الحقل country id مطلوب."]
     }
   }
   ```
   *Source: `docs/api/mutations/properties/captures.json`, line 121-138*

3. **Security Headers (Laravel Defaults)**
   ```
   x-frame-options: SAMEORIGIN
   x-xss-protection: 1; mode=block
   x-content-type-options: nosniff
   cache-control: no-cache, private
   ```
   *Source: `docs/api/queries/transactions/captures.json`, lines 18-21*

---

### 2. spatie/laravel-permission (RBAC)

| Field | Value |
|-------|-------|
| **Confidence** | 95% |
| **Evidence Type** | localStorage, permission structure |

**Evidence:**

1. **Permission Subject-Action Pattern** (Classic spatie format)
   ```json
   {
     "permissionSubjects": [
       "communities", "buildings", "leases", "transactions",
       "homeServices", "facilities", "marketPlaces", ...
     ],
     "permissionActions": [
       "VIEW", "CREATE", "UPDATE", "DELETE", "RESTORE", "FORCE_DELETE"
     ]
   }
   ```
   *Source: `docs/api/localStorage-full-export.json`, lines 102-188*

2. **Permission Count** - 394 permissions across 78 subjects with 6 actions
   - Pattern matches spatie's `subject.action` convention
   - `RESTORE` and `FORCE_DELETE` indicate soft-delete integration

3. **Role Definitions** (7 roles)
   ```json
   {
     "accountAdmins": "Super admin with all permissions (Account Owner)",
     "admins": "Admin users with configurable permissions",
     "managers": "Manager users (service, accounting, security managers)",
     "owners": "Property owners",
     "tenants": "Tenant users",
     "dependents": "Dependents/family members of owners or tenants",
     "professionals": "Service professionals/technicians"
   }
   ```
   *Source: `docs/api/localStorage-full-export.json`, lines 189-197*

---

### 3. stancl/tenancy OR Custom Multi-Tenancy

| Field | Value |
|-------|-------|
| **Confidence** | 85% (multi-tenancy exists, package uncertain) |
| **Evidence Type** | Headers, localStorage |

**Evidence:**

1. **X-Tenant Header** (Tenant identification)
   ```json
   {
     "tenantHeader": "X-Tenant: {tenant_id}"
   }
   ```
   *Source: `docs/api/api-endpoints.json`, line 6*

2. **Tenant Data in localStorage**
   ```json
   {
     "tenant": {
       "X-Tenant": "scantest2026apr",
       "tenant_id": "scantest2026apr"
     }
   }
   ```
   *Source: `docs/api/localStorage-full-export.json`, lines 4-7*

3. **Subdomain/Header Pattern** - Uses header-based tenant identification (common in stancl/tenancy API mode)

**Note:** Could also be custom implementation. Header-based tenancy is a common pattern.

---

### 4. laravel/sanctum OR tymon/jwt-auth

| Field | Value |
|-------|-------|
| **Confidence** | 75% |
| **Evidence Type** | Authentication pattern |

**Evidence:**

1. **Bearer Token Authentication**
   ```json
   {
     "authentication": {
       "type": "Bearer",
       "header": "Authorization: Bearer {token}"
     }
   }
   ```
   *Source: `docs/api/api-endpoints.json`, lines 3-4*

2. **Token Storage in localStorage**
   - Token stored as `token` key
   - Used for SPA authentication pattern

**Note:** Cannot definitively distinguish between Sanctum and JWT without seeing token format/claims.

---

### 5. Laravel Rate Limiting (Built-in)

| Field | Value |
|-------|-------|
| **Confidence** | 99% |
| **Evidence Type** | Response headers |

**Evidence:**

1. **Rate Limit Headers**
   ```
   x-ratelimit-limit: 10        (GET requests)
   x-ratelimit-remaining: 9

   x-ratelimit-limit: 5         (POST requests)
   x-ratelimit-remaining: 4
   ```
   *Source: `docs/api/queries/transactions/captures.json`, lines 16-17*
   *Source: `docs/api/mutations/properties/captures.json`, lines 23-24*

2. **Different Limits by Method** - 10 for GET, 5 for POST indicates configured throttle groups

---

### 6. Laravel API Resources

| Field | Value |
|-------|-------|
| **Confidence** | 95% |
| **Evidence Type** | Response structure |

**Evidence:**

1. **Nested Resource Transformation**
   ```json
   {
     "country": { "id": 1, "name": "المملكة العربية السعودية", "code": "SA" },
     "currency": { "id": 1, "name": "ريال سعودي", "code": "SAR" },
     "city": { "id": 1, "name": "الرياض" },
     "district": { "id": 1, "name": "الدرعية" }
   }
   ```
   *Source: `docs/api/mutations/properties/captures.json`, lines 44-61*

2. **Consistent Wrapper Pattern** - All responses use `{ code, message, data, meta }` structure

3. **Formatted Fields** (accessors or API Resource mutators)
   ```json
   {
     "amount": 50000,
     "amount_fmt": "50,000.00",
     "paid_fmt": "0.00",
     "left_fmt": "50,000.00"
   }
   ```
   *Source: `docs/api/queries/transactions/captures.json`, lines 39-64*

---

### 7. Feature Flags / Plan-Based Access

| Field | Value |
|-------|-------|
| **Confidence** | 90% |
| **Evidence Type** | localStorage, plan features |

**Evidence:**

1. **74 Plan Features**
   ```json
   {
     "planFeatures": {
       "CREATE_LEASES": true,
       "ENABLE_MARKETPLACE": true,
       "ENABLE_FACILITIES": true,
       "ENABLE_POWER_BI": true,
       "ENABLE_WHATSAPP_BUSINESS": true,
       ...
     }
   }
   ```
   *Source: `docs/api/localStorage-full-export.json`, lines 26-101*

2. **Subscription Tiers** - 3 plans (indicated by `plan: "3"` for Enterprise)

**Possible Package:** `laravel-pennant`, `spatie/laravel-feature-flags`, or custom implementation

---

### 8. Localization (laravel-lang OR spatie/laravel-translatable)

| Field | Value |
|-------|-------|
| **Confidence** | 90% |
| **Evidence Type** | Response content, headers |

**Evidence:**

1. **Arabic Validation Messages**
   ```json
   {
     "errors": {
       "name": ["الحقل الاسم مطلوب."]
     }
   }
   ```
   *Source: `docs/api/mutations/properties/captures.json`, lines 123-125*

2. **Locale Header**
   ```
   X-App-Locale: en|ar
   ```
   *Source: `docs/api/api-endpoints.json`, line 7*

3. **Frontend i18next**
   - Translation files: `/assets/locales/ar/translation.json`, `/assets/locales/en/translation.json`
   - `i18nextLng: "en"` in localStorage

---

### 9. File Storage (Laravel Storage / Spatie Media Library)

| Field | Value |
|-------|-------|
| **Confidence** | 80% |
| **Evidence Type** | API endpoints |

**Evidence:**

1. **File Upload Endpoints**
   ```
   POST /rf/files
   POST /rf/excel-sheets
   ```
   *Source: `docs/api/validations/VALIDATION-REFERENCE.md`*

2. **Bulk Import** - Excel sheet handling for buildings and units

**Possible Package:** `spatie/laravel-medialibrary` for polymorphic attachments, `maatwebsite/excel` for Excel handling

---

### 10. Excel Import/Export

| Field | Value |
|-------|-------|
| **Confidence** | 85% |
| **Evidence Type** | API endpoints, feature flags |

**Evidence:**

1. **Bulk Upload Feature Flag**
   ```json
   { "ENABLE_BULK_UPLOAD": true }
   ```
   *Source: `docs/api/localStorage-full-export.json`, line 39*

2. **Excel Sheet Endpoint**
   ```
   POST /rf/excel-sheets
   ```

**Likely Package:** `maatwebsite/excel` (most popular Laravel Excel package)

---

## Third-Party Services (Frontend & Analytics)

### Confirmed Services

| Service | Confidence | Evidence |
|---------|------------|----------|
| **Intercom** | 99% | `widget.intercom.io`, `api-iam.intercom.io` calls |
| **Google Analytics 4** | 99% | `gtag.js`, `G-Z5C0J59M80` tracking ID |
| **Microsoft Clarity** | 99% | `clarity.ms`, `h16tldnaou` project ID |
| **Cloudflare** | 99% | `server: cloudflare`, `cf-ray` headers |
| **Google Cloud** | 90% | `via: 1.1 google` header |
| **Ably (Realtime)** | 85% | `ably-transport-preference` in localStorage |

*Source: `docs/pages/auth-login/api/endpoints.json`*

---

## Package Confidence Summary

| Package/Feature | Confidence | Evidence Strength |
|-----------------|------------|-------------------|
| Laravel Framework | 99% | Response format, validation, headers |
| spatie/laravel-permission | 95% | Permission structure, role pattern |
| Laravel API Resources | 95% | Response transformation |
| Laravel Rate Limiting | 99% | Rate limit headers |
| Laravel Localization | 90% | Arabic messages, locale header |
| Multi-tenancy | 85% | X-Tenant header |
| Feature Flags | 90% | planFeatures structure |
| maatwebsite/excel | 85% | Bulk upload, excel-sheets endpoint |
| laravel/sanctum OR tymon/jwt-auth | 75% | Bearer token pattern |
| spatie/laravel-medialibrary | 80% | File upload pattern |

---

## Recommendations for Implementation

Based on this analysis, the recommended package stack for the clone:

```php
// composer.json
{
    "require": {
        "laravel/framework": "^13.0",
        "laravel/sanctum": "^4.0",           // SPA auth
        "spatie/laravel-permission": "^7.0", // RBAC
        "stancl/tenancy": "^4.0",            // Multi-tenancy
        "maatwebsite/excel": "^4.0",         // Excel import/export
        "spatie/laravel-medialibrary": "^12.0", // File handling
        "laravel-lang/lang": "^15.0"         // Translations
    }
}
```

---

## Appendix: Evidence Files

| Evidence | File Path |
|----------|-----------|
| API Endpoints | `docs/api/api-endpoints.json` |
| localStorage Export | `docs/api/localStorage-full-export.json` |
| Transaction Captures | `docs/api/queries/transactions/captures.json` |
| Property Mutations | `docs/api/mutations/properties/captures.json` |
| Login Network Trace | `docs/pages/auth-login/api/endpoints.json` |
| Validation Reference | `docs/api/validations/VALIDATION-REFERENCE.md` |
