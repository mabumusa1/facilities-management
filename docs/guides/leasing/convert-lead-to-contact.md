---
title: Convert a lead to a contact
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Turn a Qualified lead into an Owner or Resident contact record, with automatic duplicate detection to keep your contacts list clean.*

## Who this is for

Property Managers and Admins who hold the `leads.UPDATE` permission. The **Convert to Contact** (تحويل إلى جهة اتصال) button is hidden for users without this permission.

## Before you start

- The lead must be in **Qualified** status. The convert button does not appear for leads in New, Contacted, Lost, or Converted status.
- Decide whether the lead will become an **Owner** (مالك) or a **Resident** (مستأجر). You choose this inside the conversion drawer.
- You cannot reverse a conversion once it is confirmed.

## Steps

### 1. Open the lead detail page

1. Go to **Leasing → Leads** (التأجير → العملاء المحتملون).
2. Find the lead with **Qualified** status and click its name to open the detail page.

   Alternatively, click the row menu on the leads list and select **Convert to Contact** (تحويل إلى جهة اتصال) to start the conversion without first opening the detail page.

### 2. Open the Convert to Contact drawer

On the lead detail page, click **Convert to Contact** (تحويل إلى جهة اتصال) in the action bar.

The **Convert to Contact** (تحويل إلى جهة اتصال) drawer opens on the right side of the screen.

### 3. Select the contact type

Under **Contact Type** (نوع جهة الاتصال), choose one:

- **Owner** (مالك) — the contact will be added to Contacts → Owners.
- **Resident** (مستأجر) — the contact will be added to Contacts → Residents.

A **Data to be transferred** (البيانات التي سيتم نقلها) preview shows the first name (EN), last name (EN), first name (AR), last name (AR), email, and phone that will be copied from the lead to the new contact record.

### 4. Run the conversion

Click **Convert** (تحويل).

The system checks whether a contact with the same email address or the same phone number already exists in your account.

**If no duplicate is found**, the contact is created immediately. Skip to [What you'll see](#what-youll-see).

**If a possible duplicate is found**, continue to step 5.

### 5. Handle a possible duplicate

A **Possible Duplicate Found** (تحذير: احتمال وجود تكرار) dialog appears with the message:

> A contact with a matching phone or email already exists. (يوجد بالفعل جهة اتصال بنفس الهاتف أو البريد الإلكتروني.)

Choose one of the two options:

**Option A — Link to the existing contact (recommended)**

1. Select **Link lead to this existing contact (recommended)** (ربط العميل بجهة الاتصال الحالية (موصى به)).
2. Click **Continue** (متابعة).
3. A confirmation dialog titled **Link Lead to Contact** (ربط العميل بجهة اتصال موجودة) appears with the note: "No new contact record will be created."
4. Click **Confirm Link** (تأكيد الربط).

No new contact is created. The lead is linked to the existing contact and its status changes to **Converted** (تم التحويل).

**Option B — Create a new contact anyway**

1. Select **Create a new contact record anyway** (إنشاء سجل جهة اتصال جديد على أي حال).
2. Click **Continue** (متابعة).
3. A confirmation dialog titled **Create New Contact?** (إنشاء جهة اتصال جديدة؟) appears with a warning: "Creating a new record may result in duplicate contacts in the system."
4. Click **Create Anyway** (إنشاء على أي حال).

A new contact record is created even though a similar contact exists. Use this option only when you are certain the lead represents a different person.

## What you'll see

After a successful conversion:

- A success message appears at the top of the page:
  - "Lead converted — contact created." if a new contact was created.
  - "Lead linked to existing contact." if the lead was linked to an existing record.
- The lead status changes to **Converted** (تم التحويل).
- A **Converted Contact** (جهة الاتصال المحولة) card appears on the lead detail page showing:
  - The conversion date under **Converted** (تاريخ التحويل).
  - A **View contact** link that opens the new or linked contact record.
- The Activity tab on the lead records the event: "Lead converted to [Owner / Resident] contact."

## Common issues

- **The Convert to Contact button is not visible.** Either the lead is not in Qualified status, or your account does not have the `leads.UPDATE` permission. Check the lead's status badge. If the status is correct, contact your Account Admin to verify your permissions.
- **The Convert to Contact button is visible but clicking it does nothing.** Refresh the page and try again. If the issue persists, the lead may have been converted by another user at the same time — check whether a Converted Contact card now appears on the page.
- **The conversion fails with an error.** Click Convert again. If the duplicate check fails, an error message explains what went wrong. If the problem continues, contact support.
- **I converted the lead but cannot find the new contact.** Go to **Contacts → Owners** or **Contacts → Residents** (depending on the type you chose) and search by name or phone. The contact is scoped to your account only.

## Related

- [Manage leads](./manage-leads.md)
- [Create an owner contact](../contacts/create-owner-contact.md)
- [Create a resident contact](../contacts/create-resident-contact.md)
