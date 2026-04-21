# Operations Modules Audit Plan

## Scope
- Requests
- Facilities and facility bookings
- Communication (announcements)
- Visitor access
- Documents and imports

## Module 1: Requests

### Scan Targets
- `resources/js/pages/requests/Index.vue`
- `resources/js/pages/requests/Create.vue`
- `resources/js/pages/requests/Edit.vue`
- `resources/js/pages/requests/Show.vue`

### Checklist
- [ ] Request lifecycle labels/actions translated.
- [ ] Category/subcategory/type labels translated.
- [ ] Priority, status, assignee labels translated.
- [ ] Scheduling labels/messages translated.
- [ ] Action availability matches expected workflow transitions.
- [ ] Request timeline and notifications copy localized.

## Module 2: Facilities and Bookings

### Scan Targets
- `resources/js/pages/facilities/Index.vue`
- `resources/js/pages/facilities/Create.vue`
- `resources/js/pages/facilities/Edit.vue`
- `resources/js/pages/facilities/Show.vue`
- `resources/js/pages/facilities/bookings/Index.vue`
- `resources/js/pages/facilities/bookings/Create.vue`
- `resources/js/pages/facilities/bookings/Edit.vue`
- `resources/js/pages/facilities/bookings/Show.vue`

### Checklist
- [ ] Facility labels translated (name, category, capacity, hours, availability).
- [ ] Booking flow labels translated (book, approve/reject, status).
- [ ] Gender/age/capacity restrictions translated and understandable.
- [ ] Facility and booking status values translated.
- [ ] Booking date/time validation messages translated.
- [ ] UI actions reflect backend capabilities.

## Module 3: Communication (Announcements)

### Scan Targets
- `resources/js/pages/communication/announcements/Index.vue`
- `resources/js/pages/communication/announcements/Create.vue`
- `resources/js/pages/communication/announcements/Edit.vue`
- `resources/js/pages/communication/announcements/Show.vue`

### Checklist
- [ ] Announcement creation/edit/show copy translated.
- [ ] Audience targeting labels translated.
- [ ] Publish/schedule labels translated.
- [ ] Success/failure toasts translated.
- [ ] Status labels translated and consistent.

## Module 4: Visitor Access

### Scan Targets
- `resources/js/pages/visitor-access/History.vue`
- `resources/js/pages/visitor-access/Details.vue`

### Checklist
- [ ] Visitor details fields translated.
- [ ] Approve/reject/cancel actions translated.
- [ ] Reason and notes labels translated.
- [ ] Status timeline and badges translated.
- [ ] Workflow actions in UI match route/controller transitions.

## Module 5: Documents and Imports

### Scan Targets
- `resources/js/pages/documents/Index.vue`
- `resources/js/pages/documents/LeadsImportErrors.vue`

### Checklist
- [ ] Upload and import wizard copy translated.
- [ ] Step labels and helper notes translated.
- [ ] File size/type/count errors translated and interpolated correctly.
- [ ] Error review tables translated.
- [ ] Success/failure banners translated.
- [ ] Import status labels translated.

## Cross-Module Gap Checks
- [ ] Compare request/facility/visitor status states with `docs/api/queries/**` and `docs/api/mutations/**`.
- [ ] Compare upload/import validation messages with `docs/api/validations/documents/**`.
- [ ] Validate all operational toasts and confirmation dialogs have translation keys.
- [ ] Detect missing UI actions where routes/mutations exist but UI entry is absent.

## Deliverables
- Operations gap register with workflow-specific findings.
- Translation key backlog for operations domains.
- Evidence set: workflow screenshots for Arabic and English for one end-to-end flow per module.

## Exit Criteria
- [ ] Operational pages have no hardcoded user-facing strings.
- [ ] Workflow actions and states are localized and parity-checked.
- [ ] Upload/import and validation copy quality reviewed in Arabic.
