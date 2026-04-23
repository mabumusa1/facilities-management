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

## Migration rollback tests
- Use `Artisan::call('migrate:rollback', ['--step' => N, '--force' => true])` then re-apply with `Artisan::call('migrate', ['--force' => true])` to restore DB for subsequent tests. Use Schema::hasColumn() to assert before/after.
- Only works reliably when migrations are the last N in the batch — verify step count against actual migration files.
