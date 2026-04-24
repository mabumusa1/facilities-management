---
title: Assign permissions to a role
area: admin
layout: guide
lang: en
---

# Assign permissions to a role

*The permission matrix is a grid with subjects (Leases, Payments, Units…) as rows and actions (View, Create, Update, Delete, Restore, Force Delete) as columns. Tick the cells that describe what the role can do.*

## Who this is for

Admins configuring a custom role after creating it, or adjusting permissions on an existing custom role.

## Before you start

- The role must already exist. See [Create a role](./create-a-role.md).
- You cannot modify **system roles** — the ones seeded by the platform. If you need a variation, create a new custom role.

## Steps

1. Open **Admin → Roles**.
2. Find the role you want to edit. Click the **Manage Permissions** action on its row.
3. You'll land on the **Permissions — *{role name}*** page. The matrix shows every subject on the left and six action columns across the top.
4. (Optional) Apply a starting point:
   - Open the **Apply preset** dropdown at the top and select a preset that matches the role's intended function.
   - The preset fills in a standard set of cells. Adjust afterwards if you need fewer or more.
5. Tick or clear individual cells as needed:
   - Ticking **Create**, **Update**, **Delete**, **Restore**, or **Force Delete** automatically enables **View** for the same subject. View is required whenever any other action is granted.
   - You can tick or clear a full row or column using the header checkbox.
6. When the page header shows **Unsaved changes**, click **Save permissions** at the top right.

## What you'll see

- A toast: **Permissions saved.**
- The changes take effect on the next page load for users who already have this role assigned. No one needs to sign out.

## Common issues

- **The matrix is greyed out and a banner says "System roles cannot be modified."** — you're editing a seeded system role. Create a custom role instead.
- **"View is required when any other action is enabled."** appears as a tooltip — this is a built-in rule. Tick **View** first (or tick any other action and View will be auto-ticked).
- **"Could not save permissions. Please try again."** — a save failed. Check your network and retry. Your changes remain on screen until you navigate away.
- **Users with the role still see a "403" error** — verify the permission column you ticked matches what they're trying to do. A role with `leases.VIEW` can open the lease list, but needs `leases.CREATE` to add a new lease.

## Tip — multi-role users

A user can hold multiple roles. Their effective permissions are the **union** of all roles they hold. When in doubt, grant the minimum needed in each role and layer them on users.

## Related

- [Create a role](./create-a-role.md)
- [Assign a role to a user](./assign-a-role-to-a-user.md)
