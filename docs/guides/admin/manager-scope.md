---
title: Scope a manager to specific properties
area: admin
layout: guide
lang: en
---

# Scope a manager to specific properties

*Managers can be limited to specific communities, buildings, or service types. Scope is separate from permissions — a manager with the right permissions can still only act inside their scope.*

## Who this is for

Admins who operate across multiple communities or buildings and want to restrict certain managers to the properties they actually handle.

## How scope works

Scope is a **second layer** on top of permissions:

- **Permissions** decide *what* a manager can do (View leases, Update payments, etc.).
- **Scope** decides *where* those actions apply (which communities, which buildings, which service types).

A Service Manager with full permissions on Service Requests will still only see requests that belong to the communities, buildings, and service types they are scoped to.

## What can be scoped

Only **Admin-type roles** accept scope. These are the five default admin functions and any custom roles you create with the **Admin Role** type:

- Accounting Manager
- Service Manager
- Marketing Manager
- Sales & Leasing Manager
- System Admin (note: System Admin is always global and ignores scope — it has full access)

User-type roles (Tenant, Owner, Professional, etc.) are never scoped in this way.

## Scope dimensions

| Dimension | Applies to | Required? |
|---|---|---|
| **Community** | All admin roles | Required on every scoped assignment |
| **Building** | All admin roles | Optional — leave blank to include all buildings in the selected communities |
| **Service Type** | Service Manager only | Required for Service Manager assignments |

## Steps to add a scoped role assignment

1. Follow [Assign a role to a user](./assign-a-role-to-a-user.md) up to the point where the **Assign Role** drawer is open.
2. Pick a role with type **Admin Role**.
3. In **Community**, select one or more communities the manager is responsible for.
4. In **Building**, either:
   - Leave blank to apply the role to every building in the selected communities (now and in the future), or
   - Pick specific buildings to narrow the scope further.
5. If you picked a **Service Manager** role, select one or more **Service Types**.
6. Click **Assign**.

## What you'll see

The new row in the user's Roles tab shows a **Scope** column summarizing the assignment — for example, *"Al-Olaya Community · 3 buildings · 2 service types"*. Click the scope to see the full list.

## Giving a manager multiple scopes

A user can hold the same role twice with different scopes. For example, a Service Manager can have one assignment for plumbing in Community A and another for HVAC in Community B. Add each scoped assignment separately.

## Common issues

- **I don't see Service Type in the drawer** — the role you selected is not a Service Manager role. Service Type only applies to Service Manager assignments.
- **The manager still sees other properties** — check whether they hold a **System Admin** role or a role with unscoped permissions. System Admin grants global access regardless of scope.
- **"Please select at least one community for this role."** — admin roles require at least one community. Pick one.
- **A new building was added to a community and the manager cannot see it** — if you left Building blank on the original assignment, the manager will see the new building automatically on their next page load. If you selected specific buildings, you'll need to edit the assignment to include the new one.

## Related

- [Assign a role to a user](./assign-a-role-to-a-user.md)
- [Assign permissions to a role](./assign-permissions-to-a-role.md)
