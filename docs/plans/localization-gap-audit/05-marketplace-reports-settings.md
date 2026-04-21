# Marketplace, Reports, and App Settings Audit Plan

## Scope
- Marketplace module pages and admin settings surfaces.
- Reports pages and report mode experiences.
- App settings shell, categories, facilities, forms, invoice, general settings.

## Module 1: Marketplace

### Scan Targets
- `resources/js/pages/marketplace/Overview.vue`
- `resources/js/pages/marketplace/Customers.vue`
- `resources/js/pages/marketplace/Listing.vue`
- `resources/js/pages/marketplace/Visits.vue`
- `resources/js/pages/marketplace/VisitShow.vue`

### Related Routes/Contracts to Cross-Check
- `routes/web.php` marketplace and marketplace-admin groups
- `docs/api/routes.json`
- `docs/api/mutations/marketplace/**`
- `docs/api/queries/marketplace/**`
- `docs/api/validations/marketplace/**`

### Checklist
- [ ] Overview metrics and cards translated.
- [ ] Customer and listing table labels translated.
- [ ] Listing actions (create/edit/delete/list/unlist) translated.
- [ ] Visits scheduling and status labels translated.
- [ ] Price visibility action labels translated.
- [ ] Community sales-information actions translated.
- [ ] UI actions align with available routes/mutations.

## Module 2: Reports (including Power BI)

### Scan Targets
- `resources/js/pages/reports/Index.vue`

### Related Routes/Contracts to Cross-Check
- `routes/web.php` dashboard/report route groups
- `docs/api/routes.json`
- `docs/api/openapi.json`

### Checklist
- [ ] Report mode labels translated (reports, power-bi, system reports variants).
- [ ] Controls translated (load, prepare, render, print, refresh, save, save as, theme, zoom).
- [ ] Filters/pages/bookmarks/settings labels translated.
- [ ] Empty/loading/error states translated.
- [ ] UI behavior matches backend capability and is clearly signposted where stubbed.

## Module 3: App Settings

### Scan Targets
- `resources/js/pages/app-settings/settings/Index.vue`
- `resources/js/pages/app-settings/settings/FacilitiesIndex.vue`
- `resources/js/pages/app-settings/settings/FacilityShow.vue`
- `resources/js/pages/app-settings/settings/FacilityForm.vue`
- `resources/js/pages/app-settings/settings/ServiceRequestDetails.vue`
- `resources/js/pages/app-settings/settings/forms/Index.vue`
- `resources/js/pages/app-settings/settings/forms/Create.vue`
- `resources/js/pages/app-settings/settings/forms/Preview.vue`
- `resources/js/pages/app-settings/settings/forms/SelectCommunity.vue`
- `resources/js/pages/app-settings/settings/forms/SelectBuilding.vue`
- `resources/js/pages/app-settings/request-categories/Index.vue`
- `resources/js/pages/app-settings/request-categories/Create.vue`
- `resources/js/pages/app-settings/request-categories/Edit.vue`
- `resources/js/pages/app-settings/facility-categories/Index.vue`
- `resources/js/pages/app-settings/facility-categories/Create.vue`
- `resources/js/pages/app-settings/facility-categories/Edit.vue`
- `resources/js/pages/app-settings/invoice/Edit.vue`
- `resources/js/pages/app-settings/general/Index.vue`

### Related Routes/Contracts to Cross-Check
- `routes/web.php` app-settings and settings route groups
- `docs/api/mutations/settings/**`
- `docs/api/queries/common/**` and `docs/api/queries/requests/**`
- `docs/api/validations/settings/**`

### Checklist
- [ ] Settings tabs and section labels translated.
- [ ] Forms builder labels and schema controls translated.
- [ ] Category/facility/general setting CRUD text translated.
- [ ] Validation and toast feedback translated.
- [ ] All settings pages reachable from sidebar/menu with correct labels.
- [ ] UI fields and actions match expected settings contracts.

## Cross-Module Gap Checks
- [ ] Validate menu labels against actual destination modules.
- [ ] Capture any route exposed by backend but not reachable in UI.
- [ ] Capture any UI action with no matching route/contract.
- [ ] Capture contract drift for payload field names and enum values.

## Deliverables
- Marketplace/reports/settings gap register section with severity and owner.
- Translation key backlog grouped into `marketplace.*`, `reports.*`, `appSettings.*`.
- Coverage summary: page-level localization completion percentage.

## Exit Criteria
- [ ] Marketplace, reports, and app-settings text fully key-driven.
- [ ] High/critical parity gaps documented and triaged.
- [ ] Navigation and contract alignment validated for this scope.
