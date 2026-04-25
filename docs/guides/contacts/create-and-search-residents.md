---
title: Create and search resident contacts
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*Add a new resident to your community, search the existing list by name or phone, and handle duplicate-phone warnings before saving.*

## Who this is for
Property Managers and Admins who need to maintain a clean resident contact list before creating leases or service requests.

## Before you start
- You must have the **Create Resident** permission. Ask your Account Admin if the **New Resident** button is not visible.
- The resident's phone number, full name in English and Arabic, and a government-issued ID are required.

## Steps

### Search for an existing resident

1. In the main navigation, go to **Contacts → Residents** (جهات الاتصال ← المقيمون).
2. In the search box at the top of the list, type a name fragment (English or Arabic) or a phone number.
3. Results update automatically as you type. Each row shows the English name, Arabic name, phone number, number of linked units, number of leases, and status.
4. Click any row to open the resident's detail page.

### Create a new resident

1. From **Contacts → Residents**, click **New Resident** (مقيم جديد).
2. Fill in the **First name (EN)** and **Last name (EN)** fields in Latin characters.
3. Fill in **First name (AR)** (الاسم الأول - عربي) and **Last name (AR)** (الاسم الأخير - عربي) in Arabic script. These fields type right-to-left automatically.
4. Enter the resident's **Email** address.
5. Enter the **Country Code** (رمز الدولة) — default is `SA`. Accepted codes include SA, AE, KW, BH, QA, and OM.
6. Enter the **Phone Number** (رقم الهاتف) using the local format (for example, `512345678` for Saudi numbers, without the leading zero or country prefix).
7. When you move focus out of the Phone Number field, the form checks whether the phone number already exists in your community. If a match is found, an amber warning banner appears — see [Duplicate phone warning](#duplicate-phone-warning) below.
8. Choose an **ID type** (نوع الهوية) from the dropdown: National ID, Passport, Iqama, Emirates ID, or Other.
9. Enter the **ID number** (رقم الهوية).
10. Optionally select **Gender** (الجنس) and **Date of Birth** (تاريخ الميلاد).
11. Click **Create Resident** (إنشاء مقيم).

The page redirects to the new resident's detail page and shows a confirmation message.

### Duplicate phone warning

If the phone number you entered matches a resident who already exists in your community, an amber banner appears below the phone fields:

> **A resident with this phone number already exists.**
> *Name of matched resident · Unit / Building (if linked)*

You have two options:

- **Go to existing record** (الانتقال إلى السجل الموجود) — click this link to open the existing resident and use that record instead. Recommended in most cases.
- **Create a new record anyway** — if the two people genuinely share a phone number (for example, a parent and adult child), tick the checkbox **I confirm this is a different person and want to create a new record anyway.** (أؤكد أن هذا شخص مختلف وأريد إنشاء سجل جديد.) The **Create Resident** button becomes active once the checkbox is ticked.

## What you'll see

After a successful save:
- You land on the new resident's detail page.
- The resident appears in the **Contacts → Residents** list immediately.
- A toast notification confirms **Resident created successfully.** (تم إنشاء المقيم بنجاح.)

## Common issues

- **The "New Resident" button is missing** — your role does not have the Create Resident permission. Contact your Account Admin.
- **Phone validation error** — check that you entered only the local subscriber number (no leading zeros, no country prefix) and that the Country Code field contains the correct ISO code (for example, `SA` not `+966`).
- **Create Resident button stays greyed out** — the duplicate phone banner is visible and the confirmation checkbox has not been ticked. Either go to the existing record or tick the checkbox to proceed.
- **Arabic name field does not switch to RTL** — the browser or system keyboard must be set to Arabic. The field has `dir="rtl"` set automatically, so text aligns correctly once you start typing Arabic characters.
- **Search returns no results for an Arabic name** — make sure the UI locale is set to Arabic or that you are using the correct Arabic script. The search is case-insensitive but requires the correct script; transliterations (for example, typing "Ahmed" to find "أحمد") are not matched in the current version.

## Related

- [Assign a role to a user](../admin/assign-a-role-to-a-user.md)
- [Create a lease](../leasing/create-a-lease.md)
