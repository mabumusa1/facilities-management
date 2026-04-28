---
title: View and edit unit metadata
area: properties
layout: guide
lang: en
---

# View and edit unit metadata

*See and update a unit's type, room counts, specifications, amenities, and pricing reference from the unit detail page.*

## Who this is for

Property Managers and Admins who maintain unit records for Marketplace listings, Leasing quotes, and portfolio reporting.

## Before you start

- You must be signed in as a **Property Manager** or **System Admin**.
- The unit must already exist. If you are creating a unit for the first time, complete the basic details first; metadata is available on the same **Edit** page.
- Unit types and categories are configured in **Settings**. Contact your system administrator if the type or category you need does not appear in the dropdown.

## Steps

### View unit metadata

1. In the left navigation, open **Properties → Units**.
2. Click the unit you want to inspect.

The unit detail page shows four groups of cards:

- **Summary row** — Category, Type, Status, Area (sqm).
- **Details** — Floor, Year Built, Marketplace flag; **Occupancy** — current Owner and Tenant.
- **Specifications** — room counts, furnished status, parking bays, and view type (visible only when at least one specification or room count has been saved).
- **Amenities** — badge list of amenity tags; shows **No amenities listed** when none are assigned.
- **Pricing Reference** — Asking Rent with currency and period (visible only when an asking rent amount has been saved).
- **Description** — free-text about the unit (visible only when a description has been saved).

### Edit unit metadata

1. On the unit detail page, click **Edit** (in the top-right button bar).
2. The **Edit** page opens with several sections. Scroll to the sections below to update metadata fields.

#### Specifications

The **Specifications** fieldset contains two rows of controls.

**Room counts** (first row):

| Field | What to do |
|---|---|
| **Bedrooms** | Select a number from 0 to 10 in the dropdown. |
| **Bathrooms** | Select a number from 0 to 10 in the dropdown. |
| **Living Rooms** | Select a number from 0 to 10 in the dropdown. |

**Physical specifications** (second row):

| Field | What to do |
|---|---|
| **Furnished?** | Toggle the switch. **Yes** (مفروشة) means fully furnished; **No** (غير مفروشة) means unfurnished. |
| **Parking** | Select **None**, **1 Bay**, or **2 Bays** from the dropdown. |
| **View** | Select **No View**, **Sea View** (إطلالة بحرية), **City View** (إطلالة على المدينة), or **Garden View** (إطلالة على الحديقة). |

#### Amenities

The **Amenities** fieldset shows all amenity options available in the platform. Tick the checkbox next to each amenity that applies to this unit. Untick to remove an amenity.

#### Pricing Reference

The **Pricing Reference** fieldset sets the unit's indicative asking rent. This value is a reference for Marketplace listings and Leasing quotes — it is not a binding contract price.

| Field | What to do |
|---|---|
| **Currency** | Select the currency from the dropdown (e.g., SAR). |
| **Asking Rent** | Enter the rent amount. Must be greater than 0 if a currency is set. |
| **Period** | Select **Year** or **Month**. |

3. Click **Update Unit** to save all changes.

## What you'll see

- The unit detail page reloads and reflects the saved values.
- The **Specifications** card appears (if it was hidden before) with the saved room counts, furnished status, parking bays, and view type.
- The **Amenities** card shows the selected amenity badges, or **No amenities listed** if none are selected.
- The **Pricing Reference** card appears (if it was hidden before) showing the asking rent in the format: `[Currency code] [Amount] / [Period]`.
- Marketplace listings created from this unit can read the type, area, specifications, and asking rent without re-entry.
- If any validation fails, the form stays open and shows inline error messages under the affected fields.

## Validation rules

| Field | Rule |
|---|---|
| **Area (sqm)** | Must be greater than 0 when provided. |
| **Asking Rent** | Must be greater than 0 when provided. |
| **Bedrooms / Bathrooms / Living Rooms** | Integer, 0 – 10. |
| **Parking** | One of: None, 1 Bay, 2 Bays. |
| **View** | One of: No View, Sea View, City View, Garden View. |

## Common issues

- **"Area must be greater than 0"** — you entered 0 in the area field. Enter a positive number or leave the field empty.
- **Amenities card shows "No amenities listed" on the Marketplace listing** — this is expected behaviour when no amenity tags are selected. The listing is still publishable. Assign amenities on the Edit page to populate the card.
- **The unit type or category I need is not in the dropdown** — these values come from **Settings → Unit Types / Unit Categories**. Ask a system administrator to add the missing option.
- **Pricing Reference card does not appear on the detail page** — the card is hidden until an asking rent amount is saved. Complete the Pricing Reference section and click **Update Unit**.

## Related

- [Configure community metadata](./community-metadata.md)
