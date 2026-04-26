---
title: Triage and Manage Service Requests (Admin)
area: service-requests
layout: guide
lang: en
---

# {{ page.title }}

*Review all incoming service requests, assign them to a technician, set priority, and add internal notes — without residents seeing your team's internal discussion.*

## Who this is for

Property Managers and Admins. This page is not visible to Residents or Owners.

## Before you start

- You must be logged in with an Admin or Manager role. Residents see a 403 error if they navigate here directly.
- At least one service request must have been submitted by a resident (see [Submit a service request](./submit-a-request.md)).
- At least one admin user must exist in your tenant for the assignee dropdown to have entries.

## Steps

### Open the triage queue

1. In the main navigation, go to **Services → Service Requests** (طلبات الخدمة).
2. The **Service Requests** (طلبات الخدمة) triage queue opens, showing all incoming requests for your communities.

### Read the queue at a glance

The queue table has these columns:

| Column | What it shows |
|---|---|
| **Ref#** (الرقم المرجعي) | Auto-generated reference code, e.g. `SR-2026-00042` |
| **Resident** (الساكن) | Name of the resident who submitted |
| **Unit** (الوحدة) | Unit number |
| **Category** (الفئة) | Service category |
| **Urgency** (درجة الإستعجال) | **Normal** (عادي) or **Urgent** (عاجل) |
| **Status** (الحالة) | Current workflow status |
| **Submitted** (تاريخ التقديم) | Date the request was submitted |
| **Assigned To** (المعيّن له) | Admin or technician assigned, or blank if unassigned |
| **SLA Response** (زمن الاستجابة) | SLA indicator: **On time** (في الوقت), **Due soon** (يقترب موعده), or **Overdue** (متأخر) |

Rows with a red left border and red background have breached their SLA response deadline and are still unassigned — act on these first. Rows with a yellow left border are approaching their deadline.

### Use the tabs to focus your work

Above the table, four tabs filter the queue:

- **All Requests** (جميع الطلبات) — every request in your communities.
- **Unassigned** (غير معيّنة) — requests with no technician assigned yet.
- **Overdue** (متأخرة) — requests past their SLA response due date.
- **SLA Breach** (تجاوز زمن الخدمة) — requests approaching or past SLA thresholds.

Click a tab to switch views. Each tab shows a count badge.

### Filter the queue

Use the filter bar to narrow results:

1. Type a reference number or resident name in the search box.
2. Choose a **Status** (الحالة), **Category** (الفئة), **Community** (المجتمع), or **Urgency** (درجة الإستعجال) from the drop-downs.
3. Click **Apply** (تطبيق) to refresh the table.
4. Click **Reset** (مسح) to clear all filters.

### Open a request detail

Click any row to open the request detail page. The page shows:

- The reference number and submission date in the header.
- Three summary cards: **Resident** (الساكن) with name and phone, **Location** (الموقع) with community and unit, and **Category** (الفئة) with category and subcategory.
- Three more cards: **Urgency** (درجة الإستعجال), **Status** (الحالة), and **Assigned To** (المعيّن له).
- A **Description** (الوصف) card with the full text the resident entered.
- The **Assign Technician** (تعيين فني) section where you take action.
- The **Internal Notes** (ملاحظات داخلية) section for admin-only discussion.

### Assign a technician and set priority

1. In the **Assign Technician** (تعيين فني) section, click the **Assign Technician** (اختر فنياً...) drop-down and select a name. Only admin users who belong to your tenant appear in the list.
2. Click the **Priority** (الأولوية) drop-down and choose **Low** (منخفض), **Medium** (متوسط), **High** (عالي), or **Urgent** (عاجل).
3. Click **Assign & Save** (تعيين وحفظ).

The **Assigned To** card at the top of the page updates immediately. To reassign, repeat the same steps and choose a different name, then click **Assign & Save** again.

To go back to the full list without saving, click **Back to Requests** (العودة إلى القائمة).

### Add an internal note

Internal notes are visible to admins and the assigned technician only. Residents never see them.

1. In the **Internal Notes** (ملاحظات داخلية) section, click the note text area.
2. Type your note. The field accepts Arabic and English — the text direction adjusts automatically.
3. Click **Add Note** (إضافة ملاحظة).

The note is saved with your name and a timestamp. The **Notes History** (سجل الملاحظات) section below the form shows all previous notes for this request in chronological order. Click the **Notes History** heading to expand or collapse the list.

## What you'll see

After clicking **Assign & Save**, the **Assigned To** card on the detail page shows the selected technician's name. The row in the triage queue also updates to show the assignee. A red or yellow SLA indicator disappears once a request is assigned and the deadline is no longer breached.

After clicking **Add Note**, the new note appears at the bottom of the **Notes History** list with your name and the current timestamp. The note text area clears automatically.

## Common issues

- **The Assign & Save button is greyed out** — You must select a technician from the dropdown before the button activates. Priority is optional.
- **No names appear in the Assign Technician dropdown** — Your tenant has no admin users configured, or the only admin is you (you can still assign to yourself). Contact your System Admin to add users.
- **A request row shows a red left border** — The SLA response deadline has passed and the request is still unassigned. Open the request and assign it as soon as possible.
- **You see a 403 error** — Your account does not have the Admin or Manager role required to view this page. Contact your System Admin.
- **A note does not appear after clicking Add Note** — Make sure the note field is not empty (the button is disabled for blank input). If the problem persists, refresh the page.

## Related

- [Submit a service request](./submit-a-request.md)
- [Configure service request categories and SLA targets](./configure-categories-and-sla.md)
- [Service request reference codes](./service-request-reference-codes.md)
