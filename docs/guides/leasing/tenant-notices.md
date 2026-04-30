---
title: Send a notice to a tenant
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Send a formal, bilingual notice to the tenant on an active lease — rent increase, renewal offer, move-out reminder, or free-form — directly from the lease record, with every notice stored in a permanent, auditable history.*

## Who this is for

Property Managers and Admins with manager-level RBAC scope for the community the lease belongs to. The **Send Notice** button is only available on leases in **Active** status with a linked Resident contact.

## Before you start

- The lease must be in **Active** status. Notices are not accessible on leases in any other status.
- The Resident contact linked to the lease must have a valid email address on file. If the email is missing, the form will not appear — only a warning with a link to the contact record.
- You must have manager-level permission for the lease's community. Users from other communities receive a 403 error.
- Notice delivery records the notice on the lease record. Email dispatch to the resident is enabled when the email integration is configured for your account.

## Steps

### 1. Open the lease detail page

1. Go to **Leasing → Leases** (التأجير ← عقود الإيجار).
2. Open the active lease you want to send a notice for.

### 2. Open the Notices page

1. On the lease detail page, click **Send Notice** (إرسال إشعار) in the top-right action area. The button shows a badge with the count of previously sent notices when one or more exist.
2. The **Send Notice** page opens. The breadcrumb trail shows **Leasing → Leases → \<contract number\> → Notices** (الإشعارات).

### 3. Check the tenant email warning

If the Resident contact linked to this lease has no email address, an amber warning appears:

> **Tenant email address is missing. Update the Resident contact before sending notices.**

Click **Edit Contact** (تعديل جهة الاتصال) to go to the Resident record and add an email address, then return to this page. The notice form appears only after a valid email is on file.

### 4. Select the notice type

Under **Notice Type** (نوع الإشعار), select one of the four options:

| Notice type | When to use |
|---|---|
| **Rent Increase** (زيادة الإيجار) | Notify the tenant of an upcoming change in the rent amount. |
| **Renewal Offer** (عرض تجديد) | Offer the tenant a lease renewal before the current term ends. |
| **Move-Out Reminder** (تذكير بالإخلاء) | Remind the tenant of the upcoming end date and move-out process. |
| **Free-form Notice** (إشعار حر) | Any other formal communication to the tenant. |

Selecting a type is required. If you submit without choosing, the form shows a validation error.

### 5. Write the English notice

Under the **EN** section:

1. In **Subject (English)** (الموضوع (إنجليزي)), type the email subject line in English. This field is required.
2. In **Body (English)** (المحتوى (إنجليزي)), type the full notice body in English. This field is required.

### 6. Write the Arabic notice

Under the **AR** section (rendered right-to-left):

1. In **Subject (Arabic)** (الموضوع (العربية)), type the email subject line in Arabic. This field is required.
2. In **Body (Arabic)** (المحتوى (عربي)), type the full notice body in Arabic. This field is required.

Both sections are required. The notice is delivered bilingual — the tenant receives both the English and Arabic versions in the same communication.

### 7. (Optional) Preview before sending

Once you have filled in the English subject and body, a **Preview** (معاينة) toggle appears. Click it to expand an inline preview showing the English version followed by the Arabic version. Click **Preview** again to collapse it.

### 8. Send the notice

Click **Send Notice** (إرسال الإشعار). The form clears on success and you are returned to the top of the page.

## What you'll see

After the notice is sent:

- The **Notice History** (سجل الإشعارات) card at the bottom of the page updates immediately to include the new entry.
- Each entry in the history shows:
  - A **type badge** (e.g., Rent Increase / زيادة الإيجار) — the notice type you selected.
  - The **sent date and time** — recorded at the moment you clicked Send Notice.
  - The **English subject** — shown as a single truncated line.
  - A **View Body** (عرض المحتوى) link — click to expand and read the full English and Arabic body text inline.
- The **Send Notice** button on the lease detail page shows an updated badge count.
- The notice count displayed in the **Notices** (إرسال إشعار) badge on the lease detail page increments with each sent notice.

The audit trail stored with every notice includes:
- **sent_by** — the user account that clicked Send Notice.
- **sent_at** — the exact timestamp the notice was recorded.
- **type**, **subject_en**, **body_en**, **subject_ar**, **body_ar** — the full notice content as sent.

This information is permanent and cannot be edited or deleted after sending.

## Common issues

- **The Send Notice button does not appear on the lease detail page.** The lease is not in **Active** status, or your role does not have manager-level scope for this community. Check the status badge on the lease. If it is Active and the button is still missing, ask your Account Admin to verify your RBAC permissions.
- **The notice form is not visible — I see only a warning.** The Resident contact linked to this lease has no email address on file. Click **Edit Contact** (تعديل جهة الاتصال) in the warning to update the contact, then return to this page.
- **The Send Notice button is greyed out (processing).** The form is submitting. Wait for the page to reload before clicking again.
- **I see a validation error after clicking Send Notice.** One or more required fields are empty: notice type, English subject, English body, Arabic subject, or Arabic body. Fill in all fields and try again.
- **I need to correct a notice I already sent.** Sent notices are read-only. The notice history is permanent. Send a new notice of the same type with corrected content.
- **The Notice History card shows no entries.** No notices have been sent for this lease yet. The card appears empty until the first notice is recorded.

## Related

- [Amend lease terms with history trail](./lease-amendments.md)
- [Approve or reject a lease application](./approve-or-reject-lease.md)
- [Convert a quote to a lease and upload KYC documents](./convert-and-kyc.md)
