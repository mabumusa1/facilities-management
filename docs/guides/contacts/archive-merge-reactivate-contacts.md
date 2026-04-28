---
title: Archive, merge, and reactivate contacts
area: contacts
layout: guide
lang: en
---

# {{ page.title }}

*Keep your active contacts list clean by archiving inactive records, merging confirmed duplicates into a single record, and reactivating archived contacts when needed — without losing any historical data.*

## Who this is for
Property Managers and Admins who manage contact lifecycle: removing inactive records from the working list, resolving duplicates, and restoring previously archived contacts.

## Before you start
- You must have the **Manage Contacts** permission.
- **Archive** is the only way to remove a contact from the active list. Permanent deletion of contact records is not supported.
- You cannot archive a contact who has an active lease. End or transfer the lease first before archiving.
- **Merge** is permanent. Before merging, confirm which record is the authoritative one (the target) and which is the duplicate (the source). All linked records move to the target; the source is deleted.

## Steps

### Archive a contact

1. Open the contact's detail page (Resident, Owner, or Professional).
2. Click **Archive** (أرشفة) in the action menu (the three-dot menu or the action bar at the top of the page).
3. Confirm the action in the dialog.

The contact moves to **Archived** status and disappears from the default Contacts list. All linked records (past leases, invoices, service requests) are preserved and remain accessible.

If the contact has an active lease, the system blocks the archive action and shows:
> **This contact cannot be archived while they have an active lease.**

### Find archived contacts

1. In the main navigation, go to **Contacts** (جهات الاتصال).
2. In the **Status** filter, select **Archived** (مؤرشف).
3. The list updates to show only archived contacts.

### Reactivate an archived contact

1. With the **Archived** filter active, open the archived contact's detail page.
2. Click **Reactivate** (إعادة التنشيط).
3. Confirm the action.

The contact returns to **Active** status and reappears in the default Contacts list.

### Merge duplicate contacts

Use merge when two records represent the same real person, for example after a bulk import created duplicates or a duplicate-phone warning was bypassed.

1. Open the **duplicate** record (the one you want to remove after the merge).
2. Click **Merge** (دمج) in the action menu.
3. In the merge dialog, search for and select the **target record** — the authoritative record that will be kept.
4. Review the summary:
   - All linked records (leases, invoices, service requests, bookings) will be re-associated to the target.
   - The duplicate record will be permanently deleted after the merge.
   - An audit entry is created recording who merged the records, when, and the IDs of both records.
5. Click **Confirm merge** (تأكيد الدمج).

The target record now carries all historical data from both contacts. The duplicate record no longer exists.

## What you'll see

After archiving:
- The contact disappears from the default list and moves to the **Archived** filter view.
- A confirmation toast confirms **Contact archived.** (تم أرشفة جهة الاتصال.)

After reactivating:
- The contact reappears in the default list with **Active** status.
- A confirmation toast confirms **Contact reactivated.** (تمت إعادة تنشيط جهة الاتصال.)

After merging:
- The target record shows all previously separate linked records.
- The duplicate record no longer exists in any list or filter view.
- A confirmation toast confirms **Contacts merged successfully.** (تم دمج جهتَي الاتصال بنجاح.)

## Common issues

- **Archive blocked — active lease** — end or transfer the contact's active lease before archiving. The system will not allow archiving while any lease is in an active state.
- **Merge option not visible** — your role may not include the merge permission. Contact your Account Admin.
- **Cannot find the target record in the merge search** — the target contact may itself be archived. Reactivate the target first, then perform the merge.
- **After merge, some linked records still show the old contact name** — try a hard refresh (Ctrl+Shift+R or Cmd+Shift+R). The re-association is immediate but the browser cache may show stale data briefly.

## Related

- [Create and search resident contacts](./create-and-search-residents.md)
- [Search, filter, and export contacts](./search-filter-export-contacts.md)
- [View a contact's activity history](./view-contact-activity.md)
