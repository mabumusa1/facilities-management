---
title: View and manage a lead's detail
area: leasing
layout: guide
lang: en
---

# {{ page.title }}

*Open a lead record to update its status, assign it to a team member, add activity notes, and read the full audit timeline.*

## Who this is for

Admins and Property Managers who have the `leads.VIEW` permission. Updating status, assigning, or adding notes also requires the `leads.UPDATE` permission. Deleting a lead is restricted to Admins only.

## Before you start

- At least one lead must exist in **Leasing → Leads** (العملاء المحتملون). If the list is empty, add your first lead with **+ Add Lead** (+ إضافة عميل محتمل).
- To assign a lead to a team member, that person must already have an active account in your workspace.

## Steps

### 1. Open the lead detail page

1. Go to **Leasing → Leads** (التأجير → العملاء المحتملون).
2. Click any row in the leads table. The row is a link that opens the lead detail page at `/leasing/leads/{id}`.

The page header shows the lead's name, the current status badge, and an action menu (**More actions** — المزيد من الإجراءات) in the top-right corner.

---

### 2. Update the lead status

The status card appears below the page header.

1. Click the **Status** (الحالة) dropdown and select one of:
   - **New** (جديد)
   - **Contacted** (تم التواصل)
   - **Qualified** (مؤهل)
   - **Converted** (تم التحويل)
   - **Lost** (خسر)
2. If you select **Lost**, a **Lost reason (optional)** (سبب الخسارة (اختياري)) text area appears. Enter a reason (up to 500 characters).
3. When you have unsaved changes, a yellow **Unsaved changes** (تغييرات غير محفوظة) bar appears at the bottom of the card.
4. Click **Save Changes** (حفظ التغييرات) to confirm. The status badge in the header updates immediately.
   - To discard your edits without saving, click **Discard** (تجاهل).

---

### 3. Assign or reassign a team member

The **Assignment** (التعيين) section is on the **Details** (التفاصيل) tab.

**To assign for the first time:**

1. Click **Assign** (تعيين).
2. The **Assign Lead** (تعيين العميل المحتمل) sheet slides in from the right (left in Arabic).
3. Type a name in the **Search team members…** (البحث عن أعضاء الفريق…) field.
4. Click the name of the person you want to assign.
5. Click **Assign** (تعيين) to confirm. A success message appears and the **Assigned To** (المعيَّن له) label updates.

**To change the assigned person:**

1. Click **Change** (تغيير) next to the currently assigned name.
2. The assign sheet opens with the current assignee shown under **Currently assigned** (معيَّن حالياً).
3. Search for and select the new team member, then click **Assign** (تعيين).

**To remove the assignment:**

1. Click **Unassign** (إلغاء التعيين).
2. The field reverts to **Unassigned** (غير مُعيَّن).

All assignment changes are logged in the Activity timeline.

---

### 4. View contact information and notes

The **Details** (التفاصيل) tab also shows:

- **Contact Info** (بيانات التواصل) — Phone (الهاتف), Email (البريد الإلكتروني), Source (المصدر), and the date the lead was Created (تاريخ الإضافة).
- **Notes** (الملاحظات) — The free-text note entered when the lead was first created. This field is read-only on this page; edit it from **More actions → Edit lead** (تعديل العميل المحتمل).

---

### 5. Read the activity timeline

Click **Activity** (النشاط) to switch tabs. The tab label shows a count of events, for example **Activity (4)** (النشاط (٤)).

The timeline lists all events in reverse chronological order (newest first). Each entry shows:

| Event type | What it says |
|---|---|
| Assignment | Assigned to [name] — by [actor] · [time] |
| Unassignment | Unassigned from [name] — by [actor] · [time] |
| Status change | Status changed: [old] → [new] — by [actor] · [time] |
| Note | Note — body text — by [actor] · [time] |

If no events exist yet, the timeline shows **No activity yet** (لا يوجد نشاط بعد) with an **Add First Note** (أضف أول ملاحظة) link.

---

### 6. Add an activity note

You can add a free-text note from the **Activity** tab at any time.

1. Click **Add Note** (إضافة ملاحظة).
2. A compose area opens with the prompt **Write a note…** (اكتب ملاحظة…).
3. Type your note.
4. Click **Save Note** (حفظ الملاحظة). The note appears at the top of the timeline with your name and the current timestamp.
   - To cancel without saving, click **Cancel** (إلغاء).

---

### 7. Delete a lead (Admins only)

Deleting is permanent and cannot be undone.

1. Click **More actions** (المزيد من الإجراءات) in the page header.
2. Select **Delete lead** (حذف العميل المحتمل).
3. A confirmation dialog with the title **Delete Lead** (حذف العميل المحتمل) appears. Read the warning.
4. Click **Delete Lead** (حذف العميل المحتمل) to confirm. You are returned to the Leads list.
   - To cancel, click **Cancel** (إلغاء) or close the dialog.

## What you'll see

After saving a status change, the status badge in the page header updates to the new value. A success toast confirms each action:

- Status update: "Status updated to [status]" (تم تحديث الحالة إلى [الحالة])
- Assignment: "Lead assigned to [name]" (تم تعيين العميل المحتمل إلى [الاسم])
- Unassignment: the Assigned To field reverts to Unassigned
- Note saved: "Note added" (تمت إضافة الملاحظة)
- Lead deleted: "Lead deleted" (تم حذف العميل المحتمل) and you are redirected to the Leads list

Every change is reflected in the Activity timeline immediately.

## Common issues

- **The lead detail page returns a "403 Forbidden" error.** Your account does not have the `leads.VIEW` permission. Contact your Account Admin to request access.
- **The Save Changes, Assign, or Add Note buttons are not available.** You have `leads.VIEW` but not `leads.UPDATE`. Contact your Account Admin.
- **The Delete lead option is missing from More actions.** Deleting leads is restricted to Admins. If you need to remove a lead, ask an Admin to do so.
- **A team member's name does not appear in the assign search.** Only users who are active members of your workspace appear in the search. Confirm the person has accepted their invitation and that their account is active (see [Manage users](../admin/manage-users.md)).
- **The Lost reason field is required but I want to skip it.** The field is optional — you can leave it blank when changing the status to Lost. The label reads "Lost reason (optional)" (سبب الخسارة (اختياري)).
- **The activity timeline shows no entries.** No actions have been taken on this lead since it was created. Add the first note to start the log.

## Related

- [Add and search leads](./manage-leads-list.md)
- [Approve or reject a lease application](./approve-or-reject-lease.md)
- [Manage users](../admin/manage-users.md)
