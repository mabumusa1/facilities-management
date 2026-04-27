---
title: Manage active sessions
area: auth
layout: guide
lang: en
---

# Manage active sessions

*View all devices signed into your account and revoke any you don't recognize.*

## Who this is for

Any platform user who wants to monitor and control where their account is signed in.

## Accessing Active Sessions

1. Click your **profile avatar** in the top-right corner.
2. Select **Settings**.
3. Click the **Security** tab.
4. Scroll to the **Active sessions** section.

## How it works

The Active Sessions section lists every device currently signed into your account. Each entry shows:

- **Browser and operating system** (e.g., Chrome on macOS)
- **Approximate location** (e.g., Riyadh, Saudi Arabia) — shown when geolocation data is available
- **Last activity time** — how recently the device was active
- **Current session badge** — the device you are using right now is marked as "Current session"

### Revoke a single session

If you see a device you don't recognize:

1. Click **Revoke** next to that session.
2. Confirm in the dialog that opens.
3. The device is immediately signed out and removed from the list.

You cannot revoke your current session — the Revoke button is hidden for it.

### Log out all other sessions

To sign out of every device except your current one:

1. Click **Log out all other sessions** at the bottom of the list.
2. Confirm in the dialog.
3. All other sessions are invalidated. Only your current browser remains signed in.

### When only one session exists

If you have no other active sessions, you will see only your current session and the message "Only one active session." The "Log out all other sessions" button is hidden.

## Common issues

- **"Session management requires additional configuration"** — Your administrator has not enabled the session database storage. Contact your system administrator.
- **Revoke button does nothing** — You may need to re-enter your password. If prompted, complete the password confirmation flow and try again.
- **Session list is empty** — Refresh the page. If the issue persists, your session may be stored in a temporary cache that cannot be listed.
