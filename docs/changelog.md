---
title: What's New
layout: page
lang: en
---

# What's New

A plain-language running list of user-visible changes. For the developer-facing changelog, see [`CHANGELOG.md`](https://github.com/mabumusa1/facilities-management/blob/main/CHANGELOG.md) in the repo root.

## Unreleased

### Community metadata — April 2026

You can now set a community's amenities, weekly working days, and map location from a single edit form.

- **Amenities.** Pick from a standard 26-item catalog — gym, pool, parking, children's play area, and more. Click the × on any selected chip to remove it.
- **Working days.** Toggle each day of the week. The strip starts on Saturday per the GCC calendar. Amber highlights working days, grey marks non-working days. Leave everything off to mark the community as closed every day.
- **Map location.** Enter latitude and longitude as decimals, or click "Use my location" to auto-fill. Both values must be set together.
- **Bilingual.** The whole form works in English and Arabic. The day strip stays Saturday-first in Arabic; geographical coordinates are not flipped.
- **Amenity safety.** Updating other fields won't wipe your selected amenities — only explicit changes to the amenity list are saved.

Learn more: [Configure community metadata](./guides/properties/community-metadata.md).

### Sign-in improvements — April 2026

Signing in now takes you to the right page for your role automatically, handles rate-limit cooldowns more clearly, and works correctly in Arabic right-to-left.

- **Role-based landing.** Admins and Managers land on the Admin dashboard after sign-in. Owners and Residents land on the shared home today; dedicated portals are coming.
- **Clearer "too many attempts" message.** If you type the wrong password several times, the platform shows a clear amber banner with a countdown (in English and Arabic) telling you how many seconds remain before you can try again.
- **Right-to-left sign-in.** The password show/hide toggle, language switcher, and error messages are now positioned correctly when the page is in Arabic.
- **Screen-reader support.** Validation errors in the sign-in form are announced live by screen readers, so users on assistive tech get immediate feedback.

Learn more: [Sign in to your account](./guides/auth/login.md).

### Roles and permissions — April 2026

We added a full role-based access system so you can control exactly what each person on your team can see and do.

- **12 roles out of the box.** Every account now has 7 user-type roles (Account Admin, Admin, Manager, Owner, Tenant, Dependent, Professional) and 5 admin-function roles (System Admin, Accounting Manager, Service Manager, Marketing Manager, Sales & Leasing Manager). Existing admins keep their access — nothing breaks.
- **Manage roles from the admin panel.** Go to **Admin → Roles** to create custom roles alongside the defaults. Role names are bilingual (English + Arabic). Learn more: [Create a role](./guides/admin/create-a-role.md).
- **Edit permissions in a matrix.** A 31-subject × 6-action grid lets you tick exactly which actions a role can perform. Starter presets load a typical permission set you can then adjust. Learn more: [Assign permissions to a role](./guides/admin/assign-permissions-to-a-role.md).
- **Assign roles with a scope.** When you assign an admin-type role to a user, you can restrict it to specific communities, buildings, or service types. Service Managers can be scoped to specific service types as well. Learn more: [Assign a role to a user](./guides/admin/assign-a-role-to-a-user.md) and [Scope a manager to specific properties](./guides/admin/manager-scope.md).
- **Unauthorized access is blocked.** Every non-public page and action is now permission-gated. Users without the required permission see a clear error instead of the page.

Start here: [Roles and permissions — overview](./guides/admin/roles-and-permissions.md).
