---
title: Service Request Reference Codes
area: service-requests
layout: guide
lang: en
---

# {{ page.title }}

*Every service request receives a unique reference code (SR-YYYY-NNNNN) that you can use to look up, quote, or share a request without revealing internal IDs.*

## Who this is for

Property Managers and Admins who handle, escalate, or discuss service requests with residents or technicians.

## Before you start

- You must have access to the Service Requests area of the platform.
- Reference codes appear on every service request record. No extra configuration is needed.

## How reference codes work

When a service request is created, the platform automatically assigns it a reference code in the format:

```
SR-YYYY-NNNNN
```

Where:

- `SR` — fixed prefix indicating a Service Request.
- `YYYY` — the four-digit calendar year the request was created (for example, `2026`).
- `NNNNN` — a five-digit sequence number, padded with leading zeros, that counts up from `00001` within your account for that year.

**Example:** The third service request created in your account in 2026 receives the code `SR-2026-00003`. The sequence restarts at `00001` on the first request of each new year.

## Uniqueness guarantee

Reference codes are unique per account per year. No two open or closed requests in your account can share the same code for the same year. This means you can safely quote a code to a resident or in a support ticket and be certain it points to exactly one request.

## What you will see

The reference code appears:

- In the header of the service request detail page.
- In any list or search result row that shows service requests.
- On any exported or printed service request record.

## What this release enables

This release also adds two underlying tables that power upcoming features:

- **Messaging thread** — each service request will have a conversation thread where residents and staff can exchange updates. Internal notes (visible only to staff) and resident-visible messages are kept separate.
- **Activity timeline** — a tamper-proof log records every status change, assignment, and key action on a request (submitted, accepted, in progress, resolved, closed, and more). This will be visible in the request detail view in a future release.

These features are being built in stories #209–#221 and will have their own guides when they ship.

## Common issues

- **The code shows five zeros (SR-2026-00000).** This should not occur in normal use. If you see it, contact your account administrator — a code generation error may have occurred.
- **Code not visible on older requests.** Requests created before this release were backfilled with codes automatically. If a legacy record shows no code, re-save the record or contact support.

## Related

- [File a maintenance ticket](./file-a-maintenance-ticket.md)
