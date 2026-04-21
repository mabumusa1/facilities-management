# Localization and Frontend Gap Audit Plan

## Objective
Extend delivery planning with two execution tracks:

1. Arabic-first product localization (Arabic is the default UI language).
2. Frontend parity audit to detect implementation gaps by scanning every page, sidebar, menu, and major flow.

## Inputs and Sources of Truth
- Existing tracker: `IMPLEMENTATION-PLAN.md`
- Translation corpus: `docs/ar-translation.json`, `docs/en-translation.json`
- Frontend pages: `resources/js/pages/**`
- Shared UI/navigation: `resources/js/components/**`, `resources/js/layouts/**`
- Routes and page wiring: `routes/web.php`, `app/Http/Controllers/**` (`Inertia::render(...)`)
- Product contract references: `docs/api/**` (routes, openapi, validations, queries, mutations)

## Frontend Surface Baseline
Current Vue page inventory (95 pages total):
- `app-settings`: 18
- `contacts`: 17
- `properties`: 12
- `facilities`: 8
- `auth`: 7
- `marketplace`: 5
- `leasing`: 5
- `requests`: 4
- `communication`: 4
- `accounting`: 4
- `settings`: 3
- `visitor-access`: 2
- `documents`: 2
- `reports`: 1
- `notifications`: 1
- root pages: `Dashboard.vue`, `Welcome.vue`

## Workstreams and Files
- `00-governance-and-method.md`: Audit method, severity model, evidence standards.
- `01-arabic-first-localization-foundation.md`: i18n architecture and rollout foundation.
- `02-shared-auth-system-ui.md`: Sidebar, menus, shared components, auth, settings, dashboard, notifications.
- `03-core-domain-modules.md`: Properties, leasing, contacts, accounting.
- `04-operations-modules.md`: Requests, facilities, communication, visitor access, documents/imports.
- `05-marketplace-reports-settings.md`: Marketplace, reports/Power BI, app settings shell/forms/categories.
- `06-tracking-matrix.md`: Execution matrix and page-by-page completion tracker.

## Parallel Execution Model (Multi-Agent Friendly)
- Squad A: Foundation + Shared UI + Auth (`00`, `01`, `02`)
- Squad B: Core domain modules (`03`)
- Squad C: Operations modules (`04`)
- Squad D: Marketplace/Reports/Settings (`05`)
- Integrator: Consolidates findings and updates `06-tracking-matrix.md`

## Definition of Done
- Arabic is the default locale in frontend runtime.
- All user-facing text on audited pages is key-based (no hardcoded literal UI copy).
- All critical gaps are logged with severity, file path, expected behavior, and contract reference.
- All modules in scope have audit evidence and disposition (implemented, partial, missing, deferred).
- `IMPLEMENTATION-PLAN.md` includes this extension as a tracked phase.
