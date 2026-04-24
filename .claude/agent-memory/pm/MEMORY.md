# PM — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Project context
- App: multi-tenant real estate management platform (Laravel 13 + Inertia v3 + Vue 3 + Tailwind v4).
- Domains: Properties (Community, Building, Unit), Leasing (Lease, LeaseUnit, Resident, Owner), Marketplace (Unit, Offer, Visit), Facilities (Facility, FacilityBooking), Service Requests, Accounting (Transaction, Invoice, Payment), Communication (Announcement), Admin, Reports (PowerBI), Settings, Auth (Fortify), Visitor Access, Documents.
- Multi-language: first-class Arabic/RTL support. Always consider i18n when specifying user-facing copy in PRDs.
- Multi-tenancy: tenant isolation via Spatie; PRDs should call out tenant boundaries when relevant.

## Recurring personas (confirmed as of 2026-04-24)
- **Resident** — lives in a unit, books facilities, files service requests, pays rent.
- **Unit Owner** — owns units, leases them out, receives payments, sees accounting.
- **Property Manager / Admin** — runs day-to-day ops, handles service requests, approves leases, manages announcements. **Accounting Manager folds into this persona** — do not split at story level; the RBAC `accountingManagers` sub-type is an implementation detail only.
- **Marketplace Seller** — lists units for sale/rent, fields offers, schedules visits.
- **Marketplace Buyer** — browses listings, requests visits, makes offers.
- **Service Technician / Field Agent** — CONFIRMED new persona. Executes on-site maintenance jobs; sees only their assigned queue; mobile-optimized view is a requirement; limited RBAC scope (serviceManagers sub-type).
- **Security Guard / Gate Officer** — CONFIRMED new persona. Performs physical visitor check-in/check-out at community entry point; uses QR code scanner or manual code entry.
- **Data Analyst** — CONFIRMED new persona. Enterprise Power BI user who consumes platform data via API credentials; platform role is configuration-only (no day-to-day UI use).

## PRD conventions (baseline — refine with user corrections)
- Use the `prd.yml` template; fields map 1:1 to the `prd-development` skill's template.
- Every PRD must have a primary success metric with baseline + target.
- Scope section must list "Out of scope" explicitly.
- Risks section must include both tech and adoption risks.

## User preferences
_(populate as you learn — writing voice, level of detail, tolerance for discovery depth, preferred persona format, etc.)_

## Past work index
- PRD #109 — RBAC: Roles, Permissions & Admin UI — area:admin + area:auth — shipped.
  - Stories: #110 (data model), #111 (seeders), #112 (enforcement layer), #113 (scoped access), #114 (roles list UI), #115 (permission matrix UI), #116 (assign roles UI), #117 (migration).
- PRD #130 — Admin Audit Log: Who Changed What in RBAC — area:admin + area:auth — paused.
  - Stories: #131 (backend capture). Remaining: list UI, per-role inline trail.
- PRD #132 — Contacts — Resident, Owner & Professional Entity Management — area:contacts — state:draft.
- PRD #133 — Properties — Community, Building & Unit Lifecycle Management — area:properties — state:draft.
- PRD #134 — Leasing — Quote, Contract & Lease Lifecycle Management — area:leasing — state:draft.
- PRD #135 — Accounting — Transaction Recording, Invoicing & Payment Schedules — area:accounting — state:draft.
- PRD #136 — Service Requests — Resident Maintenance Flow, SLA Tracking & Field Assignment — area:service-requests — state:draft.
- PRD #137 — Documents — Template Management, Document Generation & E-Signature Infrastructure — area:documents — state:draft.
- PRD #138 — Marketplace — Unit Listings, Offers & Visit Scheduling — area:marketplace — state:draft.
- PRD #139 — Facilities — Facility Configuration, Booking Lifecycle & Booking Contracts — area:facilities — state:draft.
- PRD #140 — Communication — Announcements, Complaints, Suggestions & Community Directory — area:communication — state:draft.
- PRD #141 — Visitor Access — Pre-Registration, QR Code Access & Gate Check-In/Check-Out — area:visitor-access — state:draft.
- PRD #142 — Settings — Company Profile, Contract Types, Invoice Configuration & Form Templates — area:settings — state:draft.
- PRD #143 — Auth — Login, 2FA, Session Management & Profile Self-Service — area:auth — state:draft.
- PRD #144 — Admin — Account Subscriptions, Leads Management & Owner Registration Workflow — area:admin — state:draft.
- PRD #145 — Reports — Built-In System Reports, Financial Summaries & Operational Analytics — area:reports — state:draft.
- PRD #146 — Reports — Power BI Integration, API Credentials & Enterprise BI Connector — area:reports — state:draft.

## Product story map
- Full skeleton at `.claude/agent-memory/pm/PRODUCT_STORY_MAP.md` — 14 domains, 15 PRDs, ~180 stories (est).
- Phase 2 Batch 1 filed (2026-04-24): #132 Contacts, #133 Properties, #134 Leasing, #135 Accounting, #136 Service Requests.
- Phase 2 Batch 2 filed (2026-04-24): #137 Documents, #138 Marketplace, #139 Facilities, #140 Communication, #141 Visitor Access.
- Phase 2 Batch 3 filed (2026-04-24): #142 Settings, #143 Auth, #144 Admin, #145 Reports (system), #146 Reports (Power BI).
- **Phase 2 COMPLETE.** All 15 PRDs filed. Phase 3 (story breakdown) begins next.
- Most entangled domains (sequence together): Leasing (#134), Accounting (#135), Contacts (#132).

## Key decisions (Phase 2 Batch 1)
- Documents domain: infrastructure only — no standalone end-user document center UI. Documents surface inside parent features (lease tab, accounting tab).
- Accounting Manager persona folds into Property Manager/Admin at story level. RBAC sub-type is implementation detail.
- Service Technician, Security Guard, Data Analyst are confirmed personas — added to seed above.

## Key decisions (Phase 2 Batch 2)
- Documents (#137) moved to first position in Batch 2 because Leasing contract stories and Facilities booking contract stories are hard-blocked without it.
- Facilities (#139) has an explicit hard dependency on Documents (#137) — booking contract stories must be sequenced after Documents stories in sprint planning.
- Marketplace (#138) → Leasing handoff: Marketplace triggers quote creation but does not own the Leasing quote object. Interface contract defined at story breakdown.
- Communication (#140) complaints vs. Service Requests boundary: open question — decision rule needed before story breakdown to ensure stories land in the correct domain.
- Visitor Access (#141) Gate Officer RBAC: gate-officer role sub-type may be required; Tech Lead to assess against existing RBAC (#109). Not a PM decision.
- Visitor Access is the most self-contained Batch 2 domain — no dependency on Documents, Marketplace, or Facilities. Strong candidate for parallel delivery.

## Key decisions (Phase 2 Batch 3)
- **InvoiceSetting / ServiceSetting ownership RESOLVED:** Settings (#142) owns both config objects. Accounting (#135) reads them. Comment added to #135 (see issue #135 comment).
- Social login (OAuth/Socialite) is out of scope for Phase 2 Auth (#143). Recorded in PRD to prevent resurface.
- Mandatory 2FA enforcement is an Admin feature (future), not an Auth feature. Noted in both #143 and #144.
- Admin PRD (#144) = subscriptions + leads + owner registration ONLY. RBAC (#109) and Audit Log (#130) are explicitly excluded.
- Reports split confirmed: #145 (system reports) and #146 (Power BI) share area:reports. Independently deliverable, independent sequencing.
- Power BI (#146) Phase 2 approach: live database read, no ETL. Read replica decision deferred to Tech Lead at story breakdown.
- RBAC report access control (does scoping from #113 apply to report filters?): Tech Lead open question in #145.

## Story-splitting patterns used
- RBAC split seam: data model → seeders → enforcement → scope → UI (list) → UI (matrix) → UI (assignment) → data migration. Each seam is independently deployable without breaking the running system.
- Audit log split seam: backend capture → list UI → inline per-role trail. Backend must ship first; UI stories are independently queueable after.
