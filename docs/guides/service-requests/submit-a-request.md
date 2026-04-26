---
title: Submit a Service Request
area: service-requests
layout: guide
lang: en
---

# {{ page.title }}

*Describe a maintenance or service issue in your unit so the property team can triage and send help.*

## Who this is for

Residents with an active account on the platform.

## Before you start

- You must be logged in. Guests and non-members see a 403 error.
- Your property manager must have set up at least one active service category. If the **Category** list is empty, contact your property manager.
- Have a description of the issue ready (minimum 10 characters).

## Steps

### Fill in the request form

1. In the main navigation, go to **Services → Submit Request** (تقديم طلب).
2. The page opens at **Submit a Service Request** (تقديم طلب خدمة).
3. Under **What needs fixing?** (ما الذي يحتاج إلى إصلاح؟), select a **Category** (الفئة) from the drop-down. Only categories configured for your community appear here.
4. Once you select a category, the **Subcategory** (الفئة الفرعية) drop-down activates. Select the subcategory that best matches your issue. If there is only one subcategory, it may be the only option.
5. Under **Where is the problem?** (أين المشكلة؟), confirm your **Community** (المجتمع) and **Unit** (الوحدة). These are pre-filled from your account. Select the **Room / Location** (الغرفة / الموقع) — choose from Kitchen, Bathroom, Living Room, Bedroom, Balcony, or Other.
6. Under **How urgent is it?** (ما مدى الإلحاح؟), choose:
   - **Normal** (عادي) — Fix within SLA.
   - **Urgent** (عاجل) — Safety or emergency.
7. Under **Describe the issue** (صف المشكلة), type a detailed description in the **Description** (وصف المشكلة) field. A character counter shows how much you have written.
8. Optionally, click **+ Add Photo** (+ إضافة صورة) to attach up to five photos (JPG or PNG, maximum 10 MB each). Click **Remove** (إزالة) to remove any photo before submitting.
9. Click **Submit Request** (تقديم الطلب).

### View your confirmation

After the request is submitted, the page transitions to the confirmation screen:

1. The heading shows **Request Submitted Successfully** (تم تقديم الطلب بنجاح).
2. Your **reference number** (رقم الطلب الخاص بك) appears in the format `SR-YYYY-NNNNN` (for example, `SR-2026-00042`). Click **Copy** (نسخ) to copy it to your clipboard. Save this number to quote it when following up.
3. The confirmation also shows:
   - **Expected response by** (الرد المتوقع بحلول) — the date and time by which a technician must acknowledge your request.
   - **Expected resolution by** (الحل المتوقع بحلول) — the target date and time for the work to be completed.
4. Click **View My Requests** (عرض طلباتي) to go to your request list, or **Submit Another** (تقديم طلب آخر) to start a new request.

### Track your requests

1. Go to **Services → My Requests** (طلباتي).
2. The page lists all your submitted requests as cards. Each card shows:
   - The reference number (e.g., `SR-2026-00042`).
   - The category and subcategory.
   - The current status.
   - The response and resolution SLA due dates.
3. Click any card to open the request detail.

## What you'll see

After submitting, the confirmation screen shows your unique reference number and the SLA due dates computed from the category's configured targets. A notification is sent when a technician is assigned and again when the work is completed.

On the **My Requests** list, each card reflects the latest status. The status updates automatically as the property team works on your request.

## Common issues

- **The Subcategory drop-down is greyed out** — Select a category first. The subcategory list only loads after a category is chosen.
- **The Submit Request button stays disabled** — Check that all required fields are filled: Category, Subcategory, Community, Unit, Urgency, and Description (at least 10 characters). The button enables only when all required fields have valid values.
- **The Category list is empty** — Your property manager has not yet configured categories for your community. Contact them directly.
- **You see a 403 error** — Your account does not have access to this tenant. Contact your property manager or system administrator.
- **A photo fails to attach** — Confirm the file is JPG or PNG and is under 10 MB. Files in other formats or over the size limit are rejected before upload.

## Related

- [Configure service request categories and SLA targets](./configure-categories-and-sla.md)
- [Service request reference codes](./service-request-reference-codes.md)
