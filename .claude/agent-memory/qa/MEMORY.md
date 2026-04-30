# QA — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Test conventions for this project
- PHPUnit v12 (NOT Pest). Convert any Pest syntax you find.
- Feature tests live in `tests/Feature/`, unit tests in `tests/Unit/` (rare).
- Create with `php artisan make:test --phpunit <Name>Test --no-interaction`.
- Refresh DB via `RefreshDatabase` or `DatabaseTransactions` trait (match sibling file).
- Faker: `$this->faker` or `fake()` — follow sibling file convention.

## Multi-tenant test setup
- Every feature test touching tenant-scoped data must run inside a tenant scope.
- Use the existing tenant factories / helpers — look in `tests/` base classes first.

## Failure-path checklist (per story)
- Validation failure (422): missing required fields, malformed fields.
- Authorization failure (403): user without permission tries the action.
- Authentication failure (401): unauthenticated request hits the route.
- Not found (404): requesting a non-existent resource.
- Tenant boundary: user from tenant A cannot touch tenant B's data.
- Duplicate / conflict (409): idempotency, unique constraint violations.

## Edge-case checklist (per story)
- Empty input, max-length input, unicode/Arabic input.
- Boundary values (0, -1, int max, past/future dates, leap year).
- Concurrent actions (double-submit, race conditions for scheduled jobs).
- Pagination edges (first page, last page, empty result set).
- Multi-language text renders in both English and Arabic.

## Flaky-test patterns to watch
- Time-dependent tests without `Carbon::setTestNow()`.
- Order-dependent tests that assume DB row order.
- File-system tests that don't clean up storage.

## Past work index
_(append one line per QA report: `PR #N — <AC count / tests added> — <any persistent issue>`)_
- PR #118 — 5 ACs / 5 gap tests added — PermissionSubject has 31 cases (issue says 30); PermissionAction has EXPORT/IMPORT (issue says RESTORE/FORCE_DELETE): both flagged for PM/Tech Lead sign-off.
- PR #119 — 5 ACs / 4 gap tests added — 186 vs 180 permission count discrepancy persists (31 subjects, issue says 30); flagged again for PM sign-off. All 28 tests pass.
- PR #120 — 5 ACs / 17 gap tests added — Inertia 403 handler bug found (renderable not firing for Inertia requests); 2 tests marked skipped pending Engineer fix. All other tests pass.
- PR #121 — 6 ACs / 14 gap tests added — all 26 tests pass. Note: null-null-null model_has_roles row = system-wide (unrestricted), not "no scope". Tenant boundary tested via makeCurrent()/forgetCurrent().
- PR #122 — 4 ACs / 22 gap tests added — 33 total, all pass. Tenant isolation via withSession(['tenant_id']), system roles (account_tenant_id=NULL) correctly 403 on update/delete. Cross-tenant name uniqueness: same name_en/name_ar is allowed across different tenants (scoped unique rule).
- PR #123 — 5 ACs / 16 gap tests added — 24 total, all pass. Note: GET permissions route uses viewAny (no tenant check); only syncPermissions enforces tenant ownership. Empty permissions array is valid (clears all). Preset data accessible via `$response->original->getData()['page']['props']` in Inertia tests.
- PR #124 — 7 ACs / 9 gap tests added — 25 total, all pass. Note: non-manager roles use nullable community_id (no tenant FK check); serviceManager scope deferred (no RolesEnum case yet). Gate::before(accountAdmins) bypasses manage-user-role-assignments check.
- PR #125 — 1 AC (idempotent migration command) / 8 gap tests added — 16 total, all pass. Notes: NULL role path uses unrecognised string (NOT NULL column constraint); large-dataset test uses 210 admins to cross 200-row chunkById boundary; serviceManager scope (community_id/building_id) not implemented in command — deferred, flagged for PM.
- PR #330 — 4 ACs / 25 gap tests added — 40 total, all pass. Note: duplicate-check uses inline $request->validate() on a GET route — requires `Accept: application/json` header to get 422 instead of a redirect; use withHeaders(['Accept' => 'application/json']) for those assertions.
- PR #394 — 4 ACs / 21 gap tests added — 33 total, all pass. Notes: setUp calls actingAs() so unauthenticated tests must call auth()->logout() first; use 'dependents' role (no lease perms) for RBAC-denial tests; after:today rejects today's date (exclusive); generate_addendum is UI-only, server ignores it.

## Inertia request testing (version header)
- Inertia middleware checks `X-Inertia-Version` header against `hash_file('xxh128', public_path('build/manifest.json'))` on every GET request.
- If version mismatches, returns 409 before any controller/auth logic runs.
- For tests: compute version with `file_exists($manifest) ? hash_file('xxh128', $manifest) : ''` and send as `X-Inertia-Version` header.
- If no manifest built in CI, version is `''` and omitting the header is fine.
- Note: `withHeaders(['X-Inertia' => 'true'])` alone WILL get 409 if a built manifest exists.

## Inertia 403 handler bug (PR #120)
- The `renderable(function (AuthorizationException $e, Request $request)` in bootstrap/app.php does NOT intercept AuthorizationException for Inertia requests.
- The exception propagates unhandled to PHPUnit as an error.
- Root cause unknown — may be Laravel 13 / Inertia v3 renderable callback signature issue.
- Engineer must fix before Inertia JSON 403 AC can be verified.

## Migration rollback tests
- Use `Artisan::call('migrate:rollback', ['--step' => N, '--force' => true])` then re-apply with `Artisan::call('migrate', ['--force' => true])` to restore DB for subsequent tests. Use Schema::hasColumn() to assert before/after.
- Only works reliably when migrations are the last N in the batch — verify step count against actual migration files.
