# Implementation Status Source of Truth

Last verified: 2026-04-21

## Purpose
This document is the authoritative implementation status plan for this repository.

## Verification Basis
Status values in this file are based on direct checks against:
- docs/api/** and docs/plans/** documentation scope
- routes/web.php route surface
- app/Http/Controllers/** Inertia render targets
- resources/js/pages/** frontend page inventory
- scripts/check-translations.mjs and npm run i18n:check output
- tests/** feature and unit test inventory

## Evidence Snapshot
- Matrix page inventory: 102 expected paths, 101 present (missing only resources/js/pages/Welcome.vue, intentionally removed from app surface).
- Inertia render targets: 86 distinct render paths, 85 existing Vue pages.
- Translation key check: 1107 used keys checked, 0 missing in English, 0 missing in Arabic.
- Route surface: major module route groups are present in routes/web.php (properties, leasing, contacts, accounting, requests, facilities, communication, visitor-access, marketplace, reports, app-settings, notifications, rf lookups/uploads).

## Module Status
Status legend:
- Done: implemented and wired with verified route/controller/page presence.
- Partial: implemented foundation exists, but required guardrails or reconciliation work remains.
- Needs Implementation: no verified implementation evidence yet.

| Module | Status | Verification Evidence | Notes |
|---|---|---|---|
| Shared/Auth/System | Done | auth pages + settings pages + Dashboard + notifications pages; corresponding routes and controllers exist | Welcome page intentionally removed from app surface |
| Properties | Done | Route::resource for communities/buildings/units + matching controllers + CRUD Vue pages |  |
| Leasing | Done | Route::resource leases + sublease create/store routes + matching controller/pages |  |
| Contacts | Done | Route::resource owners/residents/admins/professionals + matching controllers/pages | Residents reuse tenants page set for index/create/edit |
| Accounting | Done | Route::resource transactions + TransactionController + CRUD pages |  |
| Requests | Done | Route::resource requests (ServiceRequestController) + CRUD pages |  |
| Facilities and Bookings | Done | Route::resource facilities + facility-bookings + matching controllers/pages |  |
| Communication | Done | Route::resource announcements + matching controller/pages |  |
| Visitor Access | Done | visitor-access history/details/approve/reject routes + controller + pages |  |
| Documents and Imports | Done | documents page + rf file upload/delete + excel import routes + controllers + pages |  |
| Marketplace | Done | marketplace + marketplace-admin route groups + MarketplaceController + pages |  |
| Reports | Done | dashboard report routes + report action routes + ReportsController + page |  |
| App Settings | Done | app-settings and settings route groups + related controllers + pages |  |
| Notifications and Shared Lookups | Done | notifications routes + rf lookup routes + shared controllers |  |
| Localization Foundation (Arabic-first runtime) | Done | initializeI18n in app bootstrap, locale persistence, lang/dir sync, key-based translation composable |  |
| Localization Guardrails | Partial | translation key checker exists and passes | Missing CI enforcement and hardcoded-string guardrail |
| Legacy Docs Route Reconciliation | Partial | docs/api/routes.json has many legacy path forms not 1:1 with current app routes | Requires mapping exercise, not blind route parity assumptions |

## Needs Implementation / Cleanup Backlog
1. Add translation checks to CI workflows (for example, run npm run i18n:check in lint/test pipelines).
2. Add hardcoded user-facing string guardrail (lint/script) for frontend pages and shared UI components.
3. Add development-time missing-key warning strategy for translation lookups.
4. Resolve app-settings request category show mismatch: controller contains a show render target for app-settings/request-categories/Show, but route resource excludes show and no Show.vue exists.
5. Execute a documented route mapping between docs/api/routes.json legacy path structure and current route architecture, then classify true feature gaps from naming/path differences.

## Update Rules
- Update this file only after direct verification from code and docs.
- Do not mark a module Done from plan intent alone.
- Keep this file in sync when route groups, page surfaces, or localization guardrails change.
