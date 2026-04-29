---
title: Attach and manage KYC documents on a contact
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*Upload, view, and delete identity verification documents (passport, national ID, visa, and others) on a Resident, Owner, or Professional contact record.*

## Who this is for
Property Managers and Admins who need to keep identity documents on file for lease audits, compliance checks, or onboarding workflows.

## Before you start
- You must have edit access to the contact record. If the KYC Documents section is not visible, contact your Account Admin about your permissions.
- Accepted file formats: PDF, JPG, JPEG, PNG. Maximum file size per document is 10 MB.
- The contact record (Resident, Owner, or Professional) must already exist before you can attach documents.

## Steps

### Upload a KYC document

1. In the main navigation, go to **Contacts** (جهات الاتصال) and open the contact record.
2. Scroll to the **KYC Documents** (وثائق التحقق من الهوية) section on the contact detail page.
3. Click **Upload Document** (رفع وثيقة).
4. Select the document type from the dropdown: **Passport** (جواز سفر), **National ID** (هوية وطنية), **Visa** (تأشيرة), or **Other** (أخرى).
5. Choose the file from your device.
6. If the document has an expiry date, enter it in the **Expiry Date** (تاريخ الانتهاء) field.
7. Click **Save** (حفظ).

The document appears in the KYC Documents list with its type label, upload date, expiry date (if entered), and a **Download** link.

### Download a KYC document

1. In the KYC Documents section, find the document you need.
2. Click **Download** (تحميل) on that row.

The file downloads directly to your device. Download links are for internal admin use only and are not shareable externally.

### Delete a KYC document

1. In the KYC Documents section, find the document row.
2. Click the delete icon (bin) on that row.
3. Confirm the deletion in the dialog that appears.

The document is permanently removed from the contact record. If you delete the only document on file, the KYC Documents section shows an empty state — the contact record itself is not affected.

## What you'll see

After a successful upload:
- The new document row appears at the top of the KYC Documents list.
- The document count on the contact summary updates immediately.
- A confirmation toast confirms **Document uploaded successfully.** (تم رفع الوثيقة بنجاح.)

## Common issues

- **Upload rejected — unsupported file type** — only PDF, JPG, JPEG, and PNG files are accepted. Convert other formats before uploading.
- **Upload rejected — file too large** — reduce the file size or scan at a lower resolution and try again.
- **KYC Documents section not visible** — your role may not include document access permissions. Contact your Account Admin.
- **Download link opens a blank page** — the file may still be processing. Wait a few seconds and refresh the page.

## Related

- [Create and search resident contacts](./create-and-search-residents.md)
- [Create and manage owner contacts](./create-and-manage-owners.md)
