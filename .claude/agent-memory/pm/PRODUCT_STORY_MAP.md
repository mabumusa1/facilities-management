# Product Story Map — Multi-Tenant Real Estate Management Platform

> Phase 1 skeleton. One file per run. Do not file GitHub issues from this document — it feeds Phase 2 (PRDs) and Phase 3 (stories).
> Last updated: 2026-04-24
> **PHASE 3 COMPLETE** — all 15 PRDs broken into stories. 177 stories filed (#147–#322, GitHub skipped #174).
> Phase 2 COMPLETE: all 15 PRDs filed (#132–#146).
> Phase 3 complete (8 sessions, 2026-04-24):
>   - Session 1: #132 Contacts → #147–#157 (11); #133 Properties → #158–#168 (11)
>   - Session 2: #134 Leasing → #169–#184 (15, GH skipped #174); #135 Accounting → #185–#198 (14)
>   - Session 3: #137 Documents → #199–#208 (10); #136 SR → #209–#223 (15)
>   - Session 4: #142 Settings → #224–#234 (11); #143 Auth → #235–#245 (11)
>   - Session 5: #139 Facilities → #246–#256 (11); #141 Visitor Access → #257–#264 (8)
>   - Session 6: #138 Marketplace → #265–#278 (14); #140 Communication → #279–#290 (12)
>   - Session 7: #144 Admin → #291–#302 (12)
>   - Session 8: #145 Reports-System → #303–#313 (11); #146 Reports-PowerBI → #314–#322 (9)
>   - TOTAL: 177 stories (#147–#322, GitHub skipped #174). Ready for Designer handoff.

---

## 1. Properties (area:properties)

**Purpose:** Define and manage the physical asset hierarchy — Communities, Buildings, and Units — that every other domain depends on for identity and location context.

**Primary personas:** Property Manager/Admin

**Backbone activities:**
1. Create and configure a Community
2. Add Buildings to a Community
3. Add and configure Units within a Building or Community
4. Manage Unit metadata (type, area, rooms, specifications, amenities, features)
5. Bulk-import properties from Excel/CSV
6. Track unit lifecycle status (available, occupied, under-maintenance, off-plan)
7. Manage media assets for properties (photos, floor plans)
8. Search, filter, and export the property portfolio

**Key tasks per activity:**
1. Create Community
   - Enter community name (EN/AR), city, district, country
   - Upload community photos and map coordinates
   - Set community-level amenities and working days
   - Configure community status (active/inactive)

2. Add Buildings
   - Attach building to community
   - Set building name, floors, and construction year
   - Upload building photos and documents

3. Add and configure Units
   - Select building or community (for standalone units)
   - Set unit number, type, category, area, floor
   - Assign rooms configuration
   - Set pricing reference (asking rent)
   - Set unit status

4. Manage Unit metadata
   - Add UnitSpecification records (furnished, parking, view)
   - Tag features and amenities
   - Link to UnitCategory and UnitType

5. Bulk import
   - Upload Excel/CSV for communities, buildings, units
   - Map columns to system fields
   - Review validation errors before confirming import
   - View import history

6. Track unit lifecycle
   - Manually change status
   - Block unit from marketplace when under maintenance
   - Restore to available after move-out complete

7. Manage media
   - Upload, reorder, and delete photos per community/building/unit
   - Set primary/cover image

8. Search and export
   - Filter by community, building, type, status, area range
   - Export to Excel

**Cross-domain dependencies:**
- All other domains read from Properties for unit identity (Leasing, Marketplace, Facilities, Service Requests, Visitor Access, Accounting)
- Contacts domain links Owner records to Unit ownership
- Bulk import uses Documents (ExcelSheet model)

**Estimated PRD + story count:** 1 PRD, ~12 stories

---

## 2. Leasing (area:leasing)

**Purpose:** Manage the full tenant lifecycle from prospective quote through active lease, renewal, sublease, and move-out, including lease unit assignments and financial terms.

**Primary personas:** Property Manager/Admin, Resident (tenant perspective), Unit Owner

**Backbone activities:**
1. Create and send a leasing quote to a prospect
2. Convert quote to a lease application
3. Activate a lease (generate lease document, assign unit(s))
4. Manage lease during tenancy (amendments, escalations, additional fees)
5. Handle subleases
6. Execute move-out workflow (inspection, deposit settlement, lease termination)
7. Manage lease renewals
8. Track lease pipeline (pipeline view, expiry watch)

**Key tasks per activity:**
1. Create and send leasing quote
   - Select unit, prospect contact, and contract type
   - Set lease duration, rent amount, payment frequency
   - Add additional charges and special terms
   - Save as draft or send via email/SMS
   - Track quote status (draft, sent, viewed, accepted, rejected, expired)
   - Revise and re-send a quote

2. Convert quote to lease application
   - Pre-fill lease from accepted quote
   - Attach KYC documents
   - Submit for manager approval

3. Activate lease
   - Generate lease contract from template
   - Assign one or more units (LeaseUnit)
   - Set security deposit and payment schedule
   - Activate and lock financial terms
   - Send contract for e-signature

4. Manage lease during tenancy
   - Add lease escalation rules
   - Record additional fees (LeaseAdditionalFee)
   - Amend lease terms with history trail
   - Send notices to tenant

5. Handle subleases
   - Create sublease linked to master lease
   - Assign sublease unit and sublessee contact
   - Track sublease payment separately

6. Move-out workflow
   - Initiate move-out from expiring or terminated lease
   - Schedule and conduct inspection
   - Document damages and calculate deposit deductions
   - Process refund or additional charge
   - Mark unit as available

7. Lease renewals
   - Generate renewal offer from existing lease
   - Adjust rent for new term
   - Convert renewal offer to new lease or amendment

8. Lease pipeline tracking
   - View all leases by status (active, expiring, expired, terminated)
   - Expiry alerts (30/60/90 day warnings)
   - Bulk export of lease data

**Cross-domain dependencies:**
- Properties: unit identity, status changes when lease activates/ends
- Contacts: Resident and Owner records as parties
- Accounting: lease triggers transaction schedules and invoices
- Documents: lease contract templates and signed documents
- Communication: tenant notices, expiry alerts

**Estimated PRD + story count:** 1 PRD, ~18 stories

---

## 3. Marketplace (area:marketplace)

**Purpose:** Provide a listing platform where available units are marketed to prospective buyers/tenants, including offer management and visit scheduling.

**Primary personas:** Property Manager/Admin, Marketplace Seller, Marketplace Buyer

**Backbone activities:**
1. List a unit on the marketplace
2. Manage marketplace listings (edit, pause, unpublish, feature)
3. Manage customer inquiries (Leads / Customers)
4. Schedule and track property visits
5. Receive and evaluate offers
6. Convert a marketplace customer to a lease
7. Track listing analytics and performance

**Key tasks per activity:**
1. List a unit
   - Select available unit from property hierarchy
   - Write listing title and description (EN/AR)
   - Upload photos and virtual tour link
   - Set asking price, payment terms, and availability date
   - Choose visibility (public/internal)
   - Set featured/promoted flag
   - Publish or save as draft

2. Manage listings
   - Edit listing details
   - Pause or archive listing
   - Duplicate listing for similar unit

3. Manage customers/leads
   - Capture inquiry from web form or manual entry
   - Assign lead to sales agent
   - Track lead source
   - View customer interest history across multiple units
   - Import leads from Excel

4. Schedule visits
   - Create visit appointment linked to unit and customer
   - Assign agent to accompany
   - Send visit confirmation (email/SMS)
   - Record visit outcome (interested, not interested, second visit)
   - Cancel or reschedule

5. Receive and evaluate offers
   - Customer or agent submits an offer with price and terms
   - Manager accepts, counters, or rejects offer
   - Track offer history and revisions

6. Convert to lease
   - Handoff from accepted offer to Leasing (create quote pre-filled)
   - Archive marketplace record as "converted"

7. Track analytics
   - View listing impressions, inquiries, visits, and conversion rate
   - Compare performance across listings

**Cross-domain dependencies:**
- Properties: unit data, availability status (marketplace reads Properties; lease activation writes back to unit status)
- Leasing: conversion from offer to quote/lease
- Contacts: customer/lead records
- Communication: visit confirmations and offer notifications

**Estimated PRD + story count:** 1 PRD, ~14 stories

---

## 4. Facilities (area:facilities)

**Purpose:** Configure and manage community shared facilities (gym, pool, hall, BBQ) and enable residents to book time slots, with manager oversight of bookings and contracts.

**Primary personas:** Property Manager/Admin, Resident

**Backbone activities:**
1. Configure community facilities
2. Set booking rules per facility
3. Browse and book a facility slot (resident)
4. Manage and approve facility bookings (manager)
5. Generate and sign booking contracts
6. Handle booking cancellations and conflicts
7. Track facility usage analytics

**Key tasks per activity:**
1. Configure facilities
   - Create facility with name (EN/AR), category, photos
   - Set location within community
   - Define capacity and operating hours
   - Enable/disable facility

2. Set booking rules
   - Define bookable time slots
   - Set min/max duration, advance booking limit
   - Configure per-booking pricing
   - Set cancellation policy
   - Toggle approval requirement

3. Book a facility slot
   - Browse available facilities
   - View availability calendar
   - Select date, time slot, duration
   - Specify guests and purpose
   - Confirm and receive booking confirmation

4. Manage bookings
   - View all bookings (list and calendar views)
   - Approve or reject pending bookings
   - Modify booking on behalf of resident
   - Mark booking as completed

5. Generate booking contracts
   - Auto-generate contract from template for qualifying bookings
   - Pre-fill resident and booking details
   - Send for digital signature with OTP
   - Store signed contract

6. Handle cancellations
   - Resident cancels within policy window
   - Manager cancels with reason
   - Apply cancellation fee if applicable
   - Notify impacted party

7. Track usage
   - Utilization rate per facility
   - Peak usage periods
   - Revenue from paid facilities

**Cross-domain dependencies:**
- Properties: community and unit identity for resident eligibility
- Contacts: Resident record
- Accounting: booking fees and deposits
- Documents: booking contract templates

**Estimated PRD + story count:** 1 PRD, ~12 stories

---

## 5. Service Requests (area:service-requests)

**Purpose:** Allow residents to submit maintenance, cleaning, and other service requests; route them to appropriate handlers; and track resolution through SLA-based workflows.

**Primary personas:** Property Manager/Admin, Resident

**New persona flagged:** Service Technician / Field Agent — the person assigned to carry out a service request in the field. Does not appear in current memory seed.

**Backbone activities:**
1. Configure service categories and subcategories (admin)
2. Configure home services (third-party service catalog)
3. Configure neighbourhood services (community-wide scheduled services)
4. Submit a unit-specific service request (resident)
5. Assign and manage service requests (manager)
6. Track and resolve a request (technician/manager)
7. Request a home service from the catalog (resident)
8. Report and resolve neighbourhood service issues

**Key tasks per activity:**
1. Configure service categories
   - Create/edit categories and subcategories (EN/AR)
   - Set SLA (response time, resolution time)
   - Assign default handler
   - Set category icon and sort order
   - Enable/disable per community

2. Configure home services
   - Create featured service entries (FeaturedService)
   - Set pricing, availability, and provider
   - Define service area coverage

3. Configure neighbourhood services
   - Define service type and zones
   - Set recurring schedule
   - Assign service provider

4. Submit service request
   - Select category and subcategory
   - Describe issue in text
   - Attach photos and location within unit
   - Set urgency
   - Track submission status

5. Assign and manage requests
   - View incoming requests dashboard
   - Assign to technician or team
   - Set priority and target resolution date
   - Add internal notes

6. Track and resolve
   - Update request status (open, in progress, pending parts, resolved, closed)
   - Record resolution notes and materials used
   - Attach completion photos
   - Collect resident satisfaction rating

7. Request a home service
   - Browse service catalog
   - Book appointment date/time
   - Track booking and receive confirmation

8. Neighbourhood service issues
   - Resident reports an issue for a common area
   - Manager acknowledges and assigns
   - Resolution tracked same as unit request

**Cross-domain dependencies:**
- Properties: unit and community context
- Contacts: Resident record
- Accounting: service fees charged to resident or cost to building
- Communication: request updates and notifications

**Estimated PRD + story count:** 1 PRD, ~15 stories

---

## 6. Accounting (area:accounting)

**Purpose:** Record all financial transactions, generate invoices and receipts, manage payment schedules tied to leases, and produce financial reports for the property portfolio.

**Primary personas:** Property Manager/Admin, Unit Owner

**New persona flagged:** Accounting Manager — one of the 5 AdminRole sub-types (accountingManagers); a distinct operator role from the general Property Manager. Already exists in the data model from RBAC work.

**Backbone activities:**
1. Record money-in transactions (rent, deposits, fees)
2. Record money-out transactions (expenses, refunds)
3. Manage recurring transaction schedules from leases
4. Generate and send invoices
5. Track overdue payments and arrears
6. Configure invoice and payment settings
7. Configure bank account details
8. Reconcile and audit transactions

**Key tasks per activity:**
1. Record money-in
   - Select transaction type, contact, lease/unit
   - Enter amount, date, payment method, reference
   - Auto-generate and send receipt
   - Attach supporting documents

2. Record money-out
   - Select expense type, vendor/contact
   - Categorize expense
   - Attach invoice/receipt

3. Manage recurring schedules
   - Create schedule from active lease
   - Set frequency, start/end dates, auto-generation date
   - Enable payment reminders
   - Handle schedule changes (rent escalation)

4. Generate invoices
   - Create invoice from transaction or lease schedule
   - Apply InvoiceSetting branding
   - Send via email
   - Track payment status (unpaid, partial, paid, overdue)

5. Track arrears
   - View overdue payments list
   - Send automated payment reminders
   - Generate aging report

6. Configure settings
   - InvoiceSetting: logo, terms, payment instructions
   - ServiceSetting: fees and charges configuration
   - Chart of accounts / transaction categories

7. Configure bank details
   - Add bank account details for display on invoices
   - Set default account per community

8. Reconcile transactions
   - Mark transactions as reconciled
   - View bank reconciliation summary
   - Export transaction history

**Cross-domain dependencies:**
- Leasing: lease activates transaction schedules; move-out triggers deposit settlement
- Properties: unit and community context on transactions
- Contacts: payer/payee identity
- Reports: financial data consumed by reports and PowerBI
- Settings: invoice and payment configuration

**Estimated PRD + story count:** 1 PRD, ~16 stories

---

## 7. Communication (area:communication)

**Purpose:** Enable property managers to broadcast announcements to residents and communities, and provide a community directory and resident feedback channels (suggestions and complaints).

**Primary personas:** Property Manager/Admin, Resident

**Backbone activities:**
1. Create and publish community announcements
2. Manage announcement targeting (community, building, unit)
3. Enable and manage resident suggestions
4. Enable and manage resident complaints
5. Maintain community directory of services and contacts
6. Track communication analytics (open rates, engagement)

**Key tasks per activity:**
1. Create announcements
   - Write announcement title and body (EN/AR)
   - Set target audience (all, by community, by building, by unit)
   - Set publish date/time (immediate or scheduled)
   - Attach files or images
   - Mark as urgent/priority

2. Manage targeting
   - Filter recipients by community, building, floor, unit
   - Preview recipient count before sending
   - Resend to new recipients

3. Resident suggestions
   - Resident submits suggestion with category and description
   - Optional anonymous submission
   - Manager reviews, responds, and updates status
   - Community voting on suggestions

4. Resident complaints
   - Resident submits formal complaint
   - Auto-route by category and severity
   - Manager investigates, responds, and resolves
   - SLA tracking on complaint resolution
   - Trend analysis on recurring issues

5. Community directory
   - Manager creates directory categories (facilities, services, emergency contacts)
   - Add entries with name, contact, hours, location
   - Resident searches directory
   - Directory linked to facility records

6. Analytics
   - Announcement delivery and read rates
   - Complaint and suggestion volume over time

**Cross-domain dependencies:**
- Properties: announcement targeting uses community/building/unit hierarchy
- Contacts: resident recipients
- Service Requests: complaints may spawn service requests
- Facilities: directory entries link to configured facilities

**Estimated PRD + story count:** 1 PRD, ~12 stories

---

## 8. Admin (area:admin)

**Purpose:** Manage the administrative layer of the platform: admin user accounts, account subscriptions, lead management, RBAC roles and permissions, and audit logging.

**Primary personas:** Property Manager/Admin (super-admin role)

**Note:** RBAC stories #110-#117 are shipped. Audit Log PRD #130 and story #131 are filed and paused. This skeleton focuses on the remaining gaps in admin.

**Backbone activities:**
1. Manage admin user accounts (RBAC — shipped)
2. Define and assign roles and permissions (RBAC — shipped)
3. Audit log: view who changed what (PRD #130 — paused, stories partially filed)
4. Manage account subscriptions (AccountMembership / AccountSubscription)
5. Manage leads (Lead / LeadSource)
6. Manage owner registrations and approvals
7. Manage platform-wide system settings

**Key tasks per activity:**
1-3. (Already covered by RBAC PRD #109 stories + Audit PRD #130 — not re-listed)

4. Account subscriptions
   - View active subscription plan and usage
   - Upgrade or downgrade plan
   - View billing history
   - Add/remove tenant seats

5. Leads management
   - Capture leads from external sources (LeadSource)
   - Import leads via Excel
   - View leads list with status tracking
   - Convert lead to owner/contact
   - Import error review (LeadsImportErrors page exists)

6. Owner registrations
   - Review owner registration requests
   - Approve or reject with reason
   - Assign owner to unit(s)

7. System settings
   - Configure platform-wide settings (SystemSetting)
   - Manage working days
   - Configure currency and regional defaults

**Cross-domain dependencies:**
- Auth: admin authentication, 2FA, session management
- All domains: RBAC enforcement touches every domain
- Contacts: lead conversion creates Owner/Resident records
- Accounting: subscription billing
- Settings: overlap with platform-wide configuration

**Estimated PRD + story count:** 1 PRD (admin ops — excluding already-shipped RBAC), ~10 stories

---

## 9. Reports (area:reports)

**Purpose:** Provide property managers and finance teams with pre-built operational reports, financial summaries, and a Power BI integration for enterprise analytics.

**Primary personas:** Property Manager/Admin, Accounting Manager

**New persona flagged:** Data Analyst — specifically for Power BI integration (enterprise enterprise clients); not a day-to-day platform user.

**Backbone activities:**
1. Generate and view financial reports (income, expenses, arrears, VAT)
2. Generate and view property reports (occupancy, vacancy, lease expiry)
3. Generate and view operational reports (service requests, maintenance)
4. Export reports to Excel/PDF
5. Schedule automated report delivery
6. Create custom report templates
7. Connect Power BI via API credentials
8. Use pre-built Power BI dashboards

**Key tasks per activity:**
1. Financial reports
   - Select report type and date range
   - Filter by community/building/unit
   - View income statement, expense breakdown, arrears aging
   - View VAT report and collection summary

2. Property reports
   - Occupancy and vacancy analysis
   - Lease expiry pipeline
   - Unit inventory and turnover

3. Operational reports
   - Service request volumes and resolution times
   - SLA compliance rates
   - Facility booking utilization

4. Export
   - Download as PDF or Excel
   - Configure export columns and layout

5. Schedule delivery
   - Set frequency (daily, weekly, monthly)
   - Add recipient email addresses
   - Configure filters for automated report

6. Custom templates
   - Select metrics and dimensions
   - Save as reusable template
   - Share with team

7. Power BI connection
   - Generate API key and secret with scope
   - Revoke credentials
   - View API usage logs

8. Power BI dashboards
   - Browse pre-built report templates
   - Configure refresh schedule
   - Apply row-level security by tenant

**Cross-domain dependencies:**
- Accounting: primary data source for financial reports
- Leasing: lease data for occupancy and expiry reports
- Properties: unit and community dimensions
- Service Requests: operational data
- Facilities: booking utilization data
- Auth: API key generation for Power BI (touches auth layer)

**Estimated PRD + story count:** 2 PRDs (1 system reports + 1 Power BI), ~14 stories total

---

## 10. Settings (area:settings)

**Purpose:** Provide administrators with the configuration layer for company profile, branding, regional preferences, form templates, and domain-specific settings that control system behavior.

**Primary personas:** Property Manager/Admin

**Backbone activities:**
1. Configure company profile and branding
2. Configure regional and localization settings
3. Manage contract type settings
4. Manage invoice and payment settings
5. Manage bank details
6. Configure form templates (FormTemplate)
7. Configure app appearance (white-label)

**Key tasks per activity:**
1. Company profile
   - Enter company name (EN/AR), registration and VAT numbers
   - Upload logo and secondary Arabic logo
   - Set primary/secondary brand colors
   - Configure default document header/footer

2. Regional settings
   - Set default currency (Currency model)
   - Set country and timezone defaults
   - Configure date format preferences

3. Contract types
   - Define contract type names (residential, commercial, retail)
   - Set default terms per contract type
   - Activate/deactivate contract types

4. Invoice and payment settings
   - Configure invoice numbering and prefix
   - Set payment terms defaults
   - Configure late payment penalty rules

5. Bank details
   - Add bank account records
   - Set display preference on invoices

6. Form templates
   - Create and manage form template definitions (FormTemplate)
   - Assign templates to contract types or service request categories

7. App appearance
   - Set tenant-specific color scheme and logo
   - Configure sidebar and navigation labels

**Cross-domain dependencies:**
- All domains consume Settings for configuration values
- Accounting reads invoice and bank settings
- Leasing reads contract type settings
- Service Requests reads category and SLA settings
- Admin reads system-wide settings (partial overlap with SystemSetting)

**Estimated PRD + story count:** 1 PRD, ~10 stories

---

## 11. Auth (area:auth)

**Purpose:** Handle all authentication flows — login, registration, password management, 2FA, session security, and the profile self-service — powered by Laravel Fortify.

**Primary personas:** All platform users (Admin, Property Manager, Resident, Unit Owner)

**Note:** RBAC enforcement layer (story #112) is shipped. Auth here means the authentication flows themselves, not authorization policy.

**Backbone activities:**
1. User login (email/password)
2. User registration and onboarding (new tenant onboarding flow)
3. Password reset (forgot password)
4. Email verification
5. Two-factor authentication (TOTP/QR codes/recovery codes)
6. Password confirmation for sensitive actions
7. User profile self-service (name, email, password, avatar)
8. Session and security management

**Key tasks per activity:**
1. Login
   - Enter credentials and authenticate
   - Handle invalid credentials with clear error
   - Remember-me session persistence

2. Registration
   - Create account with name, email, password
   - Accept terms and privacy policy

3. Password reset
   - Request reset via email
   - Receive time-limited reset link
   - Set new password

4. Email verification
   - Receive verification email on registration
   - Re-send verification link

5. Two-factor authentication
   - Enable 2FA via authenticator app (QR code + TOTP)
   - Generate and store recovery codes
   - Challenge at login when 2FA enabled
   - Disable 2FA (with password confirmation)

6. Password confirmation
   - Re-confirm password before sensitive actions (e.g., disabling 2FA)

7. Profile self-service
   - Update display name and email
   - Change password
   - Upload avatar

8. Session management
   - View active sessions (browser + device)
   - Revoke specific sessions
   - Log out all sessions

**Cross-domain dependencies:**
- Admin: admin accounts and roles are created here; RBAC enforcement enforces what auth users can do
- All domains: every page is protected by auth middleware
- Settings: login page uses company branding from Settings

**Estimated PRD + story count:** 1 PRD, ~10 stories

---

## 12. Visitor Access (area:visitor-access)

**Purpose:** Allow residents to pre-register expected visitors, generate access codes/QR codes, and enable security staff to check visitors in and out, with a complete visitor history.

**Primary personas:** Resident, Property Manager/Admin

**New persona flagged:** Security Guard / Gate Officer — the person performing physical visitor check-in and check-out at the entry point. Not in current memory seed.

**Backbone activities:**
1. Pre-register an expected visitor (resident)
2. Generate and share visitor access code/QR code
3. Check in a visitor at the gate (security)
4. Check out a visitor when they leave (security)
5. View visitor history (resident and manager)
6. Manage visitor access settings (manager)

**Key tasks per activity:**
1. Pre-register visitor
   - Enter visitor name, phone, and purpose of visit
   - Set visit date and expected arrival window
   - Generate access code automatically
   - Share code via SMS or WhatsApp

2. Generate access code
   - System generates unique QR code tied to visitor record
   - Code expires after visit date
   - Code encodes visitor identity for scan

3. Check in visitor
   - Scan QR code or enter access code manually
   - Verify against pre-registration
   - Record check-in timestamp
   - Notify resident of visitor arrival

4. Check out visitor
   - Scan QR code or enter visitor ID
   - Record check-out timestamp
   - Calculate duration of visit

5. Visitor history
   - Resident views their own visitor history
   - Manager views all visitor history by community/building
   - Filter by date, unit, visitor name
   - Export visitor log for security audit

6. Settings
   - Configure access code expiry rules
   - Set maximum concurrent visitors per unit
   - Enable/disable auto-notification to resident

**Cross-domain dependencies:**
- Properties: community and unit context for access eligibility
- Contacts: Resident record as host
- Communication: arrival notifications to resident

**Estimated PRD + story count:** 1 PRD, ~8 stories

---

## 13. Documents (area:documents)

**Purpose:** Infrastructure domain — manages document templates, generated documents (lease contracts, booking contracts, invoices, receipts), digital signature, and Excel import/export. NO standalone end-user document center UI. Documents surface inside their parent feature (signed lease appears in the Leasing tab; invoice appears in Accounting). The PRD describes template/generation/storage infrastructure consumed by Leasing, Facilities, and Accounting.

**Primary personas:** Property Manager/Admin (configuration only; generation is triggered from parent domains)

**Backbone activities:**
1. Manage document templates (lease, booking, invoice, receipt)
2. Generate documents from templates with variable substitution
3. Send documents for digital signature
4. Store and retrieve signed documents
5. Manage Excel import templates (ExcelSheet model)
6. Export data to Excel across domains

**Key tasks per activity:**
1. Manage templates
   - Upload or create document template (Word/HTML)
   - Define merge fields (placeholders) for auto-fill
   - Version templates
   - Assign template to document type (lease, booking, invoice)
   - Set EN/AR language variants

2. Generate documents
   - Trigger generation from Leasing, Facilities, Accounting context
   - Pre-fill all merge fields automatically
   - Preview generated document before sending

3. Digital signature
   - Send document to recipient email
   - Recipient signs on mobile or desktop
   - OTP verification at time of signing
   - Record signature timestamp and IP
   - Deliver signed copy to both parties

4. Store and retrieve
   - Attach signed documents to source records (lease, booking)
   - Download original and signed versions
   - Audit trail of document history

5. Excel import templates
   - Define expected column structure per import type
   - Make available for download before import
   - Track ExcelSheet import history and error logs

6. Export data
   - Export any list (units, residents, transactions) to Excel
   - Apply active filters to export scope

**Cross-domain dependencies:**
- Leasing: lease contracts generated and stored here
- Facilities: booking contracts
- Accounting: invoices and receipts
- Properties: bulk import template management
- Admin: leads import uses ExcelSheet

**Estimated PRD + story count:** 1 PRD, ~10 stories

---

## 14. Contacts (area:contacts)

**Purpose:** Manage all human entities in the platform — Residents (tenants), Unit Owners, and Professionals (vendors, contractors) — as reusable contact records shared across Leasing, Marketplace, Service Requests, and Accounting.

**Primary personas:** Property Manager/Admin

**Backbone activities:**
1. Manage Resident (tenant) contacts
2. Manage Unit Owner contacts
3. Manage Dependent contacts (family members under a resident)
4. Manage Professional contacts (vendors, contractors, service providers)
5. Import contacts from Excel
6. View contact activity history across domains
7. Manage contact lifecycle (active, inactive, archived)

**Key tasks per activity:**
1. Manage Residents
   - Create resident profile (name, phone, email, ID details)
   - Attach resident to a unit/lease
   - Upload KYC documents
   - Track residency history

2. Manage Owners
   - Create owner profile with ownership details
   - Assign units owned
   - View owner financial summary (income from leases)
   - Owner portal access configuration

3. Manage Dependents
   - Add family member linked to a resident
   - Set relationship type (spouse, child, parent)
   - Track dependents on lease record

4. Manage Professionals
   - Create professional profile (company name, specialty, contact)
   - Assign to service request categories
   - Rate and review professionals

5. Import contacts
   - Upload Excel with resident, owner, or professional data
   - Map columns, validate, and import
   - View import errors

6. Contact activity history
   - View all leases, service requests, transactions, and visits linked to a contact
   - Cross-domain 360-degree view of a contact

7. Contact lifecycle
   - Archive inactive contacts
   - Merge duplicate contact records
   - Reactivate archived contacts

**Cross-domain dependencies:**
- Leasing: residents and owners are parties to leases
- Marketplace: customers/buyers are contacts
- Service Requests: resident submitting requests
- Accounting: payer/payee on transactions
- Facilities: resident making bookings
- Visitor Access: resident as host
- Admin: owner registration workflow creates contacts

**Estimated PRD + story count:** 1 PRD, ~12 stories

---

## Roll-Up Summary Table

| Domain | PRDs (est) | Stories (est) | Highest-priority area | Key dependency |
|---|---|---|---|---|
| Properties | 1 | 12 | Unit lifecycle and bulk import | None (foundational) |
| Leasing | 1 | 18 | Quotes workflow + move-out | Properties, Contacts, Accounting |
| Marketplace | 1 | 14 | Listing + visit + offer | Properties, Leasing, Contacts |
| Facilities | 1 | 12 | Booking + contracts | Properties, Contacts, Accounting |
| Service Requests | 1 | 15 | Request lifecycle + SLA | Properties, Contacts, Accounting |
| Accounting | 1 | 16 | Transaction recording + schedules | Leasing, Properties, Contacts, Settings |
| Communication | 1 | 12 | Announcements + complaints | Properties, Contacts |
| Admin | 1 | 10 | Subscriptions + leads (RBAC shipped) | Auth, Contacts |
| Reports | 2 | 14 | Financial + operational reports | Accounting, Leasing, Properties |
| Settings | 1 | 10 | Company profile + contract types | All domains (consumed everywhere) |
| Auth | 1 | 10 | 2FA + session management | Admin, Settings |
| Visitor Access | 1 | 8 | Pre-registration + check-in | Properties, Contacts |
| Documents | 1 | 10 | Template management + e-signature | Leasing, Facilities, Accounting |
| Contacts | 1 | 12 | Resident + owner + professional mgmt | All domains (referenced everywhere) |
| **TOTAL** | **15** | **163** | | |

---

## Cross-Domain Entanglement Analysis

Domains with the highest cross-area coupling (sequence together in Phase 2):

1. **Leasing** — writes to Properties (unit status), Accounting (schedules), Documents (contracts), Contacts (parties). No other domain has more outbound write dependencies. Must sequence after Properties and Contacts are PRD-ready.

2. **Accounting** — reads from Leasing, Properties, Contacts; feeds Reports and Settings. Financial correctness requires upstream domains to be stable first. Couples tightly with Leasing (transaction schedules) and Contacts (payer identity).

3. **Contacts** — referenced as a foreign-key dependency by nearly every domain (Leasing, Marketplace, Service Requests, Accounting, Facilities, Visitor Access). Changes to Contact identity model ripple everywhere. Should be PRD-defined early to lock the entity contract.

---

## Confirmed Personas (Phase 2 Batch 1 decisions)

- **Service Technician / Field Agent** (Service Requests domain) — CONFIRMED. Assigned person who executes on-site work; sees only their own queue; mobile-optimized view needed.
- **Security Guard / Gate Officer** (Visitor Access domain) — CONFIRMED. Performs physical check-in/check-out at entry point.
- **Data Analyst** (Reports domain) — CONFIRMED. Enterprise Power BI user; platform configuration role only.
- **Accounting Manager** — FOLDED INTO Property Manager/Admin. The RBAC `accountingManagers` sub-type is implementation detail; story-level treats them as one persona. Do not split.

## Note on Accounting Manager persona
Per Phase 2 Batch 1 user decision: Accounting Manager = Property Manager/Admin at the story level. Do not create separate user story actors or persona profiles for Accounting Manager. The RBAC sub-type distinction is an authorization detail for the Tech Lead and Engineer, not a product persona.

---

## Recommended Phase 2 Sequencing

Priority order for drafting PRDs:

1. **Contacts** — foundational entity used by every domain; lock the contract first so Leasing, Marketplace, and others can reference it cleanly.
2. **Properties** — the other foundational entity; all physical context hangs off this. Already partially implemented (full CRUD pages exist) so the PRD focuses on gaps: lifecycle management, bulk import improvements, media management.
3. **Leasing** — highest business value; the core revenue flow. Depends on Contacts + Properties being defined.
4. **Accounting** — directly tied to Leasing (transaction schedules activate on lease start). High revenue-protection value.
5. **Service Requests** — high resident satisfaction impact; partially implemented (CRUD pages exist); SLA and neighbourhood services are the key gaps.
6. **Marketplace** — pre-leasing funnel; can run in parallel with Leasing PRD since they share only the conversion handoff point.
7. **Facilities** — contained domain; booking contracts add complexity but the core flow is straightforward.
8. **Visitor Access** — contained, low dependency; good for parallel delivery with Facilities.
9. **Communication** — announcements page exists; complaints and directory are the gaps.
10. **Documents** — template and e-signature infrastructure enables Leasing and Facilities to complete. Can be defined in parallel with Leasing.
11. **Settings** — foundational config; partially implemented; gaps are contract types and form templates.
12. **Auth** — Fortify is running; PRD captures 2FA and session management gaps.
13. **Admin** — RBAC shipped; remaining work is subscriptions and leads; lower urgency.
14. **Reports** — requires upstream data from Accounting, Leasing, Properties; sequence last.

---

## Phase 2 Progress

### Batch 1 — Filed 2026-04-24
| # | PRD Title | Issue | Area |
|---|---|---|---|
| 1 | Contacts — Resident, Owner & Professional Entity Management | [#132](https://github.com/mabumusa1/facilities-management/issues/132) | area:contacts |
| 2 | Properties — Community, Building & Unit Lifecycle Management | [#133](https://github.com/mabumusa1/facilities-management/issues/133) | area:properties |
| 3 | Leasing — Quote, Contract & Lease Lifecycle Management | [#134](https://github.com/mabumusa1/facilities-management/issues/134) | area:leasing |
| 4 | Accounting — Transaction Recording, Invoicing & Payment Schedules | [#135](https://github.com/mabumusa1/facilities-management/issues/135) | area:accounting |
| 5 | Service Requests — Resident Maintenance Flow, SLA Tracking & Field Assignment | [#136](https://github.com/mabumusa1/facilities-management/issues/136) | area:service-requests |

### Batch 2 — Filed 2026-04-24
| # | PRD Title | Issue | Area |
|---|---|---|---|
| 1 | Documents — Template Management, Document Generation & E-Signature Infrastructure | [#137](https://github.com/mabumusa1/facilities-management/issues/137) | area:documents |
| 2 | Marketplace — Unit Listings, Offers & Visit Scheduling | [#138](https://github.com/mabumusa1/facilities-management/issues/138) | area:marketplace |
| 3 | Facilities — Facility Configuration, Booking Lifecycle & Booking Contracts | [#139](https://github.com/mabumusa1/facilities-management/issues/139) | area:facilities |
| 4 | Communication — Announcements, Complaints, Suggestions & Community Directory | [#140](https://github.com/mabumusa1/facilities-management/issues/140) | area:communication |
| 5 | Visitor Access — Pre-Registration, QR Code Access & Gate Check-In/Check-Out | [#141](https://github.com/mabumusa1/facilities-management/issues/141) | area:visitor-access |

**Key decisions from Batch 2:**
- Documents filed first (not last) because Leasing (#134) contract stories and Facilities (#139) booking contract stories are hard-blocked on Documents infrastructure. Documents is a prerequisite, not a follow-on.
- Facilities (#139) has an explicit hard dependency on Documents (#137) — booking contract generation and e-signature cannot be implemented until Documents template management and signature flow stories land.
- Marketplace (#138) conversion-to-Leasing handoff introduces a new interface contract: Marketplace triggers quote creation but does not own the Leasing quote object. This interface must be defined at story breakdown.
- Communication (#140) complaints vs. Service Requests boundary is an open question — a decision rule is needed before story breakdown so stories are filed in the correct domain.
- Visitor Access (#141) Gate Officer RBAC: a new gate-officer role sub-type may be required. Tech Lead to assess against existing RBAC structure (#109) at story breakdown.
- Visitor Access is fully self-contained — no dependency on Documents, Marketplace, or Facilities — making it a strong parallel delivery candidate.

### Batch 3 — Filed 2026-04-24 (PHASE 2 COMPLETE)
| # | PRD Title | Issue | Area |
|---|---|---|---|
| 1 | Settings — Company Profile, Contract Types, Invoice Configuration & Form Templates | [#142](https://github.com/mabumusa1/facilities-management/issues/142) | area:settings |
| 2 | Auth — Login, 2FA, Session Management & Profile Self-Service | [#143](https://github.com/mabumusa1/facilities-management/issues/143) | area:auth |
| 3 | Admin — Account Subscriptions, Leads Management & Owner Registration Workflow | [#144](https://github.com/mabumusa1/facilities-management/issues/144) | area:admin |
| 4 | Reports — Built-In System Reports, Financial Summaries & Operational Analytics | [#145](https://github.com/mabumusa1/facilities-management/issues/145) | area:reports |
| 5 | Reports — Power BI Integration, API Credentials & Enterprise BI Connector | [#146](https://github.com/mabumusa1/facilities-management/issues/146) | area:reports |

**Key decisions from Batch 3:**
- InvoiceSetting/ServiceSetting domain ownership resolved: **Settings (#142) owns both configuration objects**. Accounting (#135) reads them. Comment added to PRD #135.
- Social login (OAuth/Socialite) is explicitly out of scope for Phase 2 Auth — decision recorded in PRD #143 to prevent resurface.
- Mandatory 2FA enforcement (admin policy) is out of scope for Auth; it is a future Admin feature — noted in both PRDs.
- Admin PRD (#144) is scoped to subscriptions, leads, and owner registration only. RBAC (#109 shipped) and Audit Log (#130 paused) are explicitly excluded.
- Reports are two separate PRDs (#145 system reports, #146 Power BI) sharing area:reports. Independently deliverable.
- Power BI PRD (#146): live database read (no ETL) in Phase 2. Read replica decision deferred to Tech Lead.

---

## Phase 2 Complete — Full PRD Roll-Up

| # | Issue | PRD Title | Area | Est. Stories |
|---|---|---|---|---|
| 1 | [#132](https://github.com/mabumusa1/facilities-management/issues/132) | Contacts — Resident, Owner & Professional Entity Management | area:contacts | 12 |
| 2 | [#133](https://github.com/mabumusa1/facilities-management/issues/133) | Properties — Community, Building & Unit Lifecycle Management | area:properties | 12 |
| 3 | [#134](https://github.com/mabumusa1/facilities-management/issues/134) | Leasing — Quote, Contract & Lease Lifecycle Management | area:leasing | 18 |
| 4 | [#135](https://github.com/mabumusa1/facilities-management/issues/135) | Accounting — Transaction Recording, Invoicing & Payment Schedules | area:accounting | 16 |
| 5 | [#136](https://github.com/mabumusa1/facilities-management/issues/136) | Service Requests — Resident Maintenance Flow, SLA Tracking & Field Assignment | area:service-requests | 15 |
| 6 | [#137](https://github.com/mabumusa1/facilities-management/issues/137) | Documents — Template Management, Document Generation & E-Signature Infrastructure | area:documents | 10 |
| 7 | [#138](https://github.com/mabumusa1/facilities-management/issues/138) | Marketplace — Unit Listings, Offers & Visit Scheduling | area:marketplace | 14 |
| 8 | [#139](https://github.com/mabumusa1/facilities-management/issues/139) | Facilities — Facility Configuration, Booking Lifecycle & Booking Contracts | area:facilities | 12 |
| 9 | [#140](https://github.com/mabumusa1/facilities-management/issues/140) | Communication — Announcements, Complaints, Suggestions & Community Directory | area:communication | 12 |
| 10 | [#141](https://github.com/mabumusa1/facilities-management/issues/141) | Visitor Access — Pre-Registration, QR Code Access & Gate Check-In/Check-Out | area:visitor-access | 8 |
| 11 | [#142](https://github.com/mabumusa1/facilities-management/issues/142) | Settings — Company Profile, Contract Types, Invoice Configuration & Form Templates | area:settings | 10 |
| 12 | [#143](https://github.com/mabumusa1/facilities-management/issues/143) | Auth — Login, 2FA, Session Management & Profile Self-Service | area:auth | 10 |
| 13 | [#144](https://github.com/mabumusa1/facilities-management/issues/144) | Admin — Account Subscriptions, Leads Management & Owner Registration Workflow | area:admin | 10 |
| 14 | [#145](https://github.com/mabumusa1/facilities-management/issues/145) | Reports — Built-In System Reports, Financial Summaries & Operational Analytics | area:reports | 12 |
| 15 | [#146](https://github.com/mabumusa1/facilities-management/issues/146) | Reports — Power BI Integration, API Credentials & Enterprise BI Connector | area:reports | 9 |
| **TOTAL** | | | | **~180** |

*Phase 2 complete. All 15 PRDs filed. Phase 3 (story breakdown) begins next.*

---

*Phase 2 Batch 3 PRDs filed 2026-04-24. Phase 2 complete.*

---

## Phase 3 Progress — Story Breakdown

### Session 1 — Filed 2026-04-24

#### PRD #132 — Contacts (11 stories: #147–#157)

| # | Issue | Story Title |
|---|---|---|
| 1 | [#147](https://github.com/mabumusa1/facilities-management/issues/147) | Contact data model — Resident, Owner, Dependent, and Professional entities |
| 2 | [#148](https://github.com/mabumusa1/facilities-management/issues/148) | Create and search Resident contacts with duplicate detection |
| 3 | [#149](https://github.com/mabumusa1/facilities-management/issues/149) | Attach and manage KYC documents on a Resident contact |
| 4 | [#150](https://github.com/mabumusa1/facilities-management/issues/150) | Create and manage Unit Owner contacts with unit ownership assignments |
| 5 | [#151](https://github.com/mabumusa1/facilities-management/issues/151) | Create and manage Dependent contacts linked to a Resident |
| 6 | [#152](https://github.com/mabumusa1/facilities-management/issues/152) | Create and manage Professional contacts with service category assignments |
| 7 | [#153](https://github.com/mabumusa1/facilities-management/issues/153) | Bulk import contacts from Excel (Residents, Owners, or Professionals) |
| 8 | [#154](https://github.com/mabumusa1/facilities-management/issues/154) | View 360-degree activity history for any contact |
| 9 | [#155](https://github.com/mabumusa1/facilities-management/issues/155) | Archive, merge duplicate, and reactivate contact records |
| 10 | [#156](https://github.com/mabumusa1/facilities-management/issues/156) | Contacts list — search, filter by type and status, and export to Excel |
| 11 | [#157](https://github.com/mabumusa1/facilities-management/issues/157) | Migrate existing resident and owner records to the unified Contacts data model |

#### PRD #133 — Properties (11 stories: #158–#168)

| # | Issue | Story Title |
|---|---|---|
| 1 | [#158](https://github.com/mabumusa1/facilities-management/issues/158) | Unit lifecycle state machine — valid status transitions and enforcement |
| 2 | [#159](https://github.com/mabumusa1/facilities-management/issues/159) | View unit status history — who changed status, when, and why |
| 3 | [#160](https://github.com/mabumusa1/facilities-management/issues/160) | Bulk import units from Excel with column mapping and row-level validation |
| 4 | [#161](https://github.com/mabumusa1/facilities-management/issues/161) | Review import errors and re-attempt failed rows after unit bulk import |
| 5 | [#162](https://github.com/mabumusa1/facilities-management/issues/162) | Upload, reorder, and set primary photo per community, building, and unit |
| 6 | [#163](https://github.com/mabumusa1/facilities-management/issues/163) | Global unit search and filter across the portfolio |
| 7 | [#164](https://github.com/mabumusa1/facilities-management/issues/164) | Export filtered unit list to Excel |
| 8 | [#165](https://github.com/mabumusa1/facilities-management/issues/165) | Complete community metadata management — amenities, working days, and map coordinates |
| 9 | [#166](https://github.com/mabumusa1/facilities-management/issues/166) | Complete building metadata management — floors, construction year, and documents |
| 10 | [#167](https://github.com/mabumusa1/facilities-management/issues/167) | Bulk status change — mark multiple units as under-maintenance or available in one action |
| 11 | [#168](https://github.com/mabumusa1/facilities-management/issues/168) | Unit detail page — complete unit metadata management (type, specs, amenities, pricing reference) |

### Session 2 — Filed 2026-04-24

#### PRD #134 — Leasing (15 stories: #169–#184; GitHub skipped #174)

| # | Issue | Story Title |
|---|---|---|
| 1 | [#169](https://github.com/mabumusa1/facilities-management/issues/169) | Quote data model and status machine |
| 2 | [#170](https://github.com/mabumusa1/facilities-management/issues/170) | Create and send a leasing quote to a prospect |
| 3 | [#171](https://github.com/mabumusa1/facilities-management/issues/171) | Revise, re-send, and expire quotes |
| 4 | [#172](https://github.com/mabumusa1/facilities-management/issues/172) | Convert accepted quote to lease application with KYC checklist |
| 5 | [#173](https://github.com/mabumusa1/facilities-management/issues/173) | Lease application approval workflow |
| 6 | [#175](https://github.com/mabumusa1/facilities-management/issues/175) | Lease activation: unit assignment, status change, and Accounting trigger (**cross-domain seam**) |
| 7 | [#176](https://github.com/mabumusa1/facilities-management/issues/176) | Generate lease contract and send for e-signature |
| 8 | [#177](https://github.com/mabumusa1/facilities-management/issues/177) | Tenancy management: escalation rules and additional fees |
| 9 | [#178](https://github.com/mabumusa1/facilities-management/issues/178) | Amend lease terms with history trail |
| 10 | [#179](https://github.com/mabumusa1/facilities-management/issues/179) | Send tenant notices from the lease record |
| 11 | [#180](https://github.com/mabumusa1/facilities-management/issues/180) | Create and manage subleases |
| 12 | [#181](https://github.com/mabumusa1/facilities-management/issues/181) | Move-out workflow: inspection and deposit deductions |
| 13 | [#182](https://github.com/mabumusa1/facilities-management/issues/182) | Move-out settlement: process refund or charge and release unit |
| 14 | [#183](https://github.com/mabumusa1/facilities-management/issues/183) | Generate and track lease renewal offers |
| 15 | [#184](https://github.com/mabumusa1/facilities-management/issues/184) | Lease pipeline view, expiry alerts, and bulk export |

#### PRD #135 — Accounting (14 stories: #185–#198)

| # | Issue | Story Title |
|---|---|---|
| 1 | [#185](https://github.com/mabumusa1/facilities-management/issues/185) | Transaction category configuration |
| 2 | [#186](https://github.com/mabumusa1/facilities-management/issues/186) | Record money-in transactions with auto-generated receipt |
| 3 | [#187](https://github.com/mabumusa1/facilities-management/issues/187) | Record money-out transactions with category and document attachment |
| 4 | [#188](https://github.com/mabumusa1/facilities-management/issues/188) | Auto-create transaction schedule when lease is activated (**cross-domain seam**) |
| 5 | [#189](https://github.com/mabumusa1/facilities-management/issues/189) | Manager review and confirm generated transaction schedule |
| 6 | [#190](https://github.com/mabumusa1/facilities-management/issues/190) | Generate invoice from schedule or on-demand with InvoiceSetting branding |
| 7 | [#191](https://github.com/mabumusa1/facilities-management/issues/191) | Record full and partial payments against an invoice |
| 8 | [#192](https://github.com/mabumusa1/facilities-management/issues/192) | Overdue invoice detection and automated payment reminder rules |
| 9 | [#193](https://github.com/mabumusa1/facilities-management/issues/193) | Aging report: overdue receivables by age bucket |
| 10 | [#194](https://github.com/mabumusa1/facilities-management/issues/194) | Configure InvoiceSetting: branding, terms, and invoice numbering (read/enforce) |
| 11 | [#195](https://github.com/mabumusa1/facilities-management/issues/195) | Add and manage bank account details; set default per community |
| 12 | [#196](https://github.com/mabumusa1/facilities-management/issues/196) | Transaction reconciliation: mark reconciled and view summary |
| 13 | [#197](https://github.com/mabumusa1/facilities-management/issues/197) | Export transaction history to Excel with active filters |
| 14 | [#198](https://github.com/mabumusa1/facilities-management/issues/198) | Financial summary views: per contact and per unit/community |

### Session 3 — Filed 2026-04-24

#### PRD #137 — Documents (10 stories: #199–#208)

| # | Issue | Story Title |
|---|---|---|
| 1 | [#199](https://github.com/mabumusa1/facilities-management/issues/199) | Document data model: DocumentTemplate, DocumentRecord, DocumentSignature, and DocumentVersion entities |
| 2 | [#200](https://github.com/mabumusa1/facilities-management/issues/200) | Create and version document templates with merge field definitions (admin-authored) |
| 3 | [#201](https://github.com/mabumusa1/facilities-management/issues/201) | Preview a generated document from a template before sending |
| 4 | [#202](https://github.com/mabumusa1/facilities-management/issues/202) | Generate a document from a template and payload (Leasing, Facilities, Accounting consumers) |
| 5 | [#203](https://github.com/mabumusa1/facilities-management/issues/203) | E-signature flow: send for signing, OTP verification, and signature recording |
| 6 | [#204](https://github.com/mabumusa1/facilities-management/issues/204) | Download and retrieve generated and signed documents from parent domain records |
| 7 | [#205](https://github.com/mabumusa1/facilities-management/issues/205) | ExcelSheet import template: define structure, make downloadable, and track import history |
| 8 | [#206](https://github.com/mabumusa1/facilities-management/issues/206) | Bulk data export to Excel from any list view with active filter scope |
| 9 | [#207](https://github.com/mabumusa1/facilities-management/issues/207) | Document template format: Word upload vs. in-platform editor (Tech Lead decision gate) |
| 10 | [#208](https://github.com/mabumusa1/facilities-management/issues/208) | Historical version pinning: audit trail of template versions used for each generated document |

#### PRD #136 — Service Requests (15 stories: #209–#223; #222 is data model — implement first)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#222](https://github.com/mabumusa1/facilities-management/issues/222) | SR data model: ServiceRequest, ServiceRequestMessage, and supporting entities | **1st** |
| 2 | [#209](https://github.com/mabumusa1/facilities-management/issues/209) | Service category and SLA configuration (admin) | 2nd |
| 3 | [#210](https://github.com/mabumusa1/facilities-management/issues/210) | Resident submits a service request with category, location, photos, and urgency | 3rd |
| 4 | [#211](https://github.com/mabumusa1/facilities-management/issues/211) | Admin triages incoming requests, assigns to technician, and sets priority | 4th |
| 5 | [#212](https://github.com/mabumusa1/facilities-management/issues/212) | Technician views work queue and accepts or declines assigned requests (mobile-optimized) | 5th |
| 6 | [#213](https://github.com/mabumusa1/facilities-management/issues/213) | Technician updates status, adds completion notes and photos, and closes the request | 6th |
| 7 | [#214](https://github.com/mabumusa1/facilities-management/issues/214) | SLA tracking: overdue detection, breach flags, and escalation alerts | parallel to 4–6 |
| 8 | [#215](https://github.com/mabumusa1/facilities-management/issues/215) | Resident rates completed request and provides feedback | 7th |
| 9 | [#216](https://github.com/mabumusa1/facilities-management/issues/216) | Create a Service Request from a Complaint (Option B manual conversion with back-link) | after Communication complaint story |
| 10 | [#217](https://github.com/mabumusa1/facilities-management/issues/217) | Bulk admin view: search, filter, and sort all requests across communities | after #210–#215 |
| 11 | [#218](https://github.com/mabumusa1/facilities-management/issues/218) | Configure home services catalog: featured services with pricing and provider details | parallel to #210+ |
| 12 | [#219](https://github.com/mabumusa1/facilities-management/issues/219) | Neighbourhood services: resident reports common-area issues and manager resolves | parallel to #210+ |
| 13 | [#220](https://github.com/mabumusa1/facilities-management/issues/220) | SR detail page: full timeline and all-parties communication thread | after #210–#215 |
| 14 | [#221](https://github.com/mabumusa1/facilities-management/issues/221) | Resident re-submits or follows up on a closed request | after #213, #215 |
| 15 | [#223](https://github.com/mabumusa1/facilities-management/issues/223) | SR operational reporting: volume, resolution time, SLA compliance, and technician performance basics | last |

### Session 4 — Filed 2026-04-24

#### PRD #142 — Settings (11 stories: #224–#234; #234 is data model — implement first)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#234](https://github.com/mabumusa1/facilities-management/issues/234) | Settings data model and seed: AppSettings, InvoiceSetting, ServiceSetting, ContractType, FormTemplate entities | **1st** |
| 2 | [#224](https://github.com/mabumusa1/facilities-management/issues/224) | Settings ownership matrix: define which domain owns which configuration object (TL decision gate) | 2nd |
| 3 | [#225](https://github.com/mabumusa1/facilities-management/issues/225) | Company profile: name, logo, VAT number, timezone, and brand configuration | 3rd |
| 4 | [#226](https://github.com/mabumusa1/facilities-management/issues/226) | Contract types: define, activate, and manage leasing contract categories | 3rd (unblocks Leasing #169) |
| 5 | [#227](https://github.com/mabumusa1/facilities-management/issues/227) | InvoiceSetting: invoice branding, numbering, payment terms, and late penalty rules | 4th (unblocks Accounting #190, #194) |
| 6 | [#228](https://github.com/mabumusa1/facilities-management/issues/228) | Regional settings: currency, locale, date format, and working-days defaults | 4th (unblocks SR #214 SLA) |
| 7 | [#229](https://github.com/mabumusa1/facilities-management/issues/229) | Form templates: create and manage reusable form definitions for service requests and contracts | 5th |
| 8 | [#230](https://github.com/mabumusa1/facilities-management/issues/230) | ServiceSetting: configure service fee rules, penalty rates, and home service pricing defaults | 5th |
| 9 | [#231](https://github.com/mabumusa1/facilities-management/issues/231) | Notification preferences: configure platform-wide email and SMS notification templates and triggers | 6th |
| 10 | [#232](https://github.com/mabumusa1/facilities-management/issues/232) | App appearance: white-label logo, sidebar color, and navigation labels | 6th |
| 11 | [#233](https://github.com/mabumusa1/facilities-management/issues/233) | Settings audit trail: log who changed which setting and when | 7th |

#### PRD #143 — Auth (11 stories: #235–#245; #243 is gap audit — implement first)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#243](https://github.com/mabumusa1/facilities-management/issues/243) | Auth data model and Fortify gap audit: identify and close gaps against PRD #143 requirements | **1st** |
| 2 | [#235](https://github.com/mabumusa1/facilities-management/issues/235) | Login flow: email/password authentication with remember-me and post-login redirect | 2nd |
| 3 | [#244](https://github.com/mabumusa1/facilities-management/issues/244) | Logout: single session logout and redirect to login page | 2nd (alongside #235) |
| 4 | [#236](https://github.com/mabumusa1/facilities-management/issues/236) | Password reset: request reset link, set new password, and enforce password policy | 3rd |
| 5 | [#237](https://github.com/mabumusa1/facilities-management/issues/237) | Email verification: verify email on account creation and re-send verification link | 3rd |
| 6 | [#238](https://github.com/mabumusa1/facilities-management/issues/238) | Two-factor authentication: enable TOTP, QR code setup, recovery codes, and login challenge | 4th |
| 7 | [#245](https://github.com/mabumusa1/facilities-management/issues/245) | 2FA recovery codes: regenerate, view count, and low-code warning | 4th (alongside #238) |
| 8 | [#241](https://github.com/mabumusa1/facilities-management/issues/241) | Password confirmation: re-confirm password before sensitive actions (2FA disable, session revoke) | 4th (prerequisite for #238, #239) |
| 9 | [#239](https://github.com/mabumusa1/facilities-management/issues/239) | Session management: view active sessions by device and revoke individual or all sessions | 5th (blocked on TL session driver confirm) |
| 10 | [#240](https://github.com/mabumusa1/facilities-management/issues/240) | Profile self-service: update name, email, phone, locale, avatar, and change password | 5th |
| 11 | [#242](https://github.com/mabumusa1/facilities-management/issues/242) | Admin user management: create, deactivate, and reset credentials for platform users | 6th |

### Session 7 — Filed 2026-04-24

#### PRD #144 — Admin (12 stories: #291–#302; #291 is data model — implement first, #302 is dashboard — implement last)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#291](https://github.com/mabumusa1/facilities-management/issues/291) | Admin data model: AccountSubscription, SubscriptionTier, Lead, LeadSource, and OwnerRegistration entities | **1st** |
| 2 | [#292](https://github.com/mabumusa1/facilities-management/issues/292) | Subscription overview: view current plan, seat usage, and billing period | 2nd |
| 3 | [#293](https://github.com/mabumusa1/facilities-management/issues/293) | Plan change request: Property Manager requests upgrade or downgrade, Operator fulfills | 3rd |
| 4 | [#294](https://github.com/mabumusa1/facilities-management/issues/294) | Billing history: view past subscription invoices and download PDF | 3rd (parallel with #293) |
| 5 | [#295](https://github.com/mabumusa1/facilities-management/issues/295) | Seat management: invite and remove admin users within subscription seat limit | 4th |
| 6 | [#296](https://github.com/mabumusa1/facilities-management/issues/296) | Leads list: view all leads with source and status filters, and add a lead manually | 2nd (parallel track) |
| 7 | [#297](https://github.com/mabumusa1/facilities-management/issues/297) | Lead detail: assign to team member, update status, and log activity notes | after #296 |
| 8 | [#298](https://github.com/mabumusa1/facilities-management/issues/298) | Lead import: upload Excel, preview row-level validation errors, and confirm import | after #296 |
| 9 | [#299](https://github.com/mabumusa1/facilities-management/issues/299) | Lead conversion: convert a qualified lead to an Owner or Resident contact record | after #296, Contacts #148+#150 |
| 10 | [#300](https://github.com/mabumusa1/facilities-management/issues/300) | Owner registration queue: review, approve (create Owner contact + unit link), or reject | after #291, Contacts #150 |
| 11 | [#301](https://github.com/mabumusa1/facilities-management/issues/301) | Platform feature flags: super-admin enables or disables features per tenant | after #291 (TL decision gate) |
| 12 | [#302](https://github.com/mabumusa1/facilities-management/issues/302) | Admin operations dashboard: subscription status, open leads, and pending registrations | **last** |

### Session 5 — Filed 2026-04-24

#### PRD #139 — Facilities (11 stories: #246–#256; #246 is data model — implement first)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#246](https://github.com/mabumusa1/facilities-management/issues/246) | Facilities data model: Facility, FacilityBooking, availability rules, and booking contracts | **1st** |
| 2 | [#247](https://github.com/mabumusa1/facilities-management/issues/247) | Facility configuration CRUD: create, edit, enable/disable facility with rules | 2nd |
| 3 | [#248](https://github.com/mabumusa1/facilities-management/issues/248) | Resident browses and books a facility slot | 3rd |
| 4 | [#249](https://github.com/mabumusa1/facilities-management/issues/249) | Admin calendar view: approve, reject, and manage all bookings | 3rd (parallel with #248) |
| 5 | [#250](https://github.com/mabumusa1/facilities-management/issues/250) | Booking cancellation and refund policy enforcement | after #248 |
| 6 | [#251](https://github.com/mabumusa1/facilities-management/issues/251) | Waitlist: resident joins waitlist when slot is full | after #248 |
| 7 | [#252](https://github.com/mabumusa1/facilities-management/issues/252) | Gate check-in/check-out for facility bookings | after #248 |
| 8 | [#253](https://github.com/mabumusa1/facilities-management/issues/253) | Booking contracts: generate, send for signature, and store (depends on Documents chain) | **last** — requires #199→#207→#200→#202→#203→#204 |
| 9 | [#254](https://github.com/mabumusa1/facilities-management/issues/254) | Booking fee invoice: generate invoice for paid facilities (cross-domain: Accounting) | after #248, requires Accounting #188+#190 |
| 10 | [#255](https://github.com/mabumusa1/facilities-management/issues/255) | My Bookings: resident views their booking history and status | after #248 |
| 11 | [#256](https://github.com/mabumusa1/facilities-management/issues/256) | Facility operational reporting: utilisation, peak hours, revenue, no-show rate | last in Facilities |

#### PRD #141 — Visitor Access (8 stories: #257–#264; #257 is data model — implement first)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#257](https://github.com/mabumusa1/facilities-management/issues/257) | Visitor Access data model: Invitation, VisitorLog, and QR token design | **1st** |
| 2 | [#258](https://github.com/mabumusa1/facilities-management/issues/258) | Resident pre-registers expected visitor and generates access QR code | 2nd |
| 3 | [#259](https://github.com/mabumusa1/facilities-management/issues/259) | Gate Officer view: today's expected visitor queue | 3rd |
| 4 | [#260](https://github.com/mabumusa1/facilities-management/issues/260) | Gate check-in: scan QR or enter code manually, record arrival | 4th |
| 5 | [#261](https://github.com/mabumusa1/facilities-management/issues/261) | Gate check-out: record departure and visit duration | after #260 |
| 6 | [#262](https://github.com/mabumusa1/facilities-management/issues/262) | Walk-in visitor: gate officer registers unannounced guest | after #260 |
| 7 | [#263](https://github.com/mabumusa1/facilities-management/issues/263) | Overstay and expired QR detection and alert | after #260 |
| 8 | [#264](https://github.com/mabumusa1/facilities-management/issues/264) | Visitor log and reporting: manager view of all visits, filter, and export | last |

### Session 6 — Filed 2026-04-24

#### PRD #138 — Marketplace (14 stories: #265–#278; #265 is data model — implement first)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#265](https://github.com/mabumusa1/facilities-management/issues/265) | Marketplace data model: MarketplaceUnit, Offer, Visit, and listing status machine | **1st** |
| 2 | [#266](https://github.com/mabumusa1/facilities-management/issues/266) | Listing eligibility: rules for which units can be listed | 2nd |
| 3 | [#267](https://github.com/mabumusa1/facilities-management/issues/267) | Seller creates and publishes a listing | 3rd |
| 4 | [#268](https://github.com/mabumusa1/facilities-management/issues/268) | Seller manages listings: edit, pause, unpublish, duplicate | after #267 |
| 5 | [#269](https://github.com/mabumusa1/facilities-management/issues/269) | Buyer browses, searches, and views listing detail | after #267 |
| 6 | [#270](https://github.com/mabumusa1/facilities-management/issues/270) | Buyer inquiry creates a Lead in Admin domain | after #269 |
| 7 | [#271](https://github.com/mabumusa1/facilities-management/issues/271) | Seller manages inquiry queue and responds | after #270 |
| 8 | [#272](https://github.com/mabumusa1/facilities-management/issues/272) | Buyer requests a visit; seller confirms appointment | after #270 |
| 9 | [#273](https://github.com/mabumusa1/facilities-management/issues/273) | Seller records visit outcome | after #272 |
| 10 | [#274](https://github.com/mabumusa1/facilities-management/issues/274) | Buyer submits an offer | after #272 |
| 11 | [#275](https://github.com/mabumusa1/facilities-management/issues/275) | Seller accepts, counters, or rejects offer | after #274 |
| 12 | [#276](https://github.com/mabumusa1/facilities-management/issues/276) | Accepted offer triggers Leasing quote creation (cross-domain seam) | after #275 |
| 13 | [#277](https://github.com/mabumusa1/facilities-management/issues/277) | Featured listings: promote and display promoted units | after #267 |
| 14 | [#278](https://github.com/mabumusa1/facilities-management/issues/278) | Marketplace conversion analytics (domain-scoped funnel) | last |

#### PRD #140 — Communication (12 stories: #279–#290; #279 is data model — implement first)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#279](https://github.com/mabumusa1/facilities-management/issues/279) | Communication data model: Announcement, Complaint, Suggestion, DirectoryEntry entities | **1st** |
| 2 | [#280](https://github.com/mabumusa1/facilities-management/issues/280) | Admin creates announcement with scheduling and audience targeting | 2nd |
| 3 | [#281](https://github.com/mabumusa1/facilities-management/issues/281) | Announcement read-receipt tracking | after #280 |
| 4 | [#282](https://github.com/mabumusa1/facilities-management/issues/282) | Resident views announcements feed | after #280 |
| 5 | [#283](https://github.com/mabumusa1/facilities-management/issues/283) | Resident submits complaint | after #279 |
| 6 | [#284](https://github.com/mabumusa1/facilities-management/issues/284) | Admin triages and responds to complaint | after #283 |
| 7 | [#285](https://github.com/mabumusa1/facilities-management/issues/285) | Admin converts complaint to Service Request | after #284, requires SR #216 |
| 8 | [#286](https://github.com/mabumusa1/facilities-management/issues/286) | Complaint analytics: volume, resolution time, recurring issues | after #283+ |
| 9 | [#287](https://github.com/mabumusa1/facilities-management/issues/287) | Resident submits suggestion with community upvote | after #279 |
| 10 | [#288](https://github.com/mabumusa1/facilities-management/issues/288) | Admin reviews and responds to suggestions | after #287 |
| 11 | [#289](https://github.com/mabumusa1/facilities-management/issues/289) | Community directory: admin CRUD for entries and categories | after #279 |
| 12 | [#290](https://github.com/mabumusa1/facilities-management/issues/290) | Community directory: resident browse and search | after #289 |

### Session 8 — Filed 2026-04-24 (PHASE 3 COMPLETE)

#### PRD #145 — Reports-System (11 stories: #303–#313; #303 is data model + domain boundary — implement first, #313 is dashboard — implement last)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#303](https://github.com/mabumusa1/facilities-management/issues/303) | Reports data model, access control, and domain boundary definition | **1st** — TL Q1 (RBAC scope propagation) must be resolved here |
| 2 | [#304](https://github.com/mabumusa1/facilities-management/issues/304) | Financial summary: revenue, collected, and outstanding by period | after #303 |
| 3 | [#305](https://github.com/mabumusa1/facilities-management/issues/305) | Occupancy report: per-community, per-building, and historical trend | after #303 |
| 4 | [#306](https://github.com/mabumusa1/facilities-management/issues/306) | Lease pipeline: expiring leases by month and renewal rate | after #303 |
| 5 | [#307](https://github.com/mabumusa1/facilities-management/issues/307) | VAT return summary: generic tax period report | after #303 |
| 6 | [#308](https://github.com/mabumusa1/facilities-management/issues/308) | Portfolio receivables aging (extends Accounting #193 to cross-community) | after #303, Accounting #193 |
| 7 | [#309](https://github.com/mabumusa1/facilities-management/issues/309) | Property portfolio health: maintenance, off-plan, and occupancy rate | after #303 |
| 8 | [#310](https://github.com/mabumusa1/facilities-management/issues/310) | Report export engine: PDF, Excel, CSV for all report types | after #303 |
| 9 | [#311](https://github.com/mabumusa1/facilities-management/issues/311) | Scheduled reports: generate and email on a cadence | after #310, #312 |
| 10 | [#312](https://github.com/mabumusa1/facilities-management/issues/312) | Async report generation engine: queue-based execution | after #303 |
| 11 | [#313](https://github.com/mabumusa1/facilities-management/issues/313) | Executive dashboard: top-line KPIs from all report types | **last** — after #304–#309 |

**Domain-scoped operational reports (NOT in Reports #145):**
- #223 SR reporting (service-requests domain)
- #256 Facilities reporting (facilities domain)
- #264 Visitor log (visitor-access domain)
- #278 Marketplace analytics (marketplace domain)
- #286 Complaint analytics (communication domain)
- #302 Admin ops dashboard (admin domain)

#### PRD #146 — Reports-PowerBI (9 stories: #314–#322; #314 is TL decision gate — implement first)

| # | Issue | Story Title | Implement order |
|---|---|---|---|
| 1 | [#314](https://github.com/mabumusa1/facilities-management/issues/314) | PowerBI data model decision gate: live DB vs. read replica, OData vs. REST, credential pattern | **1st** — all downstream blocked until TL ADR posted |
| 2 | [#315](https://github.com/mabumusa1/facilities-management/issues/315) | Feature flag enforcement: tenant-level BI access gate | after #314, Admin #301 |
| 3 | [#316](https://github.com/mabumusa1/facilities-management/issues/316) | API credentials: create, rotate, and revoke BI access tokens | after #314, #315 |
| 4 | [#317](https://github.com/mabumusa1/facilities-management/issues/317) | Endpoint catalog and schema discovery: OData service document or REST API spec | after #314, #316 |
| 5 | [#318](https://github.com/mabumusa1/facilities-management/issues/318) | Operational endpoints: Units, Leases, Residents, Service Requests | after #317 |
| 6 | [#319](https://github.com/mabumusa1/facilities-management/issues/319) | Financial endpoints: Invoices, Transactions, Owners (elevated scope) | after #317, #318 |
| 7 | [#320](https://github.com/mabumusa1/facilities-management/issues/320) | Connector UX: connection string, token discovery, and Data Analyst onboarding | after #317 |
| 8 | [#321](https://github.com/mabumusa1/facilities-management/issues/321) | Rate limiting and API call audit log | after #318, #319 |
| 9 | [#322](https://github.com/mabumusa1/facilities-management/issues/322) | Super-admin BI tenant view: enabled tenants and API usage summary | after #314, #321 |

---

## Phase 3 Complete — Full Story Roll-Up

**As of 2026-04-24: Phase 3 backlog buildout COMPLETE.**

| Domain | PRD # | Stories | Issue Range | Notes |
|---|---|---|---|---|
| Contacts | #132 | 11 | #147–#157 | Migration (#157) is highest-risk; last to implement |
| Properties | #133 | 11 | #158–#168 | Unit state machine (#158) is foundational to Marketplace + Leasing |
| Leasing | #134 | 15 | #169–#184 | GH skipped #174; activation (#175) is highest cross-domain risk |
| Accounting | #135 | 14 | #185–#198 | InvoiceSetting read (#194) blocked on Settings #227+#234 |
| Documents | #137 | 10 | #199–#208 | Template format gate (#207) blocks authoring UI (#200) |
| Service Requests | #136 | 15 | #209–#223 | Data model #222 filed last but implement first |
| Settings | #142 | 11 | #224–#234 | Seed #234 implement first; unblocks Leasing + Accounting |
| Auth | #143 | 11 | #235–#245 | Gap audit #243 implement first |
| Facilities | #139 | 11 | #246–#256 | Booking contracts (#253) depend on Documents chain |
| Visitor Access | #141 | 8 | #257–#264 | Fully self-contained; parallel delivery candidate |
| Marketplace | #138 | 14 | #265–#278 | Offer→Leasing trigger (#276) is cross-domain seam |
| Communication | #140 | 12 | #279–#290 | Complaint→SR (#285) coordinates with SR #216 |
| Admin | #144 | 12 | #291–#302 | Feature flags (#301) unblocks PowerBI #315 |
| Reports-System | #145 | 11 | #303–#313 | RBAC scope TL gate in #303; exec dashboard #313 implement last |
| Reports-PowerBI | #146 | 9 | #314–#322 | All blocked on TL ADR in #314 |
| **TOTAL** | **15** | **177** | **#147–#322** | GH skipped #174 = 176 actual issues |

**Total TL decision gates (blocking, not yet resolved at time of filing):**
1. #303 Q1 — RBAC scope propagation to report filters
2. #303 Q2 — Live query vs. snapshot table for reports
3. #307 Q1 — VAT jurisdiction (product decision, planted)
4. #309 Q1 — Maintenance threshold configurability (product decision, planted)
5. #310 Q1 — Report export reuse Documents #206 vs. separate pipeline
6. #312 Q1 — Async threshold definition
7. #312 Q2 — Queue driver isolation for report jobs
8. #314 Q1 — Live DB vs. read replica for PowerBI
9. #314 Q2 — OData v4 vs. REST JSON
10. #314 Q3 — Credential pattern (Sanctum vs. dedicated model)
11. #316 Q1 — Default credential expiry policy (product decision, planted)
12. #317 Q1 — National ID masking in BI endpoints (product decision, planted)
13. #321 Q1 — Per-credential rate limit threshold

**Recommended Designer handoff order (start here):**
1. **Contacts #132** — stories #147–#157. Entity contract is foundational; unblocks Leasing, Marketplace, and Accounting UX.
2. **Auth #143** — stories #235–#245. Login, 2FA, and profile are used on every page; UX must be consistent across all domains.
3. **Settings #142** — stories #224–#234. Company profile and branding UX needed early for login page and document headers.
4. **Properties #133** — stories #158–#168. Unit state machine and portfolio views are referenced in Reports and Marketplace.
5. All others can follow in any order after the above four are in flight.

*Phase 3 complete. 2026-04-24.*
