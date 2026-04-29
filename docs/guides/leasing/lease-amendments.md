---
title: Amend lease terms with history trail
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Change the structural terms of an active lease — end date, rental amount, contract type, payment schedule, or security deposit — and have every change recorded in a permanent, auditable history trail.*

## Who this is for

Property Managers and Admins with manager-level RBAC scope for the community the lease belongs to. The **Amend Lease Terms** button is hidden for users without this permission.

## Before you start

- The lease must be in **Active** status. The amendment form is not accessible for leases in any other status.
- You must have manager-level permission for the lease's community. Users from other communities receive a 403 error.
- Rent escalation (incremental annual increases) is a separate workflow and is not covered here.
- Tenant reassignment is not an amendable field. To move a resident to a different unit, use the relocation process (see your operations runbook).

## Steps

### 1. Open the lease detail page

1. Go to **Leasing → Leases** (التأجير ← عقود الإيجار).
2. Open the active lease you want to amend.

### 2. Open the amendment form

1. On the lease detail page, click **Amend Lease Terms** (تعديل شروط العقد) in the top-right action area.
2. The **Amend Lease Terms** page opens, showing the current value of each amendable field.

### 3. Change one or more fields

You can change any combination of the following fields. Each field shows a **Current** (الحالي) label with the value already on the lease so you know exactly what you are replacing.

| Field | Notes |
|---|---|
| **End Date** | Extend or shorten the lease period. |
| **Total Rental Amount** | Update the total rent figure for the amended term. |
| **Contract Type** | Switch to a different contract type (e.g., Yearly Rental → Commercial). |
| **Payment Schedule** | Change the payment frequency or schedule. |
| **Security Deposit** | Adjust the deposit amount. |
| **Terms and Conditions** | Update the free-text contract terms. |

Leave any field you do not want to change as-is — unchanged fields will show **(unchanged)** ((بدون تغيير)) in the Diff Preview table.

### 4. Review the Diff Preview

Below the input fields, the **Diff Preview** (معاينة الفروقات) table updates in real time as you type. Each row shows:

- **Field** — the field name.
- **Current** — the value on the lease right now.
- **New** — the value you are entering.
- A badge: **(changed)** ((تم التغيير)) in amber for fields you modified, or **(unchanged)** ((بدون تغيير)) for fields left as-is.

Review this table before submitting to confirm the changes are correct.

### 5. Enter a reason

In the **Reason for Amendment** (سبب التعديل) field, describe why you are making this change. The reason must be at least 5 characters and will appear in the amendment history visible to all managers. This field is required.

### 6. (Optional) Request an addendum document

If this amendment requires a new signed contract addendum, check **Generate signed addendum after saving** (إنشاء ملحق موقع بعد الحفظ). Addendum generation is handled by the Documents domain and will be available once that workflow ships. Checking this box today records your intent; you will be prompted to complete the addendum flow when it is ready.

### 7. Save the amendment

Click **Save Amendment** (حفظ التعديل). To discard without saving, click **Cancel** (إلغاء).

## What you'll see

After saving, you are returned to the lease detail page. The following changes take effect immediately:

- The lease record reflects the new values you entered (end date, amount, contract type, etc.).
- The **Amendment History** (سجل التعديلات) card appears at the bottom of the lease detail page. Each entry in the list shows:
  - **Amendment #N** (التعديل #N) — sequential amendment number.
  - The date and time the amendment was saved.
  - **Made by: \<name\>** (تم بواسطة: \<الاسم\>) — the user who made the change.
  - The reason you entered (shown in italics).
  - A field-by-field changes table with the old value and new value for every field that was modified.
  - An addendum status note: **Signed** (موقع) if an addendum document was attached, or **Not generated** (لم يتم الإنشاء) otherwise.

The amendment counter on the lease is incremented (Amendment #1, #2, etc.) with each saved amendment.

## Common issues

- **The "Amend Lease Terms" button is not visible.** Either the lease is not in **Active** status, or your role does not have manager-level scope for this community. Check the status badge on the lease. If the status is Active and the button is still missing, ask your Account Admin to verify your RBAC permissions.
- **The Save Amendment button is disabled.** The **Reason for Amendment** field is empty or fewer than 5 characters. Add a meaningful reason before saving.
- **I see the form but cannot save — I get a validation error on a numeric field.** Amounts must be zero or greater and must be valid numbers. Remove any currency symbols or commas from the amount fields.
- **The Diff Preview shows all rows as "(unchanged)".** None of the fields have been modified from their current values. Change at least one field before submitting.
- **The Amendment History card is not showing.** The lease has not yet been amended. The card only appears once at least one amendment has been saved.
- **I need to change the tenant on the lease.** Tenant reassignment is not supported via the amendment form. Use the relocation process instead.

## Related

- [Approve or reject a lease application](./approve-or-reject-lease.md)
- [Convert a quote to a lease and upload KYC documents](./convert-and-kyc.md)
- [Create and send a lease quote](./lease-quotes.md)
