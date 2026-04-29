---
title: Bulk import contacts from Excel
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*Upload an Excel file of Residents, Owners, or Professionals, map columns to system fields, review any validation errors, and commit the import — all without creating records one by one.*

## Who this is for
Property Managers and Admins onboarding a large volume of contacts at once, such as when going live with a new community or migrating data from a previous system.

## Before you start
- You must have the **Import Contacts** permission. Ask your Account Admin if the import option is not visible.
- Download the appropriate Excel template for the contact type you are importing (see step 1). Using the system template ensures column names are recognised automatically.
- Prepare your data: all required fields must be filled, phone numbers must use the local format (no leading zero, no country prefix), and the file must contain at least one data row.
- Import is limited to one contact type per file — Residents, Owners, or Professionals. Dependents cannot be imported via Excel; they must be added per resident in the UI.

## Steps

### Step 1 — Download the import template

1. In the main navigation, go to **Contacts** (جهات الاتصال).
2. Click **Import** (استيراد).
3. Choose the contact type: **Residents** (مقيمون), **Owners** (ملاك), or **Professionals** (مهنيون).
4. Click **Download template** (تحميل النموذج) to get the pre-formatted Excel file.
5. Fill in your data following the column headers. Include both `name_en` and `name_ar` columns where names have both-language values.

### Step 2 — Upload the file

1. Return to **Contacts → Import**.
2. Select the same contact type you prepared data for.
3. Click **Choose file** (اختر ملفًا) and select your completed Excel file (`.xlsx`).
4. Click **Next** (التالي).

If the file contains only a header row and no data, the system shows "No data rows found in this file" and stops here.

### Step 3 — Map columns

The system automatically suggests a mapping between your file's column headers and the system fields using fuzzy matching. Review each mapping:

- A green tick means the column was matched automatically.
- An amber warning means the column could not be matched — select the correct system field from the dropdown.
- Columns you do not want to import can be set to **Skip** (تجاهل).

Required fields — such as First Name (EN), Phone Number, and Country Code — must be mapped before you can proceed.

Click **Next** (التالي) when all required columns are mapped.

### Step 4 — Review validation

The system validates every row against the mapped fields. The preview table shows:

- Rows with a green tick are ready to import.
- Rows with a red X show the field name and reason for the error (for example, missing phone number or a phone number that already exists in your contacts list).

You have two options:
- **Fix and re-upload** — correct the errors in your Excel file, return to Step 2, and re-upload.
- **Import valid rows only** — click **Import valid rows** (استيراد الصفوف الصحيحة) to proceed with only the rows that passed validation. Failed rows are skipped and listed in the post-import error report.

### Step 5 — Confirm and import

1. Review the count of rows to be imported.
2. Click **Import** (استيراد).

For files with 50 rows or fewer, the import runs immediately and you see a success confirmation when it finishes.

For files with more than 50 rows, the import runs as a background job. A progress indicator appears on screen. You receive a notification when the job completes.

## What you'll see

After a successful import:
- A summary shows the number of records created and the number of rows that were skipped.
- All imported contacts appear in the **Contacts** list under the correct type tab.
- A downloadable error report is available if any rows were skipped.

## Common issues

- **"No data rows found in this file"** — the file contains only the header row. Add your data rows and re-upload.
- **Column mapping shows all amber warnings** — your file's column headers may not match the template names. Download the template, copy your data into it, and re-upload.
- **Phone number already exists error** — the phone number in that row matches a contact already in the system. Either update the existing contact or correct the phone number in the file.
- **Import job runs but no contacts appear** — the background job may still be in progress. Wait for the completion notification and then refresh the Contacts list.
- **File is rejected before mapping** — only `.xlsx` files are accepted. Save your spreadsheet in Excel Workbook format, not `.csv` or `.xls`.

## Related

- [Create and search resident contacts](./create-and-search-residents.md)
- [Create and manage owner contacts](./create-and-manage-owners.md)
- [Search, filter, and export contacts](./search-filter-export-contacts.md)
