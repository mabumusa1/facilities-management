# Atar Roles & Permissions Reference

> Auto-generated from API captures and React bundle analysis

Generated: 2026-04-12

---

## Overview

Atar uses a **multi-tenant, role-based access control (RBAC)** system with:
- 4 user contact types (Owner, Tenant, Admin, Professional)
- 5 manager roles with specialized capabilities
- Scope-based restrictions (community/building level)
- Fine-grained service request permissions

---

## User Types (Contact Categories)

| Type | Endpoint | Description |
|------|----------|-------------|
| **Owner** | `rf/owners` | Property owner; owns units/properties |
| **Tenant** | `rf/tenants` | Rents/occupies units; can be invited |
| **Admin** | `rf/admins` | Platform administrative users |
| **Professional** | `rf/professionals` | Service providers (maintenance, etc.) |

### Tenant-Specific Fields
- `invited` - Boolean flag for invitation status
- `accepted_invite` - Boolean flag for invitation acceptance

### Professional Access Control
- Must verify identity first at `/verify` route
- If `role === "ServiceProfessional"` AND `manager_type === null` → Redirect to `/no-access`
- Gated access to specific service request types

---

## Manager Roles

| ID | Role Key | Name (EN) | Name (AR) | Sub-Types |
|----|----------|-----------|-----------|-----------|
| 1 | `Admins` | Admin | مدير | None (full access) |
| 2 | `accountingManagers` | Accounting Manager | مسؤول المالي | None |
| 3 | `serviceManagers` | Service Manager | مسؤول الخدمات | 4 types (see below) |
| 4 | `marketingManagers` | Marketing Manager | مسؤول التسويق | None |
| 5 | `salesAndLeasingManagers` | Sales & Leasing Manager | مسؤول المبيعات والتأجير | None |

### Service Manager Sub-Types

| ID | Name (AR) | Name (EN - Translated) |
|----|-----------|------------------------|
| 1 | طلبات خدمات المنازل | Home Service Requests |
| 2 | طلبات خدمات المناطق المشتركة | Common Area Service Requests |
| 3 | طلبات دخول الزوار | Visitor Access Requests |
| 5 | طلبات الحجوزات للمرافق | Facility Booking Requests |

---

## Role Capabilities Matrix

| Capability | Admin | Accounting | Service | Marketing | Sales/Leasing |
|------------|-------|------------|---------|-----------|---------------|
| Manage Properties | ✅ | ❌ | ❌ | ❌ | ✅ |
| Manage Leases | ✅ | ❌ | ❌ | ❌ | ✅ |
| Manage Transactions | ✅ | ✅ | ❌ | ❌ | ❌ |
| View Financial Reports | ✅ | ✅ | ❌ | ❌ | ❌ |
| Manage Service Requests | ✅ | ❌ | ✅* | ❌ | ❌ |
| Manage Announcements | ✅ | ❌ | ❌ | ✅ | ❌ |
| Manage Marketplace | ✅ | ❌ | ❌ | ✅ | ✅ |
| Manage Settings | ✅ | ❌ | ❌ | ❌ | ❌ |
| Manage Users/Contacts | ✅ | ❌ | ❌ | ❌ | ❌ |

*Service Managers are scoped to their assigned service types

---

## Scope-Based Access Control

Admins can have **scoped access** to specific properties:

```json
{
  "selects": {
    "is_all_buildings": null | boolean,
    "is_all_communities": null | boolean,
    "buildings": { "data": [...], "count": N },
    "communities": { "data": [...], "count": N }
  }
}
```

| Scope | Description |
|-------|-------------|
| `is_all_communities: true` | Access to all communities |
| `is_all_buildings: true` | Access to all buildings |
| `communities: [...]` | Restricted to specific communities |
| `buildings: [...]` | Restricted to specific buildings |

---

## Service Request Permissions

Each service sub-category has granular permissions:

### Visibility Permissions
| Permission | Description |
|------------|-------------|
| `hide_resident_number` | Hide tenant phone number from professionals |
| `hide_resident_name` | Hide tenant name from professionals |
| `hide_professional_number_and_name` | Hide professional info from tenants |
| `show_unified_number_only` | Show only a unified reference number |

### Action Permissions
| Permission | Description |
|------------|-------------|
| `manager_close_Request` | Allow managers to close requests |
| `not_require_professional_enter_request_code` | Skip code entry requirement |
| `not_require_professional_upload_request_photo` | Skip photo upload requirement |
| `attachments_required` | Require attachments on requests |
| `allow_professional_reschedule` | Allow professionals to reschedule |

---

## Authentication Routes

| Route | Purpose |
|-------|---------|
| `/auth-login` | User authentication |
| `/auth-verify` | Identity verification |
| `/auth-no-access` | Access denied page |
| `/auth-403` | HTTP 403 Forbidden page |
| `/no-access` | Generic no-access redirect |
| `/verify` | Professional verification |

---

## API Endpoints for Role Management

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `rf/admins/manager-roles` | GET | List available manager roles |
| `rf/admins` | GET, POST, PUT | Manage admin users |
| `rf/owners` | GET, POST, PUT | Manage property owners |
| `rf/tenants` | GET, POST, PUT | Manage tenants |
| `rf/professionals` | GET, POST | Manage service professionals |

---

## Admin Creation Requirements

Required fields for `POST /rf/admins`:
- `first_name` (required)
- `last_name` (required, must be valid)
- `phone_country_code` (required)
- `phone_number` (required)
- `role` (required, must be valid manager role ID)

---

## Access Control Implementation Notes

1. **Route Protection**: React bundle checks `user.role` before rendering protected routes
2. **API Authorization**: Bearer token + X-Tenant header required for all API calls
3. **Scope Filtering**: Backend filters results based on admin's community/building scope
4. **Service Type Gating**: Service managers only see requests for their assigned types
