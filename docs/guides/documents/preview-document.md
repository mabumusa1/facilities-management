---
title: Preview a document before sending
area: documents
layout: guide
lang: en
---

# {{ page.title }}

*Preview a document template filled with sample values or real data before generating and sending it, so you can catch placeholder errors and formatting issues.*

## Who this is for

Account Admins and anyone with the `documents.VIEW` permission who manages document templates and wants to verify a template looks correct before it reaches a tenant or owner.

## Before you start

- You must have the **documents.VIEW** permission. Account Admins have this by default.
- The template must have at least one published version. If a template has no published version, the preview returns an error.
- The preview is ephemeral — it does not create a DocumentRecord, appear in the document history, or persist anywhere. It is purely for review.

## Steps

### Preview with sample data

1. From the **Document Templates** list, click the **eye icon** (Preview) on any template.
2. A side sheet opens showing the preview rendered with synthetic sample values:
   - Text fields display the field key (e.g., `{{resident.name}}`).
   - Date fields display a sample date like `2026-06-15`.
   - Currency fields display a sample amount like `1,500.00 SAR`.
   - Number fields display a sample number like `1,500`.
3. Switch between **English** and **العربية** tabs to see the preview in either language. The Arabic preview renders right-to-left.

### Preview with unresolved fields

If any merge field has an empty key (no data), it appears in an amber warning banner at the top of the preview:

- The banner lists each unresolved field by its key.
- The warning reads: **"These fields have no data or reference. Proceed or cancel."**
- You can close the preview and fix the template's merge fields from the template editor.

### Preview with real data from a generation context

When generating a document from a consumer module (Leasing, Facilities, or Accounting), the preview endpoint can receive a `context` object with real data values:

```json
{
  "lang": "en",
  "context": {
    "resident.name": "Sarah Ahmad",
    "lease.start_date": "2026-07-01"
  }
}
```

- Fields present in the context override sample values.
- Fields not in the context fall back to sample values.
- This ensures the preview shows the document exactly as it will appear when generated.

## What you'll see

- A rendered text body with all `{{placeholders}}` replaced by sample or real data values.
- An amber warning banner if any merge fields could not be resolved.
- The preview text is displayed right-to-left when the Arabic tab is selected.
- Loading indicator while the preview is being generated.
- Empty templates render as an empty preview or the message "(empty preview)".

## Common issues

- **Preview returns an error** — the template has no published version. Open the template editor and save at least one version.
- **Merge field shows as `{{key}}` instead of a value** — the field key in the body does not match any key defined in the template's merge fields. Check spelling and case in the editor.
- **Preview is blank** — the template body for the selected language is empty. Switch to the other language tab or add content in the template editor.
- **Arabic text displays left-to-right** — click the العربية tab. The preview container switches to right-to-left direction automatically.

## Related

- [Manage document templates](./document-templates.md)
- [Roles and permissions — overview](../admin/roles-and-permissions.md)
