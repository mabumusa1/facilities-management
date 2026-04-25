---
title: Facility availability and waitlist
area: facilities
layout: guide
lang: en
---

# {{ page.title }}

*Understand how the platform controls when a facility is open for booking and how residents queue for a full slot.*

## Who this is for

- **Property Managers / Admins** — set opening hours per facility.
- **Residents** — join a waitlist when a booking slot is already taken.

## Before you start

- You need the **Manage Facilities** permission to configure availability rules. Ask your Account Admin if the settings are not visible.
- Residents need an active resident account to join the waitlist.

## Availability rules

Each facility can have a separate opening window for each day of the week. For example, the Gym might be open Monday to Friday 06:00–22:00 and closed on Sunday.

### How the schedule works

- Opening hours are set per day of the week (Sunday through Saturday).
- Each day has an **open time** and a **close time**, plus a **slot duration** (the length of one bookable block) and a **maximum concurrent bookings** limit.
- Days without an availability rule are treated as closed — residents cannot book on those days.
- By default, a sample Gym facility is seeded with Monday–Saturday availability (06:00–22:00) so you can test bookings immediately after installation.

### What admins will configure (upcoming — Facilities configuration story #247)

The full availability editor is coming in a future release. When it ships, Property Managers will be able to:

1. Go to **Facilities → [Facility name] → Availability**.
2. Toggle each day of the week on or off.
3. Set the **Opening time** and **Closing time** for each active day.
4. Set the **Slot duration** in minutes (for example, 60 minutes per session).
5. Set the **Maximum concurrent bookings** — how many residents can book the same slot at the same time.
6. Save changes.

## Waitlist

When every slot in a time window is already booked, residents can join the waitlist for that slot. The platform holds their place in a first-in, first-out (FIFO) queue.

### How the waitlist works

- A resident's waitlist entry is tied to a specific facility, a specific start time, and a specific end time.
- If a confirmed booking is cancelled, the platform checks the waitlist for that slot and notifies the first resident in the queue.
- Each waitlist entry has a **time-to-live** (TTL). If a resident does not confirm within the TTL window after being notified, their spot is released and the next resident in the queue is notified.
- A resident can hold at most one waitlist entry per slot (the same facility + same start time + same end time).

### What residents will see (upcoming — Resident booking story #248)

The full booking and waitlist UI is coming in a future release. When it ships, residents will be able to:

1. Go to **Facilities → [Facility name]**.
2. Select a date and a time slot.
3. If the slot is full, a **Join Waitlist** button will appear instead of **Book Now**.
4. Tap **Join Waitlist** to take a numbered place in the queue.
5. Receive a notification if the slot opens up and confirm within the TTL window to secure the booking.

## What you'll see

These features are not yet visible in the interface — the booking and facility configuration UI ships in upcoming stories (#247 and #248). However, the underlying availability rules and waitlist are in place. The sample Gym facility is available in the system with Monday–Saturday 06:00–22:00 opening hours so development and testing can proceed.

## Common issues

- **The facility shows as closed on a day you expect it to be open** — check that an availability rule has been created for that day. Days without a rule are closed by default.
- **A resident cannot join the waitlist** — each resident can have only one waitlist entry per slot. If they already have an entry for that exact slot, the option will not appear again.
- **Waitlist notification was not received** — the notification system will be configured as part of the resident notification story. Check back after that release.

## Related

- [Book a facility (coming soon)](./book-a-facility.md)
- [Configure a facility (coming soon)](./configure-a-facility.md)
