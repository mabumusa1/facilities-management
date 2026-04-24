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

## Past work index
- Flow #114 — admin — 5 screens (index, create drawer, edit drawer, delete dialog, empty/skeleton) — system-role read-only enforcement with disabled actions + tooltips
- Flow #115 — admin — permissions matrix — 5 screens (full-page grid, system-role read-only, skeleton, save error, preset apply) — 31 subjects × 6 actions, sticky header/col, column/row bulk-toggle, preset dropdown, dirty-state bar, RTL column reversal
- Flow #116 — admin — role assignment — 8 screens (detail/roles tab, empty, skeleton, assign drawer non-scope, assign drawer manager, assign drawer serviceManager, validation, remove popover) — conditional scope selectors, deferred list, per-row remove with popover confirm, bilingual
