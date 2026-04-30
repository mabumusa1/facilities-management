---
title: Manage leads
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*View your lead pipeline, filter by status or source, and add new leads manually from the Leads page.*

## Who this is for
Property Managers and Admins whose role includes the **leads.VIEW** permission. Adding a lead also requires the **leads.CREATE** permission. Contact your Account Admin if a button described below is not visible to you.

## Before you start
- You must have an active session in the platform.
- To add a lead you need at least one lead source configured in the system. If the source dropdown in the Add Lead drawer is empty, ask your Account Admin to set up lead sources.

## Steps

### 1. Open the Leads page

In the left navigation, go to **Leasing → Leads** (التأجير ← العملاء المحتملون).

The page shows a filter bar at the top and a table of existing leads below.

### 2. Filter the list

Use any combination of the four filter controls:

| Control | What it does |
|---------|-------------|
| **Search** (بحث) | Matches against lead name or phone number |
| **Status** (الحالة) | Narrows to one status: New, Contacted, Qualified, Converted, or Lost |
| **Source** (المصدر) | Narrows to one lead source |
| **Rows** (الصفوف) | Sets how many rows appear per page (10 / 15 / 25 / 50) |

Click **Apply** (تطبيق) to run the filter. Click **Reset** (إعادة تعيين) to clear all filters and return to the default view.

### 3. Read the leads table

Each row shows:

- **Lead Name** (اسم العميل) — displayed in the UI language (English or Arabic).
- **Phone** (الهاتف) — country code and number.
- **Email** (البريد الإلكتروني) — blank if not provided.
- **Source** (المصدر) — where the lead came from.
- **Status** (الحالة) — colour-coded badge: New (blue), Contacted (outlined), Qualified (grey), Converted (blue), Lost (red).
- **Assigned To** (مسؤول عنه) — the team member responsible for this lead, if assigned.
- **Created At** (تاريخ الإضافة) — the date the record was created.

The page count and range ("Showing X to Y of Z leads") appears below the table. Use the pagination buttons to move between pages.

### 4. Add a lead manually

1. Click **+ Add Lead** (+ إضافة عميل محتمل). The Add Lead (إضافة عميل محتمل) drawer opens on the right.
2. Fill in the fields:

   | Field | Required | Notes |
   |-------|----------|-------|
   | **Lead Name (English)** (اسم العميل - إنجليزي) | At least one name field is required | Enter the name in English |
   | **Lead Name (Arabic)** (اسم العميل - عربي) | At least one name field is required | Enter the name in Arabic |
   | **Phone Number** (رقم الهاتف) | Yes | Enter the country code (e.g. +966) and the number separately |
   | **Email Address** (البريد الإلكتروني) | No | Must be a valid email format if provided |
   | **Source** (المصدر) | Yes | Select from the dropdown |
   | **Notes** (ملاحظات) | No | Free-text field, up to the platform limit |

3. Click **Save Lead** (حفظ العميل).
4. The drawer closes and the new lead appears in the table.

To discard without saving, click **Cancel** (إلغاء) or close the drawer. If you have entered any data, you will see a confirmation prompt before the drawer closes.

## What you'll see

After saving, the lead appears in the table with status **New** (جديد) and the current date in the **Created At** column. The **Assigned To** column is empty until a team member is assigned to the lead.

If the table is empty and no filters are active, you will see an empty state with a **+ Add Lead** button and a disabled **Import from Excel** button (import is coming in a future release).

## Common issues

- **The + Add Lead button is not visible.** Your role does not include the **leads.CREATE** permission. Ask your Account Admin to update your role.
- **The Source dropdown is empty.** No lead sources have been configured for your account. Ask your Account Admin to add sources before creating leads.
- **The Save Lead button is disabled or returns an error.** Check that: at least one name field (English or Arabic) is filled in, a phone number is entered, and a source is selected.
- **A lead I just added does not appear in the list.** Check whether an active filter is hiding it. Click **Reset** to clear all filters.

## Related
- [Approve or reject a lease application](./approve-or-reject-lease.md)
- [Create and send a lease quote](./lease-quotes.md)
