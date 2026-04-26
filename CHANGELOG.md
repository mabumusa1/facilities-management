# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Facilities — residents can browse bookable community facilities, pick a date and time slot, and confirm a booking from a mobile-first UI; slot grid groups sessions into Morning / Afternoon / Evening and reflects real-time availability from facility opening-hour rules and existing bookings; unavailable and closed slots are visually disabled and not selectable; race-condition-safe booking via row-level lock (409 returned if another resident books the last slot at the same instant, with a prompt to pick another slot); facilities requiring a contract create a pending booking with contract-signing instructions; permission-gated to residents with an active community membership; bilingual EN/AR ([#354](https://github.com/mabumusa1/facilities-management/pull/354))
- Visitor Access — residents can now register expected visitors from **My Visitors**: enter the visitor's name, purpose (Visit / Delivery / Service / Other), expected arrival, and optional phone, then tap **Generate QR Code** to produce a unique, cryptographically-random QR invite. The QR detail screen displays the code inline with a **Share QR** button (OS share sheet). Active invitations show on the **My Visitors** list with live status badges (Active / Used / Expired / Cancelled); residents can cancel at any time from the list. QR codes expire automatically via the nightly `ExpireVisitorInvitations` job; expiry window is per-community-configurable (default 24 h). Bilingual EN/AR. ([#342](https://github.com/mabumusa1/facilities-management/pull/342))
- Documents — admin can create, edit, and version document templates with named merge fields (key, EN/AR label, type, source path); bilingual body editing with EN/AR tabs; draft → active → archived lifecycle; version history sidebar in descending order; activate/archive/delete actions; admin-only gated by `documents.*` permissions ([#341](https://github.com/mabumusa1/facilities-management/pull/341))
- Documents — template preview with sample data before sending; English and Arabic tabs with RTL rendering for Arabic; unresolved merge field amber warning; ephemeral (no DocumentRecord created); supports real-data context from consumer module generation flows; 422 response when no published version exists ([#343](https://github.com/mabumusa1/facilities-management/pull/343))
- Added DocumentGenerator service for cross-domain contract and invoice generation ([#345](https://github.com/mabumusa1/facilities-management/pull/345))
- Facilities — availability rules let admins set per-facility opening hours for each day of the week (open time, close time, slot duration, max concurrent bookings); waitlist lets residents queue for a full slot and receive a notification if a cancellation opens their spot ([#338](https://github.com/mabumusa1/facilities-management/pull/338))
- Reports — access to all report types is now gated by the `reports.VIEW` permission; Account Admins, System Admins, and Accounting Managers have this permission by default; unauthorized requests return a 403 ([#337](https://github.com/mabumusa1/facilities-management/pull/337))
- Visitor Access — resident-created QR-coded gate passes: invitations track visitor name, purpose, expected arrival, and a QR token; gate log records every entry and exit against the attending officer; per-community settings control ID verification requirement, walk-in permission, QR validity window (default 24 h), and max scans per invitation. Introduces the Gate Officers admin role ([#336](https://github.com/mabumusa1/facilities-management/pull/336))
- Leasing → Quotes — create and send lease quotes: admins fill in unit, resident contact, contract type, duration, start date, rent, payment frequency, security deposit, additional charges, and special conditions; quotes save as Draft or send immediately; sending transitions Draft → Sent with a secure token-based preview link for the prospect; the prospect's first open auto-transitions to Viewed; status lifecycle (Draft / Sent / Viewed / Accepted / Rejected / Expired) visible on list and detail pages; public preview is token-gated with no login required ([#339](https://github.com/mabumusa1/facilities-management/pull/339))
- Leasing → Quotes — lease quote data model: six-status lifecycle (draft → sent → viewed → accepted | rejected | expired), auto-expiry by valid-until date, revision chain with versioning, and quote-to-lease conversion link; foundation for create (#170), revise (#171), and convert (#172) UI stories ([#334](https://github.com/mabumusa1/facilities-management/pull/334))
- Accounting → Transactions — record offline money-in payments (cash, bank transfer, cheque) from residents and owners; auto-generated sequential receipt on every transaction; send receipt by email from the transaction detail page via **Send Receipt**; amber warning banner when Invoice Settings are incomplete; `transactions.SEND_RECEIPT` permission gates the send action ([#340](https://github.com/mabumusa1/facilities-management/pull/340))
- Accounting → Settings → Transaction Categories — admins can configure income and expense categories for transactions; bilingual EN/AR names; six seeded defaults (Rent, Late Fee, Service Fee for income; Maintenance, Utility, Repairs for expense); deactivate/reactivate without losing historical data; default categories are protected from deletion ([#333](https://github.com/mabumusa1/facilities-management/pull/333))
- Service Requests — auto-generated reference codes (`SR-YYYY-NNNNN`, unique per account per year) now appear on every service request record; adds scheduled-date and completed-date fields; lays foundation for upcoming messaging threads and activity-timeline view ([#335](https://github.com/mabumusa1/facilities-management/pull/335))
- Contacts → Residents — create new resident contacts with bilingual EN/AR name fields, phone-based duplicate detection with an inline warning and "create anyway" override, and a debounced search across English names, Arabic names, phone, and email ([#330](https://github.com/mabumusa1/facilities-management/pull/330))
- Company Profile — configure company identity, logo and brand, regional timezone, and brand colours from a single settings page under App Settings; logo upload with PNG/SVG client-side validation; removable logo with disk cleanup; bilingual (EN/AR) with deferred skeleton loading and sticky unsaved-changes bar ([#329](https://github.com/mabumusa1/facilities-management/pull/329))
- Properties → Communities — edit amenities, working days, and map coordinates from a single form; Saturday-first day strip; optional geolocation auto-fill; bilingual (EN/AR) with RTL layout ([#327](https://github.com/mabumusa1/facilities-management/pull/327))
- Roles and permissions — 12 default roles (7 user roles + 5 admin roles) and 186 permissions seeded on every account ([#119](https://github.com/mabumusa1/facilities-management/pull/119))
- Manager scope — managers are now restricted to their assigned communities, buildings, and service types across 16 models ([#121](https://github.com/mabumusa1/facilities-management/pull/121))
- Admin → Roles — list, search, create, edit, and delete custom roles with bilingual English/Arabic names ([#122](https://github.com/mabumusa1/facilities-management/pull/122))
- Admin → Roles — permission matrix editor (31 subjects × 6 actions) with presets and a "View is required when any other action is enabled" rule ([#123](https://github.com/mabumusa1/facilities-management/pull/123))
- Admin → Users — drawer-based role assignment with community, building, and service-type scope selectors ([#124](https://github.com/mabumusa1/facilities-management/pull/124))
- Enabled Fortify update-profile-information and update-passwords features for future auth stories ([#331](https://github.com/mabumusa1/facilities-management/pull/331))
- Added locale and avatar_path columns to users table for profile self-service ([#331](https://github.com/mabumusa1/facilities-management/pull/331))
- Added Documents data model (templates, versions, records, signatures) for cross-domain document generation ([#332](https://github.com/mabumusa1/facilities-management/pull/332))

### Changed

- Sign-in — role-based post-login redirect; admins and managers land on the admin dashboard, all other roles land on home ([#325](https://github.com/mabumusa1/facilities-management/pull/325))
- Sign-in — rate-limit banner now detects throttling via the HTTP 429 `Retry-After` header and shows a clear countdown message in English and Arabic ([#325](https://github.com/mabumusa1/facilities-management/pull/325))
- Sign-in — password show/hide toggle, language switcher, and inline error messages now follow right-to-left layout correctly in Arabic ([#325](https://github.com/mabumusa1/facilities-management/pull/325))
- Sign-in — inline validation errors are now announced by screen readers via `aria-live` ([#325](https://github.com/mabumusa1/facilities-management/pull/325))

### Deprecated

### Removed

### Fixed

### Security

- Every non-public route now requires an explicit permission via middleware and Policy enforcement; unauthorized requests return a 403 with the required permission slug ([#120](https://github.com/mabumusa1/facilities-management/pull/120))

---

_Internal_
<!-- One-line notes for internal refactors that have no user-visible impact. They do not trigger a release. -->

- Migrated existing `rf_admins.role` enum values into the new role assignment table without breaking any existing admin session ([#125](https://github.com/mabumusa1/facilities-management/pull/125))
- Contact data model — additive `first_name_ar` / `last_name_ar` / `id_type` columns on Resident, Owner, Professional, and Dependent tables; new `IdType` backed enum; `(account_tenant_id, national_phone_number)` composite unique index for fast duplicate detection. Unblocks the Contacts UI chain (#148–#157) ([#326](https://github.com/mabumusa1/facilities-management/pull/326))
- Reports data model — new `report_snapshots` table with tenant isolation, JSONB payload/filters, and a `pending → ready | failed` status lifecycle; `ReportType` enum with 6 cases (`FinancialSummary`, `Occupancy`, `LeasePipeline`, `VatReturn`, `ReceivablesAging`, `PortfolioHealth`) and `isSnapshot()` flag for the live-vs-snapshot decision. Unblocks the Reports UI chain (#304–#313) and Power BI integration (#314–#322) ([#337](https://github.com/mabumusa1/facilities-management/pull/337))
- Settings data model — new `rf_contract_types` and `rf_app_settings` tables, 13 new columns on `rf_invoice_settings`, and `TenantObserver` auto-seeds default settings per tenant on creation. Includes `settings:backfill --dry-run` Artisan command for existing tenants. Fixes a pre-existing tenant-boundary leak on `rf_service_settings` (added missing `account_tenant_id` FK) and blocks a cross-tenant write vulnerability in the ServiceSettings endpoint. Unblocks Settings UI stories (#225, #226, #227, #229, #230) + dependent Accounting / Leasing stories ([#328](https://github.com/mabumusa1/facilities-management/pull/328))
- Facilities data model — new `rf_facility_availability_rules` and `rf_facility_waitlist` tables; 10 new columns on `rf_facilities` (type, pricing_mode, booking configuration); extended `rf_facility_bookings` with tenant scoping, datetime range, cancellation fields, and invoice/contract FK columns; new `FacilityAvailabilityRule` and `FacilityWaitlist` models; `FacilityWaitlistPolicy`; `FacilitySeeder` seeds a sample Gym with Mon–Sat availability. Unblocks Facilities UI stories (#247–#256) ([#338](https://github.com/mabumusa1/facilities-management/pull/338))
- Visitor Access data model — `rf_visitor_invitations`, `rf_visitor_logs`, and `rf_visitor_access_settings` tables with `BelongsToAccountTenant` tenant scoping, typed Eloquent models, domain-state factories, and `VisitorAccessSettingsSeeder` (idempotent upsert per community). Unblocks Visitor Access UI stories #258–#264 ([#336](https://github.com/mabumusa1/facilities-management/pull/336))
