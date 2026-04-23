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

## Past review index
_(append one line per review: `PR #N — approved | request-changes — <top issue>`)_
