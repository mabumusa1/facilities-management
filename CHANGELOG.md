# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Roles and permissions — 12 default roles (7 user roles + 5 admin roles) and 186 permissions seeded on every account ([#119](https://github.com/mabumusa1/facilities-management/pull/119))
- Manager scope — managers are now restricted to their assigned communities, buildings, and service types across 16 models ([#121](https://github.com/mabumusa1/facilities-management/pull/121))
- Admin → Roles — list, search, create, edit, and delete custom roles with bilingual English/Arabic names ([#122](https://github.com/mabumusa1/facilities-management/pull/122))
- Admin → Roles — permission matrix editor (31 subjects × 6 actions) with presets and a "View is required when any other action is enabled" rule ([#123](https://github.com/mabumusa1/facilities-management/pull/123))
- Admin → Users — drawer-based role assignment with community, building, and service-type scope selectors ([#124](https://github.com/mabumusa1/facilities-management/pull/124))

### Changed

### Deprecated

### Removed

### Fixed

### Security

- Every non-public route now requires an explicit permission via middleware and Policy enforcement; unauthorized requests return a 403 with the required permission slug ([#120](https://github.com/mabumusa1/facilities-management/pull/120))

---

_Internal_
<!-- One-line notes for internal refactors that have no user-visible impact. They do not trigger a release. -->

- Migrated existing `rf_admins.role` enum values into the new role assignment table without breaking any existing admin session ([#125](https://github.com/mabumusa1/facilities-management/pull/125))
