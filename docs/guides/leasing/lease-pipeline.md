---
title: Lease Pipeline View
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Browse all leases grouped by status, filter by expiry window, configure alert thresholds, and export filtered data to Excel — all from the Lease Pipeline page.*

## Who this is for

Property Managers and Admins who need to monitor upcoming lease expirations and take proactive action on renewals and terminations.

## Before you start

- You must have the `leases.VIEW` permission to access the pipeline.
- Configuring alert thresholds requires the `leases.UPDATE` permission.
- At least one Lease record must exist to see data in the pipeline.

## Viewing the pipeline

1. Go to **Leasing → Lease Pipeline** (سير عقود الإيجار).

The page displays every lease in your account grouped by status section:

| Section | Description |
|---|---|
| **Expiring Soon** (تنتهي قريباً) | Leases whose end date falls within the selected expiry window (default: 30 days). |
| **Active** (نشطة) | Leases that are active but not inside the expiry window. |
| **Expired** (منتهية) | Leases whose end date has passed and that have not been terminated. |
| **Terminated** (منهية) | Leases that were formally closed via the move-out workflow. |
| **Pending** (معلقة) | Lease applications not yet approved or activated. |

Each row shows:

- **Lease#** (العقد#) — the contract reference number
- **Unit** (الوحدة) — the unit code
- **Building** (المبنى) — the building name
- **Community** (المجتمع) — the community name
- **Tenant** (المستأجر) — the resident's name
- **Start** (البداية) — the lease start date
- **End** (النهاية) — the lease end date
- **Rent** (الإيجار) — the monthly rent amount in SAR
- **Days** (أيام) — days remaining until expiry

### Expiry badge colours

Leases in the **Expiring Soon** group carry a colour-coded badge in the **Days** column:

- **Red badge** — expiring in 14 days or fewer (e.g., "6d")
- **Amber badge** — expiring in 15–30 days (e.g., "20d")
- No badge — more than 30 days remain

Each badge also displays the day count as text so you never need to rely on colour alone.

## Filtering by expiry window

1. On the **Lease Pipeline** page, locate the filter bar at the top.
2. Open the **Expiry Window** (نافذة الانتهاء) dropdown and select **30 days** (٣٠ يوم), **60 days** (٦٠ يوم), or **90 days** (٩٠ يوم).
3. Optionally, set additional filters:
   - **Status** — show leases from one specific status group.
   - **Community** — narrow results to one community.
   - **Search** (بحث) — type a contract number or tenant name.
4. Click **Apply** (تطبيق) to refresh the list.

The **Expiring Soon** section updates to show only leases whose end date falls within the window you selected. The total count at the bottom of the page ("Displaying X of Y leases") reflects all visible rows after filters are applied.

To clear all filters and return to the default view, click **Reset** (إعادة تعيين) or **Clear Filters** (مسح الفلاتر) when the empty-state message appears.

## Configuring expiry alerts

You can configure the system to notify you by in-app notification or email whenever a lease approaches expiry.

1. On the **Lease Pipeline** page, click **Settings** (الإعدادات) in the top-right corner.
2. The **Lease Alert Settings** (إعدادات تنبيهات العقود) page opens.
3. Under **Alert Thresholds** (حدود التنبيه), you will see the default thresholds (90, 60, and 30 days before expiry). Each threshold has two toggles:
   - **In-app notification** (إشعار داخل التطبيق) — sends a notification inside the platform.
   - **Email** (بريد إلكتروني) — sends a notification to your account email address.
4. Check or uncheck each toggle to enable or disable that channel for that threshold.
5. To add a non-default threshold, click **Add Custom Threshold** (إضافة حد مخصص), enter the number of days, and enable the desired channels.
6. To remove a threshold, click **Remove** (إزالة) next to it.
7. To restore the original three defaults, click **Reset to Defaults** (إعادة للافتراضي).
8. Click **Save Settings** (حفظ الإعدادات).

When a lease's remaining days drops to or below a configured threshold, the system fires the enabled notification channels. Notifications are tenant-scoped — you only receive alerts for leases in your own account.

To return to the pipeline, click **← Pipeline** (← العودة للسير) at the top of the settings page.

## Exporting to Excel

1. On the **Lease Pipeline** page, apply any filters you need to narrow the result set.
2. Click **Export Excel** (تصدير Excel) at the bottom right of the page.
3. The **Export Lease Pipeline** (تصدير سير العقود) dialog opens and shows:
   - The number of leases that will be exported ("Exporting X leases matching current filter").
   - The columns that will be included: Lease ID, Unit, Building, Community, Tenant, Start Date, End Date, Rent Amount, Payment Frequency, Status.
   - The format selector, which defaults to **Excel (.xlsx)**.
4. Click **Download** (تحميل). The file downloads to your browser's default download location.

Open the file in any spreadsheet application (such as Microsoft Excel, Google Sheets, or LibreOffice Calc). All columns match the labels shown in the pipeline table.

## What you'll see

After saving alert settings, the pipeline continues to look the same. Alert thresholds take effect for any lease that crosses a threshold boundary — you will see a notification badge in the platform header and receive an email (if enabled) the first time a lease's remaining days reaches each configured threshold.

After downloading the export, the dialog closes and you return to the pipeline page unchanged. The downloaded file contains every row visible in the pipeline at the time of export.

## Common issues

- **The pipeline shows no leases** — check that no filters are applied; click **Reset** (إعادة تعيين) to clear all active filters and reload the full list.
- **The Lease Pipeline link is not visible** — your user role may not include the `leases.VIEW` permission. Contact your account administrator.
- **The export file is empty** — the current filter matches zero leases. Adjust or clear the filters so at least one lease is visible before exporting.
- **I am not receiving alert notifications** — confirm that the threshold is checked and the correct channel (In-app or Email) is enabled on the Lease Alert Settings page, then save again.
- **The Settings button is not visible** — configuring alert thresholds requires the `leases.UPDATE` permission. Contact your account administrator.

## Related

- [Approve or reject a lease application](./approve-or-reject-lease.md)
- [Lease amendments](./lease-amendments.md)
- [Lease renewal offers](./lease-renewals.md)
- [Initiate a move-out](./lease-move-out.md)
