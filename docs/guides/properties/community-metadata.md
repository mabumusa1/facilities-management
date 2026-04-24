---
title: Configure community metadata
area: properties
layout: guide
lang: en
---

# Configure community metadata

*Set a community's amenities, weekly operating days, and map location in one form.*

## Who this is for

Property Managers and Admins who set up and maintain communities (residential complexes, commercial buildings, mixed-use sites).

## Before you start

- You must be signed in as a **Property Manager** or **System Admin** with `communities.UPDATE` permission for the target community.
- The community must already exist. If you're creating a new community, complete the basic details first; metadata is edited on the same **Edit** page.

## Steps

1. In the left navigation, open **Properties → Communities**.
2. Click the community you want to configure, then click **Edit**.
3. Scroll to the three metadata sections: **Amenities**, **Working days**, **Map location**.

### Amenities

- Tick each amenity the community offers from the chip multi-select. Examples: Gym, Pool, Parking, Children's play area.
- Click the **×** inside a selected chip to remove it.
- The list comes from the platform's standard amenity catalog (26 items). You can't add custom amenities from this page.

### Working days

- Toggle each day of the week to mark it as a working day.
- The strip starts on **Saturday** (standard GCC calendar). An amber highlight marks working days; grey marks non-working days.
- Leaving every day off is valid — it signals the community is closed every day (useful for off-plan or inactive communities).

### Map location

- Enter the community's **latitude** and **longitude** as decimals. You can also click **Use my location** to auto-fill from the device.
- Both values must be set together. Setting only one triggers a validation error.
- Valid ranges: latitude **-90 to 90**, longitude **-180 to 180**.

4. Click **Save** at the bottom.

## What you'll see

- A toast: **Community updated.**
- The **Show** page reflects your changes: amenities appear as chips; working days appear as a coloured strip; coordinates appear with a small map preview.
- If any validation fails, the form stays open and shows inline errors under the affected fields.

## Common issues

- **"Amenity is invalid"** — the amenity list refreshes periodically. Reload the page and retry.
- **"Provide both latitude and longitude, or neither"** — you filled one coordinate but not the other. Enter both or clear both.
- **"Latitude must be between -90 and 90"** (or similar for longitude) — you entered an out-of-range value. Check the sign and magnitude.
- **My amenities disappeared after save** — they didn't. If you updated another field (name, working days) without touching the amenity multi-select, amenities are preserved. You'll see them again on refresh.

## Accessibility

- The amenity chip multi-select is keyboard-operable: tab to the chip, press **Space**/**Enter** to toggle. The dismiss **×** inside selected chips is its own focusable button.
- Working-day toggles are announced by screen readers as "Saturday, working day" / "Saturday, non-working day" etc., in the current locale.
- Map-location inputs support manual coordinate entry — you don't need geolocation permission if you'd rather type lat/lng directly.
- RTL (Arabic) layout: the working-day strip still renders Saturday-first; geographical coordinates are not flipped.

## Related

- *Create a community* (coming with the Properties onboarding story).
- *Amenity catalog* (coming with the Settings amenity-management story).
