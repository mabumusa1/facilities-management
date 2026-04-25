---
title: Visitor access — overview
area: visitor-access
layout: guide
lang: en
---

# Visitor access — overview

*Resident-created, QR-coded gate passes that let security verify and log every visitor before they enter the community.*

## Who this is for

- **Residents** — create invitations for expected guests, deliveries, or service visits.
- **Gate Officers** — scan QR codes at the gate, log walk-ins, and record exits.
- **Property Managers / Admins** — configure gate rules per community and view the full visitor logbook.

## How visitor access works

Visitor access follows a three-step flow:

1. **A resident creates an invitation.** They enter the visitor's name, the purpose of the visit (Visit, Delivery, Service, or Other), and the expected arrival date and time. The platform generates a unique QR code for that invitation.
2. **The resident shares the QR code.** The QR code link is sent to the visitor so they can show it at the gate.
3. **Security scans or logs the visitor.** The gate officer scans the QR code to verify and record entry. When the visitor leaves, the officer records the exit. Visitors without a prior invitation can also be admitted as walk-ins, which the officer logs manually.

Every entry and exit is stored in the visitor logbook for auditing.

## Before you start

- Your community must exist in the platform. Contact your Property Manager if you do not see your community listed.
- Gate Officers must be assigned the **Gate Officers** admin role before they can perform check-ins and check-outs.
- Visitor access screens and the invitation creation form are available in upcoming releases (#258–#264). The steps below describe the behaviour you will see when those screens ship.

## Invitation statuses

An invitation moves through the following statuses from the moment it is created until it is resolved:

| Status | Meaning |
|---|---|
| Pending | Created but the expected arrival time has not been reached yet. |
| Active | Within the valid arrival window — the QR code will be accepted at the gate. |
| Used | The gate officer has scanned the QR code and the visitor has entered. |
| Expired | The valid-until time has passed without the visitor arriving. |
| Cancelled | The resident or an admin cancelled the invitation before use. |

## Invitation defaults (set by your admin)

Each community has one settings record that controls the default gate rules. Your Property Manager or Admin can adjust these later.

| Setting | Default |
|---|---|
| Require ID verification at entry | Off |
| Allow walk-ins (no prior invitation required) | On |
| QR code validity window | 1440 minutes (24 hours) |
| Maximum scans per invitation | 1 |

## What you'll see (when the UI ships)

- **Residents** will find an **Invitations** section where they can create, view, and cancel their active invitations.
- **Gate Officers** will see a queue of today's expected visitors for their community, a QR scanner, and a walk-in form.
- **Property Managers / Admins** will have access to the full visitor logbook with entry and exit timestamps, the gate officer who processed each visit, and whether the visitor's ID was verified.

## Visit purposes

When creating an invitation, residents choose one of four visit purposes:

- **Visit** — a personal or social guest.
- **Delivery** — a package or courier.
- **Service** — a maintenance worker, cleaner, or similar technician.
- **Other** — anything that does not fit the above.

## Common issues

- **QR code not accepted at the gate** — check that the invitation status is **Active**. If the status is **Expired**, the resident must create a new invitation.
- **Resident cannot see the Invitations section** — the visitor access screens are being rolled out in stories #258–#264. Contact your Property Manager if the feature is expected to be live.
- **Gate Officer cannot log a visit** — confirm the user has been assigned the **Gate Officers** role by an admin in **Admin → Users**.

## Related

- [Roles and permissions](../admin/roles-and-permissions.md)
