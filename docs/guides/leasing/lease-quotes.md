---
title: Create, send, and revise lease quotes
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*A lease quote is a formal, time-limited tenancy offer you send to a prospective resident before a binding lease is created. This guide covers creating a quote, sending it, and managing it after it is sent — including revising terms, rejecting a quote, and how automatic expiry works.*

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

### Revise a quote

You can revise any quote that is in **Draft**, **Sent**, or **Viewed** status. Revising creates a new version — the original is preserved in full.

1. Open the quote from **Leasing → Quotes**.
2. Click **Revise** (مراجعة) on the quote detail page.
3. The revision form opens with all current values pre-filled. Fields that have changed since the previous version are marked with a **Changed** badge; unchanged fields show **Unchanged**.
4. Edit the fields you need to update — rent amount, valid-until date, special conditions, or any other term.
5. Add a **Revision Note** to explain the change to your own records (optional).
6. Choose an **Email Subject Prefix** to signal to the prospect that this is a revised offer (for example, "Updated Quote").
7. Click **Save Revision**.

The platform creates a new version of the quote, resets its status to **Sent**, and sends the prospect a fresh email. The **Revision History** sidebar on the detail page lists every version in descending order; click any entry to view that version's terms.

> Accepted and expired quotes are read-only. If an accepted quote needs correction, contact your Account Admin. If a quote has expired, create a new quote instead.

### Reject a quote

Reject a quote when you want to withdraw the offer before the prospect responds.

1. Open the quote from **Leasing → Quotes**.
2. Click **Reject** (رفض) on the detail page. This action is available for quotes in **Sent** or **Viewed** status.
3. In the confirmation dialog, enter a **Rejection Reason** (required, 2 000 characters max).
4. Confirm the rejection.

The status moves to **Rejected** immediately. No further emails are sent to the prospect for this quote.

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
| **Rejected** (مرفوض) | Offer withdrawn by the manager, or declined by the prospect. |
| **Expired** (منتهي) | The valid-until date passed without a response. |

Accepted, Rejected, and Expired statuses are terminal — no further actions are available from those states.

### Automatic expiry

Every night the platform checks all open quotes (Draft, Sent, Viewed) whose **Valid Until** date has passed and transitions them to **Expired**. This runs automatically with no action required from you.

Admins can also manually expire a quote from its detail page using the **Expire** action — for example, to clean up a quote where the unit has already been leased through another channel.

### Revision history

The **Revision History** panel on the right side of the quote detail page lists all versions of a quote in version-descending order (newest first). Each entry shows the version number, the date it was created, and who created it. Click any entry to open that version's detail page.

## Common issues

- **A unit I want is not in the list.** Only units in **available** status appear in the unit picker. Check the unit's status under **Properties → Units**.
- **The Revise button is not visible.** Revise is only available for Draft, Sent, and Viewed quotes. If the quote is Accepted, Rejected, or Expired the button does not appear — those states are final.
- **The Reject button is not visible.** Reject is only available for Sent and Viewed quotes.
- **I cannot see "Quotes" in the Leasing menu.** Check that your role has the **leases.VIEW** permission. Ask your Account Admin if the option is missing.
- **A quote I sent shows as Expired but the prospect is still considering it.** The valid-until date passed. Revise the quote with a later valid-until date to reopen negotiations.

## Related
- [Create and search resident contacts](../contacts/create-and-search-residents.md)
