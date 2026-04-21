# Shared UI, Navigation, Auth, and System Pages Audit Plan

## Scope
- Shared navigation/layout/components.
- Authentication pages.
- User settings pages.
- Dashboard, notifications, and welcome page.

## Scan Targets

### Shared Navigation and Layout
- `resources/js/components/AppSidebar.vue`
- `resources/js/components/NavMain.vue`
- `resources/js/components/NavGroups.vue`
- `resources/js/components/AppHeader.vue`
- `resources/js/components/Breadcrumbs.vue`
- `resources/js/components/PageHeader.vue`
- `resources/js/components/UserMenuContent.vue`
- `resources/js/layouts/AppLayout.vue`
- `resources/js/layouts/AuthLayout.vue`
- `resources/js/layouts/settings/Layout.vue`

### Authentication
- `resources/js/pages/auth/Login.vue`
- `resources/js/pages/auth/Register.vue`
- `resources/js/pages/auth/ForgotPassword.vue`
- `resources/js/pages/auth/ResetPassword.vue`
- `resources/js/pages/auth/VerifyEmail.vue`
- `resources/js/pages/auth/ConfirmPassword.vue`
- `resources/js/pages/auth/TwoFactorChallenge.vue`

### User Settings
- `resources/js/pages/settings/Profile.vue`
- `resources/js/pages/settings/Security.vue`
- `resources/js/pages/settings/Appearance.vue`

### System Pages
- `resources/js/pages/Dashboard.vue`
- `resources/js/pages/notifications/Index.vue`
- `resources/js/pages/Welcome.vue`

## Detailed Checklist

### A. Sidebar/Menu/Breadcrumb Parity
- [ ] Every sidebar group and item label comes from translation keys.
- [ ] Sidebar items are aligned with route availability and user role expectations.
- [ ] Header menu items and user menu labels are translated.
- [ ] Breadcrumb titles are key-driven or key-resolved before render.
- [ ] No hardcoded fallback label remains (example: generic Create, Platform, Settings, Log out).

### B. Auth Copy and States
- [ ] All field labels/placeholders are translated.
- [ ] All auth CTA text is translated.
- [ ] Password and OTP/recovery mode text is translated.
- [ ] Status and success states are translated (verification resend, reset status, etc.).
- [ ] Validation text shown in auth flows is localized.

### C. Settings and Preferences
- [ ] Profile/security/appearance headings and helper text translated.
- [ ] Theme labels translated.
- [ ] Account deletion dialog and warnings translated.
- [ ] Locale switcher exposed in settings or user menu and persisted.

### D. Dashboard and Notifications
- [ ] Dashboard stat labels, section headers, and table headings translated.
- [ ] Number/currency/date formatting locale-aware.
- [ ] Notification list labels and action text translated.
- [ ] Empty states and badge labels translated.

### E. Welcome/Public Entry
- [ ] Welcome page copy translated and consistent with brand glossary.
- [ ] Sign-in/register links preserve or respect locale.

## Suspected Gap Themes to Verify
- Hardcoded navigation labels across sidebar/header.
- Hardcoded auth and settings labels.
- Hardcoded dashboard and notification copy.
- Locale-insensitive formatting in dashboard metrics.

## Suggested Translation Key Buckets
- `navigation.*`
- `auth.*`
- `settings.profile.*`
- `settings.security.*`
- `settings.appearance.*`
- `dashboard.*`
- `notifications.*`
- `common.actions.*`
- `common.states.*`

## Deliverables
- Shared UI gap register section.
- Auth/settings gap register section.
- Screenshot evidence for Arabic and English of:
  - Sidebar/menu/breadcrumb
  - Login and register
  - Dashboard and notifications

## Exit Criteria
- [ ] Zero hardcoded user-facing strings in scanned shared/auth/system files.
- [ ] Locale switch works globally and updates shared components live.
- [ ] Arabic and English screenshots approved for this scope.
