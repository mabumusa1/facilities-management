---
title: What's New
layout: page
lang: en
---

# What's New

A plain-language running list of user-visible changes. For the developer-facing changelog, see [`CHANGELOG.md`](https://github.com/mabumusa1/facilities-management/blob/main/CHANGELOG.md) in the repo root.

## Unreleased

### Leads Excel import (Leasing) — April 30, 2026

Admins and Property Managers can now import multiple leads at once by uploading an Excel file. The import flow validates every row before saving anything, shows you exactly which rows have problems and why, and lets you proceed with only the valid rows.

- **Download the template.** Click **Import Leads** (استيراد العملاء) on the Leads page and choose **Download Template** (تنزيل القالب) to get the required spreadsheet with the correct column headers.
- **Upload and preview.** Fill in the template and upload it via **Import from Excel** (استيراد من Excel). The system parses the file immediately and opens a review screen with three summary cards: total rows, valid rows, and error rows.
- **Row-level error details.** An error table below the cards shows every problem row with the row number, field name, and error message — so you know exactly what to fix.
- **Import valid rows only.** When a file is a mix of valid and errored rows, a confirmation dialog makes it clear that only the passing rows will be saved. Errored rows are never partially saved.
- **Error report download.** After reviewing, click **Download Error Report** (تنزيل تقرير الأخطاء) to get a CSV of the failing rows. Use it to fix and re-import those leads.
- **Automatic source tagging.** Every lead brought in through the importer is automatically labelled **Excel Import** as its source.

Files must be `.xlsx` or `.xls`, no larger than 5 MB, and contain no more than 500 data rows.

Learn more: [Import leads from Excel](./guides/leasing/import-leads-from-excel.md) ([العربية](./ar/guides/leasing/import-leads-from-excel.md)).

### Lead detail page (Leasing) — April 30, 2026

Admins and Property Managers can now click any row in the Leads list to open a full lead record. From that page you can update the lead's status (including recording a lost reason), assign or reassign the lead to a team member, add free-text notes, and read a complete activity timeline showing every status change, assignment, and note in chronological order.

Learn more: [View and manage a lead's detail](./guides/leasing/manage-lead-detail.md) ([العربية](./ar/guides/leasing/manage-lead-detail.md)).

### Lease renewal offers (Leasing) — April 29, 2026

Property Managers can now generate, send, and track renewal offers directly from the lease detail page — keeping the full renewal decision on record inside the platform.

- **Renewal window.** When a lease is within 90 days of its end date, a blue **Renewal** banner appears on the lease detail page with a countdown and a **Generate Renewal Offer** (إنشاء عرض تجديد) button.
- **Pre-filled offer form.** The form opens with the current unit, tenant, rent amount, payment frequency, and contract type already filled in. Adjust the rent, duration, and valid-until deadline as needed. A difference indicator next to the rent field shows how much the proposed amount is above or below the current rent.
- **Bilingual message.** Add a personal message to the tenant in English and Arabic before saving or sending.
- **Send by email.** Click **Renewal Offer: Sent** (عرض التجديد: مرسل) in the banner to dispatch the offer. The tenant receives an email with the full terms.
- **Record the decision.** After the tenant responds, click **Record Decision** (تسجيل القرار) in the banner, choose **Accepted** (مقبول) or **Declined** (مرفوض), and confirm. The status updates immediately.
- **Convert to a new lease.** If the tenant accepted, click **Convert to New Lease** (تحويل إلى عقد جديد) to open the new lease form pre-filled with the renewal terms.
- **Automatic expiry.** If no decision is recorded by the valid-until date, the offer is automatically marked **Expired** overnight. The original lease is never modified by expiry.
- **Renewal Offers index.** Go to **Leasing → Renewal Offers** (عروض التجديد) to see every offer across your portfolio, with filters by status and lease contract number.

Learn more: [Generate and track lease renewal offers](./guides/leasing/lease-renewals.md) ([العربية](./ar/guides/leasing/lease-renewals.md)).

### Move-out workflow — inspection and deposit deductions (Leasing) — April 29, 2026

Property Managers can now record a full move-out directly from the lease detail page, covering everything from the departure date through room inspection to the final deposit settlement calculation.

- **Initiate the move-out.** Open an active lease and click **Initiate Move-Out** (بدء الإخلاء). Set the move-out date (defaults to the lease end date), pick a reason — End of lease term, Early termination by tenant, Early termination by management, or Other — and confirm. The unit changes to **Under Maintenance** status immediately and the lease financial terms are locked.
- **Room-by-room inspection.** On the Inspection page, name each room, choose a condition rating (**Excellent**, **Good**, **Fair**, or **Poor**), add notes, and upload photo evidence. A progress bar tracks how many rooms have been rated. Use **+ Add Room** to add spaces not in the default list. Click **Save Inspection** to save at any time, or **Proceed to Deductions** when ready.
- **Deposit deductions.** On the Deposit Deductions page, click **+ Add Deduction** (+ إضافة خصم) to record each charge with a bilingual label (English and Arabic), an amount, and a reason (Damage, Cleaning, Unpaid Rent, Utility, or Other). The summary card updates instantly to show the running **Refund Amount** (مبلغ الاسترداد) or, if deductions exceed the deposit, the **Outstanding Charge** (المبلغ المستحق). Deductions exceeding the deposit are allowed after acknowledging the amber warning.
- **One move-out per lease.** You cannot start a second move-out while one is already in progress — the system redirects you to the existing record.

The settlement step (finalising the refund payment to the tenant) will be added in an upcoming release.

Learn more: [Process a move-out — inspection and deposit deductions](./guides/leasing/lease-move-out.md) ([العربية](./ar/guides/leasing/lease-move-out.md)).

### Send notices to tenants directly from the lease record (Leasing) — April 29, 2026

Property Managers and Admins can now send formal notices to a tenant without leaving the lease record — no switching to email. All notices are stored permanently on the lease with a full audit trail.

- From any active lease's detail page, click **Send Notice** (إرسال إشعار). The button shows a badge with the count of notices already sent.
- Choose one of four notice types: **Rent Increase** (زيادة الإيجار), **Renewal Offer** (عرض تجديد), **Move-Out Reminder** (تذكير بالإخلاء), or **Free-form Notice** (إشعار حر).
- Fill in both the English and Arabic subject and body. Both are required — the notice is delivered bilingual in a single communication.
- Use the **Preview** (معاينة) toggle to review the full English-then-Arabic text before sending.
- If the tenant's Resident contact has no email address, the form is replaced by a warning with a direct link to **Edit Contact** (تعديل جهة الاتصال).
- After sending, the **Notice History** (سجل الإشعارات) card on the same page records every notice with its type badge, send date and time, subject, and an expandable **View Body** (عرض المحتوى) link. The record is permanent — sent notices cannot be edited or deleted.
- The audit trail for each notice includes who sent it (**sent_by**) and the exact timestamp (**sent_at**).

Learn more: [Send a notice to a tenant](./guides/leasing/tenant-notices.md) ([العربية](./ar/guides/leasing/tenant-notices.md)).

### Amend lease terms with history trail (Leasing) — April 29, 2026

Property Managers and Admins can now formally amend the structural terms of an active lease and have every change recorded in a permanent, auditable history.

- From any active lease's detail page, click **Amend Lease Terms** (تعديل شروط العقد) to open the amendment form.
- Six fields are amendable: **End Date**, **Total Rental Amount**, **Contract Type**, **Payment Schedule**, **Security Deposit**, and **Terms and Conditions**. Each field shows the current value so you always know what you are changing.
- A live **Diff Preview** (معاينة الفروقات) table updates as you type, highlighting each modified field with a **(changed)** badge in amber and leaving unmodified fields labelled **(unchanged)**.
- A **Reason for Amendment** (سبب التعديل) is required before you can save. The reason is stored with the amendment and is visible to all managers.
- After saving, the **Amendment History** (سجل التعديلات) card appears at the bottom of the lease detail page. Each entry shows the amendment number, who made it, when, the reason, and a field-by-field table of old and new values.
- Tenant reassignment is not part of this form — use the relocation process for that.
- An optional **Generate signed addendum after saving** (إنشاء ملحق موقع بعد الحفظ) checkbox is available; addendum generation will be fully wired once the Documents workflow ships.

Learn more: [Amend lease terms with history trail](./guides/leasing/lease-amendments.md) ([العربية](./ar/guides/leasing/lease-amendments.md)).

### Complete unit metadata — specs, amenities, and pricing reference (Properties) — April 28, 2026

Unit records are now fully editable from a single **Edit** page. You can set room counts (bedrooms, bathrooms, living rooms), physical specifications (furnished status, parking bays, view type), amenity tags, and an asking rent reference — all saved to the unit and immediately visible on its detail page.

- The unit detail page now shows a **Specifications** card, an **Amenities** card, and a **Pricing Reference** card alongside the existing details.
- **Amenities** show as badge labels; if none are assigned, the card displays **No amenities listed** — the unit is still publishable on the Marketplace.
- **Asking rent** is a reference value for Marketplace listings and Leasing quotes. It is not a binding contract price.
- Area must be greater than 0; asking rent must be greater than 0 when entered.

Learn more: [View and edit unit metadata](./guides/properties/view-and-edit-unit-metadata.md) ([العربية](./ar/guides/properties/view-and-edit-unit-metadata.md)).

### Bulk import units from Excel (Properties) — April 28, 2026

Property Managers and Admins can now onboard a large portfolio of units in a single session using a guided import wizard from **Properties → Units**.

- **Download the template.** Click **Import Units** (استيراد وحدات) on the Units page and then **Download template** (تحميل النموذج) to get a pre-formatted `.xlsx` file with the correct column headings.
- **Upload your file.** Drag and drop the completed `.xlsx` file (up to 10 MB) onto the upload zone or click to browse.
- **Map your columns.** The system auto-detects column headers and maps them to the five system fields — Unit Name (اسم الوحدة), Community (المشروع), Building (المبنى), Area sqm (المساحة (م²)), and Status (الحالة). Green **matched** (متطابق) badges confirm auto-detected mappings; amber **unmatched** (غير متطابق) badges flag columns you need to assign manually.
- **Review validation.** Before anything is saved, the system checks every row for required fields, area greater than zero, a recognised status value, buildings that exist, and duplicate unit names within the same building. A summary shows how many rows are valid and how many have errors, with row-by-row detail.
- **Import valid rows.** Click **Import N valid rows** (استيراد N صف صحيح) to proceed with the rows that passed. Rows with errors are skipped. If you prefer to fix all errors first, click **Cancel and fix the file** (إلغاء وتصحيح الملف).
- **Background processing.** Files with more than 50 rows are processed in the background. The wizard shows a progress bar for smaller imports and notifies you when a queued import is complete: *N units imported successfully. E rows skipped due to errors.* (تم استيراد N وحدة بنجاح. تم تخطي E صف بسبب أخطاء.)
- **Tenant isolation.** Imported units are always created in your own account — you cannot import units into another portfolio.

Learn more: [Bulk import units from Excel](./guides/properties/bulk-import-units.md) ([العربية](./ar/guides/properties/bulk-import-units.md)).

### Invite and manage platform users (Admin) — April 28, 2026

Admins can now invite new team members by email and manage the full user lifecycle — all from **Admin → Users**.

- **Invite a user.** Click **+ Invite User** (دعوة مستخدم) and enter the person's name, email, and initial role. An invitation email is sent immediately. The user list shows their status as **Invitation pending** (في انتظار الدعوة).
- **Invitee sets their own password.** The email contains a "Set your password" link valid for 72 hours. Clicking it opens a password-setup page. After setting a password, the account is activated and the user is signed in automatically.
- **Resend or revoke.** If the invitee does not act in time, open the **More actions** menu on their row and click **Resend invitation** (إعادة الإرسال) to generate a fresh 72-hour link. Click **Revoke invitation** (إلغاء) to cancel the invitation entirely.
- **Deactivate a user.** Select **Deactivate** (إلغاء التنشيط) from the More actions menu and confirm. The user is signed out of all devices immediately and cannot log in again until reactivated. Their data and history are not deleted.
- **Reactivate a user.** Select **Reactivate** (إعادة التنشيط) to restore access. Previous role assignments are preserved.
- **Send a password reset.** Select **Send password reset** (إرسال إعادة تعيين كلمة المرور) to dispatch a reset email to any active user.
- **Self-deactivation is blocked.** The Deactivate button is disabled on your own account — ask another admin if needed.
- **Deactivated users cannot log in.** If someone tries to sign in with a deactivated account, they see a message to contact their administrator.

Learn more: [Manage users — invite, deactivate, and reset credentials](./guides/admin/manage-users.md) ([العربية](./ar/guides/admin/manage-users.md)).

### Session Management — April 27, 2026

You can now see every device signed into your account and revoke any that look unfamiliar — all from the Security settings page.

- **Active Sessions list.** Open **Settings → Security** and scroll to the **Active Sessions** section. Each entry shows the browser, operating system, approximate location, and how recently the device was active.
- **Current session badge.** The device you are using right now is labelled "Current session" and cannot be accidentally revoked.
- **Revoke a session.** Click **Revoke** next to any other session, confirm your action, and that device is immediately signed out.
- **Log out everywhere else.** Click **Log out all other sessions** to sign out of every device except your current one with a single confirmation.

### Platform Feature Flags — April 27, 2026

Super-admins can now control which platform features are active for each tenant account from a single page.

- **Features tab.** Open any tenant from **Admin > Subscriptions** and switch to the **Features** (الميزات) tab. You will see every platform feature with its current ON/OFF status and whether it is included in the tenant's plan.
- **Enable a feature.** Flip the toggle ON and confirm in the dialog. The feature activates immediately — tenant users get access on their next page load.
- **Disable a feature.** Flip the toggle OFF and confirm in the alert dialog. An amber warning explains the immediate impact before you proceed. The feature deactivates right away; affected users see a "Feature not available" message.
- **Six initial flags.** Marketplace Module, Power BI Connector, Facilities Management, Communication Hub, Document Vault, and Reports & Analytics. Each flag knows which plans include it by default (e.g., Power BI is Enterprise-only).
- **Every change is logged.** Who toggled the flag, when, and which action (enabled/disabled) are all recorded in the audit trail.

Learn more: [Manage platform feature flags](./guides/admin/feature-flags.md) ([العربية](./ar/guides/admin/feature-flags.md)).

### Facility Calendar for admins — April 25, 2026

Property Managers and Admins can now see every facility booking across the community on a single weekly calendar and manage bookings directly from it.

- **Calendar view.** Go to **Facilities → Calendar** (تقويم المرافق) to see a weekly grid of all bookings, color-coded by status: blue for Confirmed, green for Checked-in, amber for Pending, gray for Completed, and red for Cancelled.
- **Filter by facility or status.** Use the **All Facilities** dropdown to focus on one facility. Use the tab bar — All, Confirmed, Checked-in, Completed, Cancelled — to hide statuses you are not interested in.
- **Navigate weeks.** Click the arrow buttons to move one week back or forward. The grid updates without a full page reload. Click **Today** (اليوم) to return to the current week.
- **Booking detail popover.** Click any booking block to see the resident's name, facility, date, time, duration, and status. From the popover you can **Edit**, **Check In**, or **Cancel Booking** the booking on the spot.
- **Create an admin booking.** Click **+ Create Booking** (إنشاء حجز) — or click any empty time slot directly on the grid — to open the booking form. Fill in the facility, date, start and end time, and an optional resident name. Click **Create** and the booking appears on the calendar immediately. If the slot is already taken, an **Overlap Detected** error tells you to choose a different time.
- **Access control.** This screen is admin-only. Each admin sees only the bookings for their own community.

Learn more: [Manage facility bookings from the calendar](./guides/facilities/admin-calendar.md).

### Approve or reject a lease application — April 25, 2026

Property Managers with community-level scope can now record a formal approval or rejection decision directly from the lease detail page.

- **Approval panel.** When a lease is in **Pending Application** status and you have manager scope for that community, a highlighted panel appears with **Approve** and **Reject** buttons.
- **Approve.** One click transitions the lease to Approved, records your name and the exact time, and notifies the submitting manager.
- **Reject.** A dialog asks for a written reason (at least 10 characters). Confirming transitions the lease to Rejected with the reason on record. The linked quote stays in Accepted status.
- **Approval Timeline.** After a decision is recorded, a timeline card appears for all viewers — showing who approved or rejected, when, and (for rejections) the reason.
- **Tenant isolation.** Managers from other communities cannot approve or reject leases outside their scope.

Learn more: [Approve or reject a lease application](./guides/leasing/approve-or-reject-lease.md).

### Triage incoming service requests (Admin) — April 25, 2026

Property Managers and Admins can now review, assign, and annotate all service requests from a single triage queue.

- **Triage queue.** Go to **Services → Service Requests** to see every incoming request for your communities. Columns show the reference number, resident name, unit, category, urgency, status, submission date, assigned technician, and SLA response indicator.
- **Tabs for fast triage.** Use the **All Requests**, **Unassigned**, **Overdue**, and **SLA Breach** tabs to focus on what needs attention. Each tab shows a live count.
- **SLA indicators.** Rows approaching their response deadline show a yellow left border. Rows that have already breached the deadline and are still unassigned show a red border and background — act on these first.
- **Filters.** Narrow the queue by status, category, community, urgency, or reference number. Click **Apply** to refresh and **Reset** to clear.
- **Assign and set priority.** Open any request, choose a technician from the **Assign Technician** dropdown (only admins in your account appear), set a priority, and click **Assign & Save**. The detail page and queue row update immediately.
- **Internal notes.** Type a note in the **Internal Notes** section and click **Add Note**. Notes are admin-only — residents never see them. All previous notes appear in the **Notes History** list below the form with author name and timestamp.
- **Permission-gated.** Requires Admin or Manager role. Residents reaching this URL directly receive a 403 error.

Learn more: [Triage and manage service requests](./guides/service-requests/admin-triage.md).

### Submit a service request — April 25, 2026

Residents can now report a maintenance or service issue directly from the platform.

- **New request form.** Go to **Services → Submit Request** and choose a category and subcategory from the list your property manager has configured. Pick your urgency level (Normal or Urgent), confirm your unit, select the affected room, and write a description of the issue. You can also attach up to five photos.
- **Reference number.** After you submit, the platform generates a unique reference number in the format `SR-2026-NNNNN`. A **Copy** button lets you save it to your clipboard so you can quote it when following up with your property manager.
- **SLA due dates.** The confirmation page shows when a technician must respond and when the issue must be resolved, based on the category's time targets.
- **My Requests list.** Go to **Services → My Requests** to see all your submitted requests with their status and SLA due dates. The list updates automatically as the property team works on your request.

Learn more: [Submit a service request](./guides/service-requests/submit-a-request.md).

### Convert a quote to a lease and collect KYC documents — April 25, 2026

Property Managers and Admins can now turn an accepted lease quote into a lease application in one click, then upload the required identity and income documents before submitting for approval.

- **Convert to Lease Application.** Open any accepted quote from **Leasing → Quotes** and click **Convert to Lease Application**. The form pre-fills every agreed term — unit, resident, contract type, duration, start date, rent amount, payment frequency, security deposit, and any additional charges — so you never retype what was already agreed.
- **Review and adjust.** All fields are editable before you save. A banner highlights any value that differs from the original quote so the audit trail is clear.
- **Auto-generate the contract number.** Toggle **Auto-generate contract number** to let the platform assign it, or type your own.
- **One conversion per quote.** Each accepted quote can only be converted once. If you click Convert again on the same quote, you are redirected to the existing lease — no duplicates.
- **KYC checklist.** After saving, the KYC page shows a progress bar and two sections — Required and Optional. Required documents (National ID / Iqama, Passport Copy, Employment Letter, Bank Statement, Tenancy History) must all be uploaded before you can submit. Optional documents (Previous Lease Agreement, Family Book) can be added but do not block submission. Click **Upload** on any row to attach a file, and **Remove** to replace it.
- **Submit for Approval.** Once every required document is uploaded, click **Submit for Approval**. The lease advances to the approval workflow. If documents are still missing, the button is greyed out and a banner lists what is needed.

Learn more: [Convert a quote to a lease and upload KYC documents](./guides/leasing/convert-and-kyc.md).

### Book a community facility — April 25, 2026

Residents can now browse, select, and book facility time slots directly from the app.

- **Browse facilities.** Go to **Facilities** in the bottom navigation. You will see a card grid of all community facilities available for booking, showing the name, pricing (Free, SAR per session, or SAR per hour), and capacity.
- **Pick a date.** Tap **Book** on a facility card, then tap a day from the date strip (next 7–14 days). Days when the facility is closed are not selectable.
- **Choose a time slot.** The slot grid appears, grouped into **Morning**, **Afternoon**, and **Evening**. Available slots are tappable; booked and closed slots are visually disabled.
- **Confirm.** Tap an available slot to open the Confirm Booking sheet showing the date, time, duration, and price. Tap **Confirm** to book. You receive a confirmation to your mobile number and email.
- **Race-condition protection.** If two residents try to book the same last slot at the same moment, only one succeeds. The other sees "This slot is no longer available — please choose a different time" and can tap **Pick Another Slot** to try again.
- **Contract-required facilities.** Some facilities (for example, a banquet hall) require a signed contract. Tapping **Book** creates a pending booking and you receive instructions for signing the contract.
- **Access control.** Only residents with an active community membership can see and use this screen. Non-members receive a 403 error.

Learn more: [Book a facility](./guides/facilities/book-a-facility.md).

### Configure service request categories and SLA targets — April 25, 2026

Admins can now build the category tree that residents see when they submit a service request, and set time targets for how quickly each category must be acknowledged and resolved.

- **Create categories.** Go to **Settings → Services → Service Categories** and click **+ New Category**. Give the category a bilingual name (English and Arabic), pick an emoji icon, and set the Response SLA and Resolution SLA in hours. Optionally assign a default handler and restrict the category to specific communities.
- **Add subcategories.** Expand any category and click **+ Add Subcategory**. Subcategories inherit the parent's SLA targets by default. Enter custom hours only if this subcategory needs different targets — the form shows "Inherited from parent" when no override is set.
- **Enable or disable.** Use the **Disable** / **Enable** buttons on any category or subcategory row to hide or show it in the resident request form without deleting it.
- **Delete protection.** A subcategory linked to active (non-archived) service requests cannot be deleted. Disable it instead, or archive the linked requests first.
- **Permission-gated.** Only admin accounts can access this page.

Learn more: [Configure service request categories and SLA targets](./guides/service-requests/configure-categories-and-sla.md).

### E-signature — send documents for digital signing — April 26, 2026

You can now send a generated document to a recipient for digital signature using an OTP-verified in-platform flow — no third-party SaaS required.

- **Send for signature.** Open any document record in Draft or Link Expired status and click **Send for Signature**. Enter the recipient's name and email, and the platform generates a unique signing link and transitions the document to Sent.
- **Public signing page.** The recipient clicks the link and sees a read-only rendering of the document (English or Arabic) with a **Sign Document** button. No platform account is required.
- **OTP verification.** Before signing, the recipient requests a 6-digit OTP. After entering it correctly, the system records the verification timestamp and accepts the signature.
- **Signature recording.** A `DocumentSignature` record is created with signer name, email, signature timestamp, OTP verification timestamp, and IP address. The document transitions to Signed and a countersigned PDF is produced.
- **Link expiry.** The signing link expires after 7 days. After that, the manager sees the status as **Link Expired** and can resend with a new token. The old link stops working immediately.
- **OTP security.** OTPs expire after 10 minutes and are limited to 5 attempts per code. After 5 incorrect entries, the code is invalidated and a new one must be requested.
- **Resend at any time.** While a document is Sent or Link Expired, the manager can resend the link — this generates a new token and invalidates the old one.
- **Admin-only send.** The Send and Resend actions are gated by the `documents.UPDATE` permission.

Learn more: [Send a document for e-signature](./guides/documents/e-signing-documents.md).

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

### Lease quotes — revise, reject, and expire — April 25, 2026

Property Managers and Admins can now revise sent quotes, reject offers, and manually expire quotes from the quote detail page.

- **Revise a quote.** Open any Draft, Sent, or Viewed quote and click **Revise** (مراجعة). The revision form opens with all current values pre-filled — fields that changed from the previous version are highlighted with a **Changed** badge so you can spot what is different at a glance. Edit any term, add an optional revision note, choose an email subject prefix (for example, "Updated Quote"), and click **Save Revision**. A new version is created and the prospect receives a fresh email automatically. The **Revision History** panel on the right of the detail page lists all versions in newest-first order; click any entry to open that version.
- **Reject a quote.** On any Sent or Viewed quote, click **Reject** (رفض). Enter a rejection reason (required) and confirm. The status moves to **Rejected** and no further emails are sent for this quote.
- **Expire manually.** If a unit has already been leased by another channel, use the **Expire** action on the detail page to close the quote immediately rather than waiting for the overnight job.
- **Read-only terminal states.** Accepted, Rejected, and Expired quotes show a banner explaining the current state and disable all edit controls — there is nothing further you can do on those quotes.
- **Automatic nightly expiry** remains in place: any open quote (Draft, Sent, Viewed) whose valid-until date has passed is moved to Expired overnight with no manual action needed.

Learn more: [Create, send, and revise lease quotes](./guides/leasing/lease-quotes.md).

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

Backend: New document generation engine produces filled contracts and invoices from templates when triggered by Leasing, Facilities, or Accounting.

### Manage facilities — April 25, 2026

Property Managers and Admins can now create, configure, and manage facilities from the Facilities area.

- **Create a facility.** Go to **Facilities** and click **New Facility**. Enter the facility name in English and Arabic, choose a community and category, and set the capacity.
- **Pricing.** Pick Free, Per Session, or Per Hour. When a paid mode is selected, enter the price and currency (default: SAR).
- **Booking Constraints.** Set how far ahead residents can book (days), how close to the slot they can cancel (hours), and the minimum (and optional maximum) session length in minutes.
- **7-day availability grid.** Toggle each day of the week on or off, then set the opening time, closing time, slot duration, and maximum concurrent bookings for each active day. Monday–Saturday are on by default; Sunday and Friday are off.
- **Contract required.** Tick the Contract required checkbox if residents must sign a contract before their booking is confirmed.
- **Safe deactivation.** If a facility has upcoming confirmed bookings, a yellow banner shows the count when you open the edit form. Click **Deactivate Facility** in the banner and confirm in the dialog. Existing bookings remain valid — no new bookings can be created after deactivation. To reactivate, click **Reactivate & Save**.
- **Facility detail page.** Click any facility name to see its status, pricing, upcoming booking count, active availability schedule, and constraint summary in a read-only view.
- **Permission-gated.** Creating facilities requires `facilities.CREATE`; editing requires `facilities.UPDATE`. Users without these permissions see a 403 error.
- **Bilingual.** All screens work in English and Arabic, including the Arabic name field with right-to-left input.

Learn more: [Configure a facility](./guides/facilities/configure-facility.md).

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
