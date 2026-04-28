---
title: Add and manage dependents on a resident contact
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*Link family members — spouse, child, parent, or other relationship — to an existing Resident contact so the lease record reflects all occupants and their identity documents are tracked per person.*

## Who this is for
Property Managers and Admins managing residential occupancy where dependent records are required for lease compliance or building access.

## Before you start
- The parent Resident contact must already exist. Create the resident first if needed — see [Create and search resident contacts](./create-and-search-residents.md).
- You must have edit access to the Resident's contact record.

## Steps

### Add a dependent

1. In the main navigation, go to **Contacts → Residents** (جهات الاتصال ← المقيمون) and open the resident's detail page.
2. Scroll to the **Dependents** (المعالون) panel.
3. Click **Add Dependent** (إضافة معال).
4. Fill in **First name (EN)** and **Last name (EN)** in Latin characters.
5. Fill in **First name (AR)** (الاسم الأول - عربي) and **Last name (AR)** (الاسم الأخير - عربي) in Arabic script.
6. Select the **Relationship** (العلاقة) from the dropdown: **Spouse** (زوج/زوجة), **Child** (طفل), **Parent** (أحد الوالدين), or **Other** (أخرى).
7. Optionally enter the dependent's **ID type**, **ID number**, and **Date of Birth**.
8. Click **Save** (حفظ).

The dependent appears in the Dependents panel on the resident's detail page. The resident's dependent count updates immediately.

### Edit a dependent

1. In the Dependents panel, click the dependent's name to open their record.
2. Update the fields you need to change.
3. Click **Save** (حفظ).

### Remove a dependent

1. In the Dependents panel, find the dependent row.
2. Click the delete icon (bin) on that row and confirm.

If the dependent was listed on an active lease, a warning is shown:

> **This dependent is associated with an active lease.**
> *The lease record is not automatically updated. Use the Leasing module to amend the lease if needed.*

The dependent is removed from the resident profile. The lease record is not altered by this action alone.

## What you'll see

After adding a dependent:
- The new dependent row appears in the Dependents panel.
- The resident summary shows the updated dependent count.
- A confirmation toast confirms **Dependent added successfully.** (تمت إضافة المعال بنجاح.)

## Common issues

- **Duplicate dependent warning** — if you attempt to add a dependent with a national ID that is already linked to the same resident, an inline warning identifies the existing record. No duplicate is created.
- **Dependents panel not visible** — your role may not include access to dependent management. Contact your Account Admin.
- **Arabic name field does not switch to RTL** — ensure the system keyboard is set to Arabic. The field has RTL direction set automatically once Arabic characters are typed.

## Related

- [Create and search resident contacts](./create-and-search-residents.md)
- [Attach and manage KYC documents on a contact](./manage-kyc-documents.md)
