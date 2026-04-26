---
title: Create and send a lease quote
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*A lease quote is a formal, time-limited tenancy offer you send to a prospective resident before a binding lease is created. This guide covers creating a quote, sending it to the prospect, and what they see when they open it.*

## Who this is for
Property Managers and Admins who prepare tenancy offers for prospective residents.

## Before you start
- A unit must exist in the system in **available** status before you can issue a quote for it.
- The prospective resident must have a contact record in **Contacts → Residents**.
- You must have the **Create Lease Quote** permission. Ask your Account Admin if the option is not visible.

## Steps

### Create a quote

1. Go to **Leasing → Quotes**.
2. Click **New Quote**.
3. Fill in the form:
   - **Unit** — select an available unit from the list.
   - **Resident Contact** — pick the prospective resident from the Contacts list.
   - **Contract Type** — select the tenancy contract type.
   - **Duration (months)** — enter the length of the tenancy in months.
   - **Start Date** — the date the lease would begin.
   - **Valid Until** — the deadline by which the prospect must respond. After this date the quote expires automatically overnight.
   - **Financial Terms** — enter the **Rent Amount (SAR)**, **Payment Frequency**, and **Security Deposit (SAR)**.
   - **Additional Charges** (optional) — click **Add Charge**, enter an English label, Arabic label, and amount for each charge (for example, a parking fee).
   - **Special Conditions (EN)** / **الشروط الخاصة (AR)** — enter any extra terms in both languages.
4. Choose how to save:
   - Click **Save as Draft** to save without sending. The quote appears in the **Lease Quotes** list with status **Draft** and no email is dispatched.
   - Click **Send Quote** to save and send in one step. Skip the next section — the quote is delivered immediately.

### Send a draft quote

1. Go to **Leasing → Quotes** and open the quote you want to send.
2. Click **Send** on the quote detail page.
3. Confirm the prompt: *"Are you sure you want to send this quote to the prospect?"*
4. The status changes from **Draft** to **Sent** and the prospect receives an email with a secure preview link.

## What you'll see

After sending, the quote detail page shows status **Sent**. The **Lease Quotes** list shows each quote's number, resident name, unit, rent amount, valid-until date, and current status badge.

The prospect opens the email link and lands on a read-only quote preview page — no login required. As soon as they open it, the status changes to **Viewed** and the list reflects this automatically.

### Quote status lifecycle

| Status | Meaning |
|--------|---------|
| **Draft** (مسودة) | Created and saved, not yet sent. |
| **Sent** (مرسل) | Delivered to the prospect. |
| **Viewed** (تم الاطلاع) | Prospect has opened the preview link. |
| **Accepted** (مقبول) | Prospect agreed to the terms — ready to convert to a lease. |
| **Rejected** (مرفوض) | Prospect declined the offer. |
| **Expired** (منتهي) | The valid-until date passed without a response. |

A quote can only move forward through these statuses. You cannot revert a sent quote to draft. To offer revised terms, create a new quote (story #171 — revise — is coming in a future release).

### Automatic expiry

Every night the platform checks all open quotes (Draft, Sent, Viewed) whose **Valid Until** date has passed and transitions them to **Expired**. This runs automatically with no action required from you.

## Common issues

- **A unit I want is not in the list.** Only units in **available** status appear in the unit picker. Check the unit's status under **Properties → Units**.
- **A quote I sent shows as Expired but the prospect is still considering it.** The valid-until date passed. Create a revised quote with a later date and resend it.
- **I cannot see "Quotes" in the Leasing menu.** Check that your role has the **leases.VIEW** permission. Ask your Account Admin if the option is missing.
- **I want to change terms after sending.** A sent quote cannot be edited. Create a new quote with the revised terms.

## Related
- [Create and search resident contacts](../contacts/create-and-search-residents.md)
