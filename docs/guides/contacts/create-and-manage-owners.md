---
title: Create and manage owner contacts
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*Add a Unit Owner contact, assign units to their portfolio, and view their ownership summary from a single profile page.*

## Who this is for
Property Managers and Admins who onboard property investors, assign owned units, and need a single record linking an owner's identity to their portfolio.

## Before you start
- You must have the **Create Owner** permission. Ask your Account Admin if the **New Owner** button is not visible.
- Unit records must already exist in the system before you can assign ownership. See [Properties](../properties/) for unit management.
- Each unit can have only one owner at a time. Check the unit's current ownership before assigning.

## Steps

### Create an owner contact

1. In the main navigation, go to **Contacts → Owners** (جهات الاتصال ← الملاك).
2. Click **New Owner** (مالك جديد).
3. Fill in **First name (EN)** and **Last name (EN)** in Latin characters.
4. Fill in **First name (AR)** (الاسم الأول - عربي) and **Last name (AR)** (الاسم الأخير - عربي) in Arabic script.
5. Enter **Email**, **Country Code**, and **Phone Number**. Follow the same local-format rules as for resident contacts (no leading zero, no country prefix).
6. Select **Nationality** and fill in the **ID type** and **ID number** fields.
7. Optionally select **Gender** and **Date of Birth**.
8. Click **Create Owner** (إنشاء مالك).

The page redirects to the new owner's detail page.

### Assign units to an owner

1. On the Owner detail page, scroll to the **Unit Ownership** (ملكية الوحدات) panel.
2. Click **Assign Unit** (تعيين وحدة).
3. Search for and select the unit by name or building.
4. Choose the **Ownership type**: **Full** (كامل) or **Partial** (جزئي).
5. If **Partial**, enter the ownership **Percentage** (النسبة).
6. Set the **Ownership start date** (تاريخ بدء الملكية). An end date is optional.
7. Click **Save** (حفظ).

The unit appears in the Unit Ownership list on the owner's profile. The unit's own detail view also shows the owner's name.

### Remove a unit from an owner

1. In the Unit Ownership panel, find the unit row.
2. Click the delete icon (bin) on that row and confirm.

The link between the owner and that unit is removed. The unit record itself is not deleted.

### View owner financial summary

The **Financial Summary** card on the Owner detail page shows aggregate income from active leases linked to units owned by this contact. This is a read-only view from the Accounting module — no accounting actions can be taken from here.

If the owner has no units assigned, the card shows **No active leases**.

## What you'll see

After creating an owner:
- You land on the Owner detail page.
- The owner appears in the **Contacts → Owners** list immediately.
- A confirmation toast confirms **Owner created successfully.** (تم إنشاء المالك بنجاح.)

After assigning a unit:
- The unit row appears in the Unit Ownership panel.
- The unit's detail page updates to show this owner's name.

## Common issues

- **"New Owner" button is missing** — your role does not have the Create Owner permission. Contact your Account Admin.
- **Unit does not appear in the search when assigning** — the unit may already be assigned to another owner. Open the unit's detail page to check its current ownership, or search the Contacts → Owners list for the existing owner.
- **Attempting to assign a unit that already has an owner** — the system blocks duplicate assignments and displays an error. The existing owner's record is not changed.
- **Financial summary shows zero even with assigned units** — the owner's units may not have active leases yet. Lease activity drives the financial summary.

## Related

- [Create and search resident contacts](./create-and-search-residents.md)
- [Attach and manage KYC documents on a contact](./manage-kyc-documents.md)
- [View contact activity history](./view-contact-activity.md)
