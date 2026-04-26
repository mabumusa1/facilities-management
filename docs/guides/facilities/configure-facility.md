---
title: Configure a facility
area: facilities
layout: guide
lang: en
---

# {{ page.title }}

*Create, edit, view, and deactivate bookable facilities — including the 7-day availability grid, pricing, and booking constraints.*

## Who this is for

**Property Managers / Admins** with the `facilities.CREATE` or `facilities.UPDATE` permission. Users without these permissions receive a 403 error when trying to access the create or edit forms.

## Before you start

- Confirm you have been assigned a role with `facilities.CREATE` (to add a facility) or `facilities.UPDATE` (to edit one). Ask your Account Admin if unsure.
- At least one community and one facility category must exist in the system before you can create a facility.

## Create a facility

1. Go to **Facilities** in the main navigation.
2. Click **New Facility** in the top-right corner.
3. Fill in **Facility Name (English)** and **Facility Name (Arabic)** — both are required.
4. Select a **Community** and a **Category** (for example, Gym, Pool, Hall, Court, or Other).
5. Optionally enter a **Capacity** (the maximum number of people allowed at once).
6. Choose a **Pricing** mode:
   - **Free** — no charge to book.
   - **Per Session** — a flat fee per booking. Enter the **Price** and **Currency** (default: SAR).
   - **Per Hour** — a rate charged per hour of use. Enter the **Price** and **Currency**.
7. Set **Booking Constraints** (see the table below).
8. Tick **Contract required** if residents must sign a contract before their booking is confirmed.
9. Configure the **Availability Rules** grid — one row per day of the week (see below).
10. Add any internal **Notes** (not shown to residents).
11. Click **Create Facility**.

A success toast confirms the facility was created and its status is Active.

### Booking Constraints reference

| Field | Default | What it controls |
|---|---|---|
| Booking opens (days ahead) | 14 | How many days in advance a resident may book |
| Cancellation deadline (hours before) | 2 | Minimum hours before a slot that a resident can cancel |
| Minimum duration (min) | 30 | Shortest bookable session length |
| Maximum duration (min) | — | Longest bookable session (leave blank for no limit) |

### Availability Rules grid

The grid has one row for each day of the week (Sunday through Saturday). For each day:

- Tick the **Active** checkbox to open that day for bookings. Untick to close it.
- Set **Opens** and **Closes** — the time range residents can book within.
- Choose **Slot Duration** in minutes (15 / 30 / 45 / 60 / 90 / 120).
- Enter **Max concurrent bookings** — how many overlapping bookings the system allows for the same slot.

**Defaults when you open the Create form:**

| Day | Active | Opens | Closes |
|---|---|---|---|
| Sunday | No | 06:00 | 22:00 |
| Monday | Yes | 06:00 | 22:00 |
| Tuesday | Yes | 06:00 | 22:00 |
| Wednesday | Yes | 06:00 | 22:00 |
| Thursday | Yes | 06:00 | 22:00 |
| Friday | No | 06:00 | 22:00 |
| Saturday | Yes | 08:00 | 20:00 |

Days left inactive are treated as closed — residents cannot book on those days.

## Edit a facility

1. Go to **Facilities** and click the facility name to open its detail page.
2. Click **Edit** (top-right).
3. Change any field — the form is pre-filled with the current values.
4. Click **Update** (or **Reactivate & Save** if the facility is currently inactive) to save.

Existing confirmed bookings are not automatically cancelled when you change availability rules or pricing.

### Deactivate a facility

If the facility has upcoming confirmed bookings, a yellow banner appears at the top of the edit form:

> "This facility is active — N upcoming bookings"

To deactivate:

1. Click **Deactivate Facility** in the yellow banner.
2. A confirmation dialog appears showing how many existing bookings remain valid.
3. Click **Deactivate Facility** in the dialog to confirm. Click **Cancel** to go back.

After deactivation:
- The facility no longer appears in resident discovery or booking flows.
- No new bookings can be created.
- Existing confirmed bookings remain valid and must be resolved manually.

To reactivate a deactivated facility, open the edit form and click **Reactivate & Save**.

**Note:** Attempting to set a facility inactive without going through the confirmation flow is blocked by the backend — a direct API call without the explicit confirmation flag returns a validation error.

## View a facility

Click a facility name from the **Facilities** list to open its read-only detail page. The page shows:

- **Status** badge — Active or Inactive.
- **Capacity** — maximum occupancy.
- **Pricing** — mode and amount (for example, "Per Session · SAR 50").
- **Bookings** — count of upcoming confirmed bookings.
- **Availability** — active days with open time, close time, and slot duration.
- **Booking Constraints** — the four constraint values in a summary card.
- **Notes** — internal notes, if any.

## What you'll see

After saving, the **Facilities** list updates immediately. The new or edited facility appears with its name, community, category, and Active status. Residents see the facility in the booking discovery view once it is active and has at least one active availability rule.

## Common issues

- **The Create Facility button is missing** — your role does not include `facilities.CREATE`. Contact your Account Admin.
- **Form shows a 403 error on submit** — the permission was revoked between opening the form and submitting. Refresh the page; if the error persists, contact your Account Admin.
- **Deactivation is rejected even after clicking Deactivate Facility in the dialog** — this can happen if the page session expired. Refresh and try again.
- **A day shows as closed even though it is ticked Active** — check that the **Opens** and **Closes** times are valid (close must be after open).
- **Price and Currency fields are not showing** — select **Per Session** or **Per Hour** as the Pricing mode; the fields only appear when a paid mode is selected.

## Related

- [Facility availability and waitlist](./availability-and-waitlist.md)
- [Book a facility (coming soon)](./book-a-facility.md)
