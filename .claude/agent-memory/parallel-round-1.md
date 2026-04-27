# Parallel PR Round 1 — Claude Code + opencode (DeepSeek)

> **opencode**: jump to **[Your Task — Issue #301](#prompt--opencode--deepseek-issue-301)**.
> **Claude Code**: jump to **[Your Task — Issue #184](#prompt--claude-code-issue-184)**.

## Context

18 stories tagged `state:ready-for-impl`, zero open PRs. The council documents a 2-instance area split in [`engineer/parallel-plan.md`](./engineer/parallel-plan.md) (opencode owns auth/admin/properties/settings/marketplace; Claude Code owns leasing/accounting/SR/facilities/visitor-access/reports), but no parallel work is in flight.

This is a **shakedown of the protocol** with one issue per agent. Each agent runs the full council chain (engineer → QA → review → docs → merge) inside its own worktree and its own context window. After Round 1 we regroup and decide whether to scale up.

## Round-1 Lane Assignments

| Agent | Issue | Title | Branch | Worktree |
|---|---|---|---|---|
| **Claude Code** | **#184** | Leasing — Lease pipeline view, expiry alerts, bulk export | `leasing/lease-pipeline-#184` | `.claude/worktrees/cc-184/` |
| **opencode (DeepSeek)** | **#301** | Admin — Platform feature flags (super-admin per-tenant) | `admin/feature-flags-#301` | `.claude/worktrees/oc-301/` |

**Why these two:** both have UX flow + Tech Lead design comments, both P1, both already labelled `agent:engineer`. **Zero file overlap** — #184 touches `app/Http/Controllers/Leasing/` + `resources/js/pages/leasing/pipeline/`; #301 touches `app/Http/Controllers/Admin/` + `resources/js/pages/admin/feature-flags/` + `SubscriptionTier.feature_flags` JSON column.

(Rejected #296 Leads list for opencode because its Tech Design routes the controller through `Leasing/` namespace — would have collided with Claude Code's #184.)

## Pre-flight

Already done in the planning session:
- Stale `cc-173`/`cc-249` directories removed
- `1.x` branch refreshed

## Conflict-avoidance protocol

| Concern | Files | Rule |
|---|---|---|
| i18n keys | `lang/en/app.php`, `lang/ar/app.php`, `resources/js/lib/i18n/appEnFallback.ts`, `appArFallback.ts` | Append-only. Each agent adds keys under its own namespace (`leasing.*` vs `admin.featureFlags.*`). Never touch a sibling key. |
| RBAC seeders | `database/seeders/RbacSeeder.php`, `app/Support/PermissionSubject.php` | Each agent edits only the line for its own subject. |
| Migrations | `database/migrations/*` | Use `Schema::hasColumn()` defensively (per memory note from #355). |
| `CHANGELOG.md` + `docs/changelog.md` | repo-root + docs site | Each agent appends its own `[Unreleased]` entry. **Both files MUST be grep-checked for `<<<<<<<` before push** (per memory rule from #357/#358 hotfixes). |

Zero-overlap surfaces (no coordination needed): controllers, Vue pages, models, feature tests, domain folders.

## Critical files to consult

- `.claude/agent-memory/engineer/parallel-plan.md` — area split + claim protocol
- `.claude/agent-memory/engineer/MEMORY.md` — gotchas index (worktree vendor symlink, Wayfinder regen, race-lock parent row, route ordering, etc.)
- `docs/council/labels.md` — state-label transitions
- `.claude/commands/eng-implement.md` — engineer chain step
- Tech Lead design comments: `gh issue view 184 --comments` and `gh issue view 301 --comments`

---

## Prompt — Claude Code (Issue #184)

Run this in a **fresh Claude Code session** (not the planning session). Claude Code has access to the council subagents.

```
Implement issue #184 (Leasing — Lease pipeline view, expiry alerts, bulk export) end-to-end through the council chain. Work in a dedicated worktree at .claude/worktrees/cc-184 on branch leasing/lease-pipeline-#184 from base 1.x.

Before starting (after worktree create):
- ln -sf /var/www/html/vendor .claude/worktrees/cc-184/vendor
- ln -sf /var/www/html/node_modules .claude/worktrees/cc-184/node_modules

Chain steps (each as a fresh subagent invocation):
1. engineer subagent — /eng-implement 184. Tell it the worktree path. Implement per Tech Lead design comment on #184 exactly (LeasePipelineController, leasing/pipeline/Index.vue, expiry alerts, bulk export).
2. qa subagent — /qa-test <pr#>. AC-mapped failure paths + edge cases.
3. reviewer subagent — /review <pr#>. If changes requested, loop back to engineer with the inline comments, then re-run reviewer.
4. docs subagent — /docs-feature 184. Writes docs/guides/leasing/lease-pipeline.md + Arabic translation + CHANGELOG entries on the PR branch.
5. Merge — per project memory rule (auto-merge approved council chains): once CI green, gh pr merge <pr#> --squash --delete-branch. Verify #184 auto-closes.
6. Cleanup — git worktree remove .claude/worktrees/cc-184 --force && git worktree prune.

Hard rules:
- Base branch is 1.x.
- Run vendor/bin/pint --dirty --format agent before every commit, from /var/www/html.
- Run tests from /var/www/html with absolute path to worktree test files (worktree has no real vendor/, just a symlink).
- Run php artisan wayfinder:generate inside the worktree after any controller/route change.
- Never --no-verify, --amend a published commit, or --force push.
- Stage specific files (never git add -A).
- BEFORE pushing the docs commit, grep CHANGELOG.md AND docs/changelog.md for "<<<<<<<" — abort if any markers remain.
- If Tech Lead design is missing/unclear, stop and report. Do not improvise.

Report back with: PR URL, merge commit SHA, files touched, CI status.
```

---

## Prompt — opencode / DeepSeek (Issue #301)

This is **your task**, opencode. Run the steps below verbatim. You do not have access to the council's slash commands or subagents — the chain is inlined.

````
You are working on issue #301 (Admin — Platform feature flags, super-admin per-tenant) in the Laravel monorepo at /var/www/html. Take it end-to-end through engineer → self-QA → self-review → docs → merge in a dedicated git worktree. All PRs target base branch 1.x (NOT main).

FIRST — read the Tech Lead design comment on issue #301:
  gh issue view 301 --comments
The design puts feature flags on the SubscriptionTier.feature_flags JSON column and renders a super-admin-only Feature Flags tab on the Tenant detail page (Vue pages under resources/js/pages/admin/feature-flags/). Implementation must match.

REPO FACTS YOU NEED
- PHP 8.5, Laravel 13, Inertia v3 (Vue 3), Tailwind v4, PHPUnit 12.
- Base branch: 1.x (all PRs --base 1.x).
- Format: vendor/bin/pint --dirty --format agent (NOT --test). Run before every commit, from /var/www/html.
- Tests: php artisan test --compact <path>. Run from /var/www/html, not the worktree (worktree has no vendor/, just a symlink).
- Wayfinder: php artisan wayfinder:generate from inside the worktree after any route/controller change.
- Multi-tenant: feature flags are super-admin scoped → the policy must allow only super-admins. Verify queries are tenant-scoped where applicable.
- Permissions: spatie/laravel-permission + PermissionSubject enum (app/Support/PermissionSubject.php).

STEP 1 — Claim the issue
  gh issue view 301 --json labels   # confirm state:ready-for-impl + agent:engineer
  gh issue edit 301 --remove-label "state:ready-for-impl" --add-label "state:in-progress" --assignee @me

STEP 2 — Set up worktree
  git checkout 1.x && git pull --ff-only
  git worktree add -b admin/feature-flags-#301 .claude/worktrees/oc-301
  ln -sf /var/www/html/vendor .claude/worktrees/oc-301/vendor
  ln -sf /var/www/html/node_modules .claude/worktrees/oc-301/node_modules

STEP 3 — Implement (engineer role)
Working dir for edits: .claude/worktrees/oc-301
- Controller, FormRequest, Policy, routes, Vue pages — exactly per Tech Lead design on #301.
- At least one happy-path feature test under tests/Feature/Admin/.
- Format:    vendor/bin/pint --dirty --format agent           (from /var/www/html)
- Test:      php artisan test --compact /var/www/html/.claude/worktrees/oc-301/tests/Feature/Admin/FeatureFlag*Test.php   (from /var/www/html)
- Wayfinder: cd .claude/worktrees/oc-301 && php artisan wayfinder:generate

STEP 4 — Open PR
  cd .claude/worktrees/oc-301
  git add <specific paths only — NEVER git add -A>
  git commit -m "feat(admin): platform feature flags — super-admin per-tenant (#301)"
  git push -u origin admin/feature-flags-#301
  gh pr create --base 1.x \
      --title "feat(admin): platform feature flags (#301)" \
      --body "Closes #301" \
      --label "state:in-review,agent:engineer"
  gh issue edit 301 --remove-label "state:in-progress" --add-label "state:in-review"

STEP 5 — Self-QA (failure paths + edge cases)
Add tests covering at minimum:
- non-super-admin denied (403)
- toggle persists to SubscriptionTier.feature_flags JSON
- audit log entry created on toggle
- invalid feature key rejected (422)
- tenant not found (404)
- enable→disable→enable round-trip preserves history if the design specifies it
Commit + push. Post a PR comment with an AC-by-AC pass/fail table.

STEP 6 — Self-review
  vendor/bin/pint --test --format agent          # verify clean
Read the full diff and check for:
- N+1 queries (use Laravel Boost MCP `database-query` if available)
- Missing tenant scope on any new query
- Hard-coded strings that should be in lang files / appEnFallback.ts / appArFallback.ts
- Missing return types or PHPDoc on public methods
- Routes registered AFTER any Route::resource() that could swallow them (per memory note from #376)
Fix issues as new commits (do not amend). Post a self-review summary comment.

STEP 7 — Docs (MANDATORY chain step)
On the same PR branch, create/update:
- docs/guides/admin/feature-flags.md            (English user guide)
- docs/ar/guides/admin/feature-flags.md         (Arabic translation, RTL)
- CHANGELOG.md                                  (append under [Unreleased])
- docs/changelog.md                             (append matching entry)

CRITICAL — before staging the docs commit:
  grep -n "<<<<<<<" CHANGELOG.md docs/changelog.md
If anything matches, stop and resolve markers fully — do NOT push partial resolutions (this has bitten us before, see PR #357/#358).

  git commit -m "docs(admin): feature flags user guide + changelog (#301)"
  git push
  gh issue edit 301 --remove-label "state:in-review" --add-label "state:ready-to-merge,agent:docs"

STEP 8 — Merge
  # wait for CI green
  gh pr merge <pr#> --squash --delete-branch
  # verify #301 auto-closes (requires "Closes #301" in PR body)

STEP 9 — Cleanup
  cd /var/www/html
  git worktree remove .claude/worktrees/oc-301 --force
  git worktree prune

REPORT BACK
PR URL, merge commit SHA, list of files touched, CI conclusion, any blockers hit.

HARD RULES
- NEVER --no-verify, NEVER --amend a published commit, NEVER --force push.
- NEVER commit secrets (.env, credentials).
- Stage specific files only (no git add -A or git add .).
- If the Tech Lead design comment is missing or contradicts the issue body — STOP and ask.
- If a shared file (lang, seeder) shows a merge conflict — STOP and ask, do not silently overwrite.
- If grep finds <<<<<<< markers in CHANGELOG.md or docs/changelog.md — STOP and resolve before push.
````

---

## Verification (run from any monitor session)

```bash
gh pr list --state open --json number,title,headRefName,mergeable
gh issue view 184 --json labels --jq '[.labels[].name]'
gh issue view 301 --json labels --jq '[.labels[].name]'
git worktree list
gh run list --limit 5    # CI status
```

**Round 1 is done when all of these are true:**
- Both PRs merged to `1.x`
- Both issues auto-closed and carry `state:done`
- `git worktree list` shows only `/var/www/html` (no `.claude/worktrees/*` entries)
- `grep "<<<<<<<" CHANGELOG.md docs/changelog.md` returns nothing

## After Round 1 — regroup before scaling

Decide before launching Round 2:
- Did the worktree + symlink + run-from-main-repo test pattern hold for both agents?
- Did opencode handle the docs step + Arabic translation correctly? (Riskiest step.)
- Did either agent silently bypass a hard rule? (Check `git log` for amends, force-pushes, --no-verify commits.)

Round 2 candidates (already-validated lane split, no fresh design needed):
- Claude Code → #178 (Amend lease terms) or #181 (Move-out workflow)
- opencode → #239 (Auth — Session management) or #242 (Admin user management)
