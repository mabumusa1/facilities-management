---
title: Send a document for e-signature
area: documents
layout: guide
lang: en
---

# {{ page.title }}

*Send a generated document to a recipient for digital signature using an OTP-verified in-platform flow — no third-party SaaS required.*

## Who this is for

Property Managers and Account Admins who need to send lease contracts, booking documents, or other generated documents to residents or owners for signature.

## Before you start

- You must have the **documents.UPDATE** permission. Account Admins have this permission by default.
- A DocumentRecord must exist in **draft** or **link_expired** status before you can send it for signature. Documents are generated automatically by the relevant module (Leasing, Facilities, Accounting).
- The recipient does not need a platform account to sign. The signing link is public and OTP-gated.

## Steps

### Send a document for signature

1. Open the document record from the relevant module (for example, the Lease detail page or the generation result screen).
2. Review the document details — status, template version, and generated date.
3. Click **Send for Signature**.
4. Fill in the recipient's name, email, and optional phone number.
5. Click **Send**. The platform:
   - Generates a unique, cryptographically random signing token.
   - Records the recipient as a pending signer.
   - Transitions the document status from **Draft** to **Sent**.
   - Records the timestamp when it was sent.

The recipient receives the signing link by email (future: SMS). The link directs them to a public signing page — no login required.

### Resend a signing link

If the original link expired (after 7 days) or the recipient lost it:

1. Open the document record. A **Resend Link** button appears when the status is **Sent** or **Link Expired**.
2. Click **Resend Link**. The platform generates a new signing token, invalidates the old one, and resets the sent timestamp. The document status returns to **Sent**.

### The recipient's signing experience

1. The recipient opens the unique signing link in their browser.
2. They see the document content in a read-only view — English or Arabic based on their preference — with a **Sign Document** button.
3. Clicking **Sign Document** moves to the OTP verification step.
4. The recipient clicks **Send OTP**. A 6-digit code is sent to their contact.
5. They enter their name and the 6-digit OTP, then click **Verify & Sign**.
6. If the OTP is valid, the signature is recorded and the document status changes to **Signed**.

### What happens after signing

- A `DocumentSignature` record is created with the signer's name, email, signature timestamp, OTP verification timestamp, and IP address.
- The document status moves to **Signed**.
- A signed PDF copy is produced and stored (countersigned original + signature stamp).
- Both the recipient and the manager receive an automated email with the signed PDF.
- The signed document appears on the source record's document tab.

## Important details

- **Signing link expires after 7 days.** If the recipient does not sign within 7 days, the link becomes `link_expired`. The manager can resend from the document record.
- **OTP expires after 10 minutes.** If the recipient takes longer than 10 minutes to enter the OTP, they must request a new one.
- **OTP limited to 5 attempts.** After 5 incorrect entries, the OTP is invalidated and the recipient must request a new one.
- **Resending changes the token.** When a manager resends, the old link stops working immediately — anyone with the old link sees an error page.
- **Once signed, cannot be modified.** A signed document cannot be sent again or re-signed. The signature is permanent.
- **No authentication required.** The signing page is public and accessible to anyone with a valid link. The OTP provides security.

## Common issues

- **Send for Signature button is not visible** — the document must be in **Draft** or **Link Expired** status. If it is already Sent or Signed, the button is hidden.
- **Recipient says the link does not work** — the link may have been resent (the old one was invalidated), expired after 7 days, or the document may already be signed. Check the document status.
- **Recipient did not receive the OTP** — confirm the signer's email was entered correctly when sending. Resend the link with corrected details.
- **OTP is always rejected** — the recipient may have exceeded the 5-attempt limit or the OTP may have expired (10 minutes). Ask them to request a new OTP.
- **Document shows as "Already Signed"** — the document was already signed, possibly by someone else with the link.

## Related

- [Manage document templates](./document-templates.md)
- [Roles and permissions — overview](../admin/roles-and-permissions.md)
