---
title: Generate and track lease renewal offers
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Create a renewal offer from an expiring lease, send it to the tenant, record their decision, and convert an accepted offer into a new lease — all from within the platform.*

## Who this is for
Property Managers and Admins who manage active leases and need to track renewal decisions in the platform. The **Generate Renewal Offer** (إنشاء عرض تجديد) button is only available to users with the leasing-management scope for the relevant community.

## Before you start
- The lease must be in **Active** status and within **90 days** of its end date. The renewal banner does not appear outside this window.
- You must have manager-level RBAC scope for the community the lease belongs to.
- The tenant's contact record must exist in the system so the notification email can be sent.

## Steps

### 1. Open the lease detail page

1. Go to **Leasing → Leases**.
2. Find the active lease that is expiring within 90 days and open it.

A blue **Renewal** banner appears near the top of the page. It displays a countdown: **Lease expires in {N} days** (تنتهي صلاحية العقد خلال {N} يوم) together with the end date.

### 2. Generate the renewal offer

1. In the renewal banner, click **Generate Renewal Offer** (إنشاء عرض تجديد).
2. The **Generate Renewal Offer** page opens with a **Source Lease** summary card showing the tenant name, unit(s), current term dates, and current rent.
3. Review the pre-filled **Renewal Terms** fields:
   - **New Start Date** (تاريخ البدء الجديد) — automatically set to the day after the lease end date.
   - **Duration (months)** — defaults to 12 months. Change it if the new term differs.
   - **New Rent Amount (SAR)** (قيمة الإيجار الجديدة (ر.س)) — pre-filled with the current rent. Adjust upward or downward as needed. A difference indicator below the field shows how much the new amount is above or below the current rent.
   - **Payment Frequency** — carried over from the current lease.
   - **Contract Type** — carried over from the current lease. Select a different type from the dropdown if needed.
   - **Valid Until** (صالح حتى) — defaults to 30 days before the lease end date. This is the deadline by which the tenant must respond before the offer expires automatically.
4. Optionally type a personal message to the tenant in the **Message to Tenant (English)** and **Message to Tenant (Arabic)** (رسالة للمستأجر) text areas.
5. Click **Save as Draft** (حفظ كمسودة) to save the offer without sending it, or proceed directly to step 3 to send it.

### 3. Send the offer to the tenant

After saving as draft, you are returned to the lease detail page. The renewal banner now shows the offer in **Draft** status.

1. In the renewal banner, click **Renewal Offer: Sent** (عرض التجديد: مرسل) to dispatch the offer.
2. The tenant receives an email with the renewal terms and the offer transitions to **Sent** status.

The banner updates to show the offer amount, the valid-until date, and two action buttons: **View Offer** (عرض العرض) and **Record Decision** (تسجيل القرار).

### 4. Record the tenant's decision

Once the tenant responds — by phone, in person, or via email outside the platform — record their answer:

1. In the renewal banner, click **Record Decision** (تسجيل القرار).
2. A dialog opens titled **Record Renewal Decision** (تسجيل قرار التجديد).
3. The current offer terms (rent amount and valid-until date) are shown for reference.
4. Select one of the two options:
   - **Accepted** (مقبول)
   - **Declined** (مرفوض)
5. Click **Record Decision**.

The offer status updates immediately on the lease detail page.

### 5a. After acceptance — convert to a new lease

If you selected **Accepted**, the renewal banner shows an **Accepted** green badge and a **Convert to New Lease** (تحويل إلى عقد جديد) button.

1. Click **Convert to New Lease** (تحويل إلى عقد جديد).
2. You are taken to the new lease creation form, pre-filled with the renewal terms.
3. Complete the form following the same steps as creating any new lease. The renewal offer is linked to the resulting lease automatically.

### 5b. After decline — no further action required

If you selected **Declined**, the offer moves to **Rejected** status. The original lease is not affected. You may create a new offer if negotiations continue — click **Generate Renewal Offer** again in the banner.

### What happens when a renewal offer expires automatically

If the valid-until date passes with no decision recorded, a scheduled job transitions the offer to **Expired** status overnight. The renewal banner shows an **Expired** badge and a new **Generate Renewal Offer** button so you can start fresh.

The original lease is never automatically affected when an offer expires.

## What you'll see

### Renewal Offers index

Go to **Leasing → Renewal Offers** (عروض التجديد) to see all renewal offers across your portfolio in one table. Columns show: lease number, tenant name, unit, renewal amount, status, and valid-until date.

Use the **Search** field to filter by lease contract number and the **Status** dropdown to narrow by offer state (Draft / Sent / Accepted / Rejected / Expired).

The count at the bottom of the list shows the total number of offers currently in the pipeline.

### Status progression

| Status | Meaning |
|--------|---------|
| Draft | Offer saved but not yet sent to the tenant |
| Sent | Offer emailed to the tenant; awaiting response |
| Viewed | Tenant opened the offer link in the email |
| Accepted | Tenant accepted; ready to convert to a new lease |
| Rejected / Declined | Tenant declined; original lease unaffected |
| Expired | Valid-until date passed; offer closed automatically |

## Common issues

- **The renewal banner does not appear.** Either the lease is not in Active status, or it is more than 90 days from its end date. Check the lease status badge and end date. Renewals are only triggered within the 90-day window.
- **The Generate Renewal Offer button is not visible.** Your role does not have the required permission for this community. Contact your Account Admin to verify your scope assignment.
- **The Valid Until date is in the past.** The form will not save an offer with a past expiry date. Update the Valid Until field to a future date before saving.
- **The Convert to New Lease button leads to an empty form.** The new lease creation form is pre-filled from the renewal offer, but all fields remain editable. If values look blank, check that the source lease had a complete rent and contract type before the offer was created.
- **The offer moved to Expired before I recorded a decision.** Create a new offer using **Generate Renewal Offer** in the banner. The expired offer remains in the Renewal Offers index for audit purposes.

## Related
- [Create and send a lease quote](./lease-quotes.md)
- [Approve or reject a lease application](./approve-or-reject-lease.md)
- [Convert a quote to a lease and upload KYC documents](./convert-and-kyc.md)
