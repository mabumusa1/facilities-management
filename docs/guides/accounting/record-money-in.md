---
title: Record a Money-In Transaction
area: accounting
layout: guide
lang: en
---

# {{ page.title }}

*Record an offline payment received from a resident or owner and let the platform generate the receipt automatically.*

## Who this is for

Property Manager / Admin

## Before you start

- You must have a role with the Accounting permission to create transactions.
- At least one income transaction category (Rent, Late Fee, Service Fee, or a custom category) must exist under **Accounting → Settings → Transaction Categories**.
- To have receipts emailed to payers, your email (SMTP) server must be configured.
- For the receipt to be generated automatically, your Invoice Settings (company name, logo, address) must be complete. If they are not, the transaction still saves but receipt generation is blocked until you configure the settings.

## Steps

### Record a payment

1. Go to **Accounting** in the main navigation.
2. Click **Transactions**, then click **New Transaction** (or **New Money In**).
3. The form opens with the direction badge **Money In** displayed at the top. This cannot be changed on this form.
4. In the **Payer** field, select the resident or owner who made the payment. The list is grouped into **Residents** and **Owners** sections.
5. Select the **Unit** associated with the payment.
6. Choose the **Category** — only income categories (Rent, Late Fee, Service Fee, etc.) appear here.
7. Choose the **Payment Method**: **Cash**, **Bank Transfer**, or **Cheque**.
8. Enter the **Amount**. Optionally enter a **Tax Amount**.
9. Set the **Payment Date**. The current date is pre-filled.
10. Enter a **Reference Number** (optional) — use this for a cheque number, bank reference, or any tracking code.
11. Optionally link the payment to a **Lease** by selecting it from the dropdown.
12. Add any **Notes** if needed.
13. Click **Save & Generate Receipt** to save the transaction and auto-generate a receipt. If Invoice Settings are incomplete, the button reads **Save Without Receipt** instead.

### What happens after you save

A receipt is created automatically for every transaction where Invoice Settings are complete. The receipt number is assigned sequentially by the platform — no manual step is required.

If Invoice Settings are incomplete when you save, the transaction is recorded but receipt generation is blocked. An amber warning banner on the form and on the transaction detail page tells you what to configure and provides a direct link to **App Settings → Invoice Settings**.

### Send the receipt by email

1. Open the transaction from **Accounting → Transactions**.
2. In the **Receipt** card, click **Send Receipt** (إرسال الإيصال).
3. A confirmation dialog shows the payer's name and email address. Review them, then click **Send Receipt** to confirm.
4. The receipt email is queued and sent to the payer's email address on file. The card updates to show the date it was last sent.
5. If you need to resend, the button changes to **Resend Receipt** (إعادة إرسال الإيصال).

> **Note:** The **Send Receipt** button is only visible to users with the `transactions.SEND_RECEIPT` permission. If you do not see the button, ask your System Admin to add this permission to your role.

> **Known limitation:** PDF download is not yet available. The **Download PDF** link appears only when a PDF file is attached to the receipt. Full PDF generation is planned for a future release.

## What you'll see

After saving, the transaction detail page shows:

- **Amount**, **Tax**, **Status**, and **Payment Date** summary cards at the top.
- A **Payer** card with the payer's name and payment method.
- A **Receipt** card showing the receipt status:
  - **Receipt Generated** — the receipt is ready. You can send it by email.
  - **Settings Incomplete** — Invoice Settings need to be configured before a receipt can be generated. An amber banner with a link to settings appears below the badge.
- A **Related** card with the linked lease, unit, and reference number (if provided).

## Common issues

- **The save button reads "Save Without Receipt" instead of "Save & Generate Receipt"** — Your Invoice Settings are incomplete. Click **Configure Settings** in the amber banner to complete them, then return to record the payment.
- **The "Send Receipt" button is not visible** — Your role does not have the `transactions.SEND_RECEIPT` permission. Ask your System Admin to grant it.
- **The payer's name does not appear in the Payer dropdown** — The resident or owner contact may not exist yet. Go to **Contacts → Residents** or **Contacts → Owners** to create the contact first, then return to record the transaction.
- **The receipt email did not arrive** — Check that the payer's email address is correct on their contact record. Also verify that SMTP is configured under App Settings.

## Related

- [Configure Transaction Categories](./transaction-categories.md)
- [Configure Invoice Settings](../settings/invoice-settings.md)
