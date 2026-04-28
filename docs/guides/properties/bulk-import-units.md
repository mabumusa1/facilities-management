---
title: Bulk import units from Excel
area: properties
layout: guide
lang: en
---

# Bulk import units from Excel

*Upload a spreadsheet of up to 500 units, map your column headers to system fields, review row-level validation errors, and import in one session.*

## Who this is for

Property Managers and Admins who need to onboard a large portfolio of units (typically 100–500 rows) without entering each unit manually.

## Before you start

- You must be signed in as a **Property Manager** or **System Admin** with the `units.import` permission.
- All communities and buildings that the units belong to must already exist in the system. Unit import references existing buildings by name.
- Prepare your spreadsheet as a `.xlsx` file, maximum **10 MB**. Download the system template (see Step 1) to use pre-labelled column headers.

## Steps

### Step 1 — Upload your file

1. In the left navigation, open **Properties → Units**.
2. Click **Import Units** (استيراد وحدات) next to the **New Unit** button.
   The **Import Units from Excel** (استيراد وحدات من إكسل) dialog opens at **Step 1: Upload** (رفع الملف).
3. Click **Download template** (تحميل النموذج) to get the pre-formatted `.xlsx` file with the correct column headings and an example row.
4. Populate the template (or your own file) with your unit data and save it.
5. Drag and drop your `.xlsx` file onto the upload zone, or click the zone to browse for the file.
   The zone reads: *Drag & drop your .xlsx file here or click to browse* (اسحب وأفلت ملف الإكسل هنا أو انقر للتصفح).
   Accepted format: `.xlsx` · Max: 10 MB.
6. Click **Next** once the file is selected.

### Step 2 — Map columns

The dialog moves to **Step 2: Map Columns** (مطابقة الأعمدة). The system auto-detects your column headers and attempts to match them to the five system fields.

The mapping table shows:

| System Field | Uploaded Column |
|---|---|
| Unit Name (اسم الوحدة) | Your column header dropdown |
| Community (المشروع) | Your column header dropdown |
| Building (المبنى) | Your column header dropdown |
| Area (sqm) (المساحة (م²)) | Your column header dropdown |
| Status (الحالة) | Your column header dropdown |

- A green **matched** (متطابق) badge appears next to columns the system recognised automatically.
- An amber **unmatched** (غير متطابق) badge appears next to columns with no automatic match. Open the **-- Select --** (-- اختر --) dropdown on that row and choose the correct column from your file.
- Every field with an **unmatched** badge must be mapped before you can proceed (or you may leave optional fields unmapped if your file does not include them).
- The number of detected rows is shown at the top: *N rows detected* (تم اكتشاف N صف).

Click **Next** when the mapping is complete.

### Step 3 — Review validation results

The dialog moves to **Step 3: Review** (مراجعة). The system reads every row and validates it against the database.

A summary badge bar shows:
- **N valid** — rows that will import without issues.
- **N with errors** — rows that cannot be imported as-is.

If there are errors, a scrollable table lists each problem row with:

| Row | Field | Error |
|---|---|---|
| Row number | Affected field | Error description |

**Validation checks performed:**

- **Required fields** — Unit Name and Building must be present.
- **Area must be greater than zero** — Area (sqm) cannot be 0 or negative.
- **Valid status** — Status must be one of the recognised values (e.g. `available`, `occupied`, `under_maintenance`, `off_plan`). An unrecognised value shows: *Status "X" is not valid* (الحالة "X" غير صالحة).
- **Building not found** — The building name must match an existing building in your account. An unmatched name shows: *Building "X" not found* (المبنى "X" غير موجود).
- **Duplicate within the file** — If two rows in the same file have the same unit name in the same building, the second row is flagged: *Duplicate unit number in this building* (رقم وحدة مكرر في نفس المبنى). The first occurrence is treated as valid.
- **Duplicate against existing data** — If a unit with the same name already exists in that building in the database, the row is flagged as a duplicate.

**Your options at this step:**

- **Import N valid rows** (استيراد N صف صحيح) — proceed and import only the rows that passed validation. Rows with errors are skipped.
- **Cancel and fix the file** (إلغاء وتصحيح الملف) — close the dialog, correct the spreadsheet, and start again from Step 1.

If *all* rows contain errors, the **Import** button is disabled and you must fix the file first.
If the file contains no data rows, you will see: *The uploaded file contains no data rows.* (الملف المرفوع لا يحتوي على بيانات.)

### Step 4 — Monitor the import

The dialog moves to **Step 4: Import** (استيراد).

- **For files with 50 rows or fewer**, the import runs immediately. A progress bar tracks the current count.
- **For files with more than 50 rows**, the import is queued and runs in the background. The dialog shows:
  *Import is processing in the background. You will be notified on completion.* (الاستيراد قيد المعالجة في الخلفية. ستُشعَر عند الانتهاء.)
  You may close the dialog and continue working.
- While the progress bar is active, **do not close this window** (لا تُغلق هذه النافذة.) unless the file was queued for background processing.

## What you'll see

When the import finishes, the dialog shows a completion message:

> *N units imported successfully. E rows skipped due to errors.* (تم استيراد N وحدة بنجاح. تم تخطي E صف بسبب أخطاء.)

The Units list page refreshes and the new units appear under their respective buildings.
Units created through import are automatically scoped to your account — you cannot import units into another tenant's portfolio.

## Common issues

- **"File must be .xlsx format"** (يجب أن يكون الملف بصيغة .xlsx) — the uploaded file is not a valid Excel file. Resave your spreadsheet as `.xlsx` and retry.
- **"File exceeds 10 MB limit. Please split your import."** (حجم الملف يتجاوز 10MB. يرجى تقسيم الملف.) — split the data across two or more files and import them separately.
- **Several buildings show "not found"** — check that the building names in your file exactly match the names in the system (case-insensitive). Go to **Properties → Buildings** to confirm the correct names.
- **Import is taking longer than expected** — if the background job has not completed after a few minutes, check the import history (see story #161 when that guide is available) or contact your system administrator.
- **All rows are flagged as duplicates** — if you are re-importing after a partial failure, the valid rows from the previous run are already in the database. Filter or remove those rows from the file before re-uploading.

## Related

- [Configure community metadata](./community-metadata.md)
