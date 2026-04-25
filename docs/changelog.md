---
title: What's New
layout: page
lang: en
---

# What's New

A plain-language running list of user-visible changes. For the developer-facing changelog, see [`CHANGELOG.md`](https://github.com/mabumusa1/facilities-management/blob/main/CHANGELOG.md) in the repo root.

## Unreleased

### Lease quotes — April 25, 2026

The platform now supports the concept of a **lease quote**: a formal, time-limited tenancy offer you send to a prospective resident before a binding lease is created. Lease quotes will be manageable from **Leasing → Quotes** in an upcoming release.

Key things to know now:

- **Six statuses.** A quote starts as a draft, gets sent, can be marked viewed, then ends as accepted, rejected, or expired.
- **Automatic expiry.** If the prospect has not responded by the valid-until date, the platform marks the quote expired overnight — no manual action needed.
- **Revision history.** Revising a quote creates a new version instead of overwriting the original, preserving the full offer history.
- **One-click conversion.** An accepted quote converts to a lease with all fields pre-filled.

The screens to create, revise, and convert quotes are arriving in the next few releases.

Learn more: [Understanding lease quotes](./guides/leasing/lease-quotes.md).

### Transaction categories — April 25, 2026

You can now configure the income and expense categories used when recording transactions.

- **Default categories included.** Your account starts with six pre-built categories: Rent, Late Fee, and Service Fee on the income side; Maintenance, Utility, and Repairs on the expense side. They are ready to use immediately.
- **Add custom categories.** Go to **Accounting → Settings → Transaction Categories** and click **Add Category**. Give the category a name in English and Arabic, choose Income or Expense as the type, and save.
- **Edit names.** Click **Edit** on any category to update the English or Arabic name. The Income/Expense type cannot be changed after creation.
- **Deactivate when no longer needed.** Click **Deactivate** to hide a category from new transaction forms. Existing transactions keep their category reference — nothing is lost. You can reactivate at any time.
- **Default categories are protected.** The six built-in defaults carry a **Default** badge and cannot be deleted. Deactivate them if you do not need them.
- **Bilingual.** All category names are stored in English and Arabic and display in both languages.

Learn more: [Configure transaction categories](./guides/accounting/transaction-categories.md).

### Service request reference codes — April 25, 2026

Every service request now has a unique reference code in the format `SR-YYYY-NNNNN` (for example, `SR-2026-00042`). The code appears in the request header and in all list views, making it easy to quote a specific request to a resident or in a support conversation without sharing internal IDs. The sequence restarts each calendar year and is guaranteed unique within your account.

This release also puts in place the infrastructure for two features coming soon: a per-request messaging thread (resident and staff chat, with internal-only notes) and an activity timeline that logs every status change and key action on a request.

Learn more: [Service request reference codes](./guides/service-requests/service-request-reference-codes.md).

### Resident contacts — April 25, 2026

You can now create resident contact records and search the full list by name (English or Arabic) or phone number.

- **Create a resident.** Go to **Contacts → Residents** and click **New Resident**. Fill in the English and Arabic name fields, phone number, email, ID type, and ID number.
- **Bilingual name fields.** The form has separate fields for the English name and the Arabic name. Arabic fields type right-to-left automatically.
- **Duplicate phone check.** As soon as you leave the phone number field, the platform checks whether that number is already on file. If a match exists, an amber banner shows the matched resident's name and unit so you can go to the existing record instead of creating a duplicate.
- **Create anyway.** If two people genuinely share a phone number, tick the confirmation checkbox in the banner to unlock the save button and proceed.
- **Search by name or phone.** The search box on the Residents list filters in real time across English names, Arabic names, phone numbers, and email addresses.

Learn more: [Create and search resident contacts](./guides/contacts/create-and-search-residents.md).

### Company profile — April 2026

You can now configure your company identity, logo, timezone, and brand colour from one settings page. These details appear consistently on all contracts, invoices, and the sign-in page.

- **Identity.** Set your company name in English and Arabic, VAT registration number (15 digits, appears on invoices), and commercial registration number.
- **Logo & brand.** Upload a primary logo for English documents and an optional Arabic variant for Arabic documents. Accepted formats: PNG or SVG, 2 MB max. Click **Remove** to delete a logo — the file is cleaned from storage on save.
- **Regional.** Pick a timezone from a Gulf-pinned list. Affects lease dates, invoice due dates, and booking slots across the platform.
- **Brand colours.** Enter a hex colour code (e.g. `#1A73E8`) with a live swatch preview. Applied to email notification headers and document template accents — it does **not** change the sidebar.
- **Unsaved changes.** A sticky bar slides up from the bottom whenever you modify a field, keeping you aware of uncommitted changes. Click **Save changes** or **Discard**.
- **Bilingual.** The full page works in English and Arabic (RTL) with proper `dir` attributes on directional inputs.

Learn more: [Configure your company profile](./guides/app-settings/company-profile.md).

### Community metadata — April 2026

You can now set a community's amenities, weekly working days, and map location from a single edit form.

- **Amenities.** Pick from a standard 26-item catalog — gym, pool, parking, children's play area, and more. Click the × on any selected chip to remove it.
- **Working days.** Toggle each day of the week. The strip starts on Saturday per the GCC calendar. Amber highlights working days, grey marks non-working days. Leave everything off to mark the community as closed every day.
- **Map location.** Enter latitude and longitude as decimals, or click "Use my location" to auto-fill. Both values must be set together.
- **Bilingual.** The whole form works in English and Arabic. The day strip stays Saturday-first in Arabic; geographical coordinates are not flipped.
- **Amenity safety.** Updating other fields won't wipe your selected amenities — only explicit changes to the amenity list are saved.

Learn more: [Configure community metadata](./guides/properties/community-metadata.md).

### Sign-in improvements — April 2026

Signing in now takes you to the right page for your role automatically, handles rate-limit cooldowns more clearly, and works correctly in Arabic right-to-left.

- **Role-based landing.** Admins and Managers land on the Admin dashboard after sign-in. Owners and Residents land on the shared home today; dedicated portals are coming.
- **Clearer "too many attempts" message.** If you type the wrong password several times, the platform shows a clear amber banner with a countdown (in English and Arabic) telling you how many seconds remain before you can try again.
- **Right-to-left sign-in.** The password show/hide toggle, language switcher, and error messages are now positioned correctly when the page is in Arabic.
- **Screen-reader support.** Validation errors in the sign-in form are announced live by screen readers, so users on assistive tech get immediate feedback.

Learn more: [Sign in to your account](./guides/auth/login.md).

### Roles and permissions — April 2026

We added a full role-based access system so you can control exactly what each person on your team can see and do.

- **12 roles out of the box.** Every account now has 7 user-type roles (Account Admin, Admin, Manager, Owner, Tenant, Dependent, Professional) and 5 admin-function roles (System Admin, Accounting Manager, Service Manager, Marketing Manager, Sales & Leasing Manager). Existing admins keep their access — nothing breaks.
- **Manage roles from the admin panel.** Go to **Admin → Roles** to create custom roles alongside the defaults. Role names are bilingual (English + Arabic). Learn more: [Create a role](./guides/admin/create-a-role.md).
- **Edit permissions in a matrix.** A 31-subject × 6-action grid lets you tick exactly which actions a role can perform. Starter presets load a typical permission set you can then adjust. Learn more: [Assign permissions to a role](./guides/admin/assign-permissions-to-a-role.md).
- **Assign roles with a scope.** When you assign an admin-type role to a user, you can restrict it to specific communities, buildings, or service types. Service Managers can be scoped to specific service types as well. Learn more: [Assign a role to a user](./guides/admin/assign-a-role-to-a-user.md) and [Scope a manager to specific properties](./guides/admin/manager-scope.md).
- **Unauthorized access is blocked.** Every non-public page and action is now permission-gated. Users without the required permission see a clear error instead of the page.

Start here: [Roles and permissions — overview](./guides/admin/roles-and-permissions.md).
