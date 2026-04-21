# Core Domain Modules Audit Plan

## Scope
- Properties (`communities`, `buildings`, `units`)
- Leasing (`leases`, `subleases`)
- Contacts (`tenants`, `owners`, `admins`, `professionals`, `residents show`)
- Accounting (`transactions`)

## Module 1: Properties

### Scan Targets
- `resources/js/pages/properties/communities/Index.vue`
- `resources/js/pages/properties/communities/Create.vue`
- `resources/js/pages/properties/communities/Edit.vue`
- `resources/js/pages/properties/communities/Show.vue`
- `resources/js/pages/properties/buildings/Index.vue`
- `resources/js/pages/properties/buildings/Create.vue`
- `resources/js/pages/properties/buildings/Edit.vue`
- `resources/js/pages/properties/buildings/Show.vue`
- `resources/js/pages/properties/units/Index.vue`
- `resources/js/pages/properties/units/Create.vue`
- `resources/js/pages/properties/units/Edit.vue`
- `resources/js/pages/properties/units/Show.vue`

### Checklist
- [ ] Page title/description translated.
- [ ] Form labels/placeholders/help text translated.
- [ ] Table column headers translated.
- [ ] Status and badge values translated.
- [ ] Empty/loading/error states translated.
- [ ] Filters/search labels translated.
- [ ] Ownership/tenant/location fields match contracts.
- [ ] Marketplace flags and area/floor fields have correct labels and behavior.

## Module 2: Leasing

### Scan Targets
- `resources/js/pages/leasing/leases/Index.vue`
- `resources/js/pages/leasing/leases/Create.vue`
- `resources/js/pages/leasing/leases/Edit.vue`
- `resources/js/pages/leasing/leases/Show.vue`
- `resources/js/pages/leasing/leases/SubleaseCreate.vue`

### Checklist
- [ ] Lease workflow copy translated (statuses, actions, timeline labels).
- [ ] Financial labels translated (amounts, fees, escalations, deposits).
- [ ] Date-related validations/messages translated.
- [ ] Sublease flow copy translated.
- [ ] Conditional/disabled states translated.
- [ ] Displayed fields align with validations and controller payloads.

## Module 3: Contacts

### Scan Targets
- `resources/js/pages/contacts/tenants/Index.vue`
- `resources/js/pages/contacts/tenants/Create.vue`
- `resources/js/pages/contacts/tenants/Edit.vue`
- `resources/js/pages/contacts/tenants/Show.vue`
- `resources/js/pages/contacts/owners/Index.vue`
- `resources/js/pages/contacts/owners/Create.vue`
- `resources/js/pages/contacts/owners/Edit.vue`
- `resources/js/pages/contacts/owners/Show.vue`
- `resources/js/pages/contacts/admins/Index.vue`
- `resources/js/pages/contacts/admins/Create.vue`
- `resources/js/pages/contacts/admins/Edit.vue`
- `resources/js/pages/contacts/admins/Show.vue`
- `resources/js/pages/contacts/professionals/Index.vue`
- `resources/js/pages/contacts/professionals/Create.vue`
- `resources/js/pages/contacts/professionals/Edit.vue`
- `resources/js/pages/contacts/professionals/Show.vue`
- `resources/js/pages/contacts/residents/Show.vue`

### Checklist
- [ ] Person/company labels translated consistently.
- [ ] Gender, role, nationality, active/inactive labels translated.
- [ ] Contact/identity fields translated (phone, email, IDs, job title).
- [ ] Table columns/actions translated.
- [ ] Dialogs/toasts translated.
- [ ] Role/permission naming aligns with business glossary in both languages.

## Module 4: Accounting (Transactions)

### Scan Targets
- `resources/js/pages/accounting/transactions/Index.vue`
- `resources/js/pages/accounting/transactions/Create.vue`
- `resources/js/pages/accounting/transactions/Edit.vue`
- `resources/js/pages/accounting/transactions/Show.vue`

### Checklist
- [ ] Financial labels translated (amounts, tax, due date, totals).
- [ ] Category/subcategory/type labels translated.
- [ ] Payment method/status labels translated.
- [ ] Filter and pagination labels translated.
- [ ] Validation and submission feedback localized.

## Cross-Module Gap Checks
- [ ] Compare fields shown in UI with `docs/api/validations/**` and controller validations.
- [ ] Compare available actions with documented mutations in `docs/api/mutations/**`.
- [ ] Verify every list page supports expected filter states from query contracts.
- [ ] Record parity mismatches even if translation is complete.

## Deliverables
- Core modules gap register entries with severity and fix scope.
- Translation key backlog grouped by module and page.
- Before/after screenshots for at least one list/create/show page per entity group.

## Exit Criteria
- [ ] No hardcoded user-facing copy in scanned core modules.
- [ ] High and critical parity gaps identified and logged.
- [ ] Core module glossary finalized (shared terms across properties/leasing/contacts/accounting).
