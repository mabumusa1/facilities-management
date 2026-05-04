# Reviewer — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Project conventions to enforce
- PHP 8 constructor property promotion (no empty `__construct()`).
- Explicit return types and type hints on every method.
- Curly braces on every control structure.
- TitleCase for enum keys.
- Prefer PHPDoc blocks over inline comments.
- Descriptive names: `isRegisteredForDiscounts` not `discount()`.

## N+1 hotspots in this codebase
_(append as you find them; common places: listing endpoints that touch Community/Building/Unit hierarchy, Marketplace listings that eager-load Offers/Visits, Service Request lists with User joins.)_

## Security review checklist
- Authorization: every controller action has Policy or middleware gate. Check `app/Policies/` and route definition middleware.
- Validation: every non-GET request uses FormRequest or `$request->validate()`.
- Mass assignment: `$fillable` or `$guarded` set correctly; no `->update($request->all())` without explicit allowlist.
- SQL injection: no raw `DB::raw()` with untrusted input.
- XSS: Vue escapes by default; watch for `v-html` usage.
- CSRF: Inertia handles it; watch for custom form posts that bypass.
- Secrets: nothing in the diff that looks like an API key, token, or password.
- Tenant scope: new queries respect multi-tenancy.

## Frontend review checklist
- Single root element per Vue component.
- Wayfinder route function used, not hardcoded URLs (`/api/foo`).
- Empty, loading, error states present.
- Deferred props have a skeleton.
- Tailwind classes follow existing patterns — don't invent new ones.
- RTL/Arabic not broken (check for hardcoded `left-*`/`right-*` where `inset-inline-*` is safer). Use `ms-*`/`me-*` (margin-start/end) not `ml-*`/`mr-*` for timeline indent and directional spacing.

## Magic number in frontend computed — backend-prop pattern
- Hardcoding a backend-reserved status ID in a Vue computed (e.g. `props.lease.status_id === 76`) creates a silent failure when the seeded ID differs across environments. Pattern to enforce: pass the boolean result (e.g. `isPendingApplication`) as a backend-computed prop from the controller alongside `canApprove`, remove the magic-number computed on the frontend entirely.

## Review tone
- Start with what works. End with what must change.
- Always cite `file:line`.
- Distinguish "must fix" from "nice to have" so the Engineer knows what blocks approval.

## Spatie Permission override pattern
- When overriding `Role::create()` to scope uniqueness by tenant, both the existence check AND the insert must call `withoutGlobalScopes()`. A scoped existence check + unscoped insert is inconsistent and can produce silent FK overrides via `BelongsToAccountTenant`'s `creating` listener.
- When rolling back a Spatie index in `down()`, always restore with its original explicit name (e.g. `roles_name_guard_name_unique`) to survive future Spatie upgrade migrations.

## Multi-tenancy patterns
- `BelongsToAccountTenant` trait adds a global scope AND a `creating` listener. Any `static::query()->create()` call on a model using this trait is affected — use `withoutGlobalScopes()->create()` when the caller is managing the FK explicitly.

## Seeder conventions observed
- `account_tenant_id` on `permissions`/`roles`: use `unsignedBigInteger(...)->nullable()->index()` (no `constrained()`) — this is the established project convention, confirmed across buildings, leases, leads migrations.
- `Str::headline()` is preferred over hand-rolled camelCase→title regex in seeders; both produce the same output but the helper is more maintainable.
- When a new enum case (e.g. `Settings`) produces rows but is intentionally excluded from role presets, require an in-code comment documenting the deliberate omission.

## Spatie + Gate::define interaction
- Spatie registers a `Gate::before` in `PermissionRegistrar::registerPermissions()` that intercepts **every** ability and calls `$user->checkPermissionTo($ability)`. Because Spatie's SP boots before `AppServiceProvider`, any `Gate::define` registered in `AppServiceProvider::boot()` is dead code — Spatie's `before` short-circuits before the defined closure fires.
- A `Gate::define` closure that calls `$user->can($ability)` for the same ability creates an infinite recursion if the Spatie `before` ever returned `null` (e.g., missing permission string). Avoid this pattern entirely.
- For non-model permission subjects, no `Gate::define` is needed — Spatie's `before` hook handles permission string lookups from the DB directly.

## once() in static methods — call-site cache isolation
- `once()` hashes by (file, line, class, function, spl_object_hash of captured objects). In a static method called from two locations, the line differs → two separate cache entries → DB query runs twice. Use a `static $cache` array keyed on `$user->id` inside the static method instead, so all call sites share one entry per user per request.

## Default-true match fallback — silent privilege escalation pattern
- `match (get_class($model)) { ... default => true }` in policy helpers is dangerous: any new model not listed gets a free pass. Default should be `false`; a test failure is safer than a silent grant.

## Spatie removeRole + surrogate PK on model_has_roles
- Spatie's `removeRole()` / `syncRoles()` issue `DELETE WHERE role_id = ? AND model_id = ? AND model_type = ?` with no scope FK conditions. After dropping the composite PK in favour of a surrogate BIGSERIAL PK, this deletes ALL scoped rows for the role/user pair. Must document at migration level and enforce via raw DB calls or a User model `removeRole` override that scopes the delete to the FK tuple.

## Scope helper early-return true on empty arrays — scope leakage
- Any private policy/scope helper that guards with `if (empty($communityIds) && empty($buildingIds)) { return true; }` silently grants full access to users who only have service-type-scoped rows (no community or building). Return `false` instead. This must be checked for *every* helper in the file, not just the first one that was flagged.

## viewAny vs view policy — cross-tenant GET disclosure pattern
- `viewAny(User $user)` takes no model instance — it cannot call `belongsToCurrentTenant`. Using it on a GET endpoint that accepts a route-bound model allows cross-tenant reads when the global scope does not restrict binding (e.g. Tenant::current() is null in test context or scoping is bypassed).
- For GET endpoints that load a tenant-scoped model: always use a `view(User $user, Model $model)` policy method so `belongsToCurrentTenant` can be checked. Allow system/null-tenant models explicitly if the UX calls for read-only access to them.
- Test that documents "200 OK for cross-tenant GET" is documenting a bug, not acceptable behavior. Assert `assertForbidden()` or `assertNotFound()` instead.

## Success toast missing after async save — silent success anti-pattern
- When saving via `fetch()` instead of Inertia `useForm`, there is no automatic flash/toast. Always explicitly call the app's toast composable after the success branch. Defining i18n keys without using them is a sign the toast was forgotten.

## whereColumn tautology — cross-tenant user disclosure in dropdowns
- `->whereHas('relation', fn($q) => $q->whereColumn('col', 'col'))` compares a column to itself and is always true — equivalent to no filter at all. Returns all rows platform-wide. Always check `whereColumn` calls: both sides must reference *different* tables/aliases. For tenant-scoped user dropdowns, use `->where('account_tenant_id', Tenant::current()?->id)`.
- Pair this with a tenant-scoped `Rule::exists` in the FormRequest: `Rule::exists('account_memberships', 'user_id')->where('account_tenant_id', Tenant::current()?->id)`. Without it, a malicious POST with a cross-tenant ID bypasses the dropdown and is persisted.

## BookingConflictService / lockForUpdate pattern
- Any service that checks for row-level conflicts (overlap, uniqueness) inside a `DB::transaction` must call `->lockForUpdate()` before `->first()`. Without it, two concurrent requests both read zero conflicts and both insert. Canonical pattern: `ResidentFacilityController.php:193`.
- When a FormRequest is scaffolded with `authorize(): false` and empty `rules()` but never wired into the controller, delete it or wire it. It hard-blocks any accidental caller.

## Cross-tenant exists validation for FK fields
- `'exists:table,id'` does NOT scope to the current tenant. Any `facility_id`, `community_id`, etc. passed by the client must use `Rule::exists('table', 'id')->where('account_tenant_id', Tenant::current()?->id)` to prevent cross-tenant FK injection.

## Wayfinder regen mandatory on new controllers
- After adding a new controller with routes, `php artisan wayfinder:generate` must be run and the generated `ControllerName.ts` + updated `index.ts` committed in the same PR. Importing from a missing Wayfinder file causes a build-time crash.

## Route shadowing — named-route tests masking registration-order defects
- `route('named.route')` resolves by name (bypasses registration order). Real HTTP requests follow registration order. If `Route::resource('foo', Controller::class)` is declared before `Route::get('/foo/bar', ...)`, then `/foo/bar` is matched by the resource's `{foo}` wildcard (show action) and the explicit route is dead.
- Always register sub-resource / calendar / custom routes *before* the parent `Route::resource()` in `routes/web.php`. Named-route tests will pass either way — always verify ordering by reading the route file, not the test output.

## Past review index
- PR #118 — approved (re-review) — all three prior concerns resolved; `bootBelongsToAccountTenant()` override pattern (tenant OR NULL scope) confirmed correct; nice-to-have: `Tenant::forgetCurrent()` should be in `finally` block in scope tests
- PR #119 — comment (not approved) — no code defects; two pre-merge gates: (1) PM/TL on-record sign-off on 186 vs 180 permission count, (2) comment in seeder documenting `Settings` subject intent for `admins` role
- PR #120 — approved (re-review after 9b85a17) — all 4 must-fixes verified; nice-to-have: `rentalContractTypes()` and `paymentSchedules()` (LeaseController lines 680/699) are routed but unprotected read-only endpoints; not blocking
- PR #121 — approved (Round 3) — both Round 2 blockers resolved: (1) all 6 helpers already had `return false` on empty arrays (reviewer had stale read in Round 2); (2) collectRoles() replaced with getStoredRole() chain. New test `test_service_type_only_manager_cannot_access_spatial_subjects` verifies blocker 1. Tenant::forgetCurrent() in finally blocks added. 30 tests green, Pint clean. Ready to merge.
- PR #122 — approved (Round 2) — both must-fixes resolved: (1) all 4 action buttons have scoped aria-labels via ariaEditRole/ariaDeleteRole i18n keys; (2) belongsToCurrentTenant() returns false for null tenant_id. 33 tests green. Latent cross-tenant risk now mitigated at trait level.
- PR #123 — approved (Round 2) — both blockers resolved: (1) RolePolicy::view() added with belongsToCurrentTenant, system roles exempt; (2) toast.success() wired in Permissions.vue save() success branch. 24 tests green. Nice-to-haves: subtitle shows same name twice (cosmetic); buildPresets() duplicates RbacSeeder subject lists (drift risk).
- PR #124 — approved (Round 2) — both blockers resolved: (1) Rule::prohibitedIf(true) on all 3 scope fields for scopeLevel 'none'; test inverted to assertSessionHasErrors; (2) cancelConfirm added to EN+AR, fallback removed. 25 tests green. Latent: Gate::define for 'manage-user-role-assignments' works correctly given current role set but interacts subtly with Spatie Gate::before; formatDate uses 'en-GB' hardcoded (cosmetic); /dashboard breadcrumb hardcoded URL.
- PR #125 — comment (Round 2, not yet approved) — both Round 1 must-fixes resolved: (1) dead $morphAlias removed; (2) lines 247 and 295 have explicit null scope columns. Missed: line 125 insert still omits the three null columns — not a runtime defect (columns are nullable with no DB default) but inconsistent. Flagged as nice-to-have; human to decide land-as-is or patch first. 16 tests green, Pint clean.
- PR #330 — approved (Round 1) — zero must-fixes; 4 nice-to-haves: (1) `nationality_id` in useForm but no UI selector (AC gap vs UX spec Screen 2, nullable so no security impact); (2) `form.post('/residents')` hardcoded URL matches sibling convention; (3) DataTable has no `dir` prop on Column — AR column lacks dir=rtl; (4) `createdToast` i18n key dead code (server uses PHP __() + Inertia::flash). 40 tests green, Pint clean.
- PR #375 — comment/LGTM (Round 2, self-authored so GitHub blocked formal approve) — both blockers resolved: (1) assignee dropdown JOIN replaces whereColumn tautology, fails safe when tenant is null (empty list); (2) AssignServiceRequestRequest scopes exists check to account_memberships with tenant FK. 6 tests green, Pint clean. Nice-to-haves: distinct() on assignees query; STATUS_ASSIGNED magic ID; triage policy has no model instance (relies on global scope). Ready for docs chain.
- PR #376 — comment/request-changes (Round 1, self-authored) — 6 must-fixes: (1) Wayfinder not regenerated; (2) FacilityCalendarBookingRequest has authorize():false + empty rules(), never wired; (3) lockForUpdate() missing — race condition; (4) facility_id exists check not tenant-scoped; (5) app.common.optional i18n key missing; (6) no cross-tenant show() forbidden test.
- PR #376 — comment/request-changes (Round 2, self-authored) — all 6 original blockers resolved in commit 6877791. New blocker: calendar routes declared AFTER Route::resource('facilities',...) — `/facilities/calendar` shadowed by resource show wildcard. Nice-to-haves: bookings() inline facility_id validation not tenant-scoped; resident_id exists not tenant-scoped.
- PR #377 — comment (Round 1, self-authored — GitHub blocked formal request-changes) — 3 must-fixes: (1) 14 `app.leases.approval.*` i18n keys absent from both appEnFallback.ts and appArFallback.ts — useI18n:159 returns raw key string, approval panel renders key paths in production; (2) magic number `76` in `isPendingApplication` computed — pass as backend boolean prop from LeaseController::show(); (3) no cross-tenant isolation test for approve/reject — privileged admin from tenant B attacking tenant A lease untested. Nice-to-haves: ml-1/ml-6 RTL-unsafe → ms-1/ms-6; no idempotency test (ensureTransition handles it but untested); pre-existing hardcoded delete URL.
- PR #377 — comment/LGTM (Round 2, self-authored — GitHub blocked formal approve) — all 3 blockers resolved in 8dd3a49: (1) all 14 approval i18n keys present in EN+AR, Arabic translations idiomatic; (2) isPendingApplication moved to backend prop via ExpireLeaseQuotes constant, no magic number remains; (3) two cross-tenant tests cover both approve and reject. RTL ms-* fixes on 3 sites. Ready for docs chain.

## HasContactInfo::initializeHasContactInfo() — mergeFillable pattern
- `HasContactInfo` uses `initializeHasContactInfo()` (not `bootHasContactInfo()`) to call `$this->mergeFillable([...])` at instantiation time. This means fields like `first_name`, `phone_number`, `national_phone_number`, `id_type`, etc., are NOT listed in the model's own `$fillable` array but are still mass-assignable. Always check for `initializeHas*` traits before reporting missing `$fillable` entries.

## Dead i18n key risk when server and JS use separate translation mechanisms
- `Inertia::flash('toast', ['message' => __('...')])` dispatches a PHP-side translated string. A matching Vue-side `t('...')` key in `appEn`/`appAr` is dead code — they never combine. If you see an i18n key defined but no `t()` call that uses it, check whether the message is delivered by the server via flash instead. Flag as dead code / drift risk, not as a missing translation.

## prohibitedIf missing for conditional scope fields — silent FK injection pattern
- When a FormRequest conditionally requires scope fields (community_id, building_id, service_type_id) only for certain roles (manager/serviceManager), non-manager roles should use `Rule::prohibitedIf($scopeLevel === 'none')` rather than bare `['nullable', 'integer']`. Without it, any integer — including cross-tenant IDs — is accepted and stored with no tenant validation.
- A test that asserts `assertRedirect` while the docblock/comment says "is blocked" or "is rejected" is documenting a bug as AC. Flag and invert.
- i18n keys used with `|| 'fallback'` in `t()` calls mask missing translations: Arabic users always see the English fallback. Every key used in a `t()` call must exist in both `appEn` and `appAr`.

## Private trait method access — fatal Error pattern
- Spatie's HasRoles::collectRoles() is declared `private`. Calling it from a User model override via `$this->collectRoles()` will throw `Error: Call to private method` at runtime — PHP does not expose private trait members to the using class. Use `$this->getStoredRole($arg)->getKey()` (public) instead.

## Cross-tenant mutation routes — no User global scope
- The `User` model has no `BelongsToAccountTenant` global scope. Implicit route binding for `{user}` in admin routes returns any user platform-wide. The `admin.manage` middleware only validates the *actor*'s role, not whether the *target* belongs to the actor's tenant. Every new mutation action on a user route parameter must explicitly verify `AccountMembership::where('user_id', $user->id)->where('account_tenant_id', Tenant::current()?->id)->exists()`. Canonical authorized check: `AppServiceProvider.php:69` (`manage-user-role-assignments` Gate).

## Wayfinder dev-mode auto-regen masks missing exports
- The Wayfinder Vite plugin regenerates `resources/js/routes/**` on-the-fly during `yarn run dev`. Imports of non-existent named exports (e.g. `deactivate` from `@/routes/admin/users`) resolve at dev time but cause a module-not-found crash on `yarn run build`. Always verify committed Wayfinder files contain the new exports before approving.

## Past review index (continued)
- PR #389 — comment/LGTM (Round 2, self-authored — GitHub blocked formal approve) — all 4 blockers resolved in a370b97: (1) Wayfinder regen — all 7 exports present in admin/users + set-password; (2) cross-tenant 403 guard on all 5 mutation methods + test asserting 403 + status unchanged; (3) store.url → show.url fixed at both call sites; (4) alertdialog confirm in both Index.vue and Show.vue, 3 i18n keys in EN+AR. 3 of 4 nice-to-haves addressed. 79 tests green. Open nice-to-have: lang attr hardcodes locale. Ready for docs chain.

- PR #403 — comment/request-changes (Round 1, self-authored — GitHub blocked formal review) — 2 blockers:
  (1) MoveOutPolicy::finalize() missing `$moveOut->status_id !== MoveOutStatus::COMPLETED` guard — allows re-finalizing completed move-outs creating duplicate transactions. QA test `test_finalize_returns_403_for_already_settled_move_out` explicitly documents this with assertStatus(302) instead of 403.
  (2) Transaction `assignee_id` set to `account_tenant_id` (spatie tenant) instead of `$lease->tenant_id` (Resident FK). Variable `$tenantId` misleadingly holds account_tenant_id. Existing tests don't assert assignee_id. Fix: use `$lease->tenant_id`.
  3 nice-to-haves: voiding uses `is_paid = true` instead of status transition per Tech Lead design; float precision inconsistent between settlement() and finalize() (round() used in one but not the other); Wayfinder files gitignored — verify build pipeline runs wayfinder:generate.
  51 tests green, Pint clean. Wayfinder exports present on local disk (auto-regen by Vite plugin) but not committed (gitignored). Route ordering correct: move-out sub-routes declared before `leases/{lease}` wildcard.

- PR #403 — comment/request-changes (Round 2, self-authored — GitHub blocked formal review) — 1 of 2 blockers fixed:
  ✅ Blocker 2 (assignee_id): `$lease->tenant_id` correct at MoveOutController.php:445.
  ❌ Blocker 1 (MoveOutPolicy guard): PARTIALLY fixed — guard added but `use App\Support\MoveOutStatus;` import MISSING at MoveOutPolicy.php:52 → fatal Error on every finalize call, 12 tests fail. Sub-issue: test_finalize_returns_403_for_already_settled_move_out still asserts 302 (must be 403) + stale comment must be removed.
  10 tests pass (GET endpoints), 12 fail (all finalize POSTs — same root cause). 3 Round 1 nice-to-haves still open: voiding is_paid pattern, float precision, Wayfinder gitignored.

- PR #403 — comment/LGTM (Round 3 / final, self-authored — GitHub blocked formal approve) — all 4 must-fixes resolved:
  ✅ Blocker 1 (MoveOutPolicy guard): `use App\Support\MoveOutStatus;` import present at line 10, guard at line 53.
  ✅ Blocker 2 (assignee_id): `$lease->tenant_id` at MoveOutController.php:445, used at lines 456 and 468.
  ✅ Blocker 3 (test assertion): `test_finalize_returns_403_for_already_settled_move_out` asserts 403 at MoveOutTest.php:1278, stale comment removed.
  ✅ Blocker 4 (Round 1 carryover): assignee_id confirmed.
  51 tests green (226 assertions), Pint clean. 12 x 403 assertions covering permission, cross-tenant, already-settled. 3 nice-to-haves carried forward (voiding is_paid pattern, float precision, Wayfinder gitignored/auto-regen). Route ordering correct: settlement/finalize/statement sub-routes at lines 485-487 before lease wildcard at 489. Handed off to Docs.
