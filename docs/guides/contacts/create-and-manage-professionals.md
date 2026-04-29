---
title: Create and manage professional contacts
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*Add a vendor, contractor, or service provider as a Professional contact and assign them to service request categories so they appear as selectable assignees in the Service Requests module.*

## Who this is for
Property Managers and Admins who manage a roster of external service providers and route maintenance or service work to them.

## Before you start
- You must have the **Create Professional** permission. Ask your Account Admin if the **New Professional** button is not visible.
- Service request categories must already be configured in your account before you can assign a professional to them.

## Steps

### Create a professional contact

1. In the main navigation, go to **Contacts → Professionals** (جهات الاتصال ← المهنيون).
2. Click **New Professional** (مهني جديد).
3. Enter **Company name (EN)** and **Company name (AR)** (اسم الشركة - عربي) in their respective scripts.
4. Enter **Specialty** (التخصص), **Phone Number**, **Email**, and **Country Code**.
5. Optionally add a **Contact person name** and any additional notes.
6. Click **Create Professional** (إنشاء مهني).

The page redirects to the new professional's detail page.

### Assign service categories

Assigning categories determines which service request types the professional can be selected for.

1. On the Professional detail page, scroll to the **Service Categories** (فئات الخدمة) panel.
2. Click **Assign Category** (تعيين فئة).
3. Select one or more categories from the list (for example, **Plumbing** (سباكة) or **HVAC** (تكييف)).
4. Click **Save** (حفظ).

The selected categories appear in the Service Categories panel. The professional now appears as a selectable assignee when an admin routes a service request that belongs to one of those categories.

### Remove a service category

1. In the Service Categories panel, find the category row.
2. Click the delete icon (bin) on that row and confirm.

The professional no longer appears as an assignee for that category. Existing service requests already assigned to this professional are not affected.

### Edit a professional

1. On the Professional detail page, click **Edit** (تعديل).
2. Update the fields you need to change.
3. Click **Save** (حفظ).

## What you'll see

After creating a professional:
- You land on the Professional detail page.
- The professional appears in the **Contacts → Professionals** list immediately.
- A confirmation toast confirms **Professional created successfully.** (تم إنشاء المهني بنجاح.)

After assigning a category:
- The category row appears in the Service Categories panel.
- In the Service Requests module, the professional now appears as an available assignee for requests in that category.

## Common issues

- **"New Professional" button is missing** — your role does not have the Create Professional permission. Contact your Account Admin.
- **Required field error on submit** — phone number and company name (in at least one language) are required. Fill in all highlighted fields before saving.
- **Professional does not appear in the service request assignee picker** — check that the professional has been assigned to the correct service category. A professional with no category assignments does not appear in any filtered assignee list.

## Related

- [View contact activity history](./view-contact-activity.md)
- [Attach and manage KYC documents on a contact](./manage-kyc-documents.md)
