# goatar.com Database Population Scripts

curl-based scripts to populate the goatar.com API with test data.

## Quick Start

```bash
# Make scripts executable
chmod +x *.sh

# Run all scripts in order
./run-all.sh

# Or run individual scripts
./01-communities.sh
./02-buildings.sh
# etc.
```

## Files

| Script | Description | Dependencies |
|--------|-------------|--------------|
| `00-config.sh` | Configuration (auth token, URLs, helpers) | None |
| `01-communities.sh` | Creates residential & commercial communities | None |
| `02-buildings.sh` | Creates buildings in communities | `01-communities.sh` |
| `03-units.sh` | Creates apartments, villas, offices, stores | `02-buildings.sh` |
| `04-contacts.sh` | Creates owners, tenants, admins, professionals | None |
| `05-leases.sh` | Creates lease contracts | `03-units.sh`, `04-contacts.sh` |
| `06-operations.sh` | Creates service requests, transactions | `03-units.sh`, `05-leases.sh` |
| `run-all.sh` | Master script to run all in order | All above |
| `created_ids.json` | Stores created entity IDs (auto-generated) | - |

## Configuration

Edit `00-config.sh` to update:

```bash
# Authentication
export AUTH_TOKEN="1|kS24aXkh96lDFnGy6gvV4YOX1l7P6il51eyyHDZv"
export TENANT_ID="scantest2026apr"
```

## Reference Data IDs

### Geographic (from seeder-raw)
| Entity | ID | Name |
|--------|-----|------|
| Country | 1 | Saudi Arabia |
| Currency | 1 | SAR |
| City | 1 | Riyadh |
| City | 4 | Jeddah |
| District | 15 | Al Olaya |
| District | 36 | King Fahd |

### Unit Categories & Types
| Category | ID | Types |
|----------|-----|-------|
| Residential | 2 | Apartment (17), Villa (22), Penthouse (18) |
| Commercial | 3 | Office (30), Store (26), Showroom (135) |

### Lease Contract Types
| Type | ID | Payment Schedules |
|------|-----|-------------------|
| Yearly | 13 | Monthly (4), Quarterly (5), Semi-Annual (6), Annual (7) |
| Monthly | 14 | Monthly (16), Upfront (17) |
| Daily | 15 | Upfront (18) |

### Status IDs
| Entity | Status | ID |
|--------|--------|-----|
| Unit | Vacant | 26 |
| Unit | Leased | 25 |
| Lease | New | 30 |
| Lease | Active | 31 |
| Request | New | 1 |

## API Endpoints Used

| Entity | Method | Endpoint |
|--------|--------|----------|
| Community | POST | `/rf/communities` |
| Building | POST | `/rf/buildings` |
| Unit | POST | `/rf/units` |
| Owner | POST | `/rf/owners` |
| Tenant | POST | `/rf/tenants` |
| Admin | POST | `/rf/admins` |
| Professional | POST | `/rf/professionals` |
| Lease | POST | `/rf/leases` |
| Request | POST | `/rf/requests` |
| Transaction | POST | `/rf/transactions` |

## Verification

After running scripts, verify data:

```bash
source ./00-config.sh

# Count communities
curl -s "$BASE_URL/rf/communities" -H "$AUTH" -H "$TENANT" | jq '.data | length'

# Count buildings
curl -s "$BASE_URL/rf/buildings" -H "$AUTH" -H "$TENANT" | jq '.data | length'

# Count units
curl -s "$BASE_URL/rf/units" -H "$AUTH" -H "$TENANT" | jq '.data | length'

# Count leases
curl -s "$BASE_URL/rf/leases" -H "$AUTH" -H "$TENANT" | jq '.data | length'
```

## Test Results (2026-04-20)

| Script | Status | Notes |
|--------|--------|-------|
| `01-communities.sh` | ✅ Working | Created IDs 3, 4 |
| `02-buildings.sh` | ✅ Working | Created IDs 2, 3, 4 |
| `03-units.sh` | ❌ Failing | API returns generic `unit_creation_failed` |
| `04-contacts.sh` | ✅ Working | Owners (6,7), Tenants (8,9,10), Professionals (11,12) |
| `05-leases.sh` | ❌ Failing | API returns 302 redirect |
| `06-operations.sh` | ⏸️ Blocked | Requires leases |

### Known Issues

**Unit Creation Fails:**
The API returns `{"code":400,"message":"messages.unit_creation_failed","errors":[]}` without specific validation errors. This may be due to:
- Subscription/plan unit quota limit
- Permission restrictions on the auth token
- Server-side validation not exposed via API

**Lease Creation Fails:**
The API returns HTTP 302 redirect to home page. This indicates:
- The auth token may not have lease creation permissions
- Server-side middleware is blocking the request

**Workaround:** Use existing unit ID 1 (pre-existing in tenant) for testing. Manual lease creation via UI may be required.

## Troubleshooting

### Token Expired
Update `AUTH_TOKEN` in `00-config.sh` with a fresh token.

### Validation Errors
Check the response output for specific field requirements.

### phone_country_code Error
Use ISO country code ("SA") not dial code ("+966").

### Missing Dependencies
Run scripts in order or use `run-all.sh`.

## Data Created

Running all scripts creates:
- 2 Communities (residential + commercial)
- 3 Buildings
- 6 Units (apartments, villa, penthouse, office, store) - **Blocked, see Known Issues**
- 2 Owners
- 3 Tenants (2 individual, 1 company)
- 2 Professionals
- 3 Leases (yearly contracts) - **Blocked, requires units**
- 3 Service Requests - **Blocked, requires units**
