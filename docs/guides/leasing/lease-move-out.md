---
title: Process a move-out — inspection and deposit deductions
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Record a tenant's departure, inspect each room, and calculate the deposit refund or outstanding charge — all from the lease detail page.*

## Who this is for
Property Managers and Admins with update permission on the relevant lease. Residents do not have access to this workflow.

## Before you start
- The lease must be in **Active** status. The **Initiate Move-Out** (بدء الإخلاء) action does not appear for leases in any other state.
- Only one active move-out can be open per lease at a time. If a move-out was already initiated, you are redirected to the existing move-out record.
- The security deposit amount on the lease is used as the baseline for the deduction calculation. Make sure it is correct before you begin.

## Steps

### 1. Initiate the move-out

1. Go to **Leasing → Leases** and open the lease.
2. Click **Initiate Move-Out** (بدء الإخلاء) on the lease detail page.
3. The **Initiate Move-Out** page shows a summary sidebar with the tenant name, unit, lease end date, and security deposit amount.
4. Set the **Move-Out Date** (تاريخ الإخلاء). The field defaults to the lease end date; change it if the tenant is leaving on a different day.
5. Select the **Reason** (السبب):
   - **End of lease term** (نهاية مدة العقد)
   - **Early termination by tenant** (إنهاء مبكر من المستأجر)
   - **Early termination by management** (إنهاء مبكر من الإدارة)
   - **Other** (آخر)
6. Add any optional **Notes** (ملاحظات) for internal reference.
7. Read the amber warning: *Unit status will change to Under Maintenance. Financial terms will remain locked.* (ستتغير حالة الوحدة إلى تحت الصيانة. ستبقى الشروط المالية مقفلة.) Click **Initiate Move-Out** (بدء الإخلاء) to confirm.

After this step, the lease transitions to **Move-Out In Progress** status and the unit is placed in **Under Maintenance** status automatically. You are taken directly to the Inspection page.

### 2. Inspect each room

The **Inspection** (المعاينة) page lets you document the condition of every room in the unit.

A progress bar at the top shows how many rooms have been given a condition rating out of the total.

For each room:

1. Enter the room name in the text field (for example, "Living Room", "Kitchen", "Bedroom 1").
2. Select a **Condition** (الحالة) from the dropdown:
   - **Excellent** (ممتاز)
   - **Good** (جيد)
   - **Fair** (مقبول)
   - **Poor** (سيئ)
3. Write any **Notes** (ملاحظات) — for example, specific damage or observations.
4. Click **+ Add Photo** (+ إضافة صورة) to upload photo evidence. You can add multiple photos per room. Photos upload immediately after you choose a file. To remove a photo, hover over the thumbnail and click the × button.

To add a room that is not listed, click **+ Add Room** (+ إضافة غرفة) at the bottom of the page and enter the room name.

When you are done, you have two options:

- Click **Save Inspection** (حفظ المعاينة) to save progress without leaving the page. You can return and edit at any time while the move-out is in progress.
- Click **Proceed to Deductions** (الانتقال للخصومات) to move to the next step. Inspection data is saved automatically before navigating.

### 3. Record deposit deductions

The **Deposit Deductions** (خصومات التأمين) page shows the security deposit amount and a table of all deductions entered so far.

To add a deduction:

1. Click **+ Add Deduction** (+ إضافة خصم).
2. The **Add Deduction** dialog opens. Fill in:
   - **Label (English)** — a short description of the charge in English.
   - **Label (Arabic)** (الوصف (عربي)) — the same description in Arabic.
   - **Amount (SAR)** (المبلغ (ر.س)) — the charge amount.
   - **Reason** (السبب) — choose from: **Damage**, **Cleaning**, **Unpaid Rent**, **Utility**, **Other**.
3. Click **Add Deduction** (إضافة خصم). The row appears in the table.

To remove a deduction, click the × button on its row.

The calculation summary card below the table updates in real time:

| Line | Value |
|---|---|
| Security Deposit (التأمين) | The deposit from the lease |
| Total Deductions (إجمالي الخصومات) | Sum of all deduction amounts |
| **Refund Amount** (مبلغ الاسترداد) | Deposit minus deductions — shown when positive |
| **Outstanding Charge** (المبلغ المستحق) | Deductions minus deposit — shown in red when deductions exceed the deposit |

If total deductions exceed the security deposit, an amber warning banner appears at the top of the page: *Total deductions exceed the security deposit. Please verify before proceeding.* (إجمالي الخصومات يتجاوز التأمين. يرجى التحقق قبل المتابعة.) The warning does not block saving — click **Save Deductions** (حفظ الخصومات) to proceed. An outstanding charge is recorded against the tenant.

Click **Save Deductions** (حفظ الخصومات) to save the deduction list and calculated result.

## What you'll see

After initiating the move-out:

- The lease detail page shows a **Move-Out In Progress** status badge.
- The unit linked to the lease changes to **Under Maintenance** status automatically.
- The financial terms on the lease are locked and cannot be edited.

After saving deductions:

- The move-out record holds the full inspection per room (condition, notes, and photos) and the deduction list with a calculated refund amount or outstanding charge.
- The lease remains in **Move-Out In Progress** status until you complete the settlement step.

## Common issues

- **The Initiate Move-Out button is not visible.** The lease is not in Active status, or you do not have update permission for this lease. Check the status badge; if it reads Active and the button is still missing, ask your Account Admin to verify your role permissions.
- **I cannot add photos to a room.** Photos can only be attached to rooms that have already been saved to the server (rooms with an assigned ID). Click **Save Inspection** first, then refresh the page and add photos to any room that still shows the **+ Add Photo** button.
- **The Add Deduction button is disabled.** There is no condition that disables this button — if it is missing, the page may not have loaded fully. Refresh and try again.
- **Deductions exceed the deposit.** This is allowed. The system records the difference as an outstanding charge against the tenant. The warning banner is informational; it does not prevent saving.
- **I cannot find the move-out record I started.** Open the lease detail page. A link or status indicator pointing to the active move-out record appears in the lease status area. If the move-out record is missing, contact your Account Admin.

## Related
- [Approve or reject a lease application](./approve-or-reject-lease.md)
- [Finalize a move-out — settlement, refund, and unit release](./lease-move-out-settlement.md)
- [Convert a quote to a lease and upload KYC documents](./convert-and-kyc.md)
- [Create and send a lease quote](./lease-quotes.md)
