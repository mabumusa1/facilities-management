---
title: Understanding lease quotes
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*A lease quote is a formal, time-limited offer you send to a prospective resident before a binding lease is created. It captures all the terms of the tenancy so the prospect can review and accept or reject them.*

## Who this is for
Property Managers and Admins who prepare tenancy offers for prospective residents.

## Before you start
- A unit must exist in the system before you can issue a quote for it.
- The prospective resident must have a contact record in **Contacts → Residents**.
- You must have the **Create Lease Quote** permission. Ask your Account Admin if the option is not visible.

> **Note:** The screens to create, revise, and convert quotes are being built in upcoming releases (#170 — create, #171 — revise, #172 — convert to lease). This guide explains what a lease quote is and how the status lifecycle works so you are ready when those features ship.

## What is a lease quote?

A lease quote is a draft tenancy offer. It holds all the terms a lease would need — the unit, the prospective resident, the contract type, the duration, the start date, the rent amount, the payment frequency, the security deposit, any additional charges, and any special conditions.

You can send a quote to a prospect, wait for their decision, and — if they accept — convert it directly into a lease with all fields pre-filled. No rekeying is needed.

Each quote has a **quote number** (auto-assigned, format `Q-YYYYMMDD-NNNNN`) and a **valid until** date. If that date passes without an acceptance, the platform automatically marks the quote expired overnight.

## Quote status lifecycle

A quote moves through six statuses. Three are open (the prospect can still act) and three are terminal (the quote is closed).

```
draft ──► sent ──► viewed ──► accepted  (terminal — lease can be created)
                          └──► rejected  (terminal)
 ↓ (any open status, if valid_until passes)
expired                              (terminal)
```

| Status | Arabic | Meaning |
|--------|--------|---------|
| **draft** | مسودة | Quote created and saved but not yet sent to the prospect. |
| **sent** | تم الإرسال | Quote delivered to the prospect. |
| **viewed** | تمت المشاهدة | Prospect has opened the quote. |
| **accepted** | مقبول | Prospect agreed to the terms. The quote is ready to convert to a lease. |
| **rejected** | مرفوض | Prospect declined the offer. |
| **expired** | منتهي الصلاحية | The valid-until date passed before the prospect accepted or rejected. |

Only permitted transitions succeed. You cannot move a quote backwards (for example, from **sent** back to **draft**). To offer new terms, you revise the quote, which creates a new version linked to the original.

## Automatic expiry

Every night the platform checks all open quotes (draft, sent, and viewed) whose **valid until** date is in the past and transitions them to **expired**. This runs automatically — you do not need to do anything. If you want to extend a quote's deadline before it expires, revise the quote with a later valid-until date.

## Revisions and versioning

When you revise a quote (story #171), the system creates a new version rather than overwriting the original. The revision counter increments (v1, v2, v3 ...) and the new version is linked to the original quote. This gives you a full audit trail of every offer made to a prospect.

## Quote-to-lease conversion

When a quote reaches **accepted** status, you can convert it into a lease (story #172). All fields — unit, resident, contract type, duration, start date, rent, payment frequency, security deposit, additional charges, and special conditions — carry over automatically. The resulting lease is linked to the quote so you can always trace a lease back to its offer.

## What you'll see

This feature is part of an upcoming release. When the create-quote screen ships (story #170), it will appear under **Leasing → Quotes**. The quote list will show each quote's number, status badge, prospect name, unit, rent amount, and valid-until date.

## Common issues

- **I cannot see a "Quotes" menu item.** The Leasing → Quotes section is not yet released. It ships in an upcoming update.
- **A quote I sent shows as expired even though the prospect is still considering it.** The valid-until date passed. Revise the quote with a later date and resend it (available in story #171).
- **I want to offer different terms without losing the original offer.** Use the revise action — it creates a new version and keeps the history of all previous offers.

## Related
- [Create and search resident contacts](../contacts/create-and-search-residents.md)
