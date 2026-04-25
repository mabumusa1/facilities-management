---
title: Configure Transaction Categories
area: accounting
layout: guide
lang: en
---

# {{ page.title }}

*Set up the income and expense categories that appear when recording transactions so your financial reports are meaningful.*

## Who this is for

Property Manager / Admin

## Before you start

- You must have a role with the Accounting Settings permission.
- Your account is pre-seeded with six default categories (three income, three expense). You can add more at any time.

## Steps

### View your categories

1. Go to **Accounting** in the main navigation.
2. Click **Settings**, then select **Transaction Categories** (فئات المعاملات).
3. The page opens on the **Income Categories** tab. Click **Expense Categories** to switch.

### Add a category

1. On the **Transaction Categories** page, click **Add Category** (إضافة فئة).
2. In the panel that slides in from the right, select **Income** or **Expense** as the Category Type.
3. Enter a name in the **Name (English)** field.
4. Enter a name in the **Name (Arabic)** field (typed right-to-left automatically).
5. Click **Save Category** (حفظ الفئة).

The new category appears in the correct tab immediately and is available when recording transactions.

### Edit a category

1. Find the category in the **Income Categories** or **Expense Categories** tab.
2. Click **Edit** (تعديل) in the Actions column.
3. Update the English or Arabic name.
4. Click **Save Category**.

> The category type (Income or Expense) cannot be changed after a category is created.

### Deactivate a category

Use this when a category is no longer needed for new transactions, but you want to keep the historical records intact.

1. Find the active category in the list.
2. Click **Deactivate** (إلغاء تفعيل) in the Actions column.
3. A confirmation dialog appears: "Deactivate category?" It notes that existing transactions will retain this category.
4. Click **Deactivate** to confirm, or **Keep Active** (إبقاء نشطة) to cancel.

The category moves to **Inactive** status and no longer appears in the transaction recording form.

### Reactivate a category

1. Find the inactive category in the list (Status column shows **Inactive**).
2. Click **Reactivate** (إعادة تفعيل).

The category returns to **Active** status immediately.

### Delete a custom category

You can delete only custom (non-default) categories that are not currently in use by any transaction.

1. Find the category. Only categories without the **Default** (افتراضي) badge have a **Delete** button.
2. Click **Delete** (حذف) in the Actions column.

The category is removed permanently.

## What you'll see

- **Income Categories** tab: lists all income-type categories with English name, Arabic name, status badge, and action buttons.
- **Expense Categories** tab: same layout for expense-type categories.
- **Default** badge: a small outline badge next to the name of any pre-seeded default category.
- **Active** / **Inactive** status badge: shows the current state of each category.

## Common issues

- **The Delete button is not visible for a category.** The category is a system default (it has a **Default** badge). Default categories cannot be deleted to protect data integrity. Deactivate them instead if you no longer want them to appear on transaction forms.
- **I cannot change a category from Income to Expense.** Category type is locked after creation. If you need the opposite type, deactivate or delete the existing category and create a new one with the correct type.
- **A deactivated category still appears on an existing transaction.** This is correct behaviour. Deactivating a category removes it from new transaction forms but does not alter historical records.

## Related

- [Record an income transaction](./record-income.md)
- [Record an expense transaction](./record-expense.md)
