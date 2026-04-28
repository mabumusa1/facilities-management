---
title: Manage users — invite, deactivate, and reset credentials
area: admin
layout: guide
lang: en
---

# Manage users — invite, deactivate, and reset credentials

*Admins control who can log in: invite new team members by email, deactivate accounts instantly when someone leaves, and trigger a password reset on behalf of any user.*

## Who this is for

Property Managers and Account Admins who manage the platform's user list for their organisation.

## Before you start

- You must have the Account Admin or Admin role to manage users.
- Users are not self-registered in this platform. Admins create every account.
- Role assignment happens after the user is created — see [Assign a role to a user](./assign-a-role-to-a-user.md).
- You can only manage users within your own account. Cross-account user management is not supported.

---

## Invite a new user

1. Go to **Admin → Users**.
2. Click **+ Invite User** (دعوة مستخدم) in the top-right of the Users card.
3. The **Invite User** (دعوة مستخدم) drawer opens. Fill in:
   - **First name** (الاسم الأول)
   - **Last name** (اسم العائلة)
   - **Email** — must be unique across the platform
   - **Role** — choose the initial base role from the dropdown
4. Click **Send Invitation** (إرسال الدعوة).

### What happens next

- The new user appears in the user list immediately with a status badge of **Invitation pending** (في انتظار الدعوة).
- An invitation email titled "Welcome to [app name] — Set your password" is sent to the address you entered.
- The invitation link is valid for **72 hours**. After it expires, you must resend it.

### What the invited user sees

The email contains a **Set your password** button. Clicking it opens a page where the invitee:

1. Enters a password that meets the platform password policy.
2. Confirms the password.
3. Clicks **Set Password & Sign In** (تعيين كلمة المرور وتسجيل الدخول).

After setting a password the account is activated, the user's email is verified automatically, and they are signed in. Their status changes from **Invitation pending** to **Active** (نشط).

> If the link has expired or was already used, the invitee sees a message: *"This invitation link has expired or has already been used. Please contact your administrator to issue a new invitation."* Use **Resend invitation** (described below) to send a fresh link.

---

## Resend an invitation

If the invitee did not receive the email or the 72-hour window expired:

1. Go to **Admin → Users**.
2. Find the user with status **Invitation pending**.
3. Click the **More actions** (المزيد من الخيارات) menu icon (⋯) on that row.
4. Click **Resend invitation** (إعادة الإرسال).

A new link is generated. The old link stops working immediately.

---

## Revoke an invitation

If you invited the wrong person or no longer want that account created:

1. Go to **Admin → Users**.
2. Find the user with status **Invitation pending**.
3. Click the **More actions** menu icon on that row.
4. Click **Revoke invitation** (إلغاء).

The pending user record remains in the list but no invitation link is active. The account cannot be activated. You can delete the membership record separately if needed.

---

## Deactivate a user

Use this when a team member leaves or an account is compromised.

1. Go to **Admin → Users**.
2. Find the active user.
3. Click the **More actions** menu icon on that row.
4. Click **Deactivate** (إلغاء التنشيط).
5. A confirmation dialog appears: *"This user will immediately lose access and cannot log in. You can reactivate them at any time."*
6. Click **Deactivate** (تعطيل) to confirm.

You can also deactivate from the user detail page: open the user by clicking their name, then use the **More actions** menu in the page header.

### What happens immediately

- All active sessions are invalidated — the user is signed out of every device at once.
- The user's status badge changes to **Deactivated** (معطّل).
- The user's data (leases, service requests, assignments, notes) is preserved and still appears in historical records.
- If the deactivated user attempts to log in, they see: *"Your account has been deactivated. Contact your administrator."*

### Constraint: you cannot deactivate yourself

The **Deactivate** option is disabled on your own row with the tooltip *"You cannot deactivate your own account."* Ask another admin to deactivate your account if needed.

---

## Reactivate a user

1. Go to **Admin → Users**.
2. Find the deactivated user.
3. Click the **More actions** menu icon on that row.
4. Click **Reactivate** (إعادة التنشيط).

The user's status returns to **Active**. Their role assignments from before deactivation are preserved and they can log in immediately.

---

## Send a password reset

Use this when a user cannot reset their password themselves (for example, they have lost access to their email).

1. Go to **Admin → Users**.
2. Find the active user.
3. Click the **More actions** menu icon on that row.
4. Click **Send password reset** (إرسال إعادة تعيين كلمة المرور).

A standard password reset email is sent to the user's registered address. A log entry is created recording that an admin triggered the reset.

---

## User status reference

| Status | Badge style | Meaning |
|--------|-------------|---------|
| **Active** (نشط) | Solid | Account is enabled; user can log in |
| **Invitation pending** (في انتظار الدعوة) | Outlined | Invitation sent; user has not yet set a password |
| **Deactivated** (معطّل) | Muted | Account disabled; user cannot log in |

---

## What you'll see

After each action a confirmation toast appears at the top of the screen, for example:

- *"Invitation sent to name@example.com."*
- *"[Name]'s account has been deactivated."*
- *"[Name]'s account has been reactivated."*
- *"Password reset email sent to name@example.com."*

The user list and user detail page update immediately to reflect the new status.

---

## Common issues

- **Invitee did not receive the email** — Check the email address for typos, then use **Resend invitation**. Ask the invitee to check their spam folder.
- **Invitation link says "expired or already used"** — The 72-hour window has passed. Use **Resend invitation** to generate a new link.
- **Deactivate button is greyed out** — You are viewing your own account. Another admin must deactivate it.
- **Reactivate is not available** — The user is not currently deactivated. Check their status badge.
- **Send password reset has no effect** — The user's account may be deactivated. Reactivate it first, then send the reset.

---

## Related

- [Assign a role to a user](./assign-a-role-to-a-user.md)
- [Roles and permissions — overview](./roles-and-permissions.md)
- [Scope a manager to specific properties](./manager-scope.md)
