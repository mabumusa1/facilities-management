---
title: Reports — snapshots overview
area: reports
layout: guide
lang: en
---

# Reports — snapshots overview

*Some reports run live every time you open them; others are pre-computed and stored so they open instantly. This guide explains the difference and tells you who can access reports.*

## Who this is for

Property Managers and Accounting Managers who run cross-portfolio reports (financial summary, occupancy, lease pipeline, VAT return, receivables ageing, and portfolio health).

## Live reports vs. snapshot reports

The platform has two ways to generate a report:

**Live** — the report queries the latest data every time you open it. You always see up-to-the-minute figures. Use this for reports where real-time accuracy matters more than speed.

**Snapshot** — the platform runs an expensive aggregation in the background and saves the result. When you open the report, you see the saved result immediately instead of waiting for the query to finish. The snapshot shows when it was generated so you know how fresh the data is.

The following report types are available:

| Report | Mode |
|---|---|
| Financial Summary | Snapshot |
| Occupancy | Snapshot |
| Lease Pipeline | Snapshot |
| VAT Return | Snapshot |
| Receivables Ageing | Snapshot |
| Portfolio Health | Snapshot |

All six current report types use snapshot mode because they aggregate data across accounting, leasing, and properties — queries that can take several seconds on large portfolios.

## Who can access reports

You need the **reports.VIEW** permission to open any report. Users without this permission see a 403 (Access Denied) error.

Roles that include reports.VIEW by default:

- Account Admin
- System Admin
- Accounting Manager

If your role does not include this permission and you need access, ask your System Admin to add it.

## Tenant isolation

Every report is scoped to your account. You can never see data from another account, and other accounts can never see yours. This applies to every report type without exception.

## What's coming

The report viewer pages (issue #304–#313) and Power BI integration (#314–#322) are in development. Once released, you will be able to open, filter, and export each report type from the Reports section of the admin panel. This guide will be updated when those pages ship.

## Common issues

- **Access Denied (403) when opening a report** — your role does not have the reports.VIEW permission. Contact your System Admin.
- **Report shows old figures** — snapshot reports display data as of the generation time shown on the report. A newer snapshot is generated automatically on a schedule. If you need data more recent than the latest snapshot, contact your System Admin to trigger a manual refresh (this capability is coming in a future release).

## Related

- [Roles and permissions](../admin/roles-and-permissions.md)
- [Assign permissions to a role](../admin/assign-permissions-to-a-role.md)
