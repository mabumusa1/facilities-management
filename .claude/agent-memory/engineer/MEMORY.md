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

## Past work index
_(append one line per PR: `PR #N — <branch> — <key learning or gotcha>`)_
- PR #120 — feat/permission-enforcement-112 — multi-tenant test needs withSession(['tenant_id']), Inertia version mismatch causes 409 in tests
- PR #120 bugfix — AuthorizationException is converted to AccessDeniedHttpException before renderable callbacks fire; register renderable on the Symfony exception type. Use X-Locale header (not cookie, not App::setLocale) to set locale in feature tests because SetLocale middleware reads it reliably pre-exception.
