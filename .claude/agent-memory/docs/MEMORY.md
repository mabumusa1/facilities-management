# Docs — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Project context
- App: multi-tenant real estate management platform (Laravel 13 + Inertia v3 + Vue 3 + Tailwind v4).
- User docs site: GitHub Pages, Jekyll source at `docs/` on the `main` branch, with `docs/council/` excluded.
- Docs live at `docs/guides/<area>/<slug>.md` (English) and `docs/ar/guides/<area>/<slug>.md` (Arabic).
- Developer-facing CHANGELOG: `CHANGELOG.md` at repo root, Keep a Changelog format.
- User-facing release history: `docs/changelog.md` (narrative, plain language).

## Audience
Primary readers of the user docs:
- **Property Managers / Admins** — day-to-day operators, need task-oriented how-tos.
- **Residents** — self-service for bookings, requests, payments.
- **Owners** — financial views, lease oversight, handovers.
- **Marketplace users** — buyers browsing, sellers listing.
Docs are not for engineers. For engineering docs, link to the relevant code or issue.

## Voice & style
- Second person ("you"), present tense, active voice.
- Short sentences. One action per step.
- Use the exact button labels and screen titles that appear in the UI — no paraphrasing.
- If a feature is bilingual in the UI, state both strings the first time the button appears (e.g., "Tap **Book Now** (احجز الآن)").
- Do not use marketing language. No "powerful", "seamless", "robust".
- Avoid second-guessing the user ("simply", "just", "obviously").

## Bilingual rules
- **Every guide ships in EN + AR unless explicitly scoped otherwise.**
- Arabic slug matches the English slug (filename identical, directory is `docs/ar/`).
- Arabic dates in prose use Arabic-Indic digits (٢٠٢٦); filenames and CHANGELOG use ISO (2026-04-24).
- If the English version uses a screenshot callout like "the blue button on the right," the Arabic must account for RTL ("the blue button on the left").
- Flag guides you could not translate cleanly in this memory under "needs-translation".

## Guide structure (canonical)
```
Title → 1-sentence summary → Who this is for → Before you start → Steps → What you'll see → Common issues → Related
```
Deviations need a reason noted in the PR.

## CHANGELOG conventions
- Keep a Changelog v1.1.0 sections only: Added, Changed, Deprecated, Removed, Fixed, Security.
- `## [Unreleased]` always present at the top, empty after a release cut.
- Every bullet links the PR: `- <change> ([#<pr>](<url>))`.
- Multi-area PR → one bullet per area, same PR link repeated.
- Internal refactors (no user-visible impact): `_Internal_` footer section, no release cut, one line.

## Areas & slug patterns
Follow the council's `area:` taxonomy. Guide slugs are kebab-case verb phrases:
- `docs/guides/leasing/create-a-lease.md`
- `docs/guides/marketplace/list-a-unit-for-sale.md`
- `docs/guides/facilities/book-the-gym.md`
- `docs/guides/service-requests/file-a-maintenance-ticket.md`

## Needs-translation
_(append guide slugs where Arabic was skipped or machine-stubbed, with a reason)_

## User preferences
_(populate as you learn — voice corrections, screenshot preferences, publishing cadence, etc.)_

## Past work index
- #330 — Resident create & search with phone duplicate detection (#148) — contacts — both
- #210 — Resident submits a service request (#355) — service-requests — both EN+AR
