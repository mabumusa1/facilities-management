# System Copy Implementation Plan

## Scope Alignment Baseline
This plan is aligned to the captured product surface in:
- docs/api/routes.json
- docs/api/openapi.json
- docs/api/queries/*
- docs/api/mutations/*
- docs/api/validations/*
- docs/api/docs/*

---

## Phase 1: Fix Existing Model Relationships & Missing Data
Priority: CRITICAL - current pages crash without these

### 1.1 Missing Model Relationships
- [x] Lease - add createdBy() -> belongsTo Admin, dealOwner() -> belongsTo Admin
- [x] Transaction - add category() -> belongsTo Setting, subcategory() -> belongsTo Setting, type() -> belongsTo Setting
- [x] Community - add facilities() hasMany
- [x] Professional - add requests() hasMany
- [x] FacilityBooking - verify booker() morph works (booker_type/booker_id)
- [x] Resident - add units() hasMany (was missing)

### 1.2 Fix Controller Data Loading
- [x] CommunityController@index - add withCount for buildings, units, requests
- [x] CommunityController@show - load facilities
- [x] BuildingController@show - load units (already done)
- [x] OwnerController@show - load units with community/building
- [x] TransactionController@index - load category/subcategory/type relationships
- [x] TransactionController@show - load category/subcategory/type relationships
- [x] AnnouncementController@create/edit - pass communities, buildings (already done)
- [x] AnnouncementController@store/update - fix field name body->content
- [x] AdminController@create/edit - pass communities/buildings for scope assignment
- [x] ProfessionalController@create/edit - pass service subcategories
- [x] FacilityBookingController - fix resident->booker relationship name

---

## Phase 2: Complete Form Fields (Create/Edit pages)
Priority: HIGH - forms are missing required fields

### 2.1 Unit Forms
- [x] Add owner_id Select dropdown (owners list)
- [x] Add tenant_id Select dropdown (residents list)
- [x] Add city_id / district_id cascading selects
- [x] Add marketplace flags (is_market_place, is_buy, is_off_plan_sale)
- [x] Add net_area, floor_no, about fields (already in validation)
- [x] Update UnitController create/edit to pass owners, residents, cities, districts, buildings, statuses

### 2.2 Lease Forms
- [x] Add deal_owner_id Select (admins list)
- [x] Add legal_representative text field
- [x] Add fit_out_status field
- [x] Add number_of_years, number_of_months, number_of_days
- [x] Add actual_end_at date field (Edit only)
- [x] Update LeaseController to pass admins list
- [x] Expand store/update validation with all fields

### 2.3 Owner Forms
- [x] Add nationality_id Select (countries list)
- [x] Add gender Select (already in validation)
- [x] Add active toggle
- [x] Update OwnerController create/edit to pass countries

### 2.4 Resident Forms
- [x] Add nationality_id Select (countries list)
- [x] Add gender Select (already in validation)
- [x] Add active toggle
- [x] Add source_id field
- [x] Update ResidentController create/edit to pass countries

### 2.5 Admin Forms
- [x] Add nationality_id Select (countries list)
- [x] Add gender Select (already done in Create, verified Edit)
- [x] Add national_id field (already done in Create, verified Edit)
- [x] Add communities/buildings scope checkboxes (sync in store/update)
- [x] Add georgian_birthdate, active fields
- [x] Update AdminController create/edit to pass countries, communities, buildings

### 2.6 Professional Forms
- [x] Add service subcategory assignment (sync in store/update)
- [x] Update ProfessionalController create/edit to pass subcategories

### 2.7 Transaction Forms
- [x] Verify category/type dropdowns working (already done)
- [x] Add subcategory_id dropdown (transactionSubcategories)

### 2.8 Announcement Forms
- [x] Verify community/building dropdowns working (already done)
- [x] Add published_at date field
- [x] Fix body->content field name mismatch

### 2.9 Facility Forms
- [x] Verify category/community dropdowns working (already done)

---

## Phase 3: Complete Missing Pages
Priority: HIGH

### 3.1 Facility Bookings
- [x] Add FacilityBookingController create/store/edit methods
- [x] Create FacilityBooking Create.vue page
- [x] Create FacilityBooking Edit.vue page
- [x] Update routes to allow create/edit

### 3.2 Index Table Column Improvements
- [x] Communities Index - add currency, commission rates columns
- [x] Buildings Index - add units_count, year_build columns (already done)
- [x] Units Index - add owner, tenant, net_area, floor columns
- [x] Leases Index - add tenant_type column
- [x] Owners Index - add active badge, units_count, last_active (already done)
- [x] Residents Index - add active badge, leases_count (already done)
- [x] Transactions Index - add category, type columns
- [x] Announcements Index - add published_at, building columns (already done)
- [x] Facility Bookings Index - fix resident->booker column, add guests column

### 3.3 Show Page Improvements
- [x] Owner Show - display units list with community/building info
- [x] Resident Show - create page with leases list, dependents, units
- [x] Lease Show - display additional fees, escalations, createdBy, dealOwner
- [x] Transaction Show - display category/type names instead of IDs
- [x] Community Show - display facilities card
- [x] Building Show - display units list (already done)
- [x] Facility Booking Show - add edit/delete buttons, fix booker, add notes/guests

---

## Phase 4: Settings & Shared System Modules
Priority: CRITICAL - major documented surface is missing from plan

### 4.1 Settings Pages & Tabs
- [x] S4-01 Settings shell tabs: BE route + FE tabs (invoice, service-request, visitor-request, bank-details, visits-details, sales-details) + TEST page access/navigation
- [x] S4-02 Invoice settings read model: BE GET source alignment + FE load state + TEST returned payload shape
- [x] S4-03 Invoice settings write flow: BE POST validation/persistence + FE submit/errors/toast + TEST success/validation cases
- [x] S4-04 Service request settings list page: BE category/subcategory/type payloads + FE listing/navigation + TEST page data contract
- [x] S4-05 Service request details page: BE details endpoint + FE /settings/service-request/:type/:catCode/:catId rendering + TEST route/data binding
- [x] S4-06 Service settings updateOrCreate: BE POST /rf/requests/service-settings/updateOrCreate (rf_category_id, permissions) + FE form + TEST create/update/validation
- [x] S4-07 Visitor request settings page: BE settings source + FE controls + TEST page access and save
- [x] S4-08 Bank details settings page: BE read/write endpoints + FE form + TEST validation/persistence
- [x] S4-09 Visits details settings page: BE read/write endpoints + FE form + TEST validation/persistence
- [x] S4-10 Sales details settings page: BE read/write endpoints + FE form + TEST validation/persistence
- [x] S4-11 Settings facilities list/detail: BE list/show endpoints + FE table/detail + TEST data and permissions
- [x] S4-12 Settings facilities create/edit: BE create/update validation + FE form + TEST create/update validation paths
- [x] S4-13 Settings forms index/create: BE form templates CRUD + FE list/create + TEST create and list behavior
- [x] S4-14 Settings forms community/building selection: BE lookup endpoints + FE select-community/select-building screens + TEST lookup/filter behavior
- [x] S4-15 Settings forms preview: BE preview payload builder + FE preview renderer + TEST preview structure and required fields

### 4.2 Shared System Endpoints & Widgets
- [x] S4-16 Notifications module: BE list/unread-count/mark-as-read/mark-all-as-read + FE bell/dropdown + TEST API and state transitions
- [x] S4-17 Dashboard requires-attention widgets: BE endpoint wiring + FE dashboard cards + TEST widget payload rendering
- [x] S4-18 Shared lookup services: BE/FE integration for rf/modules, rf/statuses, rf/common-lists, rf/leads, countries + TEST lookup contracts

---

## Phase 5: Marketplace, Visitor Access & Reports
Priority: HIGH - currently documented as active routes and endpoints

### 5.1 Marketplace Module
- [x] Implement /marketplace overview page
- [x] Implement /marketplace/customers page
- [x] Implement /marketplace/listing page
- [x] Implement marketplace listings create/update/delete flows
- [x] Implement marketplace offers create/update/delete flows
- [x] Implement marketplace communities list/unlist flows
- [x] Implement marketplace community sales information update/resend flows
- [x] Implement marketplace units prices-visibility flow
- [x] Implement marketplace visits list/details flows
- [x] Implement marketplace visits cancel/send-contract flows
- [x] Align marketplace bank/sales/visits settings validations with documented contracts

### 5.2 Visitor Access Module
- [x] Implement /visitor-access history page
- [x] Implement /visitor-access/visitor-details/:id page
- [x] Implement visitor access approve/reject actions with status transitions

### 5.3 Reports & Power BI
- [x] Integrate report/dashboard route set (load, prepare, render, pages, settings, filters) with permission checks

---

## Phase 6: Documents, Uploads & Imports
Priority: HIGH - documented endpoints exist but are not in active plan scope

- [x] Implement documents upload UI for POST /rf/files
- [x] Implement documents delete flow for DELETE /rf/files/:id
- [x] Implement Excel import flow for POST /rf/excel-sheets
- [x] Implement land import flow for POST /rf/excel-sheets/land
- [x] Implement leads import flow for POST /rf/excel-sheets/leads
- [x] Implement upload leads error review page integration
- [x] Enforce documented validations for image, file, and rf_community_id
- [x] Add feature tests for documents/import endpoints

---

## Phase 7: Sample Data Seeders
Priority: MEDIUM - needed for testing

- [x] CommunitySeeder - 3 sample communities with proper country/city/district
- [x] BuildingSeeder - 5 buildings across communities
- [x] UnitSeeder - 15 units across buildings
- [x] OwnerSeeder - 5 owners assigned to units
- [x] ResidentSeeder - 10 tenants
- [x] AdminSeeder - 3 admins with different roles
- [x] ProfessionalSeeder - 3 professionals
- [x] LeaseSeeder - 5 leases with units
- [x] TransactionSeeder - 10 transactions for leases
- [x] RequestSeeder - 8 service requests
- [x] AnnouncementSeeder - 3 announcements
- [x] FacilitySeeder - 3 facilities with categories
- [x] FacilityBookingSeeder - 5 bookings
- [x] RequestSubcategorySeeder - subcategories for each category

---

## Phase 8: TypeScript Types & Contract Sync
Priority: MEDIUM

- [x] Add Lead type
- [x] Add Feature type
- [x] Add Amenity type
- [x] Add LeaseAdditionalFee type
- [x] Add LeaseEscalation type
- [x] Add LeaseUnit type
- [x] Add MarketplaceUnit type
- [x] Add MarketplaceVisit type
- [x] Add ServiceSetting type
- [x] Add WorkingDay type
- [x] Add FeaturedService type
- [x] Add Media type
- [x] Add CommonList type
- [x] Add InvoiceSetting type
- [x] Add TransactionAdditionalFee type
- [x] Add UnitArea, UnitRoom, UnitSpecification types
- [x] Add Notification and NotificationUnreadCount types
- [x] Add Module, Status, DashboardRequiresAttention types

---

## Phase 9: Stabilization & Contract Quality Gates
Priority: HIGH - current captures show high error rates in key modules

- [x] Add API contract tests for settings endpoints
- [x] Add API contract tests for marketplace endpoints
- [x] Add API contract tests for documents endpoints
- [x] Add API contract tests for requests endpoints
- [x] Add API contract tests for transactions endpoints
- [x] Add OpenAPI drift check for critical routes and payloads
- [x] Add validation-schema drift check against docs/api/validations
- [x] Add smoke tests for settings, notifications, and invoice flows
- [x] Raise capture success rates to >= 80% for settings, marketplace, documents, requests, and transactions

---

## Phase 10: Advanced Features (Future)
Priority: LOW - requires business logic

- [ ] Status workflow enforcement (state machine)
- [ ] Sub-lease management UI
- [ ] Search/filtering on Index pages
- [ ] Pagination controls on Index pages
- [ ] Notification delivery channels (email/SMS/push) beyond in-app

---

## Progress Tracking
- Phase 1: 17/17 items ✅
- Phase 2: 36/36 items ✅
- Phase 3: 20/20 items ✅
- Phase 4: 18/18 items ✅
- Phase 5: 15/15 items ✅
- Phase 6: 8/8 items ✅
- Phase 7: 14/14 items ✅
- Phase 8: 18/18 items ✅
- Phase 9: 9/9 items ✅
- Phase 10: 0/5 items (future)
- Total: 155/155 items (excluding Phase 10)
