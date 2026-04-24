# PM — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Project context
- App: multi-tenant real estate management platform (Laravel 13 + Inertia v3 + Vue 3 + Tailwind v4).
- Domains: Properties (Community, Building, Unit), Leasing (Lease, LeaseUnit, Resident, Owner), Marketplace (Unit, Offer, Visit), Facilities (Facility, FacilityBooking), Service Requests, Accounting (Transaction, Invoice, Payment), Communication (Announcement), Admin, Reports (PowerBI), Settings, Auth (Fortify), Visitor Access, Documents.
- Multi-language: first-class Arabic/RTL support. Always consider i18n when specifying user-facing copy in PRDs.
- Multi-tenancy: tenant isolation via Spatie; PRDs should call out tenant boundaries when relevant.

## Recurring personas (seed — validate with user before using in new PRDs)
- **Resident** — lives in a unit, books facilities, files service requests, pays rent.
- **Unit Owner** — owns units, leases them out, receives payments, sees accounting.
- **Property Manager / Admin** — runs day-to-day ops, handles service requests, approves leases, manages announcements.
- **Marketplace Seller** — lists units for sale/rent, fields offers, schedules visits.
- **Marketplace Buyer** — browses listings, requests visits, makes offers.

## PRD conventions (baseline — refine with user corrections)
- Use the `prd.yml` template; fields map 1:1 to the `prd-development` skill's template.
- Every PRD must have a primary success metric with baseline + target.
- Scope section must list "Out of scope" explicitly.
- Risks section must include both tech and adoption risks.

## User preferences
_(populate as you learn — writing voice, level of detail, tolerance for discovery depth, preferred persona format, etc.)_

## Past work index
- PRD #109 — RBAC: Roles, Permissions & Admin UI — area:admin + area:auth — enterprise sales blocker; 30 subjects × 6 actions = 180 permission matrix (source: all.json); 7 UserRole types (accountAdmins, admins, managers, owners, tenants, dependents, professionals); 5 AdminRole sub-types stored in rf_admins.role (Admins, accountingManagers, serviceManagers, marketingManagers, salesAndLeasingManagers); managers have community/building scope; serviceManagers also scoped by ServiceType; existing rf_admins.role enum must be migrated to new Role records without breaking auth; Arabic RTL + bilingual name_ar/name_en required.
  - Stories: #110 (data model), #111 (seeders), #112 (enforcement layer), #113 (scoped access), #114 (roles list UI), #115 (permission matrix UI), #116 (assign roles UI), #117 (migration).

## Story-splitting patterns used
- RBAC split seam: data model → seeders → enforcement → scope → UI (list) → UI (matrix) → UI (assignment) → data migration. Each seam is independently deployable without breaking the running system.
