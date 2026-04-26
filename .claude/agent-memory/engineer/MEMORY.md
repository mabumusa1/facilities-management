# Engineer — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Command shortcuts for this project
- Run tests: `php artisan test --compact tests/Feature/<Name>Test.php`
- Full suite: `php artisan test --compact`
- Format: `vendor/bin/pint --dirty --format agent` (NOT `--test --format agent`)
- Wayfinder regen: `php artisan wayfinder:generate`
- Frontend dev: `yarn run dev` or `composer run dev` (bundles server+queue+logs+vite)
- Frontend build: `yarn run build`
- Full CI check: `composer run ci:check`

## Project quirks to remember
- Base branch is `1.x` (not `main`). All PRs `--base 1.x`.
- Fortify handles auth routes — don't hand-roll login/register logic.
- Multi-tenancy via Spatie — always verify queries are tenant-scoped.
- Permissions via spatie/laravel-permission — check policies before adding raw auth logic.
- Inertia v3: `Inertia::optional()` replaces `Inertia::lazy()` / `LazyProp`.
- Axios is REMOVED in Inertia v3 — use built-in XHR client or add Axios separately.
- Laravel Boost MCP is available: prefer `database-query` over `tinker`, `database-schema` before migrations, `search-docs` before unfamiliar API usage.

## Factory conventions
- Check existing factories in `database/factories/` for custom states (e.g., `->published()`, `->archived()`) before manually setting fields.
- Use `fake()` helper (or `$this->faker` — follow sibling test file convention).

## Git rules (strict)
- Never `--no-verify`, never `--amend` published commits, never `git push --force` to shared branches.
- Never commit secrets (`.env`, API keys).
- Prefer `git add <specific files>` over `git add -A` to avoid accidental secret commits.

## Common PR pitfalls
- Forgetting to regen Wayfinder after route change → TS errors in frontend.
- Forgetting to add Pint run → PR fails `lint:check`.
- Missing tenant scope on new query → cross-tenant data leak.
- Single-root rule violated in new Vue component.
- Vue component with deferred prop but no skeleton empty state.

## Multi-tenant test setup pattern
For HTTP feature tests hitting tenant-scoped routes, always:
1. `Tenant::create(['name' => '...'])` in setUp
2. Use `->withSession(['tenant_id' => $tenant->id])` on each request
3. Without the session, `NeedsTenant` middleware returns 302→login, masking the real assertion
Reference: `tests/Feature/DashboardTest.php::authenticateUserWithTenant()`

## Inertia version negotiation in tests
Sending `X-Inertia: true` without a matching `X-Inertia-Version` causes a 409 (version mismatch) instead of passing through to the controller. Without a built manifest, server version is `null`; `'' !== null` triggers 409. Use `withoutMiddleware(HandleInertiaRequests::class)` only when the goal is to test a renderable, not Inertia behavior itself.

## Worktree test isolation
Tests must be run from `/var/www/html` (main repo with vendor/) not the worktree (no vendor/). Pass the absolute path to the test file: `php artisan test --compact /var/www/html/.claude/worktrees/<id>/tests/Feature/...`. The worktree has no vendor/ so artisan there will fail.

## Inertia Form component — event vs options props
`<Form>` in Inertia v3 exposes callbacks via explicit props (`onStart`, `onSuccess`, `onError` etc.) AND a catch-all `:options` prop (`...props.options` → submitted as request options). `onHttpException` is NOT a direct prop — it goes via `:options="{ onHttpException: handler }"`. The response passed to `onHttpException(response)` is `{ status, data, headers }` where `headers` is a plain lowercased object — use `response.headers['retry-after']` NOT `.get('retry-after')`. For throttle detection: use `:options="{ onHttpException: handler }"` + `:on-start="resetHandler"` to drive an `isThrottled` ref; read `Retry-After` header for countdown. The throttle i18n key `app.auth.login.throttled` uses `{{seconds}}` interpolation.

## Past work index
_(append one line per PR: `PR #N — <branch> — <key learning or gotcha>`)_
- PR #120 — feat/permission-enforcement-112 — multi-tenant test needs withSession(['tenant_id']), Inertia version mismatch causes 409 in tests
- PR #120 bugfix — AuthorizationException is converted to AccessDeniedHttpException before renderable callbacks fire; register renderable on the Symfony exception type. Use X-Locale header (not cookie, not App::setLocale) to set locale in feature tests because SetLocale middleware reads it reliably pre-exception.
- PR #121 — feat/manager-scope-113 — model_has_roles has a composite PK (role_id, model_id, model_type); to allow multi-scope rows add a BIGSERIAL surrogate PK + functional COALESCE unique index. once() memoises scope resolution per request; only apply forManager() in HTTP controllers, not jobs/seeders. Pint flags `! $x` as needing space after `!` — use `! $expr` pattern.
- PR #122 — feat/roles-list-ui-114 — DO NOT send X-Inertia:true header in tests without a matching version — triggers 409. Just use plain get() with no Inertia headers; assertInertia() works on full HTML responses. Inertia assertInertia()->where('prop.data', fn) receives a Collection not array — remove array type hint and use collect($data)->toArray(). Wayfinder routes dir is gitignored (/resources/js/routes) — must run `php artisan wayfinder:generate` post-deploy, not committed.
- PR #123 — feat/permission-matrix-ui-115 — Use `withoutVite()` in setUp when testing new Inertia pages not yet in public/build/manifest.json (avoids ViteException 500). RolesSeeder uses PermissionsSeeder (old subjects list, no 'units'); RbacSeeder uses PermissionSubject enum (31 new subjects). Pick seeder to match which permissions you need in tests. Empty array sync needs `present` not `required` validation. PHP array destructure: `[, , $third]` not `[$, $, $third]`.
- PR #124 — feat/role-assignment-ui-116 — `model_has_roles` table has NO `created_at`/`updated_at` columns — do not include them in DB::table(...)->insert(). Gate::define receives two User args when authorizing against a target model. `syncRoles()` throws LogicException if scoped rows exist; fix by calling `removeScopedRole($name, null, null, null)` for null-scope rows then `assignRole()`. No Popover UI component — use inline row expansion for confirmations. Community FK on Building is `rf_community_id` not `community_id`.
- PR #325 (review fix) — feat/auth-login-gap-closing-235 — Named 'login' rate limiter returns bare 429 (no session errors); frontend detects via `:options="{ onHttpException }"` not errors.email substring. Test asserts `assertHeader('Retry-After')` + `assertSessionDoesntHaveErrors('email')` instead of session error substring check. Use `assertSessionDoesntHaveErrors` (not `assertSessionMissingErrors` which doesn't exist).
- PR #355 — service-requests/resident-create-request-#210 — Check `php artisan db:table <table>` BEFORE writing migrations: if a prior story already added a column (service_category_id from #209), re-adding it triggers duplicate column error. Use `viewOwn/createOwn/viewAnyOwn` naming for resident-scoped policy abilities. Backend `__('key')` uses PHP lang files, not TS messages.ts — use string literals for abort() messages. Unit factory FK is `rf_community_id` not `community_id`.
- PR #354 (review fix) — facilities/resident-booking-ui-248 — Race guard: lockForUpdate on booking rows fails for zero-row case; lock FacilityAvailabilityRule parent row instead (always exists). FacilityBookingStatus constants in app/Support/ with IDs matching StatusSeeder (PENDING_APPROVAL=19, BOOKED=20, CANCELLED=22). Tests that create Status via factory must set explicit `id` matching the constant to be counted by activeIds(). Wayfinder regen from worktree: symlink vendor+node_modules, then run from worktree dir — routes only in worktree won't appear in main-app regen.
