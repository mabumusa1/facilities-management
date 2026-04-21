# Arabic-First Localization Foundation

## Target State
- Frontend locale defaults to Arabic.
- English remains fallback locale.
- UI direction and alignment switch correctly for Arabic/English.
- All frontend copy comes from translation keys.

## Current Baseline
- Translation dictionaries exist: `docs/ar-translation.json`, `docs/en-translation.json`.
- Frontend app boot (`resources/js/app.ts`) has no i18n plugin wiring.
- `package.json` currently has no `vue-i18n` dependency.
- Navigation and page copy are primarily hardcoded in Vue files.

## Foundation Tasks

### F1. i18n Runtime Setup
- [ ] Add i18n runtime library for Vue 3.
- [ ] Create i18n bootstrap module (recommended: `resources/js/i18n/index.ts`).
- [ ] Register i18n in app bootstrap (`resources/js/app.ts`).
- [ ] Configure supported locales: `ar`, `en`.
- [ ] Set default locale to `ar`; fallback locale to `en`.

### F2. Dictionary Source and Structure
- [ ] Define source-of-truth policy for dictionaries.
- [ ] Decide key strategy:
  - Option A: keep legacy flat keys from imported system.
  - Option B: create namespaced keys by module and keep a compatibility map.
- [ ] Create migration map from legacy keys to target key structure.
- [ ] Ensure both `ar` and `en` maintain key parity.

### F3. Locale Persistence and Switching
- [ ] Decide persistence source: localStorage, user profile, or both.
- [ ] Implement locale switch API/composable.
- [ ] Ensure first load prefers persisted locale; fallback to Arabic.
- [ ] Add a visible locale switcher in shared UI (header or user menu).

### F4. RTL/LTR and Formatting
- [ ] Toggle document direction (`dir=rtl|ltr`) by locale.
- [ ] Validate layout behavior in sidebar, tables, forms, modals.
- [ ] Standardize number/date/currency formatting by locale.
- [ ] Add typography checks for Arabic readability.

### F5. Guardrails
- [ ] Add lint/script checks to detect hardcoded user-facing strings in page and shared component files.
- [ ] Add CI check for missing translation keys.
- [ ] Add runtime warning strategy for missing keys in development.

## Suggested Key Namespace Skeleton
If namespacing is adopted, start with:
- `common.*`
- `navigation.*`
- `auth.*`
- `settings.*`
- `dashboard.*`
- `properties.*`
- `leasing.*`
- `contacts.*`
- `accounting.*`
- `requests.*`
- `facilities.*`
- `communication.*`
- `visitorAccess.*`
- `documents.*`
- `marketplace.*`
- `reports.*`
- `appSettings.*`

## Acceptance Criteria
- [ ] Arabic is default locale on first authenticated and non-authenticated load.
- [ ] Locale can be switched and persists across navigation and refresh.
- [ ] Shared components react to locale changes without reload.
- [ ] RTL rendering is visually correct in key templates (sidebar/header/forms/tables).
- [ ] No missing-key runtime errors in audited module pages.
