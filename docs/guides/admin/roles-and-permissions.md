---
title: Roles and permissions
area: admin
layout: guide
lang: en
---

# Roles and permissions

*A role is a named bundle of permissions you assign to one or more users. Use roles to control what each person on your team can see and do.*

## Who this is for

Admins who manage staff and user access — typically users with the **System Admin** role.

## How the system is organized

- **Permissions** — the smallest unit of access. Each permission covers one action (**View**, **Create**, **Update**, **Delete**, **Restore**, or **Force Delete**) on one subject (Communities, Leases, Payments, and so on).
- **Roles** — named groups of permissions. Every user gets one or more roles.
- **Scope** — for manager-type roles, you can limit a role to specific communities, buildings, or service types.

## What's included out of the box

Every account starts with 12 pre-configured roles:

**User roles (7)** — one per user type on the platform:

- Account Admin
- Admin
- Manager
- Owner
- Tenant
- Dependent
- Professional

**Admin roles (5)** — for staff with specific responsibilities:

- System Admin
- Accounting Manager
- Service Manager
- Marketing Manager
- Sales & Leasing Manager

These defaults are labelled as **system roles**. You can assign them, but you cannot edit their permissions or delete them. To customize, [create a new role](./create-a-role.md) based on the preset you want.

## What you can do

- [Create a new role](./create-a-role.md)
- [Assign permissions to a role](./assign-permissions-to-a-role.md)
- [Assign a role to a user](./assign-a-role-to-a-user.md)
- [Scope a manager to specific properties](./manager-scope.md)

## Who can manage roles

Only users with the **System Admin** role — or any role that grants the `admins.UPDATE` permission — can open the Roles page. Everyone else sees a 403 if they try to access it directly.

## Related

- [Assign a role to a user](./assign-a-role-to-a-user.md)
