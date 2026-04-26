---
title: Configure Service Request Categories and SLA Targets
area: service-requests
layout: guide
lang: en
---

# {{ page.title }}

*Set up the category tree that residents use to classify service requests, and define response and resolution time targets (SLAs) per category.*

## Who this is for

Property Managers and Admins with the `settings` permission group.

## Before you start

- You need an admin account. This page is not visible to residents or owners.
- Decide on your category structure before you start. Subcategories nest one level under a parent category; you cannot nest subcategories under subcategories.
- Have SLA targets ready: response time (when a technician must acknowledge the request) and resolution time (when the request must be closed), both in hours.
- If you want a category visible only in certain communities, confirm those community names in advance.

## Steps

### Create a category

1. Go to **Settings → Services → Service Categories** (فئات الخدمة).
2. Click **+ New Category** (+ فئة جديدة). A panel slides in from the right.
3. Fill in **Name (English)** and **Name (Arabic)** (الاسم (إنجليزي) / الاسم (عربي)). Both fields are required.
4. Click an emoji in **Choose icon** (اختر أيقونة) to pick an icon for the category.
5. Under **SLA Configuration** (إعداد مستوى الخدمة), enter:
   - **Response SLA (hours)** (وقت الاستجابة المستهدف (ساعات)) — how many hours staff have to acknowledge a request (minimum 1, maximum 720).
   - **Resolution SLA (hours)** (وقت الحل المستهدف (ساعات)) — how many hours staff have to resolve a request (minimum 1, maximum 720).
6. Optionally select a **Default handler** (المسؤول الافتراضي) — the staff member pre-assigned to new requests in this category. You can still reassign per request.
7. Under **Available in communities** (متاح في المجتمعات), check each community where this category should appear. Leave all unchecked to make it available in every community.
8. Check **Require completion photo** (يتطلب صورة إنجاز) if technicians must upload a photo before marking requests in this category as resolved.
9. Set **Status** to **Active** (نشط) or **Inactive** (غير نشط).
10. Click **Save Category** (حفظ الفئة).

### Add a subcategory

1. On the **Service Categories** page, click a category row to expand it.
2. Click **+ Add Subcategory** (+ إضافة فئة فرعية) at the bottom of the expanded row. A panel slides in.
3. Fill in **Name (English)** and **Name (Arabic)**.
4. Under **SLA Configuration**, leave the SLA fields blank to inherit the parent category's targets. The panel shows "Inherited from parent" (موروث من الفئة الرئيسية) as a placeholder. Enter values only if this subcategory needs different targets.
5. Set **Status** to **Active** or **Inactive**.
6. Click **Save Subcategory** (حفظ الفئة الفرعية).

### Edit a category or subcategory

1. For a category: click **Edit** (تعديل) on its row. The panel opens pre-filled with existing values. Make your changes and click **Save Category**.
2. For a subcategory: expand the parent category, then click **Edit** on the subcategory row. Make changes and click **Save Subcategory**.

### Disable or re-enable

- On a category row, click **Disable** (تعطيل) to deactivate it. The badge changes to **Inactive**. Click **Enable** (تفعيل) to restore it.
- On a subcategory row, use the same **Disable** / **Enable** buttons.

Disabling a category hides it from the resident request form. Existing open requests are not affected.

### Delete a subcategory

1. Expand the parent category.
2. Click **Delete** (حذف) on the subcategory row.

A subcategory with active (non-archived) service requests linked to it cannot be deleted. Archive or resolve those requests first, or disable the subcategory instead.

## What you'll see

After saving a category, it appears as a collapsible row on the **Service Categories** page showing its icon, bilingual name, SLA summary (e.g., "Response 4h", "Resolution 24h"), status badge, and subcategory count.

Subcategories appear in a table inside the expanded row. The **SLA** column shows the custom SLA hours when overridden, or "Inherited from parent" in muted text when the subcategory uses the parent's targets.

## Common issues

- **The save button is disabled mid-form** — Response SLA and Resolution SLA are required for categories. Enter a value of at least 1 hour in both fields before saving. Subcategories do not require SLA values; leave them blank to inherit.
- **Delete button does nothing / error appears** — The subcategory is referenced by one or more active service requests. Disable the subcategory instead, or archive the linked requests, then retry deletion.
- **Category does not appear in the resident request form for a community** — Check that the community is ticked under **Available in communities** in the category's Edit panel. If the category is also set to **Inactive**, re-enable it.
- **SLA column shows "Inherited from parent" for a subcategory I just saved with values** — Confirm the subcategory form had values in both SLA fields before you clicked Save. If the fields were blank, the subcategory inherits. Edit the subcategory and re-enter the hours.

## Related

- [Service request reference codes](./service-request-reference-codes.md)
