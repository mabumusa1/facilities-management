---
title: What's New
layout: page
lang: en
---

# What's New

A plain-language running list of user-visible changes. For the developer-facing changelog, see [`CHANGELOG.md`](https://github.com/mabumusa1/facilities-management/blob/main/CHANGELOG.md) in the repo root.

## Unreleased

### Record money-in transactions — April 25, 2026

You can now record offline payments received from residents and owners directly in the Accounting module.

- **Record a payment.** Go to **Accounting → Transactions** and click **New Transaction**. Choose the payer (resident or owner), the unit, the income category (Rent, Late Fee, Service Fee, etc.), the payment method (Cash, Bank Transfer, or Cheque), the amount, and the date. Add a reference number if you have one (for example, a cheque number or bank transfer reference).
- **Auto-generated receipt.** Every transaction you save generates a receipt automatically. The receipt number is assigned by the platform in sequence — you do not need to create it manually.
- **Send receipt by email.** Open the transaction, then click **Send Receipt** in the Receipt card. Confirm the payer name and email address, and the receipt is sent to their email on file. The card shows the date it was last sent. If you need to send again, the button changes to **Resend Receipt**.
- **Invoice Settings requirement.** If your Invoice Settings (company name, logo, address) are not yet configured, an amber banner appears on the transaction form and detail page. The transaction saves, but the receipt is held until you complete the settings under **App Settings → Invoice Settings**.
- **Permission-gated email.** The Send Receipt button is only visible to users with the `transactions.SEND_RECEIPT` permission. Account Admins and Accounting Managers have this permission by default.
- **Known limitation.** PDF download is not yet available and will be added in a future release.

Learn more: [Record a money-in transaction](./guides/accounting/record-money-in.md).

### Register a visitor (QR invitations) — April 25, 2026

Residents can now create visitor invitations and share a QR code directly from their account.

- **Register a visitor.** Go to **My Visitors** and tap **Register Visitor**. Enter the visitor's name, purpose (Visit, Delivery, Service, or Other), expected arrival date and time, and an optional phone number. Tap **Generate QR Code** and the invitation is created instantly.
- **QR code to share.** The **Invitation Created** screen shows the QR code and a **Share QR** button. Tap it to send the code via WhatsApp, SMS, or any app on your device. Your visitor shows the code to the gate officer — no phone call needed.
- **My Visitors list.** All your invitations appear under **My Visitors**, split into Active and Past sections. Each card shows the visitor name, purpose, expected arrival, and a live status badge.
- **Cancel at any time.** Tap **Cancel Invitation** on any active invitation to immediately invalidate the QR code before the visitor arrives.
- **Automatic expiry.** If a visitor does not arrive by the valid-until time, the invitation is marked Expired overnight. Create a new invitation if they still need to visit.
- **Bilingual.** All screens work in English and Arabic, including Arabic name input on the registration form.

Learn more: [Register a visitor](./guides/visitor-access/register-a-visitor.md).

### Lease quotes — create and send — April 25, 2026

Property Managers and Admins can now create lease quotes and send them to prospective residents directly from the platform.

- **Create a quote.** Go to **Leasing → Quotes** and click **New Quote**. Select an available unit, pick the prospective resident from Contacts, set the contract type, lease duration, start date, rent amount, payment frequency, security deposit, and a valid-until deadline. Add optional extra charges (parking fee, etc.) with bilingual labels in English and Arabic. Add special conditions in both languages.
- **Save as Draft or send immediately.** Click **Save as Draft** to keep the quote private while you finalise the terms. Click **Send Quote** to dispatch it to the prospect in one step — no separate send action needed.
- **Send a draft later.** Open any Draft quote from the Quotes list and click **Send** on the detail page. The status moves from **Draft** to **Sent** and the prospect receives an email.
- **Secure prospect preview.** The email contains a link gated by a unique, cryptographically random token — the prospect can view the full quote without creating an account or logging in.
- **Automatic status tracking.** When the prospect opens the link, the quote status moves to **Viewed** automatically. The Quotes list reflects this in real time so you always know where each deal stands.
- **Status at a glance.** Every quote shows its current status — Draft, Sent, Viewed, Accepted, Rejected, or Expired — on both the list and the detail page.

Learn more: [Create and send a lease quote](./guides/leasing/lease-quotes.md).

### Document templates — April 26, 2026

You can now create and manage document templates with named merge fields for lease contracts, invoices, receipts, and booking documents.

- **Create templates.** Go to **Admin → Document Templates**, click **Create template**, give it a name (English required, Arabic optional), choose a type (Lease, Booking, Invoice, Receipt, or Custom), and save. The template starts as a draft.
- **Bilingual body.** Switch between English and Arabic tabs in the editor to author content for each language. Use `{{merge_field_key}}` placeholders where variable data should be inserted.
- **Merge fields.** Define which data points the platform should pull — resident name, lease start date, invoice amount — each with a key, English/Arabic label, data type (text, date, currency, number), and source path. Click **Add field** to build the list.
- **Version history.** Every save creates a new version. The right-hand sidebar shows all versions in descending order. Click any version to preview its body and merge fields. Existing generated documents stay pinned to the version that was current at generation time.
- **Activate and archive.** Click **Activate** to make a draft template selectable when generating documents in Leasing, Facilities, or Accounting. Click **Archive** to retire a template from new use — existing documents remain intact.
- **Admin-only access.** The Document Templates area is available only to Account Admins. Users without the `documents.VIEW` permission see a 403 error.
- **Template preview.** Click the eye icon on any template to preview it with sample data before generating and sending. Switch between English and Arabic tabs — Arabic previews render right-to-left. Unresolved merge fields show an amber warning. The preview is ephemeral (no DocumentRecord is created). Preview is also available from the generation context in consumer modules with real data.

Learn more: [Manage document templates](./guides/documents/document-templates.md) and [Preview a document](./guides/documents/preview-document.md).

### Facility availability and waitlist — April 25, 2026

The groundwork for facility bookings is now in place. Property Managers will soon be able to set opening hours for each facility on a per-day basis, and residents will be able to join a waitlist when a slot is full.

- **Availability rules.** Each facility will support a separate opening window for each day of the week — including open time, close time, session length, and the maximum number of overlapping bookings. A sample Gym facility is active with Monday–Saturday 06:00–22:00 hours so testing can begin immediately.
- **Waitlist.** When a slot is fully booked, residents will be able to join a first-in, first-out queue. If a confirmed booking is cancelled, the first resident in the queue receives a notification and a time-limited window to claim the slot.
- **Booking time ranges.** Bookings will record an exact start and end time so the calendar view and conflict checks work correctly.
- **Cancellation tracking.** Bookings will track the cancellation time, the reason, and whether it was cancelled by a resident or an admin.

The booking and facility-configuration UI is coming in upcoming releases. Learn more: [Facility availability and waitlist](./guides/facilities/availability-and-waitlist.md).

### Reports — access control — April 25, 2026

Access to all report types is now controlled by the `reports.VIEW` permission. Account Admins, System Admins, and Accounting Managers have this permission by default. If you need access and cannot open the Reports section, ask your System Admin to add the permission to your role.

The report viewer pages are in development and will ship soon. Learn more: [Reports — snapshots overview](./guides/reports/snapshots-overview.md).

### Visitor access — April 25, 2026

The platform now has the data foundation for QR-coded visitor gate passes. The screens for creating invitations, scanning at the gate, and viewing the logbook are coming in the next several releases. Here is what is being put in place behind the scenes:

- **Visitor invitations.** Residents will be able to invite a named guest, specify the visit purpose (Visit, Delivery, Service, or Other), set an expected arrival time, and receive a unique QR code to share with the visitor.
- **Gate logbook.** Every entry and exit is recorded alongside the gate officer who processed it and whether ID was verified. Visitors without a prior invitation can be admitted as walk-ins — these are also logged.
- **Per-community settings.** Admins can configure whether ID verification is required, whether walk-ins are allowed, how long a QR code stays valid (default: 24 hours), and how many times a single invitation code may be scanned at the gate (default: once).
- **Gate Officers role.** A new **Gate Officers** admin role is available in **Admin → Users**. Assign it to security staff so they can access the gate check-in and check-out screens.

Learn more: [Visitor access — overview](./guides/visitor-access/overview.md).

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

### Backend — April 25, 2026

Backend: Fortify authentication features expanded to support upcoming profile management and password self-service.
Backend: New Documents infrastructure for contract, invoice, and receipt generation across Leasing, Facilities, and Accounting modules.

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
