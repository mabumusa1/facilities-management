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
- RTL/Arabic not broken (check for hardcoded `left-*`/`right-*` where `inset-inline-*` is safer).

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
