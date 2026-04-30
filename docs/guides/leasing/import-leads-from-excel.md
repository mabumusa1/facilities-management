---
title: Import leads from Excel
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Upload a filled Excel spreadsheet to add multiple leads at once — the system validates every row before anything is saved, and only the rows that pass are imported.*

## Who this is for
Property Managers and Admins with the `leads.CREATE` permission. The **Import Leads** (استيراد العملاء) button is not visible to users without this permission.

## Before you start
- Download the official import template (see Step 1). Using a custom spreadsheet or a spreadsheet from another system will likely produce errors.
- Each file can contain up to **500 rows** and must be no larger than **5 MB**.
- Accepted file formats: `.xlsx` and `.xls`.
- Every row must have a name and a phone number. Email address is optional but must be a valid format if provided.

## Steps

### 1. Get the import template

Two ways to download the template:

- On the **Leads** page, open the **Import Leads** (استيراد العملاء) dropdown in the top-right corner and choose **Download Template** (تنزيل القالب).
- Or, when the upload panel is open (see Step 2), click the **Download the import template** (تنزيل قالب الاستيراد) link inside the panel.

Fill the template in your spreadsheet application. Do not rename or remove column headers.

### 2. Open the upload panel

1. Go to **Leasing → Leads**.
2. Click the **Import Leads** (استيراد العملاء) dropdown.
3. Choose **Import from Excel** (استيراد من Excel).
   The **Import Leads from Excel** (استيراد العملاء من Excel) panel slides in from the right.

### 3. Select your file

1. Click the file input area inside the panel and choose your completed `.xlsx` or `.xls` file.
2. Click **Upload & Preview** (رفع ومعاينة).
   The system parses the file row by row. This usually takes a few seconds.

### 4. Review the validation results

The **Review Import** page opens and shows three summary cards:

| Card | What it shows |
|------|---------------|
| **Total rows** (إجمالي الصفوف) | All data rows in the file, excluding the header |
| **Valid rows** (الصفوف الصحيحة) | Rows that will be imported if you confirm |
| **Error rows** (الصفوف الخاطئة) | Rows with at least one problem — shown highlighted |

Below the cards, an error table lists every problem row with the **Row** number (الصف), the **Field** (الحقل) that failed, and the **Error** (الخطأ) message. Review these to understand what needs fixing.

### 5. Confirm the import

Depending on what the review page shows, you have three options:

**All rows valid** — A green confirmation alert appears. Click **Import N leads** (استيراد N عميل) to save all rows immediately.

**Mixed (some valid, some errors)** — Click **Import N valid rows** (استيراد N صف صحيح). A confirmation dialog appears with the title **Import valid rows only?** (هل تريد استيراد الصفوف الصحيحة فقط؟) and a summary of how many rows will be imported and how many will be skipped. Click **Import N leads** (استيراد N عميل) to proceed. Errored rows are skipped and not saved.

**No valid rows** — The **Import** button is disabled. You cannot confirm when no rows pass validation. Fix the errors and re-upload.

To abandon the import entirely, click **Cancel — return to Leads** (إلغاء — العودة إلى العملاء المحتملين).

### 6. Download the error report (optional)

If any rows had errors, the **Download Error Report** (تنزيل تقرير الأخطاء) button appears on the review page. Click it to download a CSV file listing every failed row with its row number, field, and error message. Use this to fix the rows in your spreadsheet and re-import them.

## What you'll see

After a successful confirmation:

- A toast message at the bottom of the screen confirms **N leads imported successfully.** (تم استيراد N عميل محتمل بنجاح.)
- The Leads list refreshes and the new records appear with their source set to **Excel Import**.
- Leads that were skipped due to errors are not shown — download the error report before confirming if you need to track them.

## Common issues

- **"This file cannot be read."** — The file format is not recognised. Make sure you are uploading an `.xlsx` or `.xls` file generated from the provided template, not a `.csv` or `.ods` file.
- **All rows show errors.** — Check that the column headers in your file exactly match the template headers. Do not rename columns or add extra columns to the left of the data.
- **A row shows a duplicate phone error.** — That phone number already exists in the system or appears in another row in the same file. Leads share a unique phone number per account. Remove or correct the duplicate row.
- **A row shows an invalid email error.** — The email address in that row is not in a valid format (e.g. missing `@` or domain). Correct the email or leave the cell blank if email is not available.
- **The Import button is still disabled after upload.** — No rows passed validation. The file cannot be partially imported when every row has an error. Fix the spreadsheet and upload again.
- **I re-uploaded the same file and it was rejected.** — If the leads from this exact file were already imported, the system rejects the re-submission. Download a fresh template, correct the errored rows, and upload only those rows.

## Related
- [Add a lead manually](../leasing/add-a-lead.md)
