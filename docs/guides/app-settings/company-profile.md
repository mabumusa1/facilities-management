---
title: Configure your company profile
area: app-settings
layout: guide
lang: en
---

# Configure your company profile

*Set your company name, logo, timezone, and brand colour so they appear consistently on contracts, invoices, and the sign-in page.*

## Who this is for

System Admins and Account Admins who want to brand all generated documents and emails with the correct company identity.

## Before you start

- You must be signed in as a **System Admin** or **Account Admin** with the `companyProfile.UPDATE` permission.
- Prepare your company logo files — PNG or SVG only, 2 MB max each. You can upload separate English and Arabic variants.

## Steps

1. In the left sidebar, open **App Settings → Company Profile**.
2. The page loads with skeleton placeholders while your current settings are fetched. Once loaded, you'll see four sections.

### Identity

Basic company information that appears on all documents.

- **Company name (English)** — required. Your company's official name in English.
- **Company name (Arabic)** — required. Your company's official name in Arabic. The input is right-to-left.
- **VAT registration number** — optional. Enter a valid 15-digit VAT number. It appears on all generated invoices.
- **CR number** — optional. Your commercial registration number.

### Logo & Brand

Upload your logo for English and Arabic documents.

- **Primary logo** — appears on all lease contracts and invoices. Click **Upload logo** (or **Change** if one already exists) and select a PNG or SVG file. A thumbnail preview shows your selected image.
- **Arabic logo variant** — optional. Used on Arabic-language documents. Falls back to the primary logo if not uploaded.
- To remove a logo, click the **Remove** button next to the preview. The file is deleted from storage on save.

Accepted formats: PNG or SVG. Maximum file size: 2 MB. Invalid files show an inline error before upload.

### Regional

Set the timezone that affects all system timestamps.

- **Timezone** — select from a dropdown grouped into **Gulf** timezones (pinned at the top) and all other timezones. The default is UTC.
- This affects lease start dates, invoice due dates, and booking slots across the platform.

### Brand Colors

Set the primary brand colour used in emails and document templates.

- **Primary brand color** — enter a hex colour code (e.g. `#1A73E8`). A live swatch previews the colour next to the input.
- This colour is applied to email notification headers and document template accents. It does **not** change the platform sidebar colour.

3. As you make changes, a **sticky bar** slides up from the bottom with an amber dot and the message *Unsaved changes*.
4. Click **Save changes** to persist, or **Discard** to revert all fields to their last saved state.
5. While saving, the button shows a spinner and *Saving…*.

## What you'll see

- **On success** — a toast: **Company profile saved successfully.** The sticky bar disappears, and the form stays open with your saved values.
- **On validation error** — invalid fields show inline error messages below them. The sticky bar remains visible.
- **On server error** — an amber banner appears at the top with a retry message.

## Common issues

- **"Company name (English) is required"** — you left the English name empty. Both names are required.
- **"Company name (Arabic) is required"** — you left the Arabic name empty.
- **"VAT number must be 15 digits"** — the VAT field accepts exactly 15 numeric digits. Remove any spaces or special characters.
- **"The file must be a PNG or SVG image"** — you attempted to upload a file in the wrong format. Use only PNG or SVG.
- **"The file size must not exceed 2 MB"** — your logo file is too large. Compress or resize it before uploading.
- **Timezone not applying** — the timezone change takes effect immediately after save for new timestamps. Existing timestamps are not retroactively recalculated.

## Accessibility

- Skeleton loaders use `aria-busy` on the card container while deferred data loads, then swap to real content.
- The sticky unsaved-changes bar is marked as a `region` with an `aria-label` so screen readers announce it when it appears.
- Server error banners are announced with `role="alert"` and `aria-live="assertive"`.
- All form controls have associated labels. Inline validation errors appear below their respective inputs.
- RTL (Arabic) layout: the logo variant section renders in proper right-to-left flow. English name and CR/VAT inputs remain left-to-right (`dir="ltr"`). The Arabic name input is right-to-left (`dir="rtl"`). The timezone selector and colour picker are direction-aware.

## Related

- *Invoice settings* (coming with a future Settings story).
- *General app settings* (coming with a future Settings story).
