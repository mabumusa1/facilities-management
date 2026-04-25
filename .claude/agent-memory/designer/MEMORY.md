# Designer — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Visual system
- Tailwind v4. Follow utility-class conventions already in `resources/js/pages/`.
- Component patterns used throughout: Card, Modal/Drawer, DataTable, PageHeader, Breadcrumbs, Tabs, Badge.
- Check sibling pages in the target `area:` first — reuse existing components rather than specifying new ones.

## i18n / RTL
- App is bilingual (English + Arabic). All microcopy specs should include both languages for any user-facing surface.
- RTL considerations: icons (chevrons, arrows) flip, reading order reverses, some fixed-position elements (drawers, toasts) may need `inset-inline-*` rather than `left-*`/`right-*`.
- Arabic typography: line-height often needs bumping (~1.6+) for legibility.

## Page layout conventions
- Index pages: filter bar top, table/grid below, empty state if no results, pagination footer.
- Create/Edit: 2-column form on desktop, single-column on mobile, sticky action bar at bottom.
- Detail/Show: header with primary actions top-right, tabs for related entities.

## Accessibility baseline
- Every interactive element: keyboard focusable, visible focus ring, ARIA role if non-standard.
- Modals: focus trap, ESC to close, focus returned on close.
- Forms: `<label>` tied to input; errors announced via `aria-live="polite"`.
- Color contrast: WCAG AA minimum; tested for both light and dark mode if applicable.

## Microcopy voice
_(refine as user gives feedback — direct? friendly? formal? use plain English or property-mgmt jargon? populate here)_

## RTL-specific patterns discovered
- Name (Arabic) inputs: always force `dir="rtl"` regardless of page locale; Name (EN) inputs force `dir="ltr"`. Critical for bilingual forms.
- Drawers/Sheets: use `inset-inline-end: 0` (not `right: 0`) for automatic RTL mirroring.
- Pagination chevrons: use `rtl:scale-x-[-1]` transform on icon SVG.
- Tooltips: prefer `placement="bottom"` on action icons to avoid off-edge clipping in both directions.

## Admin area component inventory (from `resources/js/pages/admin/users/Index.vue`)
- Table, TableHeader, TableBody, TableCell, TableHead, TableRow — all available from `@/components/ui/table`
- Badge (variants: `secondary`, `outline`) — `@/components/ui/badge`
- Button (variants: `destructive`, default) — `@/components/ui/button`
- Heading (variant `small` with title + description props)
- Input, Label, InputError
- `useI18n` composable from `@/composables/useI18n`
- Pagination: `PaginationLink[]` array passed as prop, rendered as links
- Wayfinder route functions imported from `@/routes/admin/<resource>`

## Grid/matrix UI patterns
- Wide grids (31 rows × 6 cols) are full pages, not drawers — Sheet/Drawer is too narrow.
- Use `<table role="grid">` + `role="gridcell"` for ARIA grid keyboard navigation (arrow keys within grid, Tab exits grid).
- Sticky column uses `position: sticky; inset-inline-start: 0` (NOT `left: 0`) so RTL auto-resolves.
- Column "all" and row checkboxes use indeterminate state (−) when partially selected.
- Arabic column headers: never abbreviate — allow two-line wrap with `min-width: 72px`.
- Dirty-state bar: dot indicator + text on inline-start; CTAs on inline-end; `justify-between` flips correctly under RTL.
- Deferred props + skeleton for heavy matrix data (animate-pulse cells).

## Role-assignment UI patterns (from Flow #116)
- Role assignment lives on a User Detail page (Show.vue), not the index — too many rows for inline.
- Conditional scope selectors use `v-if` transition: show community + building multi-selects only when `manager_scope = true`; add service-type multi-select for `serviceManager`.
- Info note ("This role applies globally") shown for non-scoped roles so the UI never looks empty/broken.
- Remove uses an inline popover (`role="alertdialog"`), not a full-screen dialog — lighter weight for row-level destructive actions.
- Remove popover: default focus on [Cancel] (safer), `placement="bottom"` to avoid RTL clipping.
- Scope chips in table cells: Badge `variant="secondary"`, stacked vertically, multiple per cell.
- Deferred prop + skeleton for role assignment list (same pattern as permissions matrix).
- Arabic numeric dates: decide whether to force `numberingSystem: 'latn'` or allow Eastern Arabic — project has not standardised this yet.

## DataTable rendering limitation
- `DataTable.vue` `Column.render` returns `string | number` — cannot return VNodes. For rich cells (Badge, multi-line content), use raw `Table/TableBody/TableCell/TableRow` from `@/components/ui/table`, or extend DataTable to support scoped slots per column.
- DataTable's `rowHref` wraps cells in `<Link>`, making all cells clickable. Blocks inline interactive elements inside cells.

## Sheet/Drawer component patterns
- Sheet `side="right"` handles RTL sliding animations correctly (Reka UI).
- **RTL bug:** SheetContent close button uses `right-4`, not `inset-inline-end`. X stays on right in RTL. Flag for component-level fix.
- SheetFooter uses `flex flex-col gap-2 p-4` — button order auto-flips in RTL. Cancel at inline-start, Save at inline-end. Pattern: `<Button variant="outline">Cancel</Button>` then `<Button>Save</Button>`.
- Sheet width: `class="w-full sm:max-w-md"` (not fixed `w-96`). Follows AssignRoleDrawer pattern.

## Phone input pattern
- Two separate `<Input>` fields: `phone_country_code` (maxlength="5") + `phone_number`. Both `dir="ltr"`. Do NOT combine as `<select>+input`. Source: `contacts/owners/Create.vue:66-78`.

## Badge variants (from badge/index.ts)
- Available: `default` (primary bg), `secondary` (muted bg), `destructive` (red), `outline` (border-only). No color-specific variants.
- For status-specific colors (green, teal, yellow): use `variant="outline"` or `variant="secondary"` + inline Tailwind `class` overrides with dark-mode variants.

## Drawing sheet/drawer form patterns
- Bilingual name inputs: EN `dir="ltr" lang="en"`, AR `dir="rtl" lang="ar" leading-relaxed`. Source: `admin/roles/Index.vue:362-383`.
- Focus: drawer open → first input. ESC → trigger button. Validation failure → first errored field.
- `form.processing` disables submit but leaves inputs writable.
- Drawer stays open on validation failure; closes only on success.

## Marketplace area component inventory (from `resources/js/pages/marketplace/`)
- Card, CardContent, CardHeader, CardTitle — primary container pattern
- Button (variants: default, outline, destructive, sm/normal)
- Badge (variants: default, secondary)
- Input, Label, InputError — form fields
- Heading (variant `small` with title + description props)
- useI18n composable, setLayoutProps with breadcrumbs
- useForm for submissions (preserveScroll: true, onSuccess resets)
- No Table component used yet in marketplace; currently uses bordered div cards per row

## Marketplace UX patterns discovered (Flows #273–#278)
- Outcome recording: Sheet from inline-end with radio-group-style outcome cards (4 options), conditional fields per outcome, optional inquiry stage override
- Offer submission (buyer-facing): Sheet with price input (currency prefix), payment plan dropdown (lump/monthly/quarterly/annual), conditions textarea dir="auto", post-submit inline banner
- Offer management (seller-facing): Offers tab with count badge on listing detail, per-row contextual actions (Accept=AlertDialog, Counter=Sheet, Reject=Popover), negotiation timeline in detail sheet
- Conversion (accepted→Leasing): Confirmation modal with terms summary, post-conversion read-only listing (disabled actions), "View Lease Quote" link, atomic failure with retry modal
- Featured listings (admin-only): Toggle switch column visible only to admin, inline featured_until date picker, slot-limit enforcement dialog, drag-reorder drawer for featured_order
- Analytics funnel: Horizontal cards (Active→Inquiries→Visits→Offers→Accepted→Conversions) with delta indicators, date filter radiogroup (7d/30d/90d/Custom), per-listing analytics tab with outcome breakdown bars

## Funnel/analytics UI patterns (from Flow #278)
- Funnel cards: `role="region"` with `aria-label`, decorative → arrows (`aria-hidden="true"`), delta badge with `aria-label="{+12%} increase vs previous period"`
- RTL funnel direction: kept horizontal LTR for business-flow consistency; alternative is vertical stacking
- Date filters: `role="radiogroup"`, arrow-key navigable presets, active state distinct
- Outcome breakdown bars: `role="img"`, `aria-label="Interested: 5 of 8 visits (62%)"`, bar fill uses `inset-inline-start` (RTL flips)
- Per-listing nav: Analytics is 5th tab after Details/Inquiries/Visits/Offers

## Reports area component inventory (from `resources/js/pages/reports/Index.vue`)
- Uses `useHttp` for read/write actions (not `<Form>` — reports are action-driven, not form-driven)
- `watchEffect` + `setLayoutProps` for dynamic breadcrumbs
- `useI18n` composable for all user-facing strings
- Card + CardHeader + CardContent + CardTitle + CardDescription pattern
- Badge for status and summary data
- Grid layout: `grid gap-6 xl:grid-cols-2` for report panels
- `pre` tag for raw JSON response display
- `Intl.NumberFormat` for number/amount formatting with locale awareness

## Reports patterns discovered (batch: #303–#322)
- Report pages follow: filter bar (period, community) → KPI cards → data table/chart → export CTA footer
- Period picker: radio group pattern (This Month, Last Month, Custom Range) with year selector
- Bucket tables (aging): clickable cells that drill into detail; column color coding supplements text labels
- Month pipeline cards: grid of `<button>` cards each showing 3 values (count, units, rent at risk); clickable to detail list
- "Stuck in maintenance" threshold: 14 days default; configurable note displayed alongside table
- Revenue leakage: estimated figure with mandatory disclaimer (`role="note"`)
- Export button: shared component across all report pages; dropdown with PDF/Excel/CSV; disabled with tooltip until #310 lands
- Async generation: notification pattern (bell badge + notification drawer); 7-day TTL on snapshots; dedup toast for concurrent requests
- Scheduled reports: create/edit form with email chip input, cadence dropdowns, format radio; schedule log expandable per-row
- Executive dashboard: KPI card grid; each card is `role="link"` navigating to full report; skeleton loading with staggered pulse delays; "No data yet" state per-card (not error page)

## Power BI settings patterns discovered (batch: #314–#322)
- Power BI Integration section lives in Settings, not Reports; uses existing Settings page layout (Heading + sections)
- Credential management: list table with selection → actions (Rotate, Revoke); secret shown once in modal with copy-to-clipboard
- Secret reveal modal: focus trapped; default focus on "I have saved" button (not copy); show/hide toggle for secret
- Connector UX panel: read-only URL input with copy button; "How to Connect" drawer with numbered steps + EN/AR toggle
- Test Connection modal: success shows entity list with row counts; failure shows specific error + link to credentials
- API call log: tabbed view (API Call Log / Credential Events); status badges for HTTP codes; expandable rate-limit detail rows
- Rate-limit rows: yellow highlight, expandable to show retry-after and window request count
- Super-admin BI view: tenant table with inline Enable/Disable toggle; Disable requires confirm dialog (destructive — shows active credential count)
- Usage drill-down drawer: credential list + 30-day chart + top 3 entities; slides from inset-inline-end for RTL auto-flip

## RTL patterns reinforced
- Technical identifiers (URLs, client IDs, tokens, invoice numbers) stay LTR even in Arabic layout
- Currency prefix (SAR) stays LTR; number formatting follows locale
- Month cards in pipeline reverse flow direction in RTL
- `inset-inline-start`/`inset-inline-end` used for drawer, toast, notification positions (auto-mirrors)
- Arabic table column headers: full words (no abbreviation); columns reverse in RTL
- Chart X-axis: Arabic month names; RTL date order (oldest right → newest left)

## Communication area patterns (Flows #279–#290)

### Resident-facing patterns
- **Feed with pinned section:** priority announcement cards in "Important" region at top with distinct border/badge, chronological cards below. Read/unread dot indicator. Relative timestamps with `<time>` element.
- **Card-based list (suggestions):** per-card upvote button (▲) with `aria-pressed`, anonymous submitter label, status badge, manager-response indicator (💬). Sort filter bar above cards.
- **Grouped directory:** categories as collapsible/scrollable sections ordered by manager's sort_order. Entry cards with tappable `tel:` links. Facility deep-link when `facility_id` set. Real-time search filtering across categories.
- **Complaint submission success:** full success page (not toast) with prominent reference number + copy button + SLA estimate + tracking link.

### Admin communication patterns
- **Complaint queue:** DataTable with SLA badge columns (overdue/on-time), status badge color overrides. Detail page with resident info card + complaint details card (2-col), action bar (assign/acknowledge/resolve), internal vs resident-visible note toggle, activity timeline (`role="log"`).
- **Suggestion management:** detail shows real submitter identity even when anonymous to residents. Status action buttons (context-dependent — current status hidden). Decline requires modal with reason textarea (min 10 chars). Revert from declined only.
- **Directory management:** drag-and-drop category reorder (optimistic or save-on-drop), per-category entry rows with active/inactive toggle, bilingual name fields. Facility link dropdown with orphan detection.

### Read-receipt patterns
- **Progress bar:** `role="progressbar"`, `aria-valuenow/valuemin/valuemax`, delivery/read counts + rate %.
- **Unread residents drawer:** Sheet from inline-end, searchable list, export CSV button. Empty state: "All residents have read."

### Analytics patterns
- **CSS-only charts in v1:** No charting library dependency. Stacked bar segments for overdue/on-time, CSS progress bars for SLA compliance %, simple sparkline for trends. Color + pattern/hatching for accessibility.
- **KPI cards with delta:** comparator vs previous period (↑/↓). `role="region"` with `aria-label`.

### Bilingual reference numbers
- Complaint refs (CMP-2026-00042), SR refs (SR-2026-00123) always LTR monospace within RTL layout. Wrap in `<bdi dir="ltr">` or `dir="ltr"` span.
- Copy-to-clipboard button adjacent to reference on success screen.

### New RTL notes
- Phone numbers: always `dir="ltr"` regardless of page locale (LTR digits, + prefix). Use `<bdi>` wrapper.
- Working hours: Arabic day names (الأحد-الخميس) + LTR time ranges. Full string `dir="auto"` or split.
- Feed "Important" section: badge at inline-start, region label translates.
- Upvote button: ▲ stays ▲ in RTL (up direction; no flip). Count number LTR.
- Directory entry cards: icon+label flips (icon at inline-start).
- "Book Now →" flips to "احجز الآن ←" in Arabic (arrow direction reverses).

### Data model stories
- Pure data model stories (#279) get "No UX scope" comment. Relabel directly to `state:ready-for-design,agent:designer`.

## Settings area patterns discovered (batch: #224–#233)

- Settings pages follow: `Heading variant="small"` + section cards (`Card/CardHeader/CardContent`), single form page for deep config (InvoiceSetting, Regional, ServiceSetting, Appearance) or index+Sheet for CRUD (Contract Types, Form Templates)
- **Index pages:** `DataTable` with `Columns[]`; empty state with CTA; per-row actions (toggle, edit via Sheet, deactivate toggle)
- **Forms:** `<Form v-bind="Controller.method.form()">` from Inertia; `Input`, `Label`, `InputError`; sticky save bar
- **Sheets:** Slide from inline-end; `SheetFooter` with Cancel/Save; button order auto-flips in RTL
- **Bilingual inputs:** EN `dir="ltr" lang="en"`, AR `dir="rtl" lang="ar" leading-relaxed` — applied to names, labels, descriptions, instructions
- **Conditional fields:** `v-if` with transition on toggle; `aria-expanded` on controlling switch; `aria-live="polite"` on revealed content
- **Live preview:** sidebar mockup for appearance, next-invoice-number for numbering, date format for regional — all reactively update on input
- **Multi-step:** Form Templates uses 3-step Sheet (Basic Info → Fields → Where Used) with step indicator
- **Accordion index:** Notification triggers grouped by domain in collapsible accordion sections; per-row channel toggles
- **Merge fields:** Sidebar panel in template editor; click to insert `{{field}}` at cursor; unrecognized field warning
- **Contrast auto-detection:** Client-side hex-to-luminance for WCAG AA threshold warning on color picker
- **AlertDialog for blockers:** Deactivate blocked (live quotes), reset confirmation, incomplete config guard
- **Toggle chips:** Working day selection uses `role="switch"` chips (not checkboxes) — more tappable on mobile
- **Inline add row:** Public holidays use inline add (Date + EN + AR) instead of modal — faster for multiple entries

### Settings UX decisions
- #224: NO UX (TL decision gate / ownership matrix) — relabel directly
- #233: UX — read-only audit log (paginated DataTable + date filters), minimal UX
- #226: Contract Types — Index + Sheet (Create/Edit); AlertDialog for blocked deactivate
- #227: Invoice Configuration — Single form with sections; sticky save; logo upload; conditional penalty fields
- #228: Regional Settings — Single form with sections; working day toggle chips; holiday inline add
- #229: Form Templates — Index + Multi-step Sheet (3 steps: details → fields → assign); in-use edit warning
- #230: Service Configuration — Single form with tabs (Late Fees / Home Pricing / Cancellation); radio fee type
- #231: Notification Preferences — Accordion index + Sheet editor; merge field sidebar; per-template reset
- #232: App Appearance — Single form with live preview; hex color picker; navigation label table

## Past work index
- Flow #114 — admin — 5 screens (index, create drawer, edit drawer, delete dialog, empty/skeleton) — system-role read-only enforcement with disabled actions + tooltips
- Flow #115 — admin — permissions matrix — 5 screens (full-page grid, system-role read-only, skeleton, save error, preset apply) — 31 subjects × 6 actions, sticky header/col, column/row bulk-toggle, preset dropdown, dirty-state bar, RTL column reversal
- Flow #116 — admin — role assignment — 8 screens (detail/roles tab, empty, skeleton, assign drawer non-scope, assign drawer manager, assign drawer serviceManager, validation, remove popover) — conditional scope selectors, deferred list, per-row remove with popover confirm, bilingual
- Flow #296 — leasing — leads list — 7 screens (index, skeleton, filter-applied, empty-state, add drawer, validation, success toast) — deferred skeleton, Sheet drawer with bilingual form, status badge color overrides, DataTable rendering gap flagged, SheetContent close-button RTL bug flagged
- Flow #273 — marketplace — 3 screens (visits list w/ overdue badges, visit detail w/ outcome banner, outcome recording sheet) — radio-group outcome cards, conditional fields, inquiry override
- Flow #274 — marketplace — 5 screens (listing CTA, offer sheet, post-submit banner, active-offer banner, off-plan disabled) — buyer-facing, currency prefix, dir=auto textarea
- Flow #275 — marketplace — 6 screens (offers tab/table, accept dialog, counter sheet, reject popover, offer timeline, post-accept banner) — per-row contextual actions, alertdialog, negotiation timeline
- Flow #276 — marketplace — 4 screens (accepted banner, conversion modal, read-only listing, failure retry modal) — terms summary table, disabled actions, atomic failure handling
- Flow #277 — marketplace — 5 screens (admin table w/ featured column, toggle+expiry picker, slot-limit dialog, order drawer, non-admin view) — admin-only column, drag-reorder, role-based rendering
- Flow #278 — marketplace — 4 screens (funnel cards+stats, per-listing analytics tab, date filter, empty analytics) — horizontal funnel, delta indicators, outcome bars, tenant-scoped
- Flow #220–#223 — service-requests — 4 issues (1 no-ux data model #222, 3 UX flows: #220 detail page with timeline+communication thread, #221 resident re-open flow with manager review, #223 operational reporting dashboard with KPI cards+tables+export)
- Flow #209–#219 — service-requests — 11 issues: #209 category SLA config (accordion index+sheet drawer), #210 resident submits SR (form+confirmation+my requests cards), #211 admin triage (DataTable w/ SLA badges+tabs+quick assign popover+internal notes timeline), #212 technician work queue (mobile card list+bottom sheets for accept/decline+notification bell), #213 technician status updates (action buttons+completion notes+photo grid+timeline+re-open dialog), #214 SLA tracking (badge system+filter tab+pre-breach/breach/escalation notifications+tooltip), #215 resident rating (star rating form+thank you page+low rating alert+manager display), #216 complaint-to-SR conversion (conversion modal w/ pre-filled form+source banner+back-link), #217 bulk admin view (enhanced DataTable+filter chips+search+sort+export+status summary bar), #218 home services catalog (admin card grid+sheet drawer for CRUD+resident catalog+booking flow w/ date picker+confirm+success), #219 neighbourhood services (tab toggle+common area picker+scope badges+announce-to-community checkbox+mixed my requests view)
