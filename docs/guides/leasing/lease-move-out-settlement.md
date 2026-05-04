---
title: Finalize a move-out — settlement, refund, and unit release
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Review the financial breakdown, process the refund or outstanding charge, close the lease, and release the unit back to available — all in one coordinated action.*

## Who this is for
Property Managers and Admins with `leases.UPDATE` permission on the relevant lease. Residents do not have access to this workflow.

## Before you start
- The move-out must have an inspection and deductions recorded. The **Proceed to Settlement** (الانتقال للتسوية) link appears on the **Deposit Deductions** page only after deductions have been saved.
- Only one active move-out can exist per lease. You cannot initiate a new move-out while one is in progress.
- The settlement action is final and cannot be undone. Review all figures carefully before proceeding.

## Steps

### 1. Proceed to Settlement

1. Go to **Leasing → Leases** and open the lease that is in move-out.
2. Click the **Inspection** (المعاينة) button on the lease detail page, then click **Proceed to Deductions** (الانتقال للخصومات).
3. On the **Deposit Deductions** (خصومات التأمين) page, click **Proceed to Settlement** (الانتقال للتسوية).

The **Finalize Settlement** (إنهاء التسوية) page opens.

### 2. Review the settlement summary

The page shows three cards:

**Settlement Summary card** — lease number, move-out date, tenant name, and the units involved.

**Deposit Deductions card** — a table of every recorded deduction with its label and amount, followed by a calculation summary:

| Line | Description |
|------|-------------|
| **Security Deposit** (التأمين) | The deposit amount on the lease |
| **Total Deductions** (إجمالي الخصومات) | Sum of all deduction amounts (shown in red) |
| **Refund to Tenant** (استرداد للمستأجر) | Deposit minus deductions — shown when the deposit exceeds deductions |
| **Outstanding Charge** (المبلغ المستحق) | Deductions minus deposit — shown in red when deductions exceed the deposit |
| **No Balance** (لا يوجد رصيد) | Shown when deductions equal the deposit exactly |

**Actions on Finalize** (الإجراءات عند الإنهاء) card — a checklist of what will happen when you confirm:

- **Create refund transaction (SAR _amount_) in Accounting** (إنشاء معاملة استرداد (_amount_ ر.س) في المحاسبة) — a Money-Out transaction linked to the resident (shown only when a refund is due).
- **Create charge transaction (SAR _amount_) in Accounting** (إنشاء معاملة مديونية (_amount_ ر.س) في المحاسبة) — a Money-In transaction linked to the resident (shown only when an outstanding charge exists).
- **Void remaining future schedule entries** (إلغاء إدخالات الجدول المستقبلية) — any unpaid future payment schedules on the lease are voided.
- **Lease status → Terminated** (حالة العقد → منتهي) — the lease transitions to terminated status.
- **Unit _name_ status → Available in Properties** (حالة الوحدة _name_ → متاحة) — each unit is released and becomes available for a new tenant.

### 3. Finalize the move-out

1. Click **Finalize Move-Out** (إنهاء الإخلاء) — the green button at the bottom of the page.
2. A confirmation dialog opens with the title **Finalize Move-Out?** (إنهاء الإخلاء؟) and the warning *This action cannot be undone.* (لا يمكن التراجع عن هذا الإجراء.)
3. The dialog repeats the list of actions that will take place.
4. (Optional) Check the **Generate settlement statement** (إنشاء بيان تسوية) checkbox if you want to view a detailed statement after finalization.
5. Click **Confirm & Finalize** (تأكيد وإنهاء).

If you checked the statement checkbox, you are redirected to the **Settlement Statement** page. Otherwise, you are returned to the lease detail page.

### 4. View the settlement statement

The **Settlement Statement** (بيان التسوية) page shows the full financial breakdown in both English and Arabic, with:

- Lease number, units, tenant name, move-out date, and settlement date.
- A table listing the security deposit, each deduction with its amount, and the net result (refund or charge).
- A **Download PDF** (تحميل PDF) button that opens the browser's print dialog.

After the move-out is finalized, the lease detail page shows:

- A **Move-Out Completed** (تم الإخلاء) badge.
- The settlement date.
- A **View Settlement Statement** (عرض بيان التسوية) button to revisit the statement.

## What you'll see

After finalization:

- The lease transitions to **Terminated** status. It is no longer editable.
- All units attached to the lease are released to **Available** status in Properties, with a status history entry recording the release.
- A refund **Transaction** (direction: Money-Out) or charge **Transaction** (direction: Money-In) is created in Accounting, linked to the resident contact.
- Any unpaid future payment schedules on the lease are voided.
- The move-out record is marked as completed with a settlement timestamp.

## Common issues

- **The Proceed to Settlement link is not visible.** You have not saved your deductions yet. Click **Save Deductions** (حفظ الخصومات) on the Deposit Deductions page first.
- **The Finalize Move-Out button does nothing.** The move-out may already be completed. Check the lease detail page — if you see the **Move-Out Completed** badge, you are viewing an already-settled record.
- **I finalized by mistake.** The settlement action cannot be undone. If a transaction was created incorrectly, you must correct it in Accounting manually. Contact your Account Admin for assistance.
- **The Download PDF button prints instead of saving a file.** This is intentional — it opens the browser's print dialog, from which you can save as PDF.

## Related
- [Process a move-out — inspection and deposit deductions](./lease-move-out.md)
- [Approve or reject a lease application](./approve-or-reject-lease.md)
- [Convert a quote to a lease and upload KYC documents](./convert-and-kyc.md)
