## Plan: Property SaaS Implementation Blueprint

Model: Use **Claude Opus 4 (copilot)** to execute this plan. It handles multi-phase architectural work, large context (all.json + app.json + CLAUDE.md), and cross-file consistency best.

This plan is adjusted using both app capabilities in [app.json](app.json) and detailed model inputs in [all.json](all.json). It is designed for direct implementation with progress tracking.

## Global Progress

Overall completion: 0%

[----------------------------------------] 0/40 major tasks complete

How to update progress while implementing:
- Mark each checklist item as done by changing [ ] to [x].
- Update the phase counters and the global bar after each merged change.
- Run `vendor/bin/pint --dirty --format agent` after modifying PHP files.
- Run `php artisan test --compact` with the relevant test file after each model/migration.

---

## Step 0: Naming And Boundary Decisions (Resolved)

Phase progress: [xxxx] 4/4

These decisions are final. Do not re-ask for confirmation.

- [x] **Account tenancy vs rental tenancy naming:**
	- `Tenant` (Spatie model, `tenants` landlord table): multitenancy container. Already exists in `database/migrations/landlord/`.
	- `Resident` (model, `rf_tenants` table): the rental tenant person/entity. Use `protected $table = 'rf_tenants'` in the model.
	- This avoids the naming collision between Spatie's `Tenant` and the business domain "tenant".
- [x] **Financial core model:**
	- `Transaction`: the invoice-like source of truth (amount, due_on, lease/unit links). Table: `rf_transactions`.
	- `Payment`: settlement records against transactions. Table: `rf_payments`.
	- Computed fields (`paid`, `left`, `is_paid`) derive from `sum(payments.amount)`.
- [x] **Request model strategy:**
	- Single `Request` model with `category_id` + `subcategory_id` foreign keys. Table: `rf_requests`.
	- Different request types (maintenance, visitor access, manager requests) are distinguished by `RequestCategory`.
	- Status flow uses the universal `rf_statuses` table via `status_id`.
- [x] **Table naming strategy:**
	- Keep `rf_*` prefixed table names for all domain models (matches the legacy API in all.json).
	- Reference tables (`countries`, `cities`, `districts`, `currencies`, `media`) keep clean names (no prefix).
	- Each model uses `protected $table = 'rf_table_name'` when the table name doesn't match Laravel convention.

---

## Step 0.5: Cross-Cutting Architecture Decisions (Resolved)

Phase progress: [xxxxxxxx] 8/8

- [x] **Shared contact trait:** Create `App\Concerns\HasContactInfo` trait with: `first_name`, `last_name`, `email`, `phone_number`, `national_phone_number`, `phone_country_code`, `national_id`, `nationality_id`, `gender`, `georgian_birthdate`, `image`, `active`, `last_active`. Use on: `Resident`, `Owner`, `Admin`, `Professional`.
- [x] **Multitenancy column:** All `rf_*` domain tables get an `account_tenant_id` column (nullable FK to landlord `tenants.id`). Add a `BelongsToAccountTenant` trait with a global scope that filters by current tenant. Apply to: Community, Building, Unit, Resident, Owner, Lease, Transaction, Request, Admin, Professional, Announcement, Facility.
- [x] **Assignee polymorphism in Transaction:** Use `assignee_type` + `assignee_id` morphTo pattern. `assignee_type` is `Resident` or `Owner`.
- [x] **Enum classes to create:** `App\Enums\TenantType` (individual, company), `App\Enums\RentalType` (total, detailed), `App\Enums\Gender` (male, female), `App\Enums\MarketplaceType` (rent, sale, both), `App\Enums\LeaseEscalationType` (fixed, percentage), `App\Enums\AdminRole` (Admins, accountingManagers, serviceManagers, marketingManagers, salesAndLeasingManagers).
- [x] **Bilingual fields:** Models with `name_ar`/`name_en` fields store both. The `name` accessor returns the value based on app locale. No translation package needed; just a shared `HasBilingualName` trait.
- [x] **JSON map cast:** Community, Building, Unit models cast `map` to `array`. No custom cast needed.
- [x] **SoftDeletes:** Apply to `Lease`, `Transaction`, `Payment`, `Request`, `Resident`, `Owner`. Not on reference data.
- [x] **Factories and seeders:** Every model created with `php artisan make:model Name -mf --no-interaction` gets a factory. Reference data models also get seeders. Use `php artisan make:seeder NameSeeder --no-interaction`.

---

## Phase 1: Reference Data And Media Foundation (Must Come First)

Phase progress: [------------] 0/12

These are foreign key dependencies for all later phases. Build them first.

Target models: Country, City, District, Currency, Status, Setting, UnitCategory, UnitType, Media

Implementation checklist:
- [ ] Create `Country` model/migration (`countries` table). Columns from all.json: id, Iso2, Iso3, Name, name_ar, name_en, Dial, Currency, Capital, Continent, Unicode, Excel. Add factory and seeder importing from a standard countries dataset.
- [ ] Create `City` model/migration (`cities` table). Columns: id, name, name_ar, name_en, country_id (FK countries). Factory + seeder.
- [ ] Create `District` model/migration (`districts` table). Columns: id, name, name_ar, name_en, city_id (FK cities). Factory + seeder.
- [ ] Create `Currency` model/migration (`currencies` table). Columns: id, name, code, symbol. Factory + seeder.
- [ ] Create `Status` model/migration (`rf_statuses` table). Columns: id, name, name_ar, name_en, priority, type. Seeder with all status groups from all.json `statusGroups`.
- [ ] Create `Setting` model/migration (`rf_settings` table). Columns: id, name, name_ar, name_en, type (enum: rental_contract_type, payment_schedule, lease_setting, invoice_setting), parent_id (self-referencing FK). Factory + seeder.
- [ ] Create `UnitCategory` model/migration (`rf_unit_categories` table). Columns: id, name, name_ar, name_en, icon. Factory + seeder.
- [ ] Create `UnitType` model/migration (`rf_unit_types` table). Columns: id, name, name_ar, name_en, icon, category_id (FK rf_unit_categories). Factory + seeder.
- [ ] Create `Media` model/migration (`media` table). Polymorphic: id, url, name, notes, mediable_type, mediable_id, collection, timestamps. Factory.
- [ ] Create all Enum classes listed in Step 0.5 under `app/Enums/`.
- [ ] Create `HasContactInfo`, `BelongsToAccountTenant`, and `HasBilingualName` traits under `app/Concerns/`.
- [ ] Add seed verification tests: each reference table has expected rows after seeding.

Definition of done:
- All reference tables exist, are seeded, and have passing verification tests.
- Traits are created and unit tested in isolation.

---

## Phase 2: Core Account And Property Graph

Phase progress: [----------] 0/10

Target models: AccountMembership (pivot), Community, Building, Unit, Resident, Owner, Admin, Professional

Implementation checklist:
- [ ] Create `AccountMembership` pivot migration/model (`account_memberships` table). Columns: id, user_id (FK users), account_tenant_id (FK tenants), role (string or RolesEnum cast), timestamps. This links auth Users to Spatie tenant accounts.
- [ ] Create `Community` model/migration (`rf_communities` table). All columns from all.json. FKs: country_id, currency_id, city_id, district_id. Add account_tenant_id. Include relationships: country, currency, city, district, buildings (hasMany), amenities (belongsToMany via `community_amenities` pivot). Factory.
- [ ] Create `Building` model/migration (`rf_buildings` table). All columns from all.json. FK: rf_community_id. Add account_tenant_id. Relationships: community, city, district, units (hasMany). Factory.
- [ ] Create `Unit` model/migration (`rf_units` table). All columns from all.json. FKs: rf_community_id, rf_building_id (nullable), category_id, type_id, status_id, city_id, district_id. Add account_tenant_id. Relationships: community, building, category, type, status, owner, lease. Factory.
- [ ] Create `Resident` model/migration (`rf_tenants` table, `protected $table`). Use `HasContactInfo` trait. Additional columns: source_id (FK rf_lead_sources, nullable), accepted_invite (bool). Relationships: units, leases, transactions, dependents, documents (morphMany Media). Factory.
- [ ] Create `Owner` model/migration (`rf_owners` table). Use `HasContactInfo` trait. Additional columns: relation, relation_key. Relationships: units, transactions, active_requests. Factory.
- [ ] Create `Admin` model/migration (`rf_admins` table). Use `HasContactInfo` trait. Additional columns: role (AdminRole enum cast), last_login_at. Relationships: communities (belongsToMany), buildings (belongsToMany), types (belongsToMany ServiceType, for serviceManagers). Factory.
- [ ] Create `ManagerRole` model/migration (`rf_manager_roles` table). Columns: id, role, name_ar, name_en. Factory + seeder.
- [ ] Create `Professional` model/migration (`rf_professionals` table). Use `HasContactInfo` trait. Relationships: requests (hasMany), subcategories (belongsToMany). Factory.
- [ ] Add feature tests: tenant isolation (cross-tenant read blocked), Community→Building→Unit graph integrity, Resident/Owner CRUD with factory states.

Definition of done:
- Core property graph supports CRUD with valid FKs.
- No cross-tenant reads possible in feature tests.
- All models use `BelongsToAccountTenant` trait and global scope.

---

## Phase 3: Lease And Finance

Phase progress: [----------] 0/10

Target models: Lease, LeaseUnit (pivot), LeaseAdditionalFee, LeaseEscalation, Transaction, Payment, TransactionAdditionalFee

Implementation checklist:
- [ ] Create `Lease` model/migration (`rf_leases` table). All columns from all.json including: contract_number (unique), tenant_id (FK rf_tenants), status_id (FK rf_statuses), lease_unit_type_id, rental_contract_type_id, payment_schedule_id, created_by_id, deal_owner_id, date fields, enum casts (TenantType, RentalType, LeaseEscalationType), boolean flags. SoftDeletes. Factory.
- [ ] Create `LeaseUnit` pivot migration/model (`lease_units` table). Columns: lease_id, unit_id, rental_annual_type, annual_rental_amount, net_area, meter_cost.
- [ ] Create `LeaseAdditionalFee` model/migration. Linked to Lease. Factory.
- [ ] Create `LeaseEscalation` model/migration. Linked to Lease. Factory.
- [ ] Create `Transaction` model/migration (`rf_transactions` table). All columns from all.json. Polymorphic assignee (assignee_type + assignee_id). FKs: lease_id, unit_id, category_id, subcategory_id, type_id, status_id. Indexes on: due_on, status_id, lease_id, assignee_id. SoftDeletes. Factory.
- [ ] Create `Payment` model/migration (`rf_payments` table). Columns: id, transaction_id (FK), amount, payment_date, payment_method, reference, notes, timestamps. SoftDeletes. Factory.
- [ ] Create `TransactionAdditionalFee` model/migration. Linked to Transaction. Factory.
- [ ] Add computed accessors on Transaction: `paid` (sum of payments), `left` (amount - paid), formatted versions.
- [ ] Add computed accessors on Lease: `total_unpaid_amount`, `unpaid_transactions_count`.
- [ ] Add tests: partial payment, full settlement, overdue states, lease financial summary derived from transactions/payments only.

Definition of done:
- Lease financial summary calculated from transactions and payments.
- Lease→Unit many-to-many with pivot data works correctly.

---

## Phase 4: Requests, Services, And Communication

Phase progress: [----------] 0/10

Target models: RequestCategory, RequestSubcategory, Request, ServiceSetting, WorkingDay, FeaturedService, Announcement, Facility, FacilityCategory, FacilityBooking

Implementation checklist:
- [ ] Create `RequestCategory` model/migration (`rf_request_categories`). Columns from all.json. Relationships: sub_categories, icon (belongsTo Media), serviceSettings (hasOne). Factory + seeder.
- [ ] Create `RequestSubcategory` model/migration (`rf_request_subcategories`). Columns from all.json including time fields (start, end, is_all_day), terms_and_conditions. Relationships: category, professionals (belongsToMany), working_days. Factory.
- [ ] Create `Request` model/migration (`rf_requests` table). Columns: id, category_id, subcategory_id, status_id, community_id, building_id, unit_id, resident_id (or owner_id), professional_id, assigned_manager_id, description, scheduled_at, completed_at, code, timestamps. SoftDeletes. Factory.
- [ ] Create `ServiceSetting` model/migration (`rf_service_settings`). JSON columns: visibilities, permissions. Other config columns from all.json. Factory.
- [ ] Create `WorkingDay` and `FeaturedService` models/migrations linked to RequestSubcategory.
- [ ] Create `FacilityCategory` model/migration (`rf_facility_categories`). Columns: id, name, name_ar, name_en. Seeder.
- [ ] Create `Facility` model/migration (`rf_facilities`). Columns from all.json. FK: community_id, category_id. Relationships: bookings, images. Factory.
- [ ] Create `FacilityBooking` model/migration. Columns: id, facility_id, resident_id, status_id, booking_date, start_time, end_time, timestamps. Factory.
- [ ] Create `Announcement` model/migration (`rf_announcements`). Columns from all.json. Scoped by community_id / building_id. Factory.
- [ ] Add tests: request lifecycle (created → assigned → in_progress → completed → closed), announcement visibility by community/building, facility booking with capacity checks.

Definition of done:
- Request and communication modules are tenant-safe and role-safe.
- Request status flow uses rf_statuses with correct group IDs.

---

## Phase 5: Supplementary Models And Relationships

Phase progress: [--------] 0/8

Target models: Dependent, LeadSource, UnitSpecification, UnitRoom, UnitArea, Feature, Amenity, MarketplaceUnit, MarketplaceVisit

Implementation checklist:
- [ ] Create `Dependent` model/migration. Linked to Resident (or Owner). Contact fields subset. Factory.
- [ ] Create `LeadSource` model/migration (`rf_lead_sources`). Columns: id, name, name_ar, name_en. Seeder.
- [ ] Create `UnitSpecification`, `UnitRoom`, `UnitArea` models/migrations linked to Unit. Factories.
- [ ] Create `Feature` model/migration + `feature_unit` pivot table (belongsToMany with Unit). Seeder.
- [ ] Create `Amenity` model/migration + `community_amenities` pivot table (belongsToMany with Community). Seeder.
- [ ] Create `MarketplaceUnit` model/migration (`rf_marketplace_units`). Columns from all.json. FK: unit_id. Factory.
- [ ] Create `MarketplaceVisit` model/migration. Linked to MarketplaceUnit. Factory.
- [ ] Add tests: Unit detail relationships (specs, rooms, areas, features), marketplace listing flow.

Definition of done:
- All relationship paths defined in all.json are implemented and tested.

---

## Phase 6: Authorization, Policies, And Permissions Matrix

Phase progress: [------] 0/6

Implementation checklist:
- [ ] Create a `PermissionsSeeder` that maps all subjects × actions from [app.json](app.json) `permissionSubjects` and `permissionActions` into Spatie permission records. Use the format `{subject}.{ACTION}` (e.g. `communities.VIEW`, `leases.CREATE`).
- [ ] Create a `RolesSeeder` that creates roles matching [app/Enums/RolesEnum.php](app/Enums/RolesEnum.php) and assigns default permission sets per role.
- [ ] Add policies for core aggregates: `CommunityPolicy`, `BuildingPolicy`, `UnitPolicy`, `LeasePolicy`, `TransactionPolicy`, `RequestPolicy`, `AnnouncementPolicy`, `FacilityPolicy`.
- [ ] Each policy method checks both Spatie permission (`$user->can('communities.UPDATE')`) and tenant ownership (`$community->account_tenant_id === currentTenantId`).
- [ ] Register policies in `AuthServiceProvider` or use auto-discovery.
- [ ] Add feature tests: forbidden actions across roles (e.g. Professional cannot create Lease, Owner cannot delete Community) and cross-tenant access denied.

Definition of done:
- Every write action is policy-protected and permission-checked.
- Role matrix aligns with RolesEnum and app.json roles.

---

## Phase 7: Final Hardening And Go-Live Checks

Phase progress: [----] 0/4

Implementation checklist:
- [ ] Run `vendor/bin/pint --dirty --format agent` on all modified PHP files.
- [ ] Run full test suite: `php artisan test --compact`.
- [ ] Verify migration order: migrations can run fresh with `php artisan migrate:fresh --seed` without FK errors.
- [ ] Document migration rollback strategy and seed replay instructions in a `MIGRATIONS.md` (only if requested).

Definition of done:
- Full test suite passes with tenant-isolation, role-access, and core lifecycle coverage.

---

## Immediate Execution Order

1. Execute Phase 1 (reference data + traits + enums) — everything else depends on this.
2. Execute Phase 2 (property graph + people models).
3. Execute Phase 3 (lease + finance).
4. Execute Phase 4 (requests + communication).
5. Execute Phase 5 (supplementary models).
6. Execute Phase 6 (authorization).
7. Execute Phase 7 (hardening).

Run tests after each phase. Do not proceed to the next phase if the current phase has failing tests.

## Commands To Use During Implementation

```bash
# Model with migration and factory
php artisan make:model Name -mf --no-interaction

# Pivot model (no migration flag, create migration separately)
php artisan make:model LeaseUnit --pivot --no-interaction

# Policy
php artisan make:policy NamePolicy --model=Name --no-interaction

# Form Request
php artisan make:request StoreNameRequest --no-interaction

# Seeder
php artisan make:seeder NameSeeder --no-interaction

# Enum
php artisan make:enum EnumName --no-interaction

# Trait/Concern
php artisan make:class App/Concerns/TraitName --no-interaction

# Test (feature test by default)
php artisan make:test --phpunit NameTest --no-interaction

# Run specific test
php artisan test --compact tests/Feature/TargetTest.php

# Run all tests
php artisan test --compact

# Format
vendor/bin/pint --dirty --format agent

# Fresh migration with seed
php artisan migrate:fresh --seed
```

## Tracking Template For PR Updates

Use this block in each PR description:

- Completed phase items: X/Y
- Overall progress: Z%
- New models added:
- Migrations added:
- Policies added:
- Tests added:
- Risks or follow-ups: