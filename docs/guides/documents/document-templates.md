---
title: Manage document templates
area: documents
layout: guide
lang: en
---

# {{ page.title }}

*Create and version document templates with named merge fields so the platform can auto-fill lease contracts, invoices, receipts, and booking documents.*

## Who this is for

Account Admins who need to set up and maintain document templates used across Leasing, Facilities, and Accounting.

## Before you start

- You must have the **documents.VIEW** and **documents.CREATE** permissions. Account Admins have these by default. Ask your Account Admin if the **Document Templates** menu item is not visible in the admin panel.
- Templates support two formats: **In-platform editor** for HTML authoring, and **Word upload** for .docx files parsed server-side.
- Every template requires an English name. An Arabic name is optional but recommended for bilingual documents.

## Steps

### View your templates

1. In the admin navigation, go to **Document Templates** (قوالب المستندات).
2. The list shows every template in your account — draft, active, and archived — with its type, current version number, status badge, and format.
3. Click a template name to open the editor.

### Create a template

1. From **Document Templates**, click **Create template** (إنشاء قالب).
2. In the side sheet:
   - **Name (English)** — required. Enter a clear name like "Standard Lease Agreement".
   - **Name (Arabic)** (الاسم - عربي) — optional. Type right-to-left automatically.
   - **Type** — pick from Lease, Booking, Invoice, Receipt, or Custom. This controls which module the template appears in when generating documents.
3. Click **Create template**.
4. The template is saved as a **Draft** with version 1. You can now open it to add the body content and merge fields.

### Edit a template and add a body

1. From the Document Templates list, click the template name to open the editor.
2. On the left side, fill in or update the name and type.
3. Switch between the **English** and **العربية** tabs to write the body content for each language.
4. In the body text, use `{{merge_field_key}}` placeholders wherever a variable value should be inserted. For example:
   ```
   Dear {{resident.full_name}}, your lease starts on {{lease.start_date}}.
   ```
5. Click **Save & Create New Version** to save your changes. This creates a new version (version 2, 3, and so on) while keeping all previous versions available for reference.

### Define merge fields

Merge fields tell the platform what data to pull into the placeholders when a document is generated.

1. In the editor, scroll to **Merge fields** (حقول الدمج).
2. Click **Add field** (إضافة حقل).
3. For each merge field:
   - **Field key** — the placeholder name that appears in double curly braces in your template body. Use dot notation like `lease.start_date` or `resident.full_name`.
   - **Label (English)** — a human-readable name shown in the admin UI, like "Start Date".
   - **Label (Arabic)** (التسمية - عربي) — the Arabic display label, optional.
   - **Type** — the data type: Text, Date, Currency, or Number. This controls formatting at generation time.
   - **Source path** — the dot-notation path to the data model field the platform should fetch, like `lease.start_date` or `resident.full_name`.
4. Click the trash icon to remove a field.
5. Save with **Save & Create New Version**.

### Activate a template

An active template becomes selectable when generating documents in consumer modules (Leasing, Facilities, Accounting).

1. On the Document Templates list, find your draft template.
2. Click **Activate** (تفعيل).
3. The status badge changes to **Active**.

### Archive a template

Archiving removes the template from the "available" list for new documents but does not affect documents already generated against it.

1. On the Document Templates list, find an active template.
2. Click **Archive** (أرشفة).
3. The status badge changes to **Archived**. The template remains visible in the list for reference.

### Delete a template

1. On the Document Templates list, click **Delete** on any template.
2. Confirm the action. The template is soft-deleted and can be restored if needed.

### View version history

1. Open any template from the Document Templates list.
2. The right-hand sidebar lists every saved version in descending order (newest first).
3. Click any version to preview its body content and merge fields.
4. The latest version's body and merge fields are pre-loaded into the edit form when you arrive.

## What you'll see

After creating a template:
- The template appears in the **Document Templates** list with a **Draft** badge and version 1.
- Opening the editor shows the English/Arabic body tabs, merge field list, and version timeline.
- Activating a template makes it available for document generation in consumer modules (Leasing, Facilities, Accounting).
- Each save creates a new version; existing documents pinned to older versions are not affected.

## Common issues

- **"Document Templates" is not in the admin panel** — your role does not have the `documents.VIEW` permission. Contact your Account Admin.
- **Save button does nothing** — check for validation errors on any field. Red error messages appear below invalid fields.
- **Merge field placeholder does not render** — make sure the field key in the template body matches exactly (including case and dots) the key you defined in the Merge fields section.
- **Template body looks empty in preview** — confirm you are viewing the tab that matches the language of the content you authored (English or Arabic). Switch tabs if needed.
- **Changes are not reflected in existing documents** — by design. Saved documents are pinned to the version that was current at generation time. New versions apply only to future documents.

## Related

- [Roles and permissions — overview](../admin/roles-and-permissions.md)
