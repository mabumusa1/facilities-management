# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Contacts → Residents — create new resident contacts with bilingual EN/AR name fields, phone-based duplicate detection with an inline warning and "create anyway" override, and a debounced search across English names, Arabic names, phone, and email ([#330](https://github.com/mabumusa1/facilities-management/pull/330))
- Properties → Communities — edit amenities, working days, and map coordinates from a single form; Saturday-first day strip; optional geolocation auto-fill; bilingual (EN/AR) with RTL layout ([#327](https://github.com/mabumusa1/facilities-management/pull/327))
- Roles and permissions — 12 default roles (7 user roles + 5 admin roles) and 186 permissions seeded on every account ([#119](https://github.com/mabumusa1/facilities-management/pull/119))
- Manager scope — managers are now restricted to their assigned communities, buildings, and service types across 16 models ([#121](https://github.com/mabumusa1/facilities-management/pull/121))
- Admin → Roles — list, search, create, edit, and delete custom roles with bilingual English/Arabic names ([#122](https://github.com/mabumusa1/facilities-management/pull/122))
- Admin → Roles — permission matrix editor (31 subjects × 6 actions) with presets and a "View is required when any other action is enabled" rule ([#123](https://github.com/mabumusa1/facilities-management/pull/123))
- Admin → Users — drawer-based role assignment with community, building, and service-type scope selectors ([#124](https://github.com/mabumusa1/facilities-management/pull/124))

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
- Settings data model — new `rf_contract_types` and `rf_app_settings` tables, 13 new columns on `rf_invoice_settings`, and `TenantObserver` auto-seeds default settings per tenant on creation. Includes `settings:backfill --dry-run` Artisan command for existing tenants. Fixes a pre-existing tenant-boundary leak on `rf_service_settings` (added missing `account_tenant_id` FK) and blocks a cross-tenant write vulnerability in the ServiceSettings endpoint. Unblocks Settings UI stories (#225, #226, #227, #229, #230) + dependent Accounting / Leasing stories ([#328](https://github.com/mabumusa1/facilities-management/pull/328))
