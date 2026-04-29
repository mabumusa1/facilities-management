---
title: View a contact's activity history
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*See a chronological feed of every action linked to a contact — leases, payments, service requests, bookings, and more — without leaving the contact record.*

## Who this is for
Property Managers and Admins who need a full picture of a resident's, owner's, or professional's interactions across all modules before answering a query or resolving a dispute.

## Before you start
- The contact record must already exist.
- Activity is drawn from linked records in Leasing, Accounting, Service Requests, and Facilities. A newly created contact with no linked records will show an empty activity feed — that is expected behaviour, not an error.

## Steps

### Open the activity feed

1. In the main navigation, go to **Contacts** (جهات الاتصال) and open the contact record (Resident, Owner, or Professional).
2. On the contact detail page, click the **Activity** (النشاط) tab.

The feed loads the 20 most recent events in reverse-chronological order (newest first).

### Read an activity entry

Each entry shows:
- **Domain label** — the module that generated the event (for example, **Lease** (إيجار), **Invoice** (فاتورة), **Service Request** (طلب خدمة), **Booking** (حجز)).
- **Event description** — a short description of what happened.
- **Date and time** of the event.
- **Link to the source record** — click it to jump directly to the lease, invoice, request, or booking.

### Load older events

If the contact has more than 20 events, a **Load more** (تحميل المزيد) button appears at the bottom of the feed. Click it to load the next page of events.

### Refresh the feed

The activity feed does not update in real time. To see events added since you opened the page, click **Refresh** (تحديث) or reload the page.

## What you'll see

When a contact has linked records across multiple modules, the feed shows them interleaved in a single timeline. Each domain event is colour-coded with its label so you can scan quickly by type.

When a contact has no linked records, the feed shows: **No activity recorded yet.** (لا يوجد نشاط مسجل بعد.)

## Common issues

- **Activity tab is missing** — your role may not have access to activity history. Contact your Account Admin.
- **Feed shows events from only one module** — this is normal if the contact has only interacted with that module so far. The feed is populated progressively as events are created.
- **"Load more" does not appear** — the contact has 20 or fewer events. All events are already visible.
- **Link to source record returns a 404** — the linked record may have been deleted. The activity entry remains for audit purposes even when the source record no longer exists.

## Related

- [Create and search resident contacts](./create-and-search-residents.md)
- [Archive, merge, and reactivate contacts](./archive-merge-reactivate-contacts.md)
