# AGENTS.md

## Architecture

- Laravel 13 monolith with Inertia 3 + Vue 3 SPA (no Blade views except auth)
- **Multi-tenant**: spatie/laravel-multitenancy via `X-Tenant` request header (not domain-based). Models extend/implement the spatie tenant contract.
- **Auth**: Laravel Fortify with 2FA, email verification, password reset. Use `config/fortify.php` to check enabled features.
- **RBAC**: spatie/laravel-permission with custom `Permission` and `Role` models.
- **Frontend**: Tailwind CSS v4, vue-sonner (toasts), reka-ui (headless components), lucide-vue-next (icons), RTL/Arabic support.

## Key directories

| Path | Purpose |
|------|---------|
| `resources/js/pages/` | Inertia page components (auto-routed) |
| `resources/js/layouts/` | AppLayout, AuthLayout, SettingsLayout |
| `resources/js/actions/` | **Generated** — Wayfinder controller helpers |
| `resources/js/routes/` | **Generated** — Wayfinder route helpers |
| `resources/js/components/ui/` | **Generated** shadcn-like UI components |
| `app/Models/` | 90+ models including Tenant, User, Unit, Lease, Facility, etc. |
| `app/TenantFinder/` | Header-based tenant resolution |

## Commands

```bash
# Dev (starts server + queue + logs + vite concurrently)
composer run dev

# Run a single PHP test
php artisan test --compact --filter=testMethodName

# Run all tests in a file
php artisan test --compact tests/Feature/ExampleTest.php

# Run all tests
php artisan test --compact

# Frontend typecheck
npm run types:check          # vue-tsc --noEmit

# Frontend lint (autofixes)
npm run lint

# Frontend format
npm run format               # prettier --write resources/ only

# PHP format (after any PHP change)
vendor/bin/pint --dirty --format agent

# Full CI check pipeline (lint → format-check → types → test)
composer run ci:check

# E2E tests (requires app running)
npm run e2e:test

# Build frontend for production
npm run build

# Regenerate Wayfinder route typings (if routes changed)
php artisan wayfinder:generate
```

## Critical conventions

- **Never hardcode URLs** — use Wayfinder: `import route from '@/routes'` or `import action from '@/actions'`
- **ESLint ignores generated files**: `resources/js/actions/**`, `resources/js/routes/**`, `resources/js/wayfinder/**`, `resources/js/components/ui/*`
- **Prettier only formats `resources/`** — not the whole project
- **Tests use PHPUnit** (not Pest). If you see Pest, convert to PHPUnit. Always run tests with `php artisan test --compact`.
- **Testing DB is SQLite in-memory** with `BCRYPT_ROUNDS=4`, sync queue, array cache. See `phpunit.xml`.
- **Pint uses laravel preset**. Run `vendor/bin/pint --dirty --format agent` after any PHP change.
- **Always use `php artisan make:` commands** with `--no-interaction` to create files.
- **Vue components must have a single root element**.
- **Import type-only imports** as `import type { ... }` — enforced by eslint `consistent-type-imports`.
- **TypeScript strict mode** is on. `@/*` maps to `resources/js/*`.
- **Fortify features are conditionally enabled** — tests skip missing features with `skipUnlessFortifyHas()`.

## Frontend stack gotchas

- **Axios is removed** from Inertia v3. Use the built-in XHR client or `useHttp`.
- `Inertia::lazy()` / `LazyProp` → use `Inertia::optional()` instead.
- Event names changed: `invalid` → `httpException`, `exception` → `networkError`.
- `router.cancel()` → `router.cancelAll()`.
- **Vite manifest error** means you need to run `npm run build` or `npm run dev`.

## Council agents (Product Council)

Eight council subagents available in `.opencode/agents/`. Invoke via `@agent-name` or the Task tool.

**Agents:** `pm` (PRDs/stories), `tech-lead` (technical design), `designer` (UX/wireframes), `delivery-pm` (project board), `engineer` (implementation/PRs), `qa` (tests beyond happy path), `reviewer` (code review), `docs` (EN+AR user guides)

**Chain order:** PM → Designer → Tech Lead → Engineer → QA → Reviewer → Docs → human merges

**Key rules:**
- Artifacts live in GitHub issues and `.claude/agent-memory/`. Never create ad-hoc markdown for council work in `docs/`.
- Reviewer never merges. Docs never merges. Human always merges.
- PM never writes code. Tech Lead never opens PRs. Engineer never approves own PR. QA never merges. Docs never modifies PHP/Vue source.
- Each agent updates only its own memory directory (`<agent>/MEMORY.md`).
- `opencode.json` loads `AGENTS.md` and `CLAUDE.md` as instructions. Full council workflow: `docs/council/README.md`.

## When stuck

- Use `php artisan list` to discover commands
- Use `php artisan route:list` to inspect routes (filter with `--method=`, `--name=`, `--path=`)
- Read existing sibling files before creating new ones — patterns are consistent within each directory
- `CLAUDE.md` has full Laravel Boost guidelines and skill activation rules
