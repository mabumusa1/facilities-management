# PM — Agent Memory

Keep this under 200 lines. Full story tables are in `PRODUCT_STORY_MAP.md`.

## Project context
- App: multi-tenant real estate management platform (Laravel 13 + Inertia v3 + Vue 3 + Tailwind v4).
- Domains: Properties, Leasing, Marketplace, Facilities, Service Requests, Accounting, Communication, Admin, Reports (PowerBI), Settings, Auth (Fortify), Visitor Access, Documents, Contacts.
- Multi-language: first-class Arabic/RTL. Always consider i18n on user-facing copy.
- Multi-tenancy: tenant isolation via Spatie; PRDs should call out tenant boundaries when relevant.

## Recurring personas (confirmed 2026-04-24)
- **Resident** — lives in unit, books facilities, files SRs, pays rent.
- **Unit Owner** — owns units, leases out, receives payments, sees accounting.
- **Property Manager / Admin** — day-to-day ops, SRs, leases, announcements. Accounting Manager folds into this persona at story level — RBAC sub-type is implementation detail only.
- **Marketplace Seller / Buyer** — lists/browses units, makes offers.
- **Service Technician / Field Agent** — CONFIRMED. Assigned queue, mobile-optimized, serviceManagers RBAC sub-type.
- **Security Guard / Gate Officer** — CONFIRMED. QR scanner or manual code at gate entry.
- **Data Analyst** — CONFIRMED. Enterprise Power BI; platform role is config-only.

## PRD conventions
- Use `prd.yml` template. Every PRD must have: primary metric with baseline + target, explicit Out-of-scope, tech + adoption risks.

## User preferences
_(populate as you learn — writing voice, level of detail, tolerance for discovery depth.)_

## Past work index
- PRD #109 — RBAC — shipped. Stories #110–#117.
- PRD #130 — Audit Log — paused. Story #131. Remaining: list UI, per-role trail.
- **Phase 2 COMPLETE** (2026-04-24): PRDs #132–#146. 15 PRDs across all domains.
- **Phase 3 COMPLETE** (2026-04-24): 177 stories #147–#322 (GH skipped #174). Ready for Designer handoff.

### Story index by PRD (issue ranges only — full tables in PRODUCT_STORY_MAP.md)
- #132 Contacts → #147–#157 (11 stories; migration #157 last)
- #133 Properties → #158–#168 (11 stories; state machine #158 first)
- #134 Leasing → #169–#184 (15; GH skipped #174; activation #175 highest cross-domain risk)
- #135 Accounting → #185–#198 (14; InvoiceSetting #194 blocked on Settings #227+#234)
- #137 Documents → #199–#208 (10; template format gate #207 blocks #200)
- #136 SR → #209–#223 (15; data model #222 filed last, implement first)
- #142 Settings → #224–#234 (11; seed #234 first; unblocks Leasing + Accounting)
- #143 Auth → #235–#245 (11; gap audit #243 first)
- #139 Facilities → #246–#256 (11; contracts #253 depend on Documents chain)
- #141 Visitor Access → #257–#264 (8; fully self-contained, parallel candidate)
- #138 Marketplace → #265–#278 (14; offer→Leasing #276 is cross-domain seam)
- #140 Communication → #279–#290 (12; complaint→SR #285 coordinates with #216)
- #144 Admin → #291–#302 (12; feature flags #301 unblocks PowerBI #315)
- #145 Reports-System → #303–#313 (11; domain boundary in #303; exec dashboard #313 last)
- #146 Reports-PowerBI → #314–#322 (9; all blocked on TL ADR in #314)

## Key cross-domain wires (critical for sprint planning)
- Leasing #175 ↔ Accounting #188: 4 open TL questions (trigger mechanism, payload, mutation ownership, termination cascade). Both blocked until resolved.
- Documents chain: #199 → #207 → #200 → #202 → #203 → #204 → Leasing #176 + Facilities #253.
- Settings #234 + #226 → Leasing #169 (ContractType seed required before any quote can be created).
- Accounting #193 → Reports #308 (aging report; #308 extends #193 to portfolio level; sequence together).
- Admin #301 → PowerBI #315 (feature flag toggle is the same flag; #301 must land first).
- PowerBI #314 (TL ADR) → all of #315–#322 (none may enter design until ADR is posted).

## Key decisions (Phase 3 Sessions 1–7) — see individual session entries in PRODUCT_STORY_MAP.md

## Key decisions (Phase 3 Session 8 — Reports)
- Reports-System (#145) domain boundary: centralised cross-domain analytics ONLY. Domain-scoped operational reports (#223, #256, #264, #278, #286, #302) are NOT duplicated here.
- Admin #302 (admin dashboard) and Reports #313 (executive dashboard) are DISTINCT: different personas, different data. Cross-domain boundary comment filed on #302.
- #303 TL Q1 (BLOCKING): does RBAC scoped-access (#113) propagate to report filter defaults? Must resolve before any report UI story enters implementation.
- #303 TL Q2: live query vs. snapshot table pattern. Affects #312 (async engine) sequencing.
- #307 product decision: VAT jurisdiction — default "generic summary; jurisdiction-specific deferred to v2." User must confirm if ZATCA is required at launch.
- #309 product decision: maintenance threshold (14 days default). User must confirm if tenant-configurable.
- #310 TL Q1: report export reuse Documents #206 or separate pipeline?
- #312 TL Q1 (BLOCKING): async threshold (row count / always async / time-based). UX depends on answer.
- PowerBI PRD #146: #314 is a pure TL decision gate — all 3 decisions (DB architecture, OData vs. REST, credential pattern) must be ADR-documented before #315–#322 enter design.
- #316 product decision: credential expiry — default 90 days with user override. Confirm if "no expiry" must be blocked.
- #317 product decision: national ID masking in BI endpoints — default is masked (last 4 digits). Confirm if full exposure needed.
- #321 TL Q1: per-credential rate limit — suggested 60 req/min but must align with Power BI refresh interval.
- Cross-domain comments filed on Admin #301 (PowerBI #315 dependency) and #302 (boundary with Reports #313).

## Phase 3 completion statement
**2026-04-24 — Phase 3 backlog buildout COMPLETE.** 15 PRDs + 177 stories (issues #147–#322) filed across 8 sessions. Backlog is ready for Designer handoff. Recommended first PRDs to hand off: Contacts (#132), Auth (#143), Settings (#142), Properties (#133).

## User product decisions resolved (2026-04-24, post-Phase-3)
- **Password policy**: use Laravel `Password::defaults()` (recommended: `min(8)->mixedCase()->numbers()->symbols()->uncompromised()`). No reuse prevention in v1. Affects #236, #240.
- **Email verification**: REQUIRED before first login. No unverified-login fallback. Affects #237.
- **Resident self-registration**: invite-code only. Admin issues single-use time-limited code; resident redeems and then verifies email. No open self-registration. Affects #237, #242.
- **Subscription model**: ONE plan with ALL features; tiered by `max_units` bracket only. Feature-flag infra retained but all flags ON in v1. Seat limits still enforced per tier. Affects #291, #293, #295, #301.
- **Owner registration documents**: GCC standard set (National ID, Passport, Title Deed) — document-type catalog must be configurable (seeded, not hardcoded). Affects #300.

## User product decisions still pending (defaulted in stories, overridable)
- off_plan Marketplace visibility → Option B (coming-soon badge)
- QR delivery channel → OS share sheet only (v1)
- Visitor overstay threshold → 2h fixed
- VAT report format → generic (ZATCA deferred)
- Maintenance threshold → 14 days
- BI credential expiry → 90 days
- National ID masking in BI → last-4 masked
