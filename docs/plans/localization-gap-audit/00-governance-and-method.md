# Governance and Audit Method

## Audit Principles
- Arabic-first: UI defaults to Arabic; English is fallback.
- Contract-first: judge implementation against `docs/api/**` and route/controller behavior, not assumptions.
- Page-complete: scan every page and shared component that contributes text/UI behavior.
- Evidence-first: each finding must include reproducible evidence.

## Required Evidence per Finding
For each gap record, capture:
- Module and page/component path.
- Gap type: localization, behavior parity, validation, workflow, data contract, navigation.
- Severity: Critical, High, Medium, Low.
- Current behavior.
- Expected behavior (with source: translation key and/or API doc/route/controller).
- Reproduction steps.
- Suggested fix scope (frontend only, backend only, full-stack).

## Severity Model
- Critical: blocks core flow or makes Arabic-first unusable.
- High: major feature mismatch or high-visibility untranslated UX.
- Medium: partial mismatch, missing secondary actions, inconsistent labels.
- Low: copy polish, edge-state text, non-blocking inconsistencies.

## Page Scan Checklist Template
Apply this template to each page in `resources/js/pages/**`:

1. Route and access
- route exists in `routes/web.php`
- menu/surface entry points reachable
- auth/tenant/role guard behavior verified

2. Copy and localization
- page title, description, breadcrumbs
- section headings and card titles
- form labels/placeholders/help text
- button labels and menu items
- table headers, badges, status labels
- dialog/confirmation text
- toasts/alerts and empty states
- validation/error messages

3. Behavior parity
- actions expected by route/controller exist in UI
- filters/search/sort/pagination exist where contracts imply them
- status transitions and workflow actions exposed correctly
- create/edit/show/list coverage matches resource expectations

4. Data parity
- fields rendered match payload contracts and validations
- option lists and enums mapped to readable labels
- missing/null states handled

5. Localization quality
- key-based copy (no hardcoded literals)
- Arabic rendering and direction correctness
- interpolation/pluralization correctness
- locale persistence behavior

## Recommended Audit Execution Order
1. Foundation and shared navigation.
2. Authentication and user settings.
3. Core domain modules.
4. Operations modules.
5. Marketplace/reports/settings shell.
6. Cross-cutting cleanup and regression pass.

## Gap Register Format
Use this normalized format in the tracker:

- `ID`: GAP-<module>-<number>
- `Module`:
- `Page/Component`:
- `Type`:
- `Severity`:
- `Current`:
- `Expected`:
- `Evidence`:
- `Suggested Fix`:
- `Owner`:
- `Status`: Open / In Progress / Resolved / Deferred
