---
title: What's New
layout: page
lang: en
---

# What's New

A plain-language running list of user-visible changes. For the developer-facing changelog, see [`CHANGELOG.md`](https://github.com/mabumusa1/facilities-management/blob/main/CHANGELOG.md) in the repo root.

## Unreleased

### Roles and permissions — April 2026

We added a full role-based access system so you can control exactly what each person on your team can see and do.

- **12 roles out of the box.** Every account now has 7 user-type roles (Account Admin, Admin, Manager, Owner, Tenant, Dependent, Professional) and 5 admin-function roles (System Admin, Accounting Manager, Service Manager, Marketing Manager, Sales & Leasing Manager). Existing admins keep their access — nothing breaks.
- **Manage roles from the admin panel.** Go to **Admin → Roles** to create custom roles alongside the defaults. Role names are bilingual (English + Arabic). Learn more: [Create a role](./guides/admin/create-a-role.md).
- **Edit permissions in a matrix.** A 31-subject × 6-action grid lets you tick exactly which actions a role can perform. Starter presets load a typical permission set you can then adjust. Learn more: [Assign permissions to a role](./guides/admin/assign-permissions-to-a-role.md).
- **Assign roles with a scope.** When you assign an admin-type role to a user, you can restrict it to specific communities, buildings, or service types. Service Managers can be scoped to specific service types as well. Learn more: [Assign a role to a user](./guides/admin/assign-a-role-to-a-user.md) and [Scope a manager to specific properties](./guides/admin/manager-scope.md).
- **Unauthorized access is blocked.** Every non-public page and action is now permission-gated. Users without the required permission see a clear error instead of the page.

Start here: [Roles and permissions — overview](./guides/admin/roles-and-permissions.md).
