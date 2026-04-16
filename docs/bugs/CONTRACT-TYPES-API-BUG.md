# Bug Report: Contract Types API Misconfiguration

## Summary
The "Leasing → Contract Types" settings page in goatar.com is misconfigured, calling the wrong API endpoint which prevents creating rental contract types.

## Impact: CRITICAL
- **Blocks**: Quote creation, Lease creation, Service Request creation
- **Affects**: Entire leasing workflow

## Technical Details

### Expected Behavior
When saving a new Contract Type from `/settings/leasing/contract-types/AddNewSubcategory`, the form should call:
```
POST https://api.goatar.com/api-management/rf/rental-contract-types
```

### Actual Behavior
The form incorrectly calls:
```
POST https://api.goatar.com/api-management/rf/requests/sub-categories/
```

This returns a **422 Unprocessable Entity** error because the payload structure doesn't match the requests sub-categories schema.

### Additional Issue
Even if the correct endpoint were called, it returns **503 Service Unavailable**:
```javascript
fetch('https://api.goatar.com/api-management/rf/rental-contract-types', {
  headers: {
    'Authorization': 'Bearer ' + localStorage.getItem('token'),
    'X-Tenant': localStorage.getItem('X-Tenant')
  }
})
// Returns: 503 Service Unavailable
```

## Steps to Reproduce

1. Navigate to: `https://goatar.com/settings/leasing/contract-types`
2. Click "Add New Subcategory"
3. Fill in the form:
   - Subcategory Name: "Standard Residential"
   - Arabic Name: "سكني عادي"
   - Select Working Days (Sun-Thu)
   - Select Community (Test Community Alpha)
   - Select an Icon
4. Click "Save"
5. Open Network tab - observe POST to wrong endpoint

## Network Evidence

```
Request URL: https://api.goatar.com/api-management/rf/requests/sub-categories/
Request Method: POST
Status Code: 422 Unprocessable Entity
```

## Dependency Chain Blocked

```
Contract Type (BLOCKED)
    └── Quote Creation (BLOCKED)
        └── Lease Creation (BLOCKED)
            └── Unit-Tenant Assignment (BLOCKED)
                └── Service Request (BLOCKED)
```

## Environment

- Platform: goatar.com
- Date Discovered: 2026-04-15
- Browser: Chrome (via Playwright)
- Tenant: Test tenant with trial account

## Workaround

None available from client side. Requires server-side fix to:
1. Correct the API endpoint in the frontend code
2. Restore the `/rf/rental-contract-types` API service (currently 503)

## Related URLs

- Settings Page: `/settings/leasing/contract-types`
- Add Form: `/settings/leasing/contract-types/AddNewSubcategory`
- Wrong API: `/api-management/rf/requests/sub-categories/`
- Correct API: `/api-management/rf/rental-contract-types`
