---
title: Approve or reject a lease application
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Review a submitted lease application, check the KYC document checklist, and record an approval or rejection decision — all from the lease detail page.*

## Who this is for
Property Managers and Admins with a manager-level role that has scope covering the relevant community. The **Approve** and **Reject** buttons are only visible to users with this scope.

## Before you start
- The lease must be in **Pending Application** status. The approval panel does not appear for leases in any other status.
- All required KYC documents must already be uploaded by the submitting manager (see [Convert a quote to a lease and upload KYC documents](./convert-and-kyc.md)).
- You must have manager-level RBAC scope for the community the lease belongs to. Managers from other communities cannot approve or reject leases outside their scope.

## Steps

### 1. Open the lease detail page

1. Go to **Leasing → Leases**.
2. Find the lease in **Pending Application** status and open it.

### 2. Review the application

On the lease detail page you will see a highlighted panel with the heading **This application is pending your review.** (هذا الطلب في انتظار مراجعتك.) The panel appears only for managers with approval scope.

Review the lease terms shown on the page and check that the KYC document checklist shows all required documents as uploaded.

### 3. Approve the application

1. In the pending review panel, click **Approve** (موافقة).
2. The lease transitions immediately to **Approved** status.
3. The platform records your identity and the approval timestamp, and notifies the manager who submitted the application.

### 4. Reject the application

1. In the pending review panel, click **Reject** (رفض).
2. A dialog opens with the title **Reject Lease Application?** (رفض طلب الإيجار؟).
3. Read the note: rejecting will notify the submitting manager; the linked quote stays in **Accepted** status and is not reversed.
4. Enter a reason in the **Reason for rejection** (سبب الرفض) field. The reason must be at least 10 characters. The field accepts up to 2 000 characters.
5. Click **Confirm Reject** (تأكيد الرفض).
6. The lease transitions to **Rejected** status with the reason recorded. The submitting manager is notified. To return without rejecting, click **Cancel** (إلغاء) or close the dialog.

## What you'll see

After a decision is recorded, the **Approval Timeline** (الجدول الزمني للموافقة) card appears on the lease detail page regardless of which manager is viewing. It shows:

- For approvals: a green **Approved** (تمت الموافقة) badge, the approver's name, and the approval timestamp.
- For rejections: a red **Rejected** (مرفوض) badge, the rejector's name, the rejection timestamp, and the rejection reason in italics below.

The timeline is read-only and cannot be edited.

## Common issues

- **The approval panel is not visible.** Either the lease is not in **Pending Application** status, or your role does not have manager-level scope for this community. Check the lease status badge. If the status is correct, ask your Account Admin to verify your community assignment.
- **The Approve and Reject buttons are greyed out or missing.** Your account does not have the required RBAC permission for this lease's community. Attempting the action directly returns a 403 error. Contact your Account Admin.
- **The Confirm Reject button is disabled.** The rejection reason is fewer than 10 characters. Add more detail before confirming.
- **I can see the Approval Timeline but not the approval panel.** The lease has already been approved or rejected. The approval panel only appears for leases still in Pending Application status.

## Related
- [Convert a quote to a lease and upload KYC documents](./convert-and-kyc.md)
- [Create and send a lease quote](./lease-quotes.md)
