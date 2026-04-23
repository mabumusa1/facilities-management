# Tech Lead — Agent Memory

Append concise notes as you learn. Keep this under 200 lines via curation.

## Stack
- Laravel 13, PHP 8.5, Inertia v3, Vue 3, Tailwind v4, Wayfinder v0.
- Auth: Fortify v1.
- Multi-tenancy: Spatie.
- Permissions: spatie/laravel-permission.
- Tests: PHPUnit v12 (not Pest).
- Formatter: Pint (run `vendor/bin/pint --dirty --format agent`).

## Domain model map
- `app/Models/Community.php`, `Building.php`, `Unit.php` — property hierarchy.
- `app/Models/Lease.php`, `LeaseUnit.php`, `LeaseAdditionalFee.php`, `LeaseEscalation.php`, `Resident.php`, `Owner.php` — leasing.
- `app/Models/MarketplaceUnit.php`, `MarketplaceOffer.php`, `MarketplaceVisit.php` — marketplace.
- `app/Models/ServiceRequest.php`, `RequestCategory.php`, `RequestSubcategory.php` — service requests.
- `app/Models/Facility.php`, `FacilityBooking.php`, `FacilityCategory.php` — facilities.
- `app/Models/Transaction.php`, `Payment.php`, `Invoice.php`, `Currency.php`, `ServiceManagerType.php` — accounting.
- `app/Models/Announcement.php` — communication.
- `app/Models/Tenant.php` — multi-tenancy.

## Controllers live under
`app/Http/Controllers/{Accounting,Admin,AppSettings,Communication,Contacts,Documents,Facilities,Leasing,Marketplace,Properties,...}/`. Match the sibling naming convention when adding new controllers.

## Routes
- `routes/web.php` (main), `routes/console.php` (commands/schedule), `routes/settings.php` (settings area).
- Wayfinder generates TS clients — run `php artisan wayfinder:generate` after any controller/route signature change.

## Frontend conventions
- Pages in `resources/js/pages/<area>/{Index,Create,Edit,Show}.vue`.
- Single root element per Vue component.
- Inertia v3 idioms: `useForm`, `useHttp`, `<Link>`, `router` from `@inertiajs/vue3`.
- `Inertia::lazy()` / `LazyProp` are removed → use `Inertia::optional()`.
- Deferred props must have a skeleton/pulse empty state.

## Test conventions
- `tests/Feature/` for feature tests (most tests live here).
- `tests/Unit/` for unit tests only when there's no HTTP touch point.
- Use factories; check for custom states before manually setting up models.
- Multi-tenant: wrap tests in the appropriate tenant scope; do not hit mock DBs.

## Recurring risks to flag in designs
- N+1 on listing pages — always specify `->with([...])` in design.
- Tenant boundary leaks — any query must be tenant-scoped.
- Wayfinder TS drift — callout if controller signatures change.
- i18n/RTL — check both directions for any UI affecting string layout.

## Past work index
_(append one line per design you posted: `Design #N — <short title> — <gotchas>`)_
