# Tracking Matrix

## Status Legend
- `NS`: Not started
- `IP`: In progress
- `BL`: Blocked
- `RV`: In review
- `DN`: Done

## Workstream Tracker

| Workstream | Owner | Status | Target Date | Notes |
|---|---|---|---|---|
| Foundation (i18n, locale, RTL) | Engineering | IP |  | Runtime i18n, locale persistence, and RTL/LTR sync are in place; guardrails remain pending. |
| Shared UI and Navigation | Engineering | RV |  | Shared navigation and header are key-based; final parity review is pending. |
| Auth and User Settings | Engineering | DN |  | Auth and settings surfaces are localized and integrated with runtime locale switching. |
| Core Domain Modules |  | NS |  |  |
| Operations Modules |  | NS |  |  |
| Marketplace/Reports/App Settings |  | NS |  |  |
| Final QA and Regression |  | NS |  |  |

## Gap Register

| Gap ID | Module | Page/Component | Type | Severity | Status | Evidence Ref |
|---|---|---|---|---|---|---|
| GAP-SHARED-001 | Shared/Auth/System | resources/js/pages/Welcome.vue | Localization | High | DN | Removed from app surface on 2026-04-21. |

## Page-Level Scan Tracker

### Shared/Auth/System
| Path | Localization | Parity | Severity | Status | Notes |
|---|---|---|---|---|---|
| resources/js/components/AppSidebar.vue | Key-based | Pending contract parity audit | Low | RV | Runtime locale integration verified. |
| resources/js/components/AppHeader.vue | Key-based | Pending contract parity audit | Low | RV | Runtime locale integration verified. |
| resources/js/components/NavMain.vue | Key-based | Pending contract parity audit | Low | RV | Runtime locale integration verified. |
| resources/js/components/NavGroups.vue | Key-driven labels from parent | Pending contract parity audit | Low | RV | Receives localized labels and titles via props. |
| resources/js/components/Breadcrumbs.vue | Key-driven labels from parent | Pending contract parity audit | Low | RV | Receives localized breadcrumb titles via props. |
| resources/js/components/PageHeader.vue | Key-based | Pending contract parity audit | Low | RV | Runtime locale integration verified. |
| resources/js/components/UserMenuContent.vue | Key-based | Pending contract parity audit | Low | RV | Includes active locale switch controls. |
| resources/js/pages/auth/Login.vue | Key-based | Pending contract parity audit | Low | RV | Auth copy localized through runtime i18n keys. |
| resources/js/pages/auth/Register.vue | Key-based | Pending contract parity audit | Low | RV | Auth copy localized through runtime i18n keys. |
| resources/js/pages/auth/ForgotPassword.vue | Key-based | Pending contract parity audit | Low | RV | Auth copy localized through runtime i18n keys. |
| resources/js/pages/auth/ResetPassword.vue | Key-based | Pending contract parity audit | Low | RV | Auth copy localized through runtime i18n keys. |
| resources/js/pages/auth/VerifyEmail.vue | Key-based | Pending contract parity audit | Low | RV | Auth copy localized through runtime i18n keys. |
| resources/js/pages/auth/ConfirmPassword.vue | Key-based | Pending contract parity audit | Low | RV | Auth copy localized through runtime i18n keys. |
| resources/js/pages/auth/TwoFactorChallenge.vue | Key-based | Pending contract parity audit | Low | RV | Auth copy localized through runtime i18n keys. |
| resources/js/pages/settings/Profile.vue | Key-based | Pending contract parity audit | Low | RV | Settings copy localized through runtime i18n keys. |
| resources/js/pages/settings/Security.vue | Key-based | Pending contract parity audit | Low | RV | Settings copy localized through runtime i18n keys. |
| resources/js/pages/settings/Appearance.vue | Key-based | Pending contract parity audit | Low | RV | Settings copy localized through runtime i18n keys. |
| resources/js/pages/Dashboard.vue | Key-based | Pending contract parity audit | Low | RV | Dashboard labels localized through runtime i18n keys. |
| resources/js/pages/notifications/Index.vue | Key-based | Pending contract parity audit | Low | RV | Notification labels localized through runtime i18n keys. |
| resources/js/pages/Welcome.vue (removed) | N/A | N/A | N/A | DN | Removed from route surface and page inventory on 2026-04-21. |

### Core Domain
| Path | Localization | Parity | Severity | Status | Notes |
|---|---|---|---|---|---|
| resources/js/pages/properties/communities/Index.vue |  |  |  | NS |  |
| resources/js/pages/properties/communities/Create.vue |  |  |  | NS |  |
| resources/js/pages/properties/communities/Edit.vue |  |  |  | NS |  |
| resources/js/pages/properties/communities/Show.vue |  |  |  | NS |  |
| resources/js/pages/properties/buildings/Index.vue |  |  |  | NS |  |
| resources/js/pages/properties/buildings/Create.vue |  |  |  | NS |  |
| resources/js/pages/properties/buildings/Edit.vue |  |  |  | NS |  |
| resources/js/pages/properties/buildings/Show.vue |  |  |  | NS |  |
| resources/js/pages/properties/units/Index.vue |  |  |  | NS |  |
| resources/js/pages/properties/units/Create.vue |  |  |  | NS |  |
| resources/js/pages/properties/units/Edit.vue |  |  |  | NS |  |
| resources/js/pages/properties/units/Show.vue |  |  |  | NS |  |
| resources/js/pages/leasing/leases/Index.vue |  |  |  | NS |  |
| resources/js/pages/leasing/leases/Create.vue |  |  |  | NS |  |
| resources/js/pages/leasing/leases/Edit.vue |  |  |  | NS |  |
| resources/js/pages/leasing/leases/Show.vue |  |  |  | NS |  |
| resources/js/pages/leasing/leases/SubleaseCreate.vue |  |  |  | NS |  |
| resources/js/pages/contacts/tenants/Index.vue |  |  |  | NS |  |
| resources/js/pages/contacts/tenants/Create.vue |  |  |  | NS |  |
| resources/js/pages/contacts/tenants/Edit.vue |  |  |  | NS |  |
| resources/js/pages/contacts/tenants/Show.vue |  |  |  | NS |  |
| resources/js/pages/contacts/owners/Index.vue |  |  |  | NS |  |
| resources/js/pages/contacts/owners/Create.vue |  |  |  | NS |  |
| resources/js/pages/contacts/owners/Edit.vue |  |  |  | NS |  |
| resources/js/pages/contacts/owners/Show.vue |  |  |  | NS |  |
| resources/js/pages/contacts/admins/Index.vue |  |  |  | NS |  |
| resources/js/pages/contacts/admins/Create.vue |  |  |  | NS |  |
| resources/js/pages/contacts/admins/Edit.vue |  |  |  | NS |  |
| resources/js/pages/contacts/admins/Show.vue |  |  |  | NS |  |
| resources/js/pages/contacts/professionals/Index.vue |  |  |  | NS |  |
| resources/js/pages/contacts/professionals/Create.vue |  |  |  | NS |  |
| resources/js/pages/contacts/professionals/Edit.vue |  |  |  | NS |  |
| resources/js/pages/contacts/professionals/Show.vue |  |  |  | NS |  |
| resources/js/pages/contacts/residents/Show.vue |  |  |  | NS |  |
| resources/js/pages/accounting/transactions/Index.vue |  |  |  | NS |  |
| resources/js/pages/accounting/transactions/Create.vue |  |  |  | NS |  |
| resources/js/pages/accounting/transactions/Edit.vue |  |  |  | NS |  |
| resources/js/pages/accounting/transactions/Show.vue |  |  |  | NS |  |

### Operations and Marketplace/Settings
| Path | Localization | Parity | Severity | Status | Notes |
|---|---|---|---|---|---|
| resources/js/pages/requests/Index.vue |  |  |  | NS |  |
| resources/js/pages/requests/Create.vue |  |  |  | NS |  |
| resources/js/pages/requests/Edit.vue |  |  |  | NS |  |
| resources/js/pages/requests/Show.vue |  |  |  | NS |  |
| resources/js/pages/facilities/Index.vue |  |  |  | NS |  |
| resources/js/pages/facilities/Create.vue |  |  |  | NS |  |
| resources/js/pages/facilities/Edit.vue |  |  |  | NS |  |
| resources/js/pages/facilities/Show.vue |  |  |  | NS |  |
| resources/js/pages/facilities/bookings/Index.vue |  |  |  | NS |  |
| resources/js/pages/facilities/bookings/Create.vue |  |  |  | NS |  |
| resources/js/pages/facilities/bookings/Edit.vue |  |  |  | NS |  |
| resources/js/pages/facilities/bookings/Show.vue |  |  |  | NS |  |
| resources/js/pages/communication/announcements/Index.vue |  |  |  | NS |  |
| resources/js/pages/communication/announcements/Create.vue |  |  |  | NS |  |
| resources/js/pages/communication/announcements/Edit.vue |  |  |  | NS |  |
| resources/js/pages/communication/announcements/Show.vue |  |  |  | NS |  |
| resources/js/pages/visitor-access/History.vue |  |  |  | NS |  |
| resources/js/pages/visitor-access/Details.vue |  |  |  | NS |  |
| resources/js/pages/documents/Index.vue |  |  |  | NS |  |
| resources/js/pages/documents/LeadsImportErrors.vue |  |  |  | NS |  |
| resources/js/pages/marketplace/Overview.vue |  |  |  | NS |  |
| resources/js/pages/marketplace/Customers.vue |  |  |  | NS |  |
| resources/js/pages/marketplace/Listing.vue |  |  |  | NS |  |
| resources/js/pages/marketplace/Visits.vue |  |  |  | NS |  |
| resources/js/pages/marketplace/VisitShow.vue |  |  |  | NS |  |
| resources/js/pages/reports/Index.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/Index.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/FacilitiesIndex.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/FacilityShow.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/FacilityForm.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/ServiceRequestDetails.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/forms/Index.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/forms/Create.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/forms/Preview.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/forms/SelectCommunity.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/settings/forms/SelectBuilding.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/request-categories/Index.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/request-categories/Create.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/request-categories/Edit.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/facility-categories/Index.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/facility-categories/Create.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/facility-categories/Edit.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/invoice/Edit.vue |  |  |  | NS |  |
| resources/js/pages/app-settings/general/Index.vue |  |  |  | NS |  |

## Sign-Off
- Localization Lead:
- Product Lead:
- Engineering Lead:
- QA Lead:
- Date:
