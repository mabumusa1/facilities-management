# Roles & Permissions

The system uses **role-based access control (RBAC)** to ensure each user only sees and does what their role allows.

## User Types

There are four user types in the system:

### 👤 Admin / Staff
Administrative users who manage the platform. There are five specialized admin roles (see below).

### 🏠 Owner
Property owners with read access to their own units, leases, and financial summaries. Owners can also submit service requests for their properties.

### 🔑 Tenant
Residents occupying rented units. Tenants can:
- View their active lease
- Submit service requests
- Book community facilities
- Manage visitor access permits

### 🔨 Service Professional
Maintenance contractors and service providers who:
- Receive and accept assigned service requests
- Update request status
- Submit invoices and quotes

---

## Admin Roles

Administrators are assigned one of five specialized roles that determine what they can manage:

| Role | What They Can Do |
|------|-----------------|
| **Admin** | Full access to all features including properties, leases, contacts, transactions, service requests, announcements, marketplace, and system settings |
| **Accounting Manager** | Manage transactions, record payments, and view financial reports |
| **Service Manager** | Manage service requests (limited to their assigned request types) |
| **Marketing Manager** | Manage community announcements and marketplace listings |
| **Sales & Leasing Manager** | Manage properties, create/edit leases, and manage marketplace |

### Service Manager Sub-Types

Service Managers are further specialized by the type of requests they manage:

| Sub-Type | Requests They Handle |
|----------|---------------------|
| **Home Service Requests** | Maintenance and repair requests inside individual units |
| **Common Area Requests** | Requests for shared spaces and amenities |
| **Visitor Access Requests** | Visitor entry permits and check-in management |
| **Facility Booking Requests** | Approvals for community facility reservations |

---

## Capability Matrix

| Capability | Admin | Accounting | Service | Marketing | Sales/Leasing |
|------------|:-----:|:----------:|:-------:|:---------:|:-------------:|
| Manage Properties | ✅ | ❌ | ❌ | ❌ | ✅ |
| Manage Leases | ✅ | ❌ | ❌ | ❌ | ✅ |
| Manage Transactions | ✅ | ✅ | ❌ | ❌ | ❌ |
| View Financial Reports | ✅ | ✅ | ❌ | ❌ | ❌ |
| Manage Service Requests | ✅ | ❌ | ✅* | ❌ | ❌ |
| Manage Announcements | ✅ | ❌ | ❌ | ✅ | ❌ |
| Manage Marketplace | ✅ | ❌ | ❌ | ✅ | ✅ |
| System Settings | ✅ | ❌ | ❌ | ❌ | ❌ |
| Manage Users/Contacts | ✅ | ❌ | ❌ | ❌ | ❌ |

*Service Managers only manage requests of their assigned types.

---

## Scoped Access

Admin users can have their access **restricted to specific communities or buildings**. For example, a service manager might only see requests from Building A, while the head admin sees everything.

::: info
If you believe you're missing access to something, contact your system administrator to check your scope settings.
:::

---

## What Happens if I Don't Have Access?

If you try to access a page or perform an action outside your permissions, you will see a **403 Access Denied** page. Contact your administrator if you believe you should have access.
