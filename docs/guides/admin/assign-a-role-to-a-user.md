---
title: Assign a role to a user
area: admin
layout: guide
lang: en
---

# Assign a role to a user

*After a role exists and has permissions, you assign it to one or more users. For manager-type roles, you also choose which properties or service types the role applies to.*

## Who this is for

Admins onboarding a new staff member, changing someone's responsibilities, or adding a scoped assignment.

## Before you start

- The user must already exist in the system.
- The role must already be created and have permissions assigned — see [Create a role](./create-a-role.md) and [Assign permissions to a role](./assign-permissions-to-a-role.md).
- A user can hold multiple roles at once. Adding a role does not remove any existing assignment.

## Steps

1. Open **Admin → Users** and click the user you want to update.
2. On the user's detail page, open the **Roles** tab.
3. Click **Assign Role** at the top right of the tab.
4. In the **Assign Role** drawer:
   - **Role** — pick the role to assign from the dropdown.
   - Depending on the role's type, scope selectors appear:
     - **User-type roles** (Tenant, Owner, etc.) show the note **"This role applies globally. No scope selection needed."** Skip to step 5.
     - **Admin-type roles** (Accounting Manager, Service Manager, etc.) show scope selectors:
       - **Community** — pick one or more communities.
       - **Building** — *optional.* Leave blank to apply to all buildings in the selected communities, or pick specific ones.
       - **Service Type** — required for Service Manager roles only. Pick one or more service types.
5. Click **Assign**.

## What you'll see

- A toast: **Role assigned successfully.**
- The new assignment appears in the Roles tab with its role name, scope summary, and the date it was assigned.
- The user's new permissions take effect on their next page navigation — they don't need to sign out.

## Removing a role

In the user's **Roles** tab, find the assignment and click the remove icon on its row. Confirm in the popover. A toast shows **Role assignment removed.** The revocation takes effect immediately.

## Common issues

- **"Please select a role."** — the Role dropdown is empty. Choose one.
- **"Please select at least one community for this role."** — the role is scoped, so a community is mandatory. Pick one.
- **"Please select at least one service type for this role."** — the role is a Service Manager variant. Pick at least one service type.
- **The user still cannot access the expected page** — check the role's permissions. Assigning a role that has no `VIEW` permission on a subject will not grant access. See [Assign permissions to a role](./assign-permissions-to-a-role.md).

## Related

- [Scope a manager to specific properties](./manager-scope.md)
- [Create a role](./create-a-role.md)
