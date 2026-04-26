---
title: Convert a quote to a lease and upload KYC documents
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Once a prospect accepts a lease quote, convert it into a binding lease application in seconds — all terms pre-fill automatically — then attach the required identity and income documents before submitting for approval.*

## Who this is for
Property Managers and Admins with the **Convert Lease Quote**, **Upload KYC Documents**, and **Submit for Approval** permissions.

## Before you start
- The quote must be in **Accepted** status. The **Convert to Lease Application** button is only visible on accepted quotes.
- KYC document types must be configured in **App Settings → KYC Documents**. Your account starts with five required types (National ID / Iqama, Passport Copy, Employment Letter, Bank Statement, Tenancy History) and two optional types (Previous Lease Agreement, Family Book). An admin can add or deactivate types at any time.

## Steps

### 1. Open the Convert form

1. Go to **Leasing → Quotes**.
2. Open an accepted quote (status badge shows **Accepted**).
3. Click **Convert to Lease Application** on the quote detail page.
4. The Convert form opens pre-filled with the quote's unit, resident contact, contract type, duration, start date, rent amount, payment frequency, security deposit, and additional charges.

### 2. Review and adjust the lease terms

- A banner at the top identifies the source: **Source: Quote #[number] (Accepted)**.
- If you change any field, a **Changes detected from quote** notice appears so you know which values diverge from the original quote.
- Toggle **Auto-generate contract number** if you want the platform to assign the contract number for you.
- Edit any field as needed — the quote is your starting point, not a lock.

### 3. Save and go to KYC

Click **Save & Go to KYC** to create the lease and proceed directly to the document checklist. The new lease is created in **Pending Application** status and the quote is marked as converted (you cannot convert the same quote twice — clicking Convert again redirects to the existing lease's KYC page).

If you are not ready to upload documents yet, click **Save as Draft** instead. You can return to the KYC page from the lease detail page later.

### 4. Upload KYC documents

The KYC page shows a progress bar: **N/Total required** reflects how many required document slots you have filled.

The checklist has two sections:

- **Required Documents** — National ID / Iqama, Passport Copy, Employment Letter, Bank Statement (3 months), and Tenancy History must all be uploaded before you can submit. A red note appears under any unfilled required slot.
- **Optional Documents** — Previous Lease Agreement and Family Book; uploading these is encouraged but does not block submission.

For each document type:

1. Click **Upload** next to the document type name.
2. Select the file from your device.
3. The progress bar updates immediately once the upload completes. A green checkmark replaces the empty circle on the document row.
4. To replace or remove a document, click **Remove** on that row and upload a fresh file.

### 5. Submit for approval

When every required document is uploaded, the **Submit for Approval** button becomes active. Click it to advance the lease to the next status and hand it off to the approval workflow.

If any required documents are still missing, the button is disabled and a banner lists the missing items: for example, *"2 required document(s) missing: Employment Letter, Bank Statement (3 months)"*.

## What you'll see

After submission the lease status changes from **Pending Application** to the next stage in your approval workflow. The KYC page remains accessible so you can view uploaded files, but upload and remove actions are no longer available once the lease moves past Pending Application.

### KYC document types (defaults)

| Document | Required? |
|----------|-----------|
| National ID / Iqama | Yes |
| Passport Copy | Yes |
| Employment Letter | Yes |
| Bank Statement (3 months) | Yes |
| Tenancy History | Yes |
| Previous Lease Agreement | No |
| Family Book | No |

## Common issues

- **Convert to Lease Application is not visible.** The button only appears on quotes in **Accepted** status. Check the quote's current status badge.
- **I clicked Convert but was redirected to an existing lease.** This quote was already converted. The platform enforces one lease per quote — you were redirected to the existing lease's KYC page.
- **No document types appear on the KYC page.** Your account has no active KYC document types configured. Go to **App Settings → KYC Documents** and add or activate the required types.
- **Submit for Approval is greyed out.** One or more required documents are missing. The banner above the button lists which ones.
- **I cannot see Convert to Lease Application.** Check that your role has the **leases.CREATE** permission (which gates the Convert ability). Ask your Account Admin if the button is missing.

## Related
- [Create and send a lease quote](./lease-quotes.md)
- [Approve or reject a lease application](./approve-or-reject-lease.md)
