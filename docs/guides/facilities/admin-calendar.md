---
title: Manage facility bookings from the calendar
area: facilities
layout: guide
lang: en
---

# {{ page.title }}

*View all bookings across your community's facilities on a weekly calendar, create admin bookings on behalf of residents, and manage check-ins and cancellations from a single screen.*

## Who this is for

Property Managers and Admins (admin role required). Residents do not have access to this screen.

## Before you start

- At least one active facility must be configured under **Facilities → Settings**.
- You must have the admin role in your account. Cross-tenant access is blocked — you can only see bookings for your own community.

## Steps

### Open the calendar

1. In the sidebar, go to **Facilities → Calendar** (تقويم المرافق).
2. The calendar opens to the current week, showing all facilities and all statuses.

### Filter by facility

3. From the **All Facilities** (جميع المرافق) dropdown at the top left, select a specific facility to narrow the view.
4. The calendar reloads automatically to show only bookings for that facility.

### Navigate weeks

5. Use the **◀** (previous) and **▶** (next) arrow buttons to move one week back or forward. The calendar refreshes without a full page reload.
6. Click **Today** (اليوم) to jump back to the current week.

### Filter by status

7. The tab bar below the controls lets you filter bookings by status:
   - **All** (الكل) — show every booking regardless of status.
   - **Confirmed** (مؤكد) — show only confirmed (booked) slots.
   - **Checked-in** (تم الدخول) — show only bookings where the resident has checked in.
   - **Completed** (مكتمل) — show only completed bookings.
   - **Cancelled** (ملغي) — show only cancelled bookings.

### Read the color legend

Each booking block on the grid is color-coded:

| Color | Status |
|-------|--------|
| Blue | Confirmed |
| Green | Checked-in |
| Amber | Pending (awaiting contract) |
| Gray | Completed |
| Red | Cancelled |

The legend is shown at the bottom of the calendar for reference.

### View booking details

8. Click any colored booking block on the calendar grid.
9. The **Booking Detail** (تفاصيل الحجز) popover opens, showing:
   - Resident name (or "—" if no resident is linked)
   - Facility name
   - Date
   - Start and end time
   - Duration
   - Status badge
10. From the popover you can:
    - Click **Edit** (تعديل) to open the booking edit form (available when the booking can be modified).
    - Click **Check In** (تسجيل دخول) to mark the resident as arrived (available for confirmed bookings).
    - Click **Cancel Booking** (إلغاء الحجز) to cancel the booking (available when the booking is not yet completed or already cancelled).
11. Click the ✕ button or press **Escape** to close the popover without making changes.

### Create an admin booking

Admins can create bookings on behalf of residents or reserve time for community events.

12. Click **+ Create Booking** (إنشاء حجز) in the top-right area of the controls row.
    Alternatively, click an empty time slot directly on the calendar grid — the form opens with the date and time pre-filled.
13. The **Create Booking** (إنشاء حجز) modal opens. Fill in the fields:
    - **Facility** — required. Select the facility from the dropdown.
    - **Date** — required. Choose the booking date.
    - **Start** — required. Set the start time (15-minute increments).
    - **End** — required. Set the end time (15-minute increments).
    - **Resident** (optional) — search by name or unit number. Leave blank to create an admin reservation with no linked resident.
    - **Notes** (ملاحظات) — optional free text for internal reference.
14. Click **Create** (إنشاء).
    - If the booking is created successfully, it appears on the calendar immediately in the Confirmed (blue) state.
    - If the time slot is already taken, an **Overlap Detected** (تم اكتشاف تعارض) error banner appears. Adjust the start or end time and try again.
15. Click **Cancel** (إلغاء) or press **Escape** to close the modal without saving.

## What you'll see

After creating a booking, the new block appears on the calendar grid at the correct day and hour, color-coded blue (Confirmed). If you reload the page, the booking persists. Booking blocks show the resident's name (or "Admin Reservation" for admin-created bookings without a linked resident), the start–end time, and the status label.

## Common issues

- **Overlap Detected error** — The time slot you chose conflicts with an existing booking for the same facility. Use the popover on the conflicting block to check the times, then adjust your start or end time before trying again.
- **Facility not in the dropdown** — The facility may be inactive. Go to **Facilities → Settings** and verify the facility is enabled.
- **Can't see bookings from another community** — Access is scoped to your own account. You cannot view or manage bookings from other tenants.
- **Check In button not showing** — The Check In action is only available for bookings in Confirmed status that have not yet been checked in.
- **Calendar shows a loading spinner indefinitely** — Check your internet connection and reload the page. If the issue persists, contact your system administrator.

## Related

- [Configure a facility](./configure-facility.md)
- [Book a facility (resident guide)](./book-a-facility.md)
- [Facility availability rules and waitlist](./availability-and-waitlist.md)
