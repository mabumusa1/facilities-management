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

## Past work index
_(append one line per UX flow: `Flow #issue — <area> — <screens> — <key interaction>`)_
