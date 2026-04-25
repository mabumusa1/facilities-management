# Tech Lead — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Stack
- Laravel 13, PHP 8.5, Inertia v3, Vue 3, Tailwind v4, Wayfinder v0.
- Auth: Fortify v1.
- Multi-tenancy: Spatie.
- Permissions: spatie/laravel-permission.
- Tests: PHPUnit v12 (not Pest).
- Formatter: Pint (run `vendor/bin/pint --dirty --format agent`).

## Domain model map
- `app/Models/Community.php`, `Building.php`, `Unit.php` — property hierarchy.
- `app/Models/Lease.php`, `LeaseUnit.php`, `LeaseAdditionalFee.php`, `LeaseEscalation.php`, `Resident.php`, `Owner.php` — leasing.
- `app/Models/MarketplaceUnit.php`, `MarketplaceOffer.php`, `MarketplaceVisit.php` — marketplace.
- `app/Models/ServiceRequest.php`, `RequestCategory.php`, `RequestSubcategory.php` — service requests.
- `app/Models/Facility.php`, `FacilityBooking.php`, `FacilityCategory.php` — facilities.
- `app/Models/Transaction.php`, `Payment.php`, `Invoice.php`, `Currency.php`, `ServiceManagerType.php` — accounting.
- `app/Models/Announcement.php` — communication.
- `app/Models/Tenant.php` — multi-tenancy.

## Controllers live under
`app/Http/Controllers/{Accounting,Admin,AppSettings,Communication,Contacts,Documents,Facilities,Leasing,Marketplace,Properties,...}/`. Match the sibling naming convention when adding new controllers.

## Routes
- `routes/web.php` (main), `routes/console.php` (commands/schedule), `routes/settings.php` (settings area).
- Wayfinder generates TS clients — run `php artisan wayfinder:generate` after any controller/route signature change.

## Frontend conventions
- Pages in `resources/js/pages/<area>/{Index,Create,Edit,Show}.vue`.
- Single root element per Vue component.
- Inertia v3 idioms: `useForm`, `useHttp`, `<Link>`, `router` from `@inertiajs/vue3`.
- `Inertia::lazy()` / `LazyProp` are removed → use `Inertia::optional()`.
- Deferred props must have a skeleton/pulse empty state.

## Test conventions
- `tests/Feature/` for feature tests (most tests live here).
- `tests/Unit/` for unit tests only when there's no HTTP touch point.
- Use factories; check for custom states before manually setting up models.
- Multi-tenant: wrap tests in the appropriate tenant scope; do not hit mock DBs.

## Recurring risks to flag in designs
- N+1 on listing pages — always specify `->with([...])` in design.
- Tenant boundary leaks — any query must be tenant-scoped.
- Wayfinder TS drift — callout if controller signatures change.
- i18n/RTL — check both directions for any UI affecting string layout.

## RBAC architecture (Design #110)
- Spatie permission tables (`roles`, `permissions`, `role_has_permissions`, `model_has_roles`) already migrated (2026_04_20).
- Custom `app/Models/Role.php` and `app/Models/Permission.php` extend Spatie base models; register in `config/permission.php`.
- Tenant isolation: add `account_tenant_id` to `roles` + use `BelongsToAccountTenant` concern. Drop Spatie's default unique index on `(name, guard_name)` and replace with `(account_tenant_id, name, guard_name)`.
- Manager scope columns (`community_id`, `building_id`, `service_type_id`) bolt onto `model_has_roles` (the Spatie polymorphic pivot) — no separate `user_roles` table.
- Existing enums: `app/Enums/RolesEnum.php` (7 UserRole values) and `app/Enums/AdminRole.php` (5 AdminRole values) — do not duplicate.
- Net-new enums: `RoleType`, `PermissionSubject` (31 subjects from all.json), `PermissionAction`.
- `rf_admins.role` string column coexists during migration window; FK linkage is a separate story.
- `all.json` has 31 permission subjects (PRD says 30 — verify with PM before enum creation).

## RBAC enforcement patterns (Design #112)
- 8 policies already exist in `app/Policies/`; use controller `$this->authorize()` not route middleware (policies do per-instance tenant checks that require resolved model binding).
- Super-admin bypass: one `Gate::before()` in `AppServiceProvider::boot()` is sufficient; no per-policy `before()` needed.
- Non-model subjects (reports, settings, companyProfile, invoiceSettings, leaseSettings, directories, suggestions, complaints, homeServices, neighbourhoodServices): use `Gate::define()` in AppServiceProvider, not synthetic model classes.
- `PermissionSubject::Tenants` maps to `App\Models\Resident`, NOT `App\Models\Tenant` (tenancy model).
- `ReportsController` has a custom `authorizeReportsAccess()` that only checks AccountMembership existence — must be replaced with `Gate::authorize('reports.VIEW')`.
- 403 Inertia JSON shape: catch `AuthorizationException` in `bootstrap/app.php` renderable; return `{"message": __('errors.forbidden')}` with status 403 when `X-Inertia` header present.
- `authorizeResource()` in constructor covers view/create/update/delete but NOT restore/forceDelete — those routes must be explicit if they exist.

## Past work index
- Design #110 — RBAC roles/permissions schema — Spatie tables already exist; unique index must be replaced for tenant isolation; 31 vs 30 subject discrepancy needs PM clarification.
- Design #111 — RbacSeeder — replaces stale PermissionsSeeder + RolesSeeder; use App\Models\Role (not Spatie base) with withoutGlobalScopes() + updateOrCreate; 186 permissions (31 subjects × 6 actions); orphan deletion scoped to whereNull('account_tenant_id'); Arabic name map needs native review (merge blocker); PM must confirm permission preset matrix before merge.
- Design #112 — Permission enforcement end-to-end — controller authorize() + Gate::define for non-model subjects + Gate::before super-admin bypass + Inertia 403 JSON shape. ~10-12 new policies, ~140 authorize() insertions, no route changes, no Wayfinder regen.
- Design #113 — Manager scope filtering — HasManagerScope trait (opt-in local scope ->forManager($user)) + ManagerScopeHelper (scopesForUser via once() + userCanAccessModel for policy write checks). 16 models, ~12 controllers, all policies updated. accountAdmin bypass = is_unrestricted flag from scopesForUser(). No new migrations. Risks: Resident/Owner indirect path via lease_units (historical visibility), Professional via service_type subcategory chain (PM must confirm), N+1 guarded by once().
- Design #114 — Roles List UI — RoleController (Admin namespace, 4 routes in admin.manage group), RolePolicy (blocks system roles via isSystemRole() helper on Role model), StoreRoleRequest + UpdateRoleRequest (type immutable on update). Index passes is_system flag + users_count via withCount. Wayfinder regen required. Key risks: Spatie permission cache must be busted after mutations; name column must mirror name_en for Spatie compatibility; RTL drawer needs inset-inline-end not right-0.
- Design #115 — Permission Matrix UI — Two new routes (GET/PUT admin/roles/{role}/permissions) added to RoleController; new SyncRolePermissionsRequest; RolePolicy::managePermissions() (only blocks PUT, not GET). Sync: full-replace via Permission::whereIn + ->permissions()->sync($ids) (NOT Spatie syncPermissions() — it N+1s per name). Deferred prop for permissions array (flat string[] of "subject.ACTION" names). Presets built server-side from enums (7 presets, no DB table). Permissions.vue: 31×6 grid, sticky subject column (inset-inline-start:0), sticky header row, sticky action bar, auto-check VIEW rule, useHttp for PUT (no redirect). System role: checkboxes disabled, info banner, no action bar, GET still allowed via viewAny policy. Wayfinder regen required.
- Design #117 — Admin role data migration — Artisan command `rbac:migrate-admin-roles`; reads rf_admins.role enum, maps to system-wide Role by name, bulk-inserts model_has_roles rows per chunk. Idempotent (pre-fetch existing pairs per chunk). rf_admins.role column preserved. Risks: morph alias mismatch, RbacSeeder prerequisite, Spatie cache invalidation needed after run. No migrations, no routes, no frontend changes.
- Design #116 — Role Assignment UI — New UserRoleAssignmentController (store + destroy), AccountUserController::show(), 4 new routes in admin.manage group. model_has_roles rows addressed by surrogate PK id. Assign uses raw DB::table insert (no assignScopedRole()). Detach uses User::removeScopedRole(). RolesEnum gains scopeLevel() returning 'none'|'manager'|'serviceManager'. Gate::define('manage-user-role-assignments') in AppServiceProvider. Deferred prop for assignments (single join query). Show.vue + RolesTab.vue + AssignRoleDrawer.vue. Pre-existing bug: AccountUserController::update() calls syncRoles() which throws when scoped rows exist — fix required in scope. Wayfinder regen required. PM must confirm scalar vs batch insert and serviceManager RolesEnum case. — Two new routes (GET/PUT admin/roles/{role}/permissions) added to RoleController; new SyncRolePermissionsRequest; RolePolicy::managePermissions() (only blocks PUT, not GET). Sync: full-replace via Permission::whereIn + ->permissions()->sync($ids) (NOT Spatie syncPermissions() — it N+1s per name). Deferred prop for permissions array (flat string[] of "subject.ACTION" names). Presets built server-side from enums (7 presets, no DB table). Permissions.vue: 31×6 grid, sticky subject column (inset-inline-start:0), sticky header row, sticky action bar, auto-check VIEW rule, useHttp for PUT (no redirect). System role: checkboxes disabled, info banner, no action bar, GET still allowed via viewAny policy. Wayfinder regen required.

## Subscription Architecture (from admin batch #291-#302)
- **Dual-model approach:** The `laravelcm/subscriptions` package handles time-based lifecycle (active/trial/canceled). A new companion `AccountSubscription` model stores tier/seat/billing metadata the package does not track. NEVER use `AccountSubscription.status` as source of truth -- always derive status from the package's `plan_subscriptions` row.
- `Tenant` hasOne `AccountSubscription`; `AccountSubscription` belongsTo `SubscriptionTier`.
- `billing_cycle` lives on `AccountSubscription` (per-tenant), NOT on `SubscriptionTier`.
- Subscription tiers: 3 tiers (Starter/Pro/Enterprise) with identical `feature_flags` (all ON) in v1; differ only by `max_units` and `seat_limit`.
- Plan changes auto-trigger from `Unit` observer when unit count crosses a bracket boundary (event-driven, not nightly job).
- Seat count: compute live from `AccountMembership::count()`, denormalise to `AccountSubscription.seats_used` for cache warmth.
- Seat deactivation on downgrade: set `AccountMembership.deactivated_at` (never delete), notify affected users.

## Leads Domain (from admin batch #297-#299)
- Existing `Lead` model at `app/Models/Lead.php` (table `rf_leads`). Controller: new `app/Http/Controllers/Admin/LeadController.php`.
- Lead status pipeline: New -> Contacted -> Qualified -> Converted/Lost. Stored via `status_id` FK to `Status` model.
- Lead conversion creates Owner or Resident contact records cross-domain via `ConvertLeadToContact` Action class.
- Converted leads: `lead.converted_contact_id`/`_type` polymorphic; excluded from default list views.
- Lead import: polishes existing `documents/LeadsImportErrors.vue`; row limit 500, file limit 5MB, synchronous for <=500 rows.
- Lead activity feed: dedicated `lead_activities` table (not reusable notifications); types: assigned/unassigned/status_change/note.

## Feature Flags Infrastructure (from #301)
- Flags stored in `SubscriptionTier.feature_flags` (JSON, all ON in v1) with per-tenant overrides in `feature_flag_overrides` table.
- Effective flag = tier default OR override if one exists. Resolved via `FeatureFlagService`.
- Audit log: `feature_flag_audit_logs` table (actor_id, flag_key, action, timestamp).
- Must add `@/components/ui/switch/Switch.vue` before engineer implements #301 (shadcn-vue Switch pattern).
- Permission: only `RolesEnum::ACCOUNT_ADMINS` can toggle flags; 403 for others.

## Owner Registration (from #300)
- New model `OwnerRegistration` (tenant-scoped via `BelongsToAccountTenant`) with status (pending/approved/rejected).
- Document types: configurable via `rf_settings` table (type='owner_registration_document_type'), not hardcoded.
- Approval: checks unit conflicts pre-fetch (not at PATCH time); locks units with `lockForUpdate()` in transaction.
- Portal login is NOT auto-created on approval (separate Auth #242 concern).
- Documents via morphMany `Media` on OwnerRegistration; signed URLs with 5-min lifetime.

## Missing UI Components to Add
- `resources/js/components/ui/switch/Switch.vue` — needed for #301 feature flags (shadcn-vue pattern).
- Sheet/Drawer component — referenced by UX flows but not yet in UI lib. Check if `@/components/ui/sheet` exists or needs adding.
- Progress bar — build inline (simple `<div role="progressbar">`) until shadcn needed.

## New Admin Routes Pattern
- All admin routes live under `Route::prefix('admin')->name('admin.')->middleware('admin.manage')`.
- New resources: `admin/leads`, `admin/dashboard`, `admin/subscription` (singular), `admin/owner-registrations`.
- Tenant-scoped vs super-admin: same route, role-based conditional rendering in controller. Check `$user->hasRole(RolesEnum::ACCOUNT_ADMINS->value)`.
- Wayfinder must be regenerated after ALL route changes.
