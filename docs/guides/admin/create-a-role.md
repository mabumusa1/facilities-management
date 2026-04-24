---
title: Create a role
area: admin
layout: guide
lang: en
---

# Create a role

*Create a custom role when the built-in system roles don't match how your team works.*

## Who this is for

Admins who need to define role bundles tailored to a specific job function — for example, a "Front Desk" role or a "Leasing Assistant" role.

## Before you start

- You must be signed in as a **System Admin** (or any user with the `admins.UPDATE` permission).
- Decide in advance: is the new role for a **user type** (Tenant, Owner, Manager, etc.) or a specific **admin function** (Accounting, Service, Marketing, Sales & Leasing)? You cannot change the role type later.

## Steps

1. In the left navigation, open the **Admin** section and select **Roles**.
2. Click **+ New Role** at the top right of the Roles list.
3. In the **New Role** drawer, fill in:
   - **Role name (English)** — the display name users will see in the English interface.
   - **Role name (Arabic)** — the display name for Arabic users.
   - **Type** — pick **User Role** or **Admin Role**. Remember: **role type cannot be changed after creation**.
4. Click **Create Role**.

## What you'll see

- A toast appears: **Role created successfully.**
- The new role shows up in the Roles list. Its type column reflects your selection.
- The new role starts with **no permissions**. You must open it and assign permissions before anyone assigned to it can do anything.

## Next step

[Assign permissions to the role](./assign-permissions-to-a-role.md) — without this, the role is empty.

## Common issues

- **The Create Role button is disabled** — one of the name fields is empty. Both English and Arabic names are required.
- **"Something went wrong. Please try again."** — a temporary issue. Retry; if it persists, contact support.
- **The Type dropdown is missing an option** — only **User Role** and **Admin Role** exist; there are no sub-types at creation time. To tag a role as a specific admin function (like Accounting), apply the matching preset when [assigning permissions](./assign-permissions-to-a-role.md).

## Related

- [Assign permissions to a role](./assign-permissions-to-a-role.md)
- [Assign a role to a user](./assign-a-role-to-a-user.md)
