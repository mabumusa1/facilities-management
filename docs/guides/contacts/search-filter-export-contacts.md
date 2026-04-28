---
title: Search, filter, and export contacts
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*Find any contact quickly using the search bar or type and status filters, then export the filtered results to an Excel file for reporting or sharing with external stakeholders.*

## Who this is for
Property Managers and Admins who need to locate specific contacts across a large list or produce contact reports for internal or external use.

## Before you start
- You must have the **View Contacts** permission.
- Export to Excel requires the **Export Contacts** permission. Contact your Account Admin if the **Export** button is not visible.

## Steps

### Search for a contact

1. In the main navigation, go to **Contacts** (جهات الاتصال).
2. In the **Search** box at the top of the list, type a name (English or Arabic) or a phone number.
3. Results update automatically as you type. Each row shows the contact's name (EN and AR), contact type, phone number, and status.
4. Click any row to open the contact's detail page.

Arabic-only contacts (where only the Arabic name is populated) appear in search results when their Arabic name matches the search term, even if the UI locale is set to English. Their Arabic name is displayed in a dedicated column.

### Filter by contact type

1. In the **Type** filter, select one option: **Residents** (مقيمون), **Owners** (ملاك), or **Professionals** (مهنيون).
2. The list immediately narrows to that contact type.

To remove the filter, select **All types** (جميع الأنواع).

### Filter by status

1. In the **Status** filter, select **Active** (نشط) or **Archived** (مؤرشف).
2. The default view shows active contacts only. Switch to **Archived** to see contacts you have archived.

### Combine filters

You can combine search text, a type filter, and a status filter at the same time. For example: search for a partial phone number AND filter by **Residents** to narrow to that phone pattern among residents only.

### Export to Excel

1. Apply any search text or filters to narrow the list to the records you want.
2. Click **Export** (تصدير).
3. The file downloads to your device as an `.xlsx` file.

The exported file includes both `name_en` and `name_ar` columns for every contact, regardless of the UI locale.

If the current filter returns zero contacts, the **Export** button is disabled and a warning appears:
> **No contacts match the current filters — adjust filters before exporting.**

## What you'll see

- Results load within 2 seconds for most filter combinations.
- Each row in the list shows: name (EN/AR based on locale, with fallback to the other language), contact type badge, phone number, and status badge.
- When a search or filter is active, a count of matching records appears above the list.

## Common issues

- **Search returns no results for an Arabic name typed in English** — the search does not transliterate. Type the name in the same script it was saved in.
- **Export button is disabled with results visible** — your role may not have the Export Contacts permission. Contact your Account Admin.
- **Exported file is missing Arabic names** — check that Arabic name fields were filled in when the contacts were created. The export includes whatever is stored; empty fields appear blank in the file.
- **A contact does not appear in the list** — the contact may be archived. Change the Status filter to **Archived** or **All** to find them.

## Related

- [Create and search resident contacts](./create-and-search-residents.md)
- [Bulk import contacts from Excel](./bulk-import-contacts.md)
- [Archive, merge, and reactivate contacts](./archive-merge-reactivate-contacts.md)
